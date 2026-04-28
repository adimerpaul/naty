<?php

namespace App\Services\Impuestos;

use App\Models\Cufd;
use App\Models\Cui;
use App\Models\Venta;
use DOMDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleXMLElement;
use SoapClient;

class SiatService
{
    public function estadoActual(): array
    {
        $codigoPuntoVenta = config('siat.codigo_punto_venta');
        $codigoSucursal = config('siat.codigo_sucursal');

        return [
            'configurado' => $this->estaConfigurado(),
            'cuis' => $this->getCuisVigente($codigoPuntoVenta, $codigoSucursal),
            'cufd' => $this->getCufdVigente($codigoPuntoVenta, $codigoSucursal),
            'codigo_punto_venta' => $codigoPuntoVenta,
            'codigo_sucursal' => $codigoSucursal,
            'ambiente' => config('siat.ambiente'),
            'modalidad' => config('siat.modalidad'),
            'url' => config('siat.url'),
        ];
    }

    public function generarCuis(int $codigoPuntoVenta = 0, int $codigoSucursal = 0): Cui
    {
        $this->assertConfigurado();

        $vigente = $this->getCuisVigente($codigoPuntoVenta, $codigoSucursal);
        if ($vigente) {
            return $vigente;
        }

        $client = $this->soapClient('FacturacionCodigos?WSDL');
        $result = $client->cuis([
            'SolicitudCuis' => [
                'codigoAmbiente' => config('siat.ambiente'),
                'codigoModalidad' => config('siat.modalidad'),
                'codigoPuntoVenta' => $codigoPuntoVenta,
                'codigoSistema' => config('siat.codigo_sistema'),
                'codigoSucursal' => $codigoSucursal,
                'nit' => config('siat.nit'),
            ],
        ]);

        $respuesta = $result->RespuestaCuis ?? null;
        if (!$respuesta || empty($respuesta->codigo)) {
            throw new RuntimeException('SIAT no devolvio un CUIS valido.');
        }

        return Cui::query()->create([
            'codigo' => $respuesta->codigo,
            'fechaVigencia' => Carbon::parse($respuesta->fechaVigencia),
            'fechaCreacion' => now(),
            'codigoPuntoVenta' => $codigoPuntoVenta,
            'codigoSucursal' => $codigoSucursal,
        ]);
    }

    public function generarCufd(int $codigoPuntoVenta = 0, int $codigoSucursal = 0): Cufd
    {
        $this->assertConfigurado();

        $vigente = $this->getCufdVigente($codigoPuntoVenta, $codigoSucursal);
        if ($vigente) {
            return $vigente;
        }

        $cuis = $this->generarCuis($codigoPuntoVenta, $codigoSucursal);
        $client = $this->soapClient('FacturacionCodigos?WSDL');
        $result = $client->cufd([
            'SolicitudCufd' => [
                'codigoAmbiente' => config('siat.ambiente'),
                'codigoModalidad' => config('siat.modalidad'),
                'codigoPuntoVenta' => $codigoPuntoVenta,
                'codigoSistema' => config('siat.codigo_sistema'),
                'codigoSucursal' => $codigoSucursal,
                'cuis' => $cuis->codigo,
                'nit' => config('siat.nit'),
            ],
        ]);

        $respuesta = $result->RespuestaCufd ?? null;
        if (!$respuesta || empty($respuesta->codigo) || empty($respuesta->codigoControl)) {
            throw new RuntimeException('SIAT no devolvio un CUFD valido.');
        }

        $fechaCreacion = now();

        return Cufd::query()->create([
            'codigo' => $respuesta->codigo,
            'codigoControl' => $respuesta->codigoControl,
            'direccion' => $respuesta->direccion ?? config('siat.direccion'),
            'fechaVigencia' => $this->fechaVigenciaCufd($fechaCreacion),
            'fechaCreacion' => $fechaCreacion,
            'codigoPuntoVenta' => $codigoPuntoVenta,
            'codigoSucursal' => $codigoSucursal,
        ]);
    }

