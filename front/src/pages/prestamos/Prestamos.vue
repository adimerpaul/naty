<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center tipo-banner" :class="tipoThemeClass">
        <div>
          <div class="text-h6">{{ tituloPagina }}</div>
          <div class="text-caption text-grey-7">Registro de prestamos y venta de material desde inventarios</div>
        </div>
        <q-space />
        <q-btn color="positive" no-caps icon="add" label="Registrar" @click="nuevoPrestamo" class="q-mr-sm" />
        <q-btn-dropdown color="deep-orange" no-caps icon="picture_as_pdf" label="Reportes" :loading="loadingPdf" class="q-mr-sm">
          <q-list dense>
            <q-item clickable v-close-popup @click="abrirReporte('todos')">
              <q-item-section avatar><q-icon name="summarize" color="deep-orange" /></q-item-section>
              <q-item-section>Todos los reportes</q-item-section>
            </q-item>
            <q-item clickable v-close-popup @click="abrirReporte('prestamo')">
              <q-item-section avatar><q-icon name="assignment_returned" color="orange-8" /></q-item-section>
              <q-item-section>Prestamos</q-item-section>
            </q-item>
            <q-item clickable v-close-popup @click="abrirReporte('venta')">
              <q-item-section avatar><q-icon name="sell" color="primary" /></q-item-section>
              <q-item-section>Vendidos</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn color="primary" flat no-caps icon="refresh" label="Actualizar" :loading="loading" @click="cargarAll" />
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-caption text-grey-7">Vendido material</div>
            <div class="text-h6 text-weight-bold">{{ money(cajaResumen.vendidos_monto) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-caption text-grey-7">Prestamos material</div>
            <div class="text-h6 text-weight-bold">{{ money(cajaResumen.prestamos_monto) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-caption text-grey-7">Pendiente cobro</div>
            <div class="text-h6 text-weight-bold text-orange-8">{{ money(cajaResumen.pendiente_monto) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <div>
              <div class="text-caption text-grey-7">Caja prestamos</div>
              <div class="text-h6 text-weight-bold text-primary">{{ money(cajaResumen.caja_total) }} Bs</div>
            </div>
            <q-space />
            <q-btn dense flat round icon="history" color="primary" @click="abrirCajaHistorial" />
            <q-btn dense flat round icon="remove_circle" color="negative" @click="abrirCajaMovimiento('egreso')" />
            <q-btn dense flat round icon="add_circle" color="positive" @click="abrirCajaMovimiento('ingreso')" />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-card flat bordered>
      <q-table
        dense
        flat
        bordered
        :rows="prestamos"
        :columns="colsPrestamos"
        row-key="id"
        :filter="filter"
        v-model:pagination="pagination"
        :rows-per-page-options="[25, 50, 100]"
        :loading="loading"
      >
        <template #top-right>
          <q-input v-model="filter" dense outlined label="Buscar" style="width: 260px">
            <template #append><q-icon name="search" /></template>
          </q-input>
        </template>
        <template #body-cell-actions="props">
          <q-td :props="props">
            <q-btn-dropdown dense color="primary" label="Opciones" no-caps>
              <q-list dense>
                <q-item clickable v-close-popup :disable="!puedeRetornar(props.row)" @click="abrirRetornoParcial(props.row)">
                  <q-item-section avatar><q-icon name="keyboard_return" color="primary" /></q-item-section>
                  <q-item-section>Retorno parcial</q-item-section>
                </q-item>
                <q-item clickable v-close-popup :disable="!puedeRetornar(props.row)" @click="retornarPrestamo(props.row)">
                  <q-item-section avatar><q-icon name="assignment_return" color="positive" /></q-item-section>
                  <q-item-section>Retornar completo</q-item-section>
                </q-item>
                <q-item clickable v-close-popup @click="abrirHistorial(props.row)">
                  <q-item-section avatar><q-icon name="history" color="indigo" /></q-item-section>
                  <q-item-section>Historial retornos</q-item-section>
                </q-item>
                <q-item clickable v-close-popup @click="imprimirPrestamo(props.row)">
                  <q-item-section avatar><q-icon name="print" color="primary" /></q-item-section>
                  <q-item-section>Imprimir</q-item-section>
                </q-item>
                <q-item clickable v-close-popup :disable="!puedeDarBaja(props.row)" @click="darBajaPrestamo(props.row)">
                  <q-item-section avatar><q-icon name="money_off" color="negative" /></q-item-section>
                  <q-item-section>Dar de baja</q-item-section>
                </q-item>
              </q-list>
            </q-btn-dropdown>
          </q-td>
        </template>
        <template #body-cell-tipo="props">
          <q-td :props="props">
            <q-chip dense :color="props.row.tipo === 'venta' ? 'primary' : 'warning'" text-color="white">
              {{ props.row.tipo === 'venta' ? 'Venta' : 'Prestamo' }}
            </q-chip>
          </q-td>
        </template>
        <template #body-cell-estado="props">
          <q-td :props="props">
            <q-chip dense :color="estadoColor(props.row.estado)" text-color="white">{{ props.row.estado }}</q-chip>
          </q-td>
        </template>
        <template #body-cell-fisico_recibido="props">
          <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.fisico_recibido) }}</q-td>
        </template>
        <template #body-cell-monto_pendiente="props">
          <q-td :props="props" class="text-right text-negative text-weight-bold">{{ money(props.row.monto_pendiente ?? props.row.efectivo_actual) }}</q-td>
        </template>
      </q-table>
    </q-card>

    <q-dialog v-model="dialogPrestamo">
      <q-card style="width: 980px; max-width: 98vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Registrar {{ tituloCorto }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarPrestamo">
            <q-select
              v-model="pres.cliente_id"
              dense
              outlined
              emit-value
              map-options
              use-input
              input-debounce="200"
              :options="clientesFiltrados"
              label="Cliente"
              :rules="[req]"
              class="q-mb-sm"
              @filter="filtrarClientes"
            />
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4">
                <q-select
                  v-model="pres.tipo"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="[
                    { label: 'Prestamo', value: 'prestamo' },
                    { label: 'Venta material', value: 'venta' }
                  ]"
                  label="Tipo"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model.number="pres.cantidad" dense outlined type="number" min="1" label="Cantidad" />
              </div>
              <div class="col-12 col-md-4">
                <q-input :model-value="money(selectedInventario ? selectedInventario.precio : 0)" dense outlined readonly label="Precio referencia" suffix="Bs" />
              </div>
              <div class="col-12 col-md-8">
                <q-select
                  v-model="pres.inventario_id"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="inventariosOptions"
                  label="Inventario"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input :model-value="selectedInventario ? selectedInventario.cantidad : 0" dense outlined readonly label="Disponible" />
              </div>
              <div class="col-12 col-md-4">
                <q-input
                  v-model.number="pres.efectivo"
                  dense
                  outlined
                  type="number"
                  min="0"
                  step="0.01"
                  label="Monto recibido"
                  @update:model-value="pres.efectivo_manual = true"
                >
                  <template #append>
                    <q-btn flat dense no-caps size="sm" icon="calculate" label="Sugerir" @click="aplicarPrecioSugerido" />
                  </template>
                </q-input>
              </div>
              <div class="col-12 col-md-4" v-if="pres.tipo === 'venta'">
                <q-select
                  v-model="pres.metodo_pago"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="[
                    { label: 'Efectivo', value: 'efectivo' },
                    { label: 'QR', value: 'qr' }
                  ]"
                  label="Metodo"
                />
              </div>
              <div class="col-12 col-md-8">
                <q-input v-model="pres.observacion" dense outlined label="Fisico recibido" />
              </div>
              <div class="col-12 col-md-4 flex items-end">
                <q-btn
                  color="primary"
                  no-caps
                  icon="add"
                  label="Agregar a tabla"
                  class="full-width"
                  @click="agregarPrestamoItem"
                />
              </div>
              <div class="col-12 q-mt-sm">
                <q-table
                  dense
                  flat
                  bordered
                  :rows="prestamoItems"
                  :columns="colsPrestamoItems"
                  row-key="uid"
                  hide-pagination
                  :rows-per-page-options="[0]"
                >
                  <template #body-cell-tipo="props">
                    <q-td :props="props">
                      <q-chip dense :color="props.row.tipo === 'venta' ? 'primary' : 'warning'" text-color="white">
                        {{ props.row.tipo === 'venta' ? 'Venta' : 'Prestamo' }}
                      </q-chip>
                    </q-td>
                  </template>
                  <template #body-cell-efectivo="props">
                    <q-td :props="props" class="text-right">{{ money(props.row.efectivo) }}</q-td>
                  </template>
                  <template #body-cell-actions="props">
                    <q-td :props="props">
                      <q-btn dense flat round color="negative" icon="delete" @click="quitarPrestamoItem(props.row.uid)" />
                    </q-td>
                  </template>
                </q-table>
              </div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat color="negative" no-caps label="Cancelar" v-close-popup />
              <q-btn color="primary" no-caps label="Guardar todo" type="submit" :loading="loadingGuardar" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogRetorno">
      <q-card style="width: 560px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Retorno parcial #{{ retornoRow?.id }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm q-mb-sm">
            <div class="col-6"><q-input :model-value="retornoRow?.cantidad_actual || 0" dense outlined readonly label="Cantidad pendiente" /></div>
            <div class="col-6"><q-input :model-value="money(retornoRow?.monto_pendiente ?? retornoRow?.efectivo_actual)" dense outlined readonly label="Monto pendiente" /></div>
          </div>
          <q-form @submit.prevent="guardarRetornoParcial">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4">
                <q-input v-model.number="retorno.cantidad" dense outlined type="number" min="0" label="Cantidad" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model.number="retorno.efectivo" dense outlined type="number" min="0" step="0.01" label="Fisico retornado" />
              </div>
              <div class="col-12">
                <q-input v-model="retorno.observacion" dense outlined label="Observacion" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat color="negative" no-caps label="Cancelar" v-close-popup />
              <q-btn color="primary" no-caps label="Guardar retorno" type="submit" :loading="loadingRetorno" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogHistorial">
      <q-card style="width: 760px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Historial de retornos #{{ retornoRow?.id }}</div>
          <q-space />
          <q-btn v-if="retornoRow" flat dense no-caps icon="print" color="primary" label="Imprimir" class="q-mr-sm" @click="imprimirHistorial(retornoRow)" />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-table dense flat bordered :rows="retornoRow?.retornos || []" :columns="colsRetornos" row-key="id" hide-pagination>
            <template #body-cell-efectivo="props">
              <q-td :props="props" class="text-right">{{ money(props.row.efectivo) }}</q-td>
            </template>
            <template #body-cell-user="props">
              <q-td :props="props">{{ props.row.user?.name || props.row.user?.username || '-' }}</q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogCajaMov">
      <q-card style="width: 520px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">{{ cajaForm.tipo_movimiento === 'egreso' ? 'Retirar de caja prestamos' : 'Ingreso caja prestamos' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarCajaMovimiento">
            <q-input v-model.number="cajaForm.monto" dense outlined type="number" min="0.01" step="0.01" label="Monto" :rules="[req]" class="q-mb-sm" />
            <q-input v-model="cajaForm.observacion" dense outlined label="Observacion" class="q-mb-md" />
            <div class="row justify-end q-gutter-sm">
              <q-btn flat color="negative" no-caps label="Cancelar" v-close-popup />
              <q-btn color="primary" no-caps label="Guardar" type="submit" :loading="loadingCajaMov" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogCajaHistorial">
      <q-card style="width: 900px; max-width: 98vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Historial caja prestamos</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm q-mb-sm items-end">
            <div class="col-12 col-md-3"><q-input v-model="cajaFiltros.date_from" dense outlined type="date" label="Desde" /></div>
            <div class="col-12 col-md-3"><q-input v-model="cajaFiltros.date_to" dense outlined type="date" label="Hasta" /></div>
            <div class="col-12 col-md-3"><q-btn color="primary" no-caps icon="refresh" label="Actualizar" :loading="loadingCajaHistorial" @click="cargarCajaMovimientos" /></div>
            <div class="col-12 col-md-3 text-right text-weight-bold">Saldo: {{ money(cajaResumen.caja_total) }} Bs</div>
          </div>
          <q-table dense flat bordered :rows="cajaMovimientos" :columns="colsCajaMov" row-key="id" :loading="loadingCajaHistorial" />
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogReporte">
      <q-card style="width: 620px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Reporte de prestamos</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-6">
              <q-select
                v-model="reporte.tipo"
                dense
                outlined
                emit-value
                map-options
                :options="reporteTipoOptions"
                label="Reporte"
              />
            </div>
            <div class="col-12 col-md-6">
              <q-select
                v-model="reporte.estado"
                dense
                outlined
                emit-value
                map-options
                clearable
                :options="reporteEstadoOptions"
                label="Estado"
              />
            </div>
            <div class="col-12 col-md-4">
              <q-toggle v-model="reporte.unDia" dense label="Solo un dia" @update:model-value="syncReporteUnDia" />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="reporte.date_from" dense outlined type="date" :label="reporte.unDia ? 'Fecha' : 'Desde'" @update:model-value="syncReporteUnDia" />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="reporte.date_to" dense outlined type="date" label="Hasta" :disable="reporte.unDia" />
            </div>
          </div>
          <div class="row justify-end q-gutter-sm q-mt-md">
            <q-btn flat color="negative" no-caps label="Cancelar" v-close-popup />
            <q-btn color="primary" no-caps icon="print" label="Imprimir ticket" :loading="loadingPdf" @click="imprimirReporteTicket" />
            <q-btn color="deep-orange" no-caps icon="picture_as_pdf" label="Generar PDF" :loading="loadingPdf" @click="exportReportePdf" />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
    <div id="myElement" class="hidden"></div>
  </q-page>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'

export default {
  name: 'PrestamosPage',
  data () {
    return {
      loading: false,
      loadingGuardar: false,
      loadingRetorno: false,
      loadingPdf: false,
      filter: '',
      prestamos: [],
      clientes: [],
      clientesFiltrados: [],
      inventarios: [],
      prestamoItems: [],
      dialogPrestamo: false,
      dialogRetorno: false,
      dialogHistorial: false,
      dialogCajaMov: false,
      dialogCajaHistorial: false,
      dialogReporte: false,
      retornoRow: null,
      loadingCajaMov: false,
      loadingCajaHistorial: false,
      cajaResumen: {
        vendidos_monto: 0,
        prestamos_monto: 0,
        pendiente_monto: 0,
        caja_total: 0
      },
      cajaForm: {
        tipo_movimiento: 'egreso',
        monto: null,
        observacion: ''
      },
      cajaFiltros: {
        date_from: new Date().toISOString().slice(0, 10),
        date_to: new Date().toISOString().slice(0, 10)
      },
      cajaMovimientos: [],
      reporte: {
        tipo: null,
        estado: null,
        unDia: true,
        date_from: new Date().toISOString().slice(0, 10),
        date_to: new Date().toISOString().slice(0, 10)
      },
      retorno: {
        cantidad: 0,
        efectivo: 0,
        fisico: '',
        observacion: ''
      },
      pagination: { page: 1, rowsPerPage: 50, sortBy: 'id', descending: true },
      pres: {
        cliente_id: null,
        inventario_id: null,
        cantidad: 1,
        tipo: 'prestamo',
        tipo_venta: 'detalle',
        efectivo: 0,
        efectivo_manual: false,
        metodo_pago: 'efectivo',
        fisico: '',
        observacion: ''
      },
      colsPrestamos: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'cliente', label: 'Cliente', field: row => row.cliente?.nombre || '-', align: 'left' },
        { name: 'inventario', label: 'Inventario', field: row => row.inventario?.nombre || '-', align: 'left' },
        { name: 'cantidad', label: 'Cant. prestada', field: 'cantidad', align: 'right' },
        { name: 'cantidad_actual', label: 'Cant. pendiente', field: 'cantidad_actual', align: 'right' },
        { name: 'tipo', label: 'Tipo', field: 'tipo', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'fisico_recibido', label: 'Monto recibido', field: 'fisico_recibido', align: 'right' },
        { name: 'monto_pendiente', label: 'Monto pendiente', field: 'monto_pendiente', align: 'right' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' }
      ],
      colsPrestamoItems: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'inventario_nombre', label: 'Inventario', field: 'inventario_nombre', align: 'left' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'tipo', label: 'Tipo', field: 'tipo', align: 'left' },
        { name: 'efectivo', label: 'Monto recibido', field: 'efectivo', align: 'right' },
        { name: 'observacion', label: 'Fisico recibido', field: 'observacion', align: 'left' }
      ],
      colsRetornos: [
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'efectivo', label: 'Fisico retornado', field: 'efectivo', align: 'right' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' },
        { name: 'user', label: 'Usuario', field: row => row.user?.name || '-', align: 'left' }
      ],
      colsCajaMov: [
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha', field: 'created_at', align: 'left' },
        { name: 'tipo_movimiento', label: 'Tipo', field: 'tipo_movimiento', align: 'left' },
        { name: 'monto', label: 'Monto', field: 'monto', align: 'right' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' },
        { name: 'usuario', label: 'Usuario', field: 'usuario', align: 'left' }
      ],
      reporteTipoOptions: [
        { label: 'Todos', value: null },
        { label: 'Prestamos', value: 'prestamo' },
        { label: 'Vendidos', value: 'venta' }
      ],
      reporteEstadoOptions: [
        { label: 'Vendido', value: 'VENDIDO' },
        { label: 'En prestamo', value: 'EN PRESTAMO' },
        { label: 'Parcial', value: 'PARCIAL' },
        { label: 'Retornado', value: 'RETORNADO' },
        { label: 'Anulado', value: 'ANULADO' },
        { label: 'Baja', value: 'BAJA' }
      ]
    }
  },
  computed: {
    tipoCliente () { return this.$route.params.tipo === 'local' ? 'local' : 'detalle' },
    tituloPagina () { return this.tipoCliente === 'local' ? 'Prestamo local' : 'Prestamo detalle' },
    tituloCorto () { return this.tipoCliente === 'local' ? 'prestamo local' : 'prestamo detalle' },
    tipoThemeClass () { return this.tipoCliente === 'local' ? 'tipo-local' : 'tipo-detalle' },
    inventariosOptions () {
      return this.inventarios
        .filter(i => Number(i.cantidad || 0) > 0 && this.isInventarioActivo(i))
        .map(i => ({ label: `${i.nombre} (${i.cantidad}) - ${this.money(i.precio)} Bs`, value: i.id }))
    },
    selectedInventario () {
      return this.inventarios.find(i => i.id === this.pres.inventario_id) || null
    }
  },
  watch: {
    '$route.params.tipo' () {
      this.pres = this.defaultPres()
      this.cargarAll()
    },
    'pres.inventario_id' () {
      this.pres.efectivo_manual = false
      this.recalcularPrecio()
    },
    'pres.cantidad' () {
      this.recalcularPrecio()
    },
    'pres.tipo' () {
      this.recalcularPrecio()
    }
  },
  mounted () {
    this.cargarAll()
  },
  methods: {
    defaultPres () {
      return {
        cliente_id: null,
        inventario_id: null,
        cantidad: 1,
        tipo: 'prestamo',
        tipo_venta: this?.tipoCliente || 'detalle',
        efectivo: 0,
        efectivo_manual: false,
        metodo_pago: 'efectivo',
        fisico: '',
        observacion: ''
      }
    },
    req (v) { return !!v || 'Campo requerido' },
    money (n) { return Number(n || 0).toFixed(2) },
    isInventarioActivo (inventario) {
      return String(inventario?.estado || '').toUpperCase() === 'ACTIVO'
    },
    estadoColor (estado) {
      if (estado === 'VENDIDO') return 'positive'
      if (estado === 'RETORNADO') return 'indigo'
      if (estado === 'ANULADO') return 'negative'
      if (estado === 'BAJA') return 'brown-7'
      if (estado === 'PARCIAL') return 'deep-orange'
      return 'orange-8'
    },
    puedeRetornar (row) {
      return ['EN PRESTAMO', 'PARCIAL'].includes(row.estado)
    },
    puedeDarBaja (row) {
      if (row.tipo === 'venta') return false
      return !['RETORNADO', 'ANULADO', 'BAJA'].includes(row.estado)
    },
    async cargarAll () {
      this.loading = true
      try {
        await Promise.all([this.cargarInventarios(), this.cargarPrestamos(), this.cargarClientes(), this.cargarCajaResumen()])
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar prestamos')
      } finally {
        this.loading = false
      }
    },
    async cargarInventarios () {
      const r = await this.$axios.get('inventarios')
      this.inventarios = (r.data || []).filter(i => this.isInventarioActivo(i))
    },
    async cargarPrestamos () {
      const r = await this.$axios.get('prestamos', { params: { tipo_venta: this.tipoCliente } })
      this.prestamos = r.data || []
    },
    async cargarClientes () {
      const r = await this.$axios.get('clientes', { params: { tipo_cliente: this.tipoCliente } })
      this.clientes = (r.data || []).filter(c => !!c.estado)
      this.clientesFiltrados = this.clientesOptions(this.clientes)
    },
    async cargarCajaResumen () {
      const r = await this.$axios.get('prestamos/caja/resumen', { params: { tipo_venta: this.tipoCliente } })
      this.cajaResumen = r.data || this.cajaResumen
    },
    abrirReporte (tipo) {
      const today = new Date().toISOString().slice(0, 10)
      this.reporte = {
        tipo: tipo === 'todos' ? null : tipo,
        estado: null,
        unDia: true,
        date_from: today,
        date_to: today
      }
      this.dialogReporte = true
    },
    syncReporteUnDia () {
      if (this.reporte.unDia) {
        this.reporte.date_to = this.reporte.date_from
      }
    },
    reporteRowsFiltradas () {
      this.syncReporteUnDia()
      const desde = this.reporte.date_from || ''
      const hasta = this.reporte.date_to || desde
      return (this.prestamos || []).filter(row => {
        const fecha = String(row.fecha || '').slice(0, 10)
        const okTipo = this.reporte.tipo ? row.tipo === this.reporte.tipo : true
        const okEstado = this.reporte.estado ? row.estado === this.reporte.estado : true
        const okDesde = desde ? fecha >= desde : true
        const okHasta = hasta ? fecha <= hasta : true
        return okTipo && okEstado && okDesde && okHasta
      })
    },
    reporteFiltroLabel () {
      const tipo = this.reporte.tipo === 'venta'
        ? 'Vendidos'
        : (this.reporte.tipo === 'prestamo' ? 'Prestamos' : 'Todos')
      return `${tipo}${this.reporte.estado ? ' - ' + this.reporte.estado : ''}`
    },
    imprimirReporteTicket () {
      Imprimir.reportePrestamosTicket(this.reporteRowsFiltradas(), {
        titulo: 'Reporte Prestamos',
        tipoVenta: this.tipoCliente,
        dateFrom: this.reporte.date_from,
        dateTo: this.reporte.date_to,
        filtro: this.reporteFiltroLabel()
      })
      this.dialogReporte = false
    },
    async exportReportePdf () {
      this.syncReporteUnDia()
      this.loadingPdf = true
      try {
        const res = await this.$axios.get('prestamos/reporte/pdf', {
          params: {
            tipo_venta: this.tipoCliente,
            tipo: this.reporte.tipo,
            estado: this.reporte.estado,
            date_from: this.reporte.date_from,
            date_to: this.reporte.date_to
          },
          responseType: 'blob',
          timeout: 120000
        })
        const url = window.URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
        const a = document.createElement('a')
        a.href = url
        a.download = `reporte-prestamos-${this.tipoCliente}-${this.reporte.date_from}-${this.reporte.date_to}.pdf`
        document.body.appendChild(a)
        a.click()
        a.remove()
        window.URL.revokeObjectURL(url)
        this.dialogReporte = false
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo generar reporte PDF')
      } finally {
        this.loadingPdf = false
      }
    },
    abrirCajaMovimiento (tipo) {
      this.cajaForm = { tipo_movimiento: tipo, monto: null, observacion: '' }
      this.dialogCajaMov = true
    },
    async guardarCajaMovimiento () {
      this.loadingCajaMov = true
      try {
        await this.$axios.post('prestamos/caja/movimientos', this.cajaForm)
        this.dialogCajaMov = false
        await this.cargarCajaResumen()
        if (this.dialogCajaHistorial) await this.cargarCajaMovimientos()
        this.$alert.success('Movimiento de caja registrado')
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar movimiento')
      } finally {
        this.loadingCajaMov = false
      }
    },
    abrirCajaHistorial () {
      this.dialogCajaHistorial = true
      this.cargarCajaMovimientos()
    },
    async cargarCajaMovimientos () {
      this.loadingCajaHistorial = true
      try {
        const r = await this.$axios.get('prestamos/caja/movimientos', { params: this.cajaFiltros })
        this.cajaMovimientos = r.data?.movimientos || []
        await this.cargarCajaResumen()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar historial de caja')
      } finally {
        this.loadingCajaHistorial = false
      }
    },
    clientesOptions (rows) {
      return rows.map(c => ({ label: c.nombre, value: c.id }))
    },
    filtrarClientes (val, update) {
      update(() => {
        const t = String(val || '').toLowerCase()
        const rows = !t
          ? this.clientes
          : this.clientes.filter(c =>
            String(c.nombre || '').toLowerCase().includes(t) ||
            String(c.ci || '').toLowerCase().includes(t)
          )
        this.clientesFiltrados = this.clientesOptions(rows)
      })
    },
    nuevoPrestamo () {
      this.pres = this.defaultPres()
      this.prestamoItems = []
      this.dialogPrestamo = true
    },
    aplicarPrecioSugerido () {
      const base = Number(this.selectedInventario?.precio || 0)
      const cantidad = Math.max(1, Number(this.pres.cantidad || 1))
      this.pres.efectivo = Number((base * cantidad).toFixed(2))
      this.pres.efectivo_manual = false
    },
    recalcularPrecio () {
      if (this.pres.efectivo_manual) return
      this.aplicarPrecioSugerido()
    },
    agregarPrestamoItem () {
      if (!this.pres.cliente_id) {
        this.$alert.error('Debe seleccionar cliente')
        return
      }
      if (!this.pres.inventario_id) {
        this.$alert.error('Debe seleccionar inventario')
        return
      }
      if (Number(this.pres.cantidad || 0) < 1) {
        this.$alert.error('Debe registrar cantidad valida')
        return
      }

      const disponible = Number(this.selectedInventario?.cantidad || 0)
      const usados = this.prestamoItems
        .filter(i => i.inventario_id === this.pres.inventario_id)
        .reduce((acc, it) => acc + Number(it.cantidad || 0), 0)

      if (Number(this.pres.cantidad || 0) + usados > disponible) {
        this.$alert.error('La cantidad supera el inventario disponible')
        return
      }
      if (this.pres.tipo === 'venta' && Number(this.pres.efectivo || 0) <= 0) {
        this.$alert.error('Debe registrar monto efectivo para venta de material')
        return
      }

      this.prestamoItems.push({
        uid: `${Date.now()}-${Math.round(Math.random() * 100000)}`,
        cliente_id: this.pres.cliente_id,
        inventario_id: this.pres.inventario_id,
        inventario_nombre: this.selectedInventario?.nombre || '-',
        cantidad: Number(this.pres.cantidad || 0),
        tipo: this.pres.tipo,
        efectivo: Number(this.pres.efectivo || 0),
        metodo_pago: this.pres.metodo_pago || 'efectivo',
        observacion: this.pres.observacion || ''
      })

      const clienteId = this.pres.cliente_id
      this.pres = {
        ...this.defaultPres(),
        cliente_id: clienteId
      }
    },
    quitarPrestamoItem (uid) {
      this.prestamoItems = this.prestamoItems.filter(i => i.uid !== uid)
    },
    async guardarPrestamo () {
      if (!this.prestamoItems.length) {
        this.$alert.error('Debe agregar al menos un material a la tabla')
        return
      }
      this.loadingGuardar = true
      try {
        for (const item of this.prestamoItems) {
          await this.$axios.post('prestamos', {
            cliente_id: item.cliente_id,
            inventario_id: item.inventario_id,
            cantidad: item.cantidad,
            tipo: item.tipo,
            efectivo: item.efectivo,
            metodo_pago: item.metodo_pago,
            fisico: '',
            observacion: item.observacion,
            tipo_venta: this.tipoCliente
          })
        }
        this.dialogPrestamo = false
        this.prestamoItems = []
        await Promise.all([this.cargarPrestamos(), this.cargarInventarios(), this.cargarCajaResumen()])
        this.$alert.success('Registros guardados')
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar')
      } finally {
        this.loadingGuardar = false
      }
    },
    abrirRetornoParcial (row) {
      this.retornoRow = row
      this.retorno = {
        cantidad: 0,
        efectivo: 0,
        fisico: '',
        observacion: ''
      }
      this.dialogRetorno = true
    },
    abrirHistorial (row) {
      this.retornoRow = row
      this.dialogHistorial = true
    },
    async guardarRetornoParcial () {
      if (!this.retornoRow) return
      this.loadingRetorno = true
      try {
        await this.$axios.post(`prestamos/${this.retornoRow.id}/retorno-parcial`, this.retorno)
        this.dialogRetorno = false
        await Promise.all([this.cargarPrestamos(), this.cargarInventarios(), this.cargarCajaResumen()])
        this.$alert.success('Retorno parcial registrado')
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar retorno parcial')
      } finally {
        this.loadingRetorno = false
      }
    },
    retornarPrestamo (row) {
      this.$alert.dialog('Seguro que va a retornar todo?')
        .onOk(async () => {
        try {
          await this.$axios.post(`prestamos/${row.id}/retornar`)
          await Promise.all([this.cargarPrestamos(), this.cargarInventarios(), this.cargarCajaResumen()])
          this.$alert.success('Prestamo retornado completo')
        } catch (e) {
          this.$alert.error(e.response?.data?.message || 'No se pudo retornar el prestamo')
        }
      })
    },
    darBajaPrestamo (row) {
      this.$alert.dialog('Desea dar de baja este prestamo? El material no volvera al inventario.')
        .onOk(async () => {
          try {
            await this.$axios.post(`prestamos/${row.id}/dar-baja`, {
              monto: Number(row.monto_pendiente ?? row.efectivo_actual ?? 0),
              observacion: 'Baja desde prestamos'
            })
            await Promise.all([this.cargarPrestamos(), this.cargarCajaResumen()])
            if (this.dialogCajaHistorial) await this.cargarCajaMovimientos()
            this.$alert.success('Prestamo dado de baja')
          } catch (e) {
            this.$alert.error(e.response?.data?.message || 'No se pudo dar de baja')
          }
        })
    },
    imprimirPrestamo (row) {
      Imprimir.prestamoMaterialTicket(row)
    },
    imprimirHistorial (row) {
      Imprimir.prestamoHistorialTicket(row)
    }
  }
}
</script>

<style scoped>
.tipo-banner {
  border-radius: 8px;
  border: 1px solid transparent;
}
.tipo-detalle {
  background: #bbdefb;
  border-color: #42a5f5;
}
.tipo-local {
  background: #c8e6c9;
  border-color: #66bb6a;
}
</style>
