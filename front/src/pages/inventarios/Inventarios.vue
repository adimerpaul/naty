<template>
  <q-page class="q-pa-md">
    <q-card flat bordered>
      <q-table
        dense
        flat
        bordered
        :rows="inventarios"
        :columns="colsInventario"
        row-key="id"
        :filter="fInventario"
        v-model:pagination="pagInventario"
        :rows-per-page-options="[50, 100]"
      >
        <template #top-right>
          <q-input v-model="fInventario" dense outlined label="Buscar" class="q-mr-sm" />
          <q-btn color="positive" no-caps icon="add" label="Nuevo" @click="nuevoInventario" class="q-mr-sm" />
          <q-btn color="primary" no-caps icon="refresh" label="Actualizar" @click="cargarInventarios" />
        </template>
        <template #body-cell-actions="props">
          <q-td :props="props">
            <q-btn-dropdown dense color="primary" label="Opciones" no-caps>
              <q-list dense>
                <q-item clickable v-close-popup @click="editarInventario(props.row)"><q-item-section avatar><q-icon name="edit" /></q-item-section><q-item-section>Editar</q-item-section></q-item>
                <q-item clickable v-close-popup @click="eliminarInventario(props.row.id)"><q-item-section avatar><q-icon name="delete" color="negative" /></q-item-section><q-item-section>Eliminar</q-item-section></q-item>
              </q-list>
            </q-btn-dropdown>
          </q-td>
        </template>
        <template #body-cell-estado="props">
          <q-td :props="props">
            <q-chip dense :color="inventarioEstadoColor(props.row.estado)" text-color="white">
              {{ inventarioEstadoLabel(props.row.estado) }}
            </q-chip>
          </q-td>
        </template>
      </q-table>
    </q-card>

    <q-dialog v-model="dialogInventario">
      <q-card style="width: 560px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none"><div class="text-subtitle1 text-weight-bold">{{ inv.id ? 'Editar inventario' : 'Nuevo inventario' }}</div><q-space /><q-btn icon="close" flat round dense v-close-popup /></q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarInventario">
            <div class="row q-col-gutter-sm">
              <div class="col-6"><q-input v-model="inv.codigo" dense outlined label="Codigo" /></div>
              <div class="col-6"><q-input v-model="inv.fecha" type="date" dense outlined label="Fecha" /></div>
              <div class="col-12"><q-input v-model="inv.nombre" dense outlined label="Nombre" :rules="[req]" /></div>
              <div class="col-6"><q-input v-model.number="inv.cantidad" type="number" min="0" dense outlined label="Cantidad" :rules="[req]" /></div>
              <div class="col-6"><q-input v-model.number="inv.precio" type="number" min="0" step="0.01" dense outlined label="Precio" /></div>
              <div class="col-6"><q-input v-model.number="inv.orden" type="number" min="0" dense outlined label="Orden" /></div>
              <div class="col-6 flex items-center">
                <q-toggle
                  v-model="inv.estado"
                  true-value="ACTIVO"
                  false-value="INACTIVO"
                  color="positive"
                  :label="inventarioEstadoLabel(inv.estado)"
                />
              </div>
              <div class="col-12"><q-input v-model="inv.detalle" dense outlined label="Detalle" /></div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md"><q-btn flat color="negative" no-caps label="Cancelar" v-close-popup /><q-btn color="primary" no-caps label="Guardar" type="submit" :loading="loading" /></div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'InventariosPage',
  data () {
    return {
      tab: 'inventario',
      loading: false,
      fInventario: '',
      fPrestamo: '',
      pagInventario: { page: 1, rowsPerPage: 50, sortBy: 'id', descending: true },
      pagPrestamo: { page: 1, rowsPerPage: 50, sortBy: 'id', descending: true },
      inventarios: [],
      prestamos: [],
      clientes: [],
      dialogInventario: false,
      dialogPrestamo: false,
      inv: {},
      pres: { cliente_id: null, inventario_id: null, cantidad: 1, tipo: 'prestamo', tipo_venta: 'detalle', efectivo: 0, metodo_pago: 'efectivo', fisico: '', observacion: '' },
      colsInventario: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'codigo', label: 'Codigo', field: 'codigo', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'detalle', label: 'Detalle', field: 'detalle', align: 'left' },
        { name: 'precio', label: 'Precio', field: 'precio', align: 'right' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' }
      ],
      colsPrestamos: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'cliente', label: 'Cliente', field: row => row.cliente?.nombre, align: 'left' },
        { name: 'inventario', label: 'Inventario', field: row => row.inventario?.nombre, align: 'left' },
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
    clientesOptions () { return this.clientes.map(c => ({ label: c.nombre, value: c.id })) },
    inventariosOptions () {
      return this.inventarios
        .filter(i => this.isInventarioActivo(i))
        .map(i => ({ label: `${i.nombre} (${i.cantidad})`, value: i.id }))
    }
  },
  mounted () {
    this.cargarAll().then(() => this.aplicarPrefillDesdeRuta())
  },
  methods: {
    req (v) { return !!v || 'Campo requerido' },
    estadoColor (estado) {
      if (estado === 'VENDIDO') return 'positive'
      if (estado === 'RETORNADO') return 'indigo'
      return 'orange-8'
    },
    normalizeInventarioEstado (estado) {
      return String(estado || '').toUpperCase() === 'ACTIVO' ? 'ACTIVO' : 'INACTIVO'
    },
    isInventarioActivo (inventario) {
      return this.normalizeInventarioEstado(inventario?.estado) === 'ACTIVO'
    },
    inventarioEstadoLabel (estado) {
      return this.normalizeInventarioEstado(estado) === 'ACTIVO' ? 'Activo' : 'Inactivo'
    },
    inventarioEstadoColor (estado) {
      return this.normalizeInventarioEstado(estado) === 'ACTIVO' ? 'positive' : 'grey-7'
    },
    async cargarAll () {
      await Promise.all([this.cargarInventarios(), this.cargarPrestamos(), this.cargarClientes()])
    },
    async cargarInventarios () { const r = await this.$axios.get('inventarios'); this.inventarios = r.data || [] },
    async cargarPrestamos () { const r = await this.$axios.get('prestamos'); this.prestamos = r.data || [] },
    async cargarClientes () { const r = await this.$axios.get('clientes'); this.clientes = r.data || [] },
    nuevoInventario () {
      this.inv = { codigo: '', fecha: new Date().toISOString().slice(0, 10), nombre: '', cantidad: 0, detalle: '', orden: 0, estado: 'ACTIVO', precio: 0 }
      this.dialogInventario = true
    },
    editarInventario (row) {
      this.inv = { ...row, estado: this.normalizeInventarioEstado(row.estado) }
      this.dialogInventario = true
    },
    async guardarInventario () {
      this.loading = true
      try {
        const payload = { ...this.inv, estado: this.normalizeInventarioEstado(this.inv.estado) }
        if (this.inv.id) await this.$axios.put(`inventarios/${this.inv.id}`, payload)
        else await this.$axios.post('inventarios', payload)
        this.dialogInventario = false
        await this.cargarInventarios()
        this.$alert.success('Inventario guardado')
      } catch (e) { this.$alert.error(e.response?.data?.message || 'No se pudo guardar') } finally { this.loading = false }
    },
    eliminarInventario (id) {
      this.$alert.dialog('Eliminar inventario?').onOk(async () => {
        await this.$axios.delete(`inventarios/${id}`)
        await this.cargarInventarios()
        this.$alert.success('Inventario eliminado')
      })
    },
    nuevoPrestamo () {
      this.pres = { cliente_id: null, inventario_id: null, cantidad: 1, tipo: 'prestamo', tipo_venta: 'detalle', efectivo: 0, metodo_pago: 'efectivo', fisico: '', observacion: '' }
      this.dialogPrestamo = true
    },
    aplicarPrefillDesdeRuta () {
      const q = this.$route.query || {}
      if (q.tab === 'prestamos') {
        this.tab = 'prestamos'
      }
      if (q.cliente_id) {
        this.pres.cliente_id = Number(q.cliente_id)
        this.pres.tipo_venta = q.tipo_venta === 'local' ? 'local' : 'detalle'
        this.dialogPrestamo = true
      }
    },
    async guardarPrestamo () {
      this.loading = true
      try {
        await this.$axios.post('prestamos', this.pres)
        this.dialogPrestamo = false
        await Promise.all([this.cargarPrestamos(), this.cargarInventarios()])
        this.$alert.success('Registro guardado')
      } catch (e) { this.$alert.error(e.response?.data?.message || 'No se pudo guardar') } finally { this.loading = false }
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

<style scoped>
.tabs-inventario {
  background: linear-gradient(90deg, #e3f2fd 0%, #e8f5e9 100%);
}
.tabs-inventario :deep(.q-tab--active) {
  background: #1e88e5;
}
</style>
