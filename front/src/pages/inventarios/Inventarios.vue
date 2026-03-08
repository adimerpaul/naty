<template>
  <q-page class="q-pa-md">
    <q-card flat bordered>
      <q-tabs v-model="tab" dense class="tabs-inventario" align="left" active-color="white" indicator-color="transparent">
        <q-tab no-caps name="inventario" icon="inventory_2" label="inventario" />
        <q-tab no-caps name="prestamos" icon="payments" label="prestamos / garantias" />
      </q-tabs>
      <q-separator />
      <q-tab-panels v-model="tab" animated>
        <q-tab-panel name="inventario">
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
          </q-table>
        </q-tab-panel>

        <q-tab-panel name="prestamos">
          <q-table
            dense
            flat
            bordered
            :rows="prestamos"
            :columns="colsPrestamos"
            row-key="id"
            :filter="fPrestamo"
            v-model:pagination="pagPrestamo"
            :rows-per-page-options="[50, 100]"
          >
            <template #top-right>
              <q-input v-model="fPrestamo" dense outlined label="Buscar" class="q-mr-sm" />
              <q-btn color="positive" no-caps icon="add" label="Registrar" @click="nuevoPrestamo" class="q-mr-sm" />
              <q-btn color="primary" no-caps icon="refresh" label="Actualizar" @click="cargarPrestamos" />
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
              <q-td :props="props"><q-chip dense :color="props.row.tipo === 'venta' ? 'primary' : 'warning'" text-color="white">{{ props.row.tipo }}</q-chip></q-td>
            </template>
            <template #body-cell-estado="props">
              <q-td :props="props"><q-chip dense :color="estadoColor(props.row.estado)" text-color="white">{{ props.row.estado }}</q-chip></q-td>
            </template>
          </q-table>
        </q-tab-panel>
      </q-tab-panels>
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
              <div class="col-6"><q-input v-model="inv.estado" dense outlined label="Estado" /></div>
              <div class="col-12"><q-input v-model="inv.detalle" dense outlined label="Detalle" /></div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md"><q-btn flat color="negative" no-caps label="Cancelar" v-close-popup /><q-btn color="primary" no-caps label="Guardar" type="submit" :loading="loading" /></div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogPrestamo">
      <q-card style="width: 620px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none"><div class="text-subtitle1 text-weight-bold">Registrar prestamo / venta material</div><q-space /><q-btn icon="close" flat round dense v-close-popup /></q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarPrestamo">
            <q-select v-model="pres.cliente_id" dense outlined emit-value map-options :options="clientesOptions" label="Cliente" :rules="[req]" class="q-mb-sm" />
            <q-select v-model="pres.inventario_id" dense outlined emit-value map-options :options="inventariosOptions" label="Inventario" :rules="[req]" class="q-mb-sm" />
            <div class="row q-col-gutter-sm">
              <div class="col-4"><q-input v-model.number="pres.cantidad" dense outlined type="number" min="1" label="Cantidad" :rules="[req]" /></div>
              <div class="col-4"><q-select v-model="pres.tipo" dense outlined emit-value map-options :options="[{label:'Prestamo',value:'prestamo'},{label:'Venta',value:'venta'}]" label="Tipo" /></div>
              <div class="col-4"><q-select v-model="pres.tipo_venta" dense outlined emit-value map-options :options="[{label:'Detalle',value:'detalle'},{label:'Local',value:'local'}]" label="Tipo venta" /></div>
            </div>
            <q-input v-if="pres.tipo==='venta'" v-model.number="pres.efectivo" dense outlined type="number" min="0" step="0.01" label="Efectivo" class="q-mt-sm" />
            <q-select v-if="pres.tipo==='venta'" v-model="pres.metodo_pago" dense outlined emit-value map-options :options="[{label:'Efectivo',value:'efectivo'},{label:'QR',value:'qr'}]" label="Metodo" class="q-mt-sm" />
            <q-input v-model="pres.fisico" dense outlined label="Fisico" class="q-mt-sm" />
            <q-input v-model="pres.observacion" dense outlined label="Observacion" class="q-mt-sm" />
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
    inventariosOptions () { return this.inventarios.map(i => ({ label: `${i.nombre} (${i.cantidad})`, value: i.id })) }
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
    editarInventario (row) { this.inv = { ...row }; this.dialogInventario = true },
    async guardarInventario () {
      this.loading = true
      try {
        if (this.inv.id) await this.$axios.put(`inventarios/${this.inv.id}`, this.inv)
        else await this.$axios.post('inventarios', this.inv)
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
