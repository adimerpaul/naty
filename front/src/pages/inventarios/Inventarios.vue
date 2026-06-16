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
                <q-item clickable v-close-popup @click="abrirMovimiento(props.row, 'AUMENTO')"><q-item-section avatar><q-icon name="add_circle" color="positive" /></q-item-section><q-item-section>Aumentar</q-item-section></q-item>
                <q-item clickable v-close-popup @click="abrirMovimiento(props.row, 'DISMINUCION')"><q-item-section avatar><q-icon name="remove_circle" color="warning" /></q-item-section><q-item-section>Disminuir</q-item-section></q-item>
                <q-item clickable v-close-popup @click="abrirHistorial(props.row)"><q-item-section avatar><q-icon name="history" color="indigo" /></q-item-section><q-item-section>Historial</q-item-section></q-item>
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
              <div class="col-6"><q-input v-model.number="inv.precio" type="number" min="0" step="0.01" dense outlined label="Precio" /></div>
              <div class="col-6"><q-input v-model.number="inv.orden" type="number" min="0" dense outlined label="Orden" /></div>
              <div class="col-6" v-if="inv.id"><q-input :model-value="inv.cantidad" dense outlined readonly label="Cantidad actual" /></div>
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

    <q-dialog v-model="dialogMovimiento">
      <q-card style="width: 560px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            {{ mov.tipo === 'AUMENTO' ? 'Aumentar inventario' : 'Disminuir inventario' }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarMovimiento">
            <div class="q-mb-sm text-grey-8"><b>Inventario:</b> {{ invSel?.nombre || '-' }}</div>
            <div class="q-mb-md text-grey-8"><b>Cantidad actual:</b> {{ invSel?.cantidad ?? 0 }}</div>
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4"><q-input v-model="mov.fecha" dense outlined type="date" label="Fecha" /></div>
              <div class="col-12 col-md-4"><q-input v-model.number="mov.cantidad" dense outlined type="number" min="1" label="Cantidad" :rules="[req]" /></div>
              <div class="col-12"><q-input v-model="mov.motivo" dense outlined label="Motivo" /></div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat color="negative" no-caps label="Cancelar" v-close-popup />
              <q-btn color="primary" no-caps label="Guardar" type="submit" :loading="loadingMov" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogHistorial">
      <q-card style="width: 980px; max-width: 98vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Historial de movimientos</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm items-end q-mb-sm">
            <div class="col-12 col-md-4"><q-input v-model="historial.date_from" dense outlined type="date" label="Desde" /></div>
            <div class="col-12 col-md-4"><q-input v-model="historial.date_to" dense outlined type="date" label="Hasta" /></div>
            <div class="col-12 col-md-4">
              <q-btn color="primary" no-caps icon="refresh" label="Actualizar" :loading="loadingHistorial" @click="cargarHistorial" />
            </div>
          </div>
          <div class="row q-col-gutter-sm q-mb-sm">
            <div class="col-12 col-md-4"><q-chip color="positive" text-color="white">Aumentado: {{ historialResumen.aumento }}</q-chip></div>
            <div class="col-12 col-md-4"><q-chip color="warning" text-color="black">Disminuido: {{ historialResumen.disminucion }}</q-chip></div>
            <div class="col-12 col-md-4"><q-chip color="indigo" text-color="white">Neto: {{ historialResumen.neto }}</q-chip></div>
          </div>
          <q-table dense flat bordered :rows="movimientos" :columns="colsMovimientos" row-key="id" :loading="loadingHistorial">
            <template #body-cell-tipo="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.tipo === 'AUMENTO' ? 'positive' : 'warning'" :text-color="props.row.tipo === 'AUMENTO' ? 'white' : 'black'">
                  {{ props.row.tipo === 'AUMENTO' ? 'Aumento' : 'Disminucion' }}
                </q-chip>
              </q-td>
            </template>
            <template #body-cell-estado="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.estado === 'ANULADO' ? 'negative' : 'primary'" text-color="white">
                  {{ props.row.estado }}
                </q-chip>
              </q-td>
            </template>
            <template #body-cell-actions="props">
              <q-td :props="props">
                <q-btn
                  dense
                  no-caps
                  color="negative"
                  icon="block"
                  label="Anular"
                  :disable="props.row.estado === 'ANULADO'"
                  @click="anularMovimiento(props.row)"
                />
              </q-td>
            </template>
          </q-table>
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
      loadingMov: false,
      loadingHistorial: false,
      fInventario: '',
      pagInventario: { page: 1, rowsPerPage: 50, sortBy: 'id', descending: true },
      inventarios: [],
      movimientos: [],
      dialogInventario: false,
      dialogMovimiento: false,
      dialogHistorial: false,
      inv: {},
      invSel: null,
      mov: { fecha: new Date().toISOString().slice(0, 10), tipo: 'AUMENTO', cantidad: 1, motivo: '' },
      historial: { date_from: new Date().toISOString().slice(0, 10), date_to: new Date().toISOString().slice(0, 10) },
      colsInventario: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'codigo', label: 'Codigo', field: 'codigo', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'cantidad_prestada', label: 'Prestados', field: row => row.cantidad_prestada || 0, align: 'right' },
        { name: 'detalle', label: 'Detalle', field: 'detalle', align: 'left' },
        { name: 'precio', label: 'Precio', field: 'precio', align: 'right' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' }
      ],
      colsMovimientos: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'tipo', label: 'Tipo', field: 'tipo', align: 'left' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'cantidad_anterior', label: 'Antes', field: 'cantidad_anterior', align: 'right' },
        { name: 'cantidad_nueva', label: 'Despues', field: 'cantidad_nueva', align: 'right' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'motivo', label: 'Motivo', field: 'motivo', align: 'left' },
        { name: 'user', label: 'Usuario', field: row => row.user?.name || row.user?.username || '-', align: 'left' }
      ]
    }
  },
  computed: {
    historialResumen () {
      let aumento = 0
      let disminucion = 0
      this.movimientos.forEach(m => {
        if (m.estado === 'ANULADO') return
        if (m.tipo === 'AUMENTO') aumento += Number(m.cantidad || 0)
        if (m.tipo === 'DISMINUCION') disminucion += Number(m.cantidad || 0)
      })
      return {
        aumento,
        disminucion,
        neto: aumento - disminucion
      }
    }
  },
  mounted () {
    this.cargarInventarios()
  },
  methods: {
    req (v) { return !!v || 'Campo requerido' },
    normalizeInventarioEstado (estado) {
      return String(estado || '').toUpperCase() === 'ACTIVO' ? 'ACTIVO' : 'INACTIVO'
    },
    inventarioEstadoLabel (estado) {
      return this.normalizeInventarioEstado(estado) === 'ACTIVO' ? 'Activo' : 'Inactivo'
    },
    inventarioEstadoColor (estado) {
      return this.normalizeInventarioEstado(estado) === 'ACTIVO' ? 'positive' : 'grey-7'
    },
    async cargarInventarios () { const r = await this.$axios.get('inventarios'); this.inventarios = r.data || [] },
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
    abrirMovimiento (row, tipo) {
      this.invSel = row
      this.mov = {
        fecha: new Date().toISOString().slice(0, 10),
        tipo,
        cantidad: 1,
        motivo: ''
      }
      this.dialogMovimiento = true
    },
    async guardarMovimiento () {
      if (!this.invSel?.id) return
      this.loadingMov = true
      try {
        await this.$axios.post(`inventarios/${this.invSel.id}/movimientos`, this.mov)
        this.dialogMovimiento = false
        await this.cargarInventarios()
        this.$alert.success('Movimiento registrado')
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar movimiento')
      } finally {
        this.loadingMov = false
      }
    },
    abrirHistorial (row) {
      this.invSel = row
      this.historial = {
        date_from: new Date().toISOString().slice(0, 10),
        date_to: new Date().toISOString().slice(0, 10)
      }
      this.dialogHistorial = true
      this.cargarHistorial()
    },
    async cargarHistorial () {
      if (!this.invSel?.id) return
      this.loadingHistorial = true
      try {
        const res = await this.$axios.get(`inventarios/${this.invSel.id}/movimientos`, {
          params: {
            date_from: this.historial.date_from,
            date_to: this.historial.date_to
          }
        })
        this.movimientos = res.data || []
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar historial')
      } finally {
        this.loadingHistorial = false
      }
    },
    anularMovimiento (row) {
      if (!this.invSel?.id) return
      this.$alert.dialog('Desea anular este movimiento?').onOk(async () => {
        try {
          await this.$axios.post(`inventarios/${this.invSel.id}/movimientos/${row.id}/anular`, {})
          await Promise.all([this.cargarInventarios(), this.cargarHistorial()])
          this.$alert.success('Movimiento anulado')
        } catch (e) {
          this.$alert.error(e.response?.data?.message || 'No se pudo anular movimiento')
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
