<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <div>
          <div class="text-h6">{{ tituloPagina }}</div>
          <div class="text-caption text-grey-7">Registro de prestamos y venta de material desde inventarios</div>
        </div>
        <q-space />
        <q-btn color="positive" no-caps icon="add" label="Registrar" @click="nuevoPrestamo" class="q-mr-sm" />
        <q-btn color="primary" flat no-caps icon="refresh" label="Actualizar" :loading="loading" @click="cargarAll" />
      </q-card-section>
    </q-card>

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
                <q-item clickable v-close-popup :disable="props.row.estado !== 'EN PRESTAMO'" @click="retornarPrestamo(props.row)">
                  <q-item-section avatar><q-icon name="assignment_return" color="positive" /></q-item-section>
                  <q-item-section>Retornar</q-item-section>
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
        <template #body-cell-efectivo="props">
          <q-td :props="props" class="text-right">{{ money(props.row.efectivo) }}</q-td>
        </template>
      </q-table>
    </q-card>

    <q-dialog v-model="dialogPrestamo">
      <q-card style="width: 680px; max-width: 96vw;">
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
            <q-select
              v-model="pres.inventario_id"
              dense
              outlined
              emit-value
              map-options
              :options="inventariosOptions"
              label="Inventario"
              :rules="[req]"
              class="q-mb-sm"
            />
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4">
                <q-input v-model.number="pres.cantidad" dense outlined type="number" min="1" label="Cantidad" :rules="[req]" />
              </div>
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
                <q-input :model-value="selectedInventario ? selectedInventario.cantidad : 0" dense outlined readonly label="Disponible" />
              </div>
              <div class="col-12 col-md-4">
                <q-input :model-value="money(selectedInventario ? selectedInventario.precio : 0)" dense outlined readonly label="Precio referencia" suffix="Bs" />
              </div>
              <div class="col-12 col-md-4" v-if="pres.tipo === 'venta'">
                <q-input
                  v-model.number="pres.efectivo"
                  dense
                  outlined
                  type="number"
                  min="0"
                  step="0.01"
                  label="Efectivo"
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
              <div class="col-12 col-md-6">
                <q-input v-model="pres.fisico" dense outlined label="Fisico" />
              </div>
              <div class="col-12 col-md-6">
                <q-input v-model="pres.observacion" dense outlined label="Observacion" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat color="negative" no-caps label="Cancelar" v-close-popup />
              <q-btn color="primary" no-caps label="Guardar" type="submit" :loading="loadingGuardar" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'PrestamosPage',
  data () {
    return {
      loading: false,
      loadingGuardar: false,
      filter: '',
      prestamos: [],
      clientes: [],
      clientesFiltrados: [],
      inventarios: [],
      dialogPrestamo: false,
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
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'tipo', label: 'Tipo', field: 'tipo', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'efectivo', label: 'Efectivo', field: 'efectivo', align: 'right' },
        { name: 'fisico', label: 'Fisico', field: 'fisico', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' }
      ]
    }
  },
  computed: {
    tipoCliente () { return this.$route.params.tipo === 'local' ? 'local' : 'detalle' },
    tituloPagina () { return this.tipoCliente === 'local' ? 'Prestamo local' : 'Prestamo detalle' },
    tituloCorto () { return this.tipoCliente === 'local' ? 'prestamo local' : 'prestamo detalle' },
    inventariosOptions () {
      return this.inventarios
        .filter(i => Number(i.cantidad || 0) > 0 && String(i.estado || '').toUpperCase() === 'ACTIVO')
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
    estadoColor (estado) {
      if (estado === 'VENDIDO') return 'positive'
      if (estado === 'RETORNADO') return 'indigo'
      if (estado === 'ANULADO') return 'negative'
      return 'orange-8'
    },
    async cargarAll () {
      this.loading = true
      try {
        await Promise.all([this.cargarInventarios(), this.cargarPrestamos(), this.cargarClientes()])
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar prestamos')
      } finally {
        this.loading = false
      }
    },
    async cargarInventarios () {
      const r = await this.$axios.get('inventarios')
      this.inventarios = r.data || []
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
      this.dialogPrestamo = true
    },
    aplicarPrecioSugerido () {
      const base = Number(this.selectedInventario?.precio || 0)
      const cantidad = Math.max(1, Number(this.pres.cantidad || 1))
      this.pres.efectivo = Number((base * cantidad).toFixed(2))
      this.pres.efectivo_manual = false
    },
    recalcularPrecio () {
      if (this.pres.tipo !== 'venta') return
      if (this.pres.efectivo_manual) return
      this.aplicarPrecioSugerido()
    },
    async guardarPrestamo () {
      this.loadingGuardar = true
      try {
        await this.$axios.post('prestamos', {
          ...this.pres,
          tipo_venta: this.tipoCliente
        })
        this.dialogPrestamo = false
        await Promise.all([this.cargarPrestamos(), this.cargarInventarios()])
        this.$alert.success('Registro guardado')
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar')
      } finally {
        this.loadingGuardar = false
      }
    },
    retornarPrestamo (row) {
      this.$alert.dialog('Desea retornar este prestamo?').onOk(async () => {
        try {
          await this.$axios.post(`prestamos/${row.id}/retornar`)
          await Promise.all([this.cargarPrestamos(), this.cargarInventarios()])
          this.$alert.success('Prestamo retornado')
        } catch (e) {
          this.$alert.error(e.response?.data?.message || 'No se pudo retornar el prestamo')
        }
      })
    }
  }
}
</script>