    public function facturarVenta(Venta $venta): array
    {
        $this->assertConfigurado();

        $venta->loadMissing(['detalles', 'cliente', 'user']);

        if (!$venta->facturado) {
            throw new RuntimeException('La venta no esta marcada como facturada.');
        }

        $documento = trim((string) ($venta->cliente?->ci ?: $venta->cliente?->nit ?: ''));
        if ($documento === '') {
            throw new RuntimeException('El cliente no tiene CI/NIT para facturar.');
        }

        if (($venta->detalles->count()) === 0) {
            throw new RuntimeException('La venta no tiene detalle para facturar.');
        }

        $codigoPuntoVenta = config('siat.codigo_punto_venta');
        $codigoSucursal = config('siat.codigo_sucursal');
        $cuis = $this->getCuisVigente($codigoPuntoVenta, $codigoSucursal);
        $cufd = $this->getCufdVigente($codigoPuntoVenta, $codigoSucursal);

        if (!$cuis) {
            throw new RuntimeException('No existe CUIS vigente. Genere primero el CUIS.');
        }
        if (!$cufd) {
            throw new RuntimeException('No existe CUFD vigente. Genere primero el CUFD.');
        }

        $fecha = $venta->fecha_venta
            ? $venta->fecha_venta->format('Y-m-d')
            : (optional($venta->created_at)?->toDateString() ?: now()->toDateString());
        $horaBase = optional($venta->created_at)->format('H:i:s') ?: now()->format('H:i:s');
        $fechaEnvio = Carbon::parse($fecha . ' ' . $horaBase)->format('Y-m-d\TH:i:s.000');
        $fechaCuf = Carbon::parse($fecha . ' ' . $horaBase)->format('YmdHis000');

        $cuf = (new Cuf())->obtener(
            (string) config('siat.nit'),
            $fechaCuf,
            (string) $codigoSucursal,
            (string) config('siat.modalidad'),
            '1',
            (string) config('siat.tipo_factura_documento'),
            (string) config('siat.codigo_documento_sector'),
            (string) $venta->id,
            (string) $codigoPuntoVenta
        ) . $cufd->codigoControl;

        $leyenda = $this->leyendaAleatoria();
        $xmlString = $this->buildXml($venta, $cuf, $cufd, $fechaEnvio, $leyenda);
        $paths = $this->persistXml($venta, $xmlString);
        $this->validarXml($paths['xml']);
        $archivo = $this->gzipContents($paths['gz']);
        $hashArchivo = hash('sha256', $archivo);

        $client = $this->soapClient('ServicioFacturacionCompraVenta?WSDL');
        $result = $client->recepcionFactura([
            'SolicitudServicioRecepcionFactura' => [
                'codigoAmbiente' => config('siat.ambiente'),
                'codigoDocumentoSector' => config('siat.codigo_documento_sector'),
                'codigoEmision' => 1,
                'codigoModalidad' => config('siat.modalidad'),
                'codigoPuntoVenta' => $codigoPuntoVenta,
                'codigoSistema' => config('siat.codigo_sistema'),
                'codigoSucursal' => $codigoSucursal,
                'cufd' => $cufd->codigo,
                'cuis' => $cuis->codigo,
                'nit' => config('siat.nit'),
                'tipoFacturaDocumento' => config('siat.tipo_factura_documento'),
                'archivo' => $archivo,
                'fechaEnvio' => $fechaEnvio,
                'hashArchivo' => $hashArchivo,
            ],
        ]);

        $respuesta = $result->RespuestaServicioFacturacion ?? null;
        $transaccion = (bool) ($respuesta->transaccion ?? false);
        $mensajes = collect($respuesta->mensajesList ?? [])->map(function ($item) {
            return trim((string) (($item->descripcion ?? '') ?: ($item->codigoDescripcion ?? '')));
        })->filter()->values()->all();

        $facturaEstado = $transaccion ? 'VALIDADA' : 'ERROR';
        $facturaError = $transaccion ? null : (implode(' | ', $mensajes) ?: 'SIAT rechazo la factura.');

        Venta::query()->whereKey($venta->id)->update([
            'cuf' => $cuf,
            'cufd' => $cufd->codigo,
            'leyenda' => $leyenda,
            'online' => $transaccion,
            'factura_estado' => $facturaEstado,
            'factura_error' => $facturaError,
            'factura_xml_path' => $paths['xml'],
            'factura_gz_path' => $paths['gz'],
            'siat_codigo_recepcion' => $respuesta->codigoRecepcion ?? null,
        ]);

        return [
            'ok' => $transaccion,
            'estado' => $facturaEstado,
            'cuf' => $cuf,
            'cufd' => $cufd->codigo,
            'leyenda' => $leyenda,
            'codigo_recepcion' => $respuesta->codigoRecepcion ?? null,
            'mensajes' => $mensajes,
            'xml_path' => $paths['xml'],
            'gz_path' => $paths['gz'],
            'respuesta' => $respuesta,
        ];
    }

    public function getCuisVigente(int $codigoPuntoVenta, int $codigoSucursal): ?Cui
    {
        return Cui::query()
            ->where('codigoPuntoVenta', $codigoPuntoVenta)
            ->where('codigoSucursal', $codigoSucursal)
            ->where('fechaVigencia', '>=', now())
            ->latest('id')
            ->first();
    }

