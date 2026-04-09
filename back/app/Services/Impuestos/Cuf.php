<?php

namespace App\Services\Impuestos;

class Cuf
{
    public function obtener(string $nit, string $fh, string $sucursal, string $modalidad, string $tipoEmision, string $codigoDocumentoFiscal, string $codigoDocumentoSector, string $numeroFactura, string $puntoVenta): string
    {
        $cadena = '';
        $cadena .= str_pad($nit, 13, '0', STR_PAD_LEFT);
        $cadena .= $fh;
        $cadena .= str_pad($sucursal, 4, '0', STR_PAD_LEFT);
        $cadena .= $modalidad;
        $cadena .= $tipoEmision;
        $cadena .= $codigoDocumentoFiscal;
        $cadena .= str_pad($codigoDocumentoSector, 2, '0', STR_PAD_LEFT);
        $cadena .= str_pad($numeroFactura, 10, '0', STR_PAD_LEFT);
        $cadena .= str_pad($puntoVenta, 4, '0', STR_PAD_LEFT);
        $cadena .= $this->calculaDigitoMod11($cadena, 1, 9, false);

        return $this->base16($cadena);
    }

    private function calculaDigitoMod11(string $dado, int $numDig, int $limMult, bool $x10): string
    {
        if (!$x10) {
            $numDig = 1;
        }

        for ($n = 1; $n <= $numDig; $n++) {
            $soma = 0;
            $mult = 2;
            for ($i = strlen($dado) - 1; $i >= 0; $i--) {
                $soma += ($mult * substr($dado, $i, 1));
                if (++$mult > $limMult) {
                    $mult = 2;
                }
            }

            $dig = $x10 ? (($soma * 10) % 11) % 10 : $soma % 11;

            if ($dig === 10) {
                $dado .= '1';
            } elseif ($dig === 11) {
                $dado .= '0';
            } else {
                $dado .= $dig;
            }
        }

        return substr($dado, strlen($dado) - $numDig, $numDig);
    }

    private function base16(string $number, bool $uppercase = true): string
    {
        $hexvalues = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'];
        $hexval = '';
        while ($number !== '0') {
            $hexval = $hexvalues[bcmod($number, '16')] . $hexval;
            $number = bcdiv($number, '16', 0);
        }

        return $uppercase ? strtoupper($hexval) : $hexval;
    }
}
