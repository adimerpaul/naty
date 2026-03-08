<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <div>
          <div class="text-h6">{{ tituloPagina }}</div>
          <div class="text-caption text-grey-7">Listado de ventas registradas</div>
        </div>
        <q-space />
        <q-btn color="primary" no-caps icon="add" label="Nueva venta" @click="irNuevaVenta" class="q-mr-sm" />
        <q-btn color="secondary" no-caps icon="account_balance_wallet" label="Ingreso / Egreso" @click="abrirMovimiento" class="q-mr-sm" />
        <q-btn color="grey-8" flat no-caps icon="search" label="Buscar" @click="ventasGet" :loading="loading" class="q-mr-sm" />
        <q-btn color="grey-8" flat no-caps icon="refresh" label="Actualizar" @click="ventasGet" :loading="loading" />
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-md-2"><q-input v-model="filters.date_from" type="date" dense outlined label="Desde" /></div>
      <div class="col-12 col-md-2"><q-input v-model="filters.date_to" type="date" dense outlined label="Hasta" /></div>
      <div class="col-12 col-md-2">
        <q-select
          v-model="filters.tipo_movimiento"
          dense
          outlined
          emit-value
          map-options
          :options="movOptions"
          label="Tipo"
          clearable
        />
      </div>
      <div class="col-12 col-md-2">
        <q-select v-model="filters.tipo_pago" dense outlined emit-value map-options :options="tipoPagoOptions" label="Tipo pago" clearable />
      </div>
      <div class="col-12 col-md-2">
        <q-select v-model="filters.metodo_pago" dense outlined emit-value map-options :options="metodoPagoOptions" label="Metodo pago" clearable />
      </div>
      <div class="col-12 col-md-2">
        <q-select v-model="filters.user_id" dense outlined emit-value map-options :options="userOptions" label="Usuario" clearable />
      </div>
      <div class="col-12 col-md-3">
        <q-select v-model="filters.cliente_id" dense outlined emit-value map-options :options="clienteOptions" label="Cliente" clearable />
      </div>
      <div class="col-12 col-md-3"><q-input v-model="filters.search" dense outlined label="Buscar" @keyup.enter="ventasGet" /></div>
    </div>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat class="kpi-card kpi-activa">
          <q-card-section>
            <div class="text-caption">Ventas activas</div>
            <div class="text-h6 text-weight-bold">{{ money(totalActivas) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat class="kpi-card kpi-anulada">
          <q-card-section>
            <div class="text-caption">Ventas anuladas</div>
            <div class="text-h6 text-weight-bold">{{ money(totalAnuladas) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-caption text-grey-7">Cantidad ingresos</div>
            <div class="text-h6 text-weight-bold text-positive">{{ cantidadIngresos }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-caption text-grey-7">Cantidad egresos</div>
            <div class="text-h6 text-weight-bold text-orange-8">{{ cantidadEgresos }}</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-table
      dense
      flat
      bordered
      :rows="ventas"
      :columns="columns"
      row-key="id"
      v-model:pagination="pagination"
      :rows-per-page-options="[10, 25, 50, 100]"
      :loading="loading"
    >
      <template #body-cell-created_at="props">
        <q-td :props="props">{{ formatDate(props.row.created_at) }}</q-td>
      </template>
      <template #body-cell-total="props">
        <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.total) }}</q-td>
      </template>
      <template #body-cell-tipo_movimiento="props">
        <q-td :props="props">
          <q-chip dense :color="props.row.tipo_movimiento === 'egreso' ? 'orange-7' : 'positive'" text-color="white">
            {{ props.row.tipo_movimiento || 'ingreso' }}
          </q-chip>
        </q-td>
      </template>
      <template #body-cell-tipo_pago="props">
        <q-td :props="props">
          <q-chip dense :color="props.row.tipo_pago === 'contado' ? 'positive' : 'warning'" text-color="white">{{ props.row.tipo_pago }}</q-chip>
        </q-td>
      </template>
      <template #body-cell-estado="props">
        <q-td :props="props">
          <q-chip dense :color="props.row.estado === 'ANULADA' ? 'negative' : 'primary'" text-color="white">{{ props.row.estado }}</q-chip>
        </q-td>
      </template>
      <template #body-cell-actions="props">
        <q-td :props="props" class="text-left">
          <q-btn-dropdown dense color="primary" label="Opciones" no-caps>
            <q-list dense>
              <q-item clickable v-close-popup @click="verVenta(props.row)">
                <q-item-section avatar><q-icon name="visibility" color="primary" /></q-item-section>
                <q-item-section>Ver</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="imprimirFicha(props.row)">
                <q-item-section avatar><q-icon name="print" color="deep-orange" /></q-item-section>
                <q-item-section>Imprimir ficha</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="imprimirHojaRuta(props.row)">
                <q-item-section avatar><q-icon name="receipt_long" color="indigo" /></q-item-section>
                <q-item-section>Imprimir hoja ruta</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="descargarPdf(props.row)">
                <q-item-section avatar><q-icon name="picture_as_pdf" color="negative" /></q-item-section>
                <q-item-section>Descargar PDF</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="enviarWhatsapp(props.row)">
                <q-item-section avatar><q-icon name="fa-brands fa-whatsapp" color="positive" /></q-item-section>
                <q-item-section>Enviar WhatsApp</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="abrirEditar(props.row)">
                <q-item-section avatar><q-icon name="edit" color="warning" /></q-item-section>
                <q-item-section>Editar</q-item-section>
              </q-item>
              <q-item clickable v-close-popup :disable="props.row.estado === 'ANULADA'" @click="anularVenta(props.row)">
                <q-item-section avatar><q-icon name="block" color="negative" /></q-item-section>
                <q-item-section>Anular</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialogDetalle">
      <q-card style="width: 900px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Venta #{{ ventaSel?.id }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm q-mb-sm">
            <div class="col-6 col-md-3"><b>Cliente:</b> {{ ventaSel?.cliente_nombre || '-' }}</div>
            <div class="col-6 col-md-3"><b>Tipo:</b> {{ ventaSel?.tipo_movimiento || 'ingreso' }}</div>
            <div class="col-6 col-md-3"><b>Usuario:</b> {{ ventaSel?.user?.name || ventaSel?.user?.username || '-' }}</div>
            <div class="col-6 col-md-3"><b>Pago:</b> {{ ventaSel?.tipo_pago }}</div>
            <div class="col-6 col-md-3"><b>Metodo:</b> {{ ventaSel?.pagos?.[0]?.metodo || '-' }}</div>
            <div class="col-6 col-md-3"><b>Total:</b> {{ money(ventaSel?.total) }} Bs</div>
          </div>
          <q-table dense flat bordered :rows="ventaSel?.detalles || []" :columns="colsDetalle" row-key="id" hide-pagination />
          <div class="q-mt-md">
            <div class="text-subtitle2 q-mb-xs">Pagos</div>
            <q-table dense flat bordered :rows="ventaSel?.pagos || []" :columns="colsPagos" row-key="id" hide-pagination />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogMovimiento">
      <q-card style="width: 560px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Registrar ingreso / egreso</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarMovimiento">
            <div class="row q-col-gutter-sm q-mb-sm">
              <div class="col-12 col-md-4">
                <q-select
                  v-model="movForm.tipo_movimiento"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="movOptions"
                  label="Tipo"
                  :rules="[req]"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input dense outlined readonly label="Pago" model-value="contado" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model.number="movForm.monto" dense outlined type="number" min="1" step="1" label="Monto" :rules="[req, entero]" />
              </div>
            </div>
            <div class="row q-col-gutter-sm q-mb-sm">
              <div class="col-12 col-md-8">
                <q-input v-model="movForm.concepto" dense outlined label="Concepto" :rules="[req]" />
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  v-model="movForm.metodo_pago"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="metodoPagoOptions"
                  label="Metodo"
                />
              </div>
            </div>
            <q-input v-model="movForm.observacion" dense outlined type="textarea" autogrow label="Observacion" class="q-mb-md" />
            <div class="row justify-end q-gutter-sm">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar" type="submit" :loading="loadingMov" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogEditar">
      <q-card style="width: 560px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Editar venta #{{ editForm.id }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarEditar">
            <q-input v-model="editForm.cliente_nombre" dense outlined readonly label="Cliente" class="q-mb-sm" />
            <div class="row q-col-gutter-sm q-mb-sm">
              <div class="col-12 col-md-6">
                <q-select
                  v-model="editForm.tipo_movimiento"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="movOptions"
                  label="Tipo"
                  :rules="[req]"
                />
              </div>
              <div class="col-12 col-md-6">
                <q-select
                  v-model="editForm.tipo_pago"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="[
                    { label: 'Contado', value: 'contado' },
                    { label: 'Credito', value: 'credito' }
                  ]"
                  label="Pago"
                  :rules="[req]"
                />
              </div>
            </div>
            <q-input v-model="editForm.observacion" dense outlined type="textarea" autogrow label="Observacion" class="q-mb-md" />
            <div class="row justify-end q-gutter-sm">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar cambios" type="submit" :loading="loadingEdit" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    <div id="myElement" ></div>
  </q-page>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'

export default {
  name: 'VentasPage',
  data () {
    const today = new Date().toISOString().slice(0, 10)
    return {
      loading: false,
      ventas: [],
      users: [],
      clientes: [],
      dialogDetalle: false,
      dialogMovimiento: false,
      dialogEditar: false,
      loadingMov: false,
      loadingEdit: false,
      ventaSel: null,
      pagination: { page: 1, rowsPerPage: 25, sortBy: 'id', descending: true },
      filters: {
        date_from: today,
        date_to: today,
        tipo_movimiento: null,
        tipo_pago: null,
        metodo_pago: null,
        user_id: null,
        cliente_id: null,
        search: ''
      },
      movForm: {
        tipo_movimiento: 'ingreso',
        metodo_pago: 'efectivo',
        monto: null,
        concepto: '',
        observacion: ''
      },
      editForm: {
        id: null,
        cliente_nombre: '',
        tipo_movimiento: 'ingreso',
        tipo_pago: 'contado',
        observacion: ''
      },
      movOptions: [
        { label: 'Ingreso', value: 'ingreso' },
        { label: 'Egreso', value: 'egreso' }
      ],
      tipoPagoOptions: [
        { label: 'Contado', value: 'contado' },
        { label: 'Credito', value: 'credito' }
      ],
      metodoPagoOptions: [
        { label: 'Efectivo', value: 'efectivo' },
        { label: 'QR', value: 'qr' }
      ],
      columns: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha', field: 'created_at', align: 'left' },
        { name: 'cliente_nombre', label: 'Cliente', field: 'cliente_nombre', align: 'left' },
        { name: 'user', label: 'Usuario', field: row => row.user?.name || row.user?.username || '-', align: 'left' },
        { name: 'tipo_movimiento', label: 'Tipo', field: 'tipo_movimiento', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'tipo_pago', label: 'Pago', field: 'tipo_pago', align: 'left' },
        { name: 'total', label: 'Total', field: 'total', align: 'right' }
      ],
      colsDetalle: [
        { name: 'producto_nombre', label: 'Producto / Concepto', field: 'producto_nombre', align: 'left' },
        { name: 'precio', label: 'Precio', field: 'precio', align: 'right' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'subtotal', label: 'Subtotal', field: 'subtotal', align: 'right' }
      ],
      colsPagos: [
        { name: 'nro_cuota', label: 'Cuota', field: 'nro_cuota', align: 'left' },
        { name: 'monto', label: 'Monto', field: 'monto', align: 'right' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'metodo', label: 'Metodo', field: 'metodo', align: 'left' },
        { name: 'fecha_programada', label: 'F. Programada', field: 'fecha_programada', align: 'left' },
        { name: 'fecha_pago', label: 'F. Pago', field: 'fecha_pago', align: 'left' }
      ]
    }
  },
  computed: {
    tipoVenta () { return this.$route.params.tipo === 'local' ? 'local' : 'detalle' },
    tituloPagina () { return this.tipoVenta === 'local' ? 'Ventas local' : 'Ventas detalle' },
    totalActivas () { return this.ventas.filter(v => v.estado !== 'ANULADA').reduce((a, b) => a + Number(b.total || 0), 0) },
    totalAnuladas () { return this.ventas.filter(v => v.estado === 'ANULADA').reduce((a, b) => a + Number(b.total || 0), 0) },
    cantidadIngresos () { return this.ventas.filter(v => (v.tipo_movimiento || 'ingreso') === 'ingreso').length },
    cantidadEgresos () { return this.ventas.filter(v => v.tipo_movimiento === 'egreso').length },
    userOptions () { return this.users.map(u => ({ label: u.name || u.username, value: u.id })) },
    clienteOptions () { return this.clientes.map(c => ({ label: c.nombre, value: c.id })) }
  },
  watch: {
    '$route.params.tipo' () { this.cargarCombos(); this.ventasGet() }
  },
  mounted () {
    this.cargarCombos()
    this.ventasGet()
  },
  methods: {
    req (v) { return !!v || 'Campo requerido' },
    entero (v) { return Number.isInteger(Number(v)) || 'Solo numeros enteros' },
    money (n) { return Number(n || 0).toFixed(2) },
    formatDate (value) {
      if (!value) return '-'
      const date = new Date(value)
      if (Number.isNaN(date.getTime())) return value
      const dd = String(date.getDate()).padStart(2, '0')
      const mm = String(date.getMonth() + 1).padStart(2, '0')
      const yy = date.getFullYear()
      const hh = String(date.getHours()).padStart(2, '0')
      const mi = String(date.getMinutes()).padStart(2, '0')
      return `${dd}/${mm}/${yy} ${hh}:${mi}`
    },
    async cargarCombos () {
      const [u, c] = await Promise.all([
        this.$axios.get('users'),
        this.$axios.get('clientes', { params: { tipo_cliente: this.tipoVenta } })
      ])
      this.users = u.data || []
      this.clientes = c.data || []
    },
    ventasGet () {
      this.loading = true
      this.$axios.get('ventas', {
        params: {
          tipo_venta: this.tipoVenta,
          date_from: this.filters.date_from,
          date_to: this.filters.date_to,
          tipo_movimiento: this.filters.tipo_movimiento,
          tipo_pago: this.filters.tipo_pago,
          metodo_pago: this.filters.metodo_pago,
          user_id: this.filters.user_id,
          cliente_id: this.filters.cliente_id,
          search: this.filters.search
        }
      }).then(r => { this.ventas = r.data })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar ventas'))
        .finally(() => { this.loading = false })
    },
    irNuevaVenta () { this.$router.push(`/ventas/${this.tipoVenta}/nueva`) },
    abrirMovimiento () {
      this.movForm = {
        tipo_movimiento: 'ingreso',
        metodo_pago: 'efectivo',
        monto: null,
        concepto: '',
        observacion: ''
      }
      this.dialogMovimiento = true
    },
    abrirEditar (row) {
      this.editForm = {
        id: row.id,
        cliente_nombre: row.cliente_nombre || '-',
        tipo_movimiento: row.tipo_movimiento || 'ingreso',
        tipo_pago: row.tipo_pago || 'contado',
        observacion: row.observacion || ''
      }
      this.dialogEditar = true
    },
    async guardarEditar () {
      this.loadingEdit = true
      try {
        await this.$axios.put(`ventas/${this.editForm.id}`, {
          tipo_movimiento: this.editForm.tipo_movimiento,
          tipo_pago: this.editForm.tipo_pago,
          observacion: this.editForm.observacion
        })
        this.$alert.success('Venta actualizada')
        this.dialogEditar = false
        this.ventasGet()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo actualizar la venta')
      } finally {
        this.loadingEdit = false
      }
    },
    async guardarMovimiento () {
      this.loadingMov = true
      try {
        await this.$axios.post('ventas', {
          tipo_venta: this.tipoVenta,
          tipo_movimiento: this.movForm.tipo_movimiento,
          tipo_pago: 'contado',
          metodo_pago: this.movForm.metodo_pago,
          monto: this.movForm.monto,
          concepto: this.movForm.concepto,
          observacion: this.movForm.observacion,
          items: []
        })
        this.$alert.success('Movimiento registrado')
        this.dialogMovimiento = false
        this.ventasGet()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar movimiento')
      } finally {
        this.loadingMov = false
      }
    },
    async verVenta (row) {
      try {
        const res = await this.$axios.get(`ventas/${row.id}`)
        this.ventaSel = res.data
        this.dialogDetalle = true
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar detalle')
      }
    },
    async getVenta(id) {
      const res = await this.$axios.get(`ventas/${id}`)
      return res.data
    },
    async imprimirFicha(row) {
      try {
        const venta = await this.getVenta(row.id)
        Imprimir.fichaDespacho(venta)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo imprimir ficha')
      }
    },
    async imprimirHojaRuta(row) {
      try {
        const venta = await this.getVenta(row.id)
        Imprimir.hojaRuta(venta)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo imprimir hoja de ruta')
      }
    },
    async descargarPdf(row) {
      try {
        const res = await this.$axios.get(`ventas/${row.id}/pdf`, { responseType: 'blob' })
        const blob = new Blob([res.data], { type: 'application/pdf' })
        const url = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `venta-${row.id}.pdf`
        document.body.appendChild(a)
        a.click()
        a.remove()
        window.URL.revokeObjectURL(url)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo descargar PDF')
      }
    },
    enviarWhatsapp(row) {
      let telefono = (row.cliente_telefono || '').replace(/\D/g, '')
      if (!telefono) {
        this.$alert.error('La venta no tiene telefono de cliente')
        return
      }
      if (telefono.length === 8) telefono = `591${telefono}`
      const msg = encodeURIComponent(`Naty - Venta #${row.id}\nTotal: ${this.money(row.total)} Bs`)
      window.open(`https://wa.me/${telefono}?text=${msg}`, '_blank')
    },
    anularVenta (row) {
      this.$alert.dialog('Desea anular la venta?')
        .onOk(async () => {
          await this.$axios.post(`ventas/${row.id}/anular`)
          this.$alert.success('Venta anulada')
          this.ventasGet()
        })
    }
  }
}
</script>

<style scoped>
.kpi-card {
  border-radius: 10px;
}
.kpi-activa {
  background: #1f9d55;
  color: #fff;
}
.kpi-anulada {
  background: #f4f4f4;
  color: #111;
}
</style>