    public function getCufdVigente(int $codigoPuntoVenta, int $codigoSucursal): ?Cufd
    {
        return Cufd::query()
            ->where('codigoPuntoVenta', $codigoPuntoVenta)
            ->where('codigoSucursal', $codigoSucursal)
            ->where('fechaVigencia', '>=', now())
            ->latest('id')
            ->first();
    }

    private function fechaVigenciaCufd(Carbon $fechaCreacion): Carbon
    {
        return $fechaCreacion->copy()->endOfDay();
    }

    private function soapClient(string $wsdl): SoapClient
    {
        return new SoapClient(config('siat.url') . $wsdl, [
            'stream_context' => stream_context_create([
                'http' => [
                    'header' => 'apikey: TokenApi ' . config('siat.token'),
                ],
            ]),
            'cache_wsdl' => WSDL_CACHE_NONE,
            'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE,
            'trace' => 1,
            'use' => SOAP_LITERAL,
            'style' => SOAP_DOCUMENT,
        ]);
    }

    private function assertConfigurado(): void
    {
        $faltantes = [];
        foreach (['token', 'url', 'nit', 'razon', 'codigo_sistema', 'direccion', 'telefono'] as $key) {
            if (blank(config('siat.' . $key))) {
                $faltantes[] = strtoupper($key);
            }
        }

        if ($faltantes !== []) {
            throw new RuntimeException('Faltan variables SIAT: ' . implode(', ', $faltantes));
        }
    }

    private function estaConfigurado(): bool
    {
        try {
            $this->assertConfigurado();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private function buildXml(Venta $venta, string $cuf, Cufd $cufd, string $fechaEnvio, string $leyenda): string
    {
        $cliente = $venta->cliente;
        $nombreCliente = trim((string) ($cliente?->razon ?: $cliente?->nombre ?: $venta->cliente_nombre ?: 'SIN NOMBRE'));
        $numeroDocumento = trim((string) ($cliente?->ci ?: $cliente?->nit ?: '0'));
        $codigoTipoDocumento = $cliente?->nit ? 5 : 1;
        $usuario = trim((string) ($venta->user?->username ?: $venta->user?->name ?: 'sistema'));
        $usuario = Str::of($usuario)->explode(' ')->first() ?: 'sistema';

        $detalleFactura = '';
        foreach ($venta->detalles as $detalle) {
            $detalleFactura .= '<detalle>'
                . '<actividadEconomica>' . $this->xmlSafe((string) config('siat.actividad_economica')) . '</actividadEconomica>'
                . '<codigoProductoSin>' . $this->xmlSafe((string) config('siat.codigo_producto_sin')) . '</codigoProductoSin>'
                . '<codigoProducto>' . $this->xmlSafe((string) ($detalle->producto_id ?: $detalle->id)) . '</codigoProducto>'
                . '<descripcion>' . $this->xmlSafe((string) $detalle->producto_nombre) . '</descripcion>'
                . '<cantidad>' . $this->numberString($detalle->cantidad) . '</cantidad>'
                . '<unidadMedida>' . $this->xmlSafe((string) config('siat.unidad_medida')) . '</unidadMedida>'
                . '<precioUnitario>' . $this->numberString($detalle->precio) . '</precioUnitario>'
                . '<montoDescuento>0</montoDescuento>'
                . '<subTotal>' . $this->numberString($detalle->subtotal) . '</subTotal>'
                . "<numeroSerie xsi:nil='true'/>"
                . "<numeroImei xsi:nil='true'/>"
                . '</detalle>';
        }

        return "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>"
            . "<facturaComputarizadaCompraVenta xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='facturaComputarizadaCompraVenta.xsd'>"
            . '<cabecera>'
            . '<nitEmisor>' . $this->xmlSafe((string) config('siat.nit')) . '</nitEmisor>'
            . '<razonSocialEmisor>' . $this->xmlSafe((string) config('siat.razon')) . '</razonSocialEmisor>'
            . '<municipio>' . $this->xmlSafe((string) config('siat.municipio')) . '</municipio>'
            . '<telefono>' . $this->xmlSafe((string) config('siat.telefono')) . '</telefono>'
            . '<numeroFactura>' . $venta->id . '</numeroFactura>'
            . '<cuf>' . $cuf . '</cuf>'
            . '<cufd>' . $cufd->codigo . '</cufd>'
            . '<codigoSucursal>' . config('siat.codigo_sucursal') . '</codigoSucursal>'
            . '<direccion>' . $this->xmlSafe((string) ($cufd->direccion ?: config('siat.direccion'))) . '</direccion>'
            . '<codigoPuntoVenta>' . config('siat.codigo_punto_venta') . '</codigoPuntoVenta>'
            . '<fechaEmision>' . $fechaEnvio . '</fechaEmision>'
            . '<nombreRazonSocial>' . $this->xmlSafe($nombreCliente) . '</nombreRazonSocial>'
            . '<codigoTipoDocumentoIdentidad>' . $codigoTipoDocumento . '</codigoTipoDocumentoIdentidad>'
            . '<numeroDocumento>' . $this->xmlSafe($numeroDocumento) . '</numeroDocumento>'
            . "<complemento xsi:nil='true'/>"
            . '<codigoCliente>' . ($cliente?->id ?: $venta->id) . '</codigoCliente>'
            . '<codigoMetodoPago>' . config('siat.codigo_metodo_pago') . '</codigoMetodoPago>'
            . "<numeroTarjeta xsi:nil='true'/>"
            . '<montoTotal>' . $this->numberString($venta->total) . '</montoTotal>'
            . '<montoTotalSujetoIva>' . $this->numberString($venta->total) . '</montoTotalSujetoIva>'
            . '<codigoMoneda>' . config('siat.codigo_moneda') . '</codigoMoneda>'
            . '<tipoCambio>' . $this->numberString(config('siat.tipo_cambio')) . '</tipoCambio>'
            . '<montoTotalMoneda>' . $this->numberString($venta->total) . '</montoTotalMoneda>'
            . "<montoGiftCard xsi:nil='true'/>"
            . '<descuentoAdicional>0</descuentoAdicional>'
            . '<codigoExcepcion>' . ($codigoTipoDocumento === 5 ? 1 : 0) . '</codigoExcepcion>'
            . "<cafc xsi:nil='true'/>"
            . '<leyenda>' . $this->xmlSafe($leyenda) . '</leyenda>'
            . '<usuario>' . $this->xmlSafe((string) $usuario) . '</usuario>'
            . '<codigoDocumentoSector>' . config('siat.codigo_documento_sector') . '</codigoDocumentoSector>'
            . '</cabecera>'
            . $detalleFactura
            . '</facturaComputarizadaCompraVenta>';
    }

    private function persistXml(Venta $venta, string $xmlString): array
    {
        $dir = config('siat.storage_dir');
        File::ensureDirectoryExists($dir);

        $xmlPath = $dir . DIRECTORY_SEPARATOR . 'venta-' . $venta->id . '.xml';
        $gzPath = $xmlPath . '.gz';

        $xml = new SimpleXMLElement($xmlString);
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($xmlPath);

        $fp = gzopen($gzPath, 'w9');
        gzwrite($fp, file_get_contents($xmlPath));
        gzclose($fp);

        return ['xml' => $xmlPath, 'gz' => $gzPath];
    }

    private function validarXml(string $xmlPath): void
    {
        $xsdPath = config('siat.xsd_compra_venta');
        if (!File::exists($xsdPath)) {
            throw new RuntimeException('No se encontro el XSD de compra venta en ' . $xsdPath);
        }

        $xml = new DOMDocument();
        $xml->load($xmlPath);

        if (!$xml->schemaValidate($xsdPath)) {
            throw new RuntimeException('El XML generado no es valido contra el XSD de compra venta.');
        }
    }

    private function gzipContents(string $fileName): string
    {
        $handle = fopen($fileName, 'rb');
        $contents = fread($handle, filesize($fileName));
        fclose($handle);

        return $contents;
    }

    private function leyendaAleatoria(): string
    {
        $leyendas = [
            'Ley N° 453: Puedes acceder a la reclamacion cuando tus derechos han sido vulnerados.',
            'Ley N° 453: El proveedor debe brindar atencion sin discriminacion, con respeto, calidez y cordialidad.',
            'Ley N° 453: Esta prohibido importar, distribuir o comercializar productos expirados o prontos a expirar.',
            'Ley N° 453: Los alimentos declarados de primera necesidad deben suministrarse de manera adecuada y oportuna.',
            'Ley N° 453: Tienes derecho a recibir informacion sobre las caracteristicas y contenidos de los productos que consumes.',
            'Ley N° 453: Tienes derecho a un trato equitativo sin discriminacion en la oferta de productos.',
            'Ley N° 453: El proveedor debera entregar el producto en las modalidades y terminos ofertados o convenidos.',
            'Ley N° 453: Los servicios deben suministrarse en condiciones de inocuidad, calidad y seguridad.',
        ];

        return $leyendas[array_rand($leyendas)];
    }

    private function xmlSafe(string $value): string
    {
        return htmlspecialchars(trim($value), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function numberString(float|int|string|null $value): string
    {
        return number_format((float) $value, 2, '.', '');
    }
}
