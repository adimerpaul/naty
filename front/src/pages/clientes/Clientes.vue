<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <div>
          <div class="text-h6 text-title">{{ tituloPagina }}</div>
          <div class="text-caption text-grey-7">
            Gestion de clientes de compras y ventas
          </div>
        </div>
        <q-space />
        <q-input v-model="filter" label="Buscar" dense outlined debounce="250" style="width: 280px">
          <template #append><q-icon name="search" /></template>
        </q-input>
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <q-avatar color="primary" text-color="white" icon="groups" />
            <div class="q-ml-md">
              <div class="text-caption text-grey-7">Total clientes</div>
              <div class="text-h6 text-weight-bold">{{ kpi.total }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <q-avatar color="positive" text-color="white" icon="check_circle" />
            <div class="q-ml-md">
              <div class="text-caption text-grey-7">Clientes activos</div>
              <div class="text-h6 text-weight-bold">{{ kpi.activos }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <q-avatar color="negative" text-color="white" icon="cancel" />
            <div class="q-ml-md">
              <div class="text-caption text-grey-7">Clientes inactivos</div>
              <div class="text-h6 text-weight-bold">{{ kpi.inactivos }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-table
      :rows="clientes"
      :columns="columns"
      row-key="id"
      dense
      flat
      bordered
      wrap-cells
      :filter="filter"
      v-model:pagination="pagination"
      :rows-per-page-options="[10, 25, 50, 100]"
      loading-label="Cargando..."
      no-data-label="Sin registros"
    >
      <template #top-right>
        <q-btn
          color="positive"
          label="Nuevo"
          icon="add_circle_outline"
          no-caps
          class="q-mr-sm"
          :loading="loading"
          @click="clienteNuevo"
        />
        <q-btn
          color="primary"
          label="Actualizar"
          icon="refresh"
          no-caps
          class="q-mr-sm"
          :loading="loading"
          @click="clientesGet"
        />
        <q-btn
          color="indigo"
          label="Excel"
          icon="table_view"
          no-caps
          class="q-mr-sm"
          :loading="loading"
          @click="exportExcel"
        />
        <q-btn
          color="deep-orange"
          label="PDF"
          icon="picture_as_pdf"
          no-caps
          :loading="loadingPdf"
          @click="exportPdf"
        />
      </template>

      <template #body-cell-estado="props">
        <q-td :props="props">
          <q-chip
            dense
            :color="props.row.estado ? 'positive' : 'grey-7'"
            text-color="white"
          >
            {{ props.row.estado ? 'Activo' : 'Inactivo' }}
          </q-chip>
        </q-td>
      </template>

      <template #body-cell-actions="props">
        <q-td :props="props" class="text-left">
          <q-btn-dropdown
            dense
            color="primary"
            label="Opciones"
            no-caps
            size="10px"
          >
            <q-list dense>
              <q-item clickable v-close-popup @click="clienteEditar(props.row)">
                <q-item-section avatar>
                  <q-icon name="edit" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Editar</q-item-label>
                </q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="verHistorial(props.row)">
                <q-item-section avatar>
                  <q-icon name="history" color="primary" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Historial</q-item-label>
                </q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="clienteEliminar(props.row.id)">
                <q-item-section avatar>
                  <q-icon name="delete" color="negative" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Eliminar</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialogCliente" persistent>
      <q-card style="width: 860px; max-width: 98vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            {{ cliente.id ? 'Editar cliente' : 'Nuevo cliente' }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense @click="dialogCliente = false" />
        </q-card-section>

        <q-card-section class="q-pt-sm">
          <q-form @submit.prevent="cliente.id ? clientePut() : clientePost()">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4" v-if="tipoCliente === 'local'">
                <q-input v-model="cliente.local" label="Local" dense outlined :rules="[req]" />
              </div>
              <div :class="tipoCliente === 'local' ? 'col-12 col-md-8' : 'col-12'">
                <q-input v-model="cliente.titular" label="Titular" dense outlined :rules="[req]" />
              </div>
              <div class="col-12 col-md-4" v-if="tipoCliente === 'local'">
                <q-select
                  v-model="cliente.tipo"
                  :options="tipoLocalOptions"
                  label="Tipo"
                  dense
                  outlined
                  emit-value
                  map-options
                  :rules="[req]"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="cliente.ci" label="CI" dense outlined />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="cliente.telefono" label="Telefono" dense outlined />
              </div>
              <div class="col-12 col-md-6">
                <q-input v-model="cliente.direccion" label="Direccion" dense outlined />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="cliente.fechanac" type="date" label="Fecha nac." dense outlined />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="cliente.nit" label="NIT" dense outlined />
              </div>
              <div class="col-12 col-md-4" v-if="tipoCliente === 'local'">
                <q-select
                  v-model="cliente.legalidad"
                  :options="legalidadOptions"
                  label="Legalidad"
                  dense
                  outlined
                  emit-value
                  map-options
                  :rules="[req]"
                />
              </div>
              <div class="col-12 col-md-4" v-if="tipoCliente === 'local'">
                <q-select
                  v-model="cliente.categoria"
                  :options="categoriaOptions"
                  label="Categoria"
                  dense
                  outlined
                  emit-value
                  map-options
                  :rules="[req]"
                />
              </div>
              <div class="col-12 col-md-4" v-if="tipoCliente === 'local'">
                <q-input v-model="cliente.razon" label="Razon social" dense outlined />
              </div>
              <div class="col-12">
                <q-input v-model="cliente.observacion" type="textarea" autogrow label="Observacion" dense outlined />
              </div>
              <div class="col-12">
                <cliente-mapa
                  class="q-mb-md"
                  :lat="cliente.lat"
                  :lng="cliente.lng"
                  @update:lat="cliente.lat = $event"
                  @update:lng="cliente.lng = $event"
                />
              </div>
            </div>

            <q-toggle v-model="cliente.estado" label="Activo" color="positive" class="q-mb-md" />

            <div class="row justify-end q-gutter-sm">
              <q-btn color="negative" flat no-caps label="Cancelar" @click="dialogCliente = false" :disable="loading" />
              <q-btn color="primary" no-caps label="Guardar" type="submit" :loading="loading" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogHistorial">
      <q-card style="width: 980px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Historial de compras - {{ historialCliente?.nombre || '' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-table dense flat bordered :rows="historialVentas" :columns="colsHistorial" row-key="id" hide-pagination @row-click="seleccionarVentaHistorial">
            <template #body-cell-created_at="props">
              <q-td :props="props">{{ normalDate(props.row.created_at) }}</q-td>
            </template>
            <template #body-cell-tipo_pago="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.tipo_pago === 'credito' ? 'warning' : 'positive'" text-color="white">
                  {{ props.row.tipo_pago }}
                </q-chip>
              </q-td>
            </template>
            <template #body-cell-saldo_pendiente="props">
              <q-td :props="props">
                <q-chip dense :color="Number(props.row.saldo_pendiente || 0) > 0 ? 'orange-8' : 'positive'" text-color="white">
                  {{ money(props.row.saldo_pendiente) }}
                </q-chip>
              </q-td>
            </template>
            <template #body-cell-deuda_oculta="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.deuda_oculta ? 'grey-8' : 'blue-7'" text-color="white">
                  {{ props.row.deuda_oculta ? 'Ocultado' : 'Visible' }}
                </q-chip>
              </q-td>
            </template>
          </q-table>
          <div class="q-mt-md">
            <div class="text-subtitle2 q-mb-xs">Pagos de la venta seleccionada</div>
            <q-table dense flat bordered :rows="historialPagos" :columns="colsPagos" row-key="id" hide-pagination>
              <template #body-cell-estado="props">
                <q-td :props="props">
                  <q-chip dense :color="chipColor(props.row.estado)" text-color="white">{{ props.row.estado }}</q-chip>
                </q-td>
              </template>
              <template #body-cell-fecha_pago="props">
                <q-td :props="props">{{ normalDate(props.row.fecha_pago) }}</q-td>
              </template>
            </q-table>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import ClienteMapa from 'src/components/ClienteMapa.vue'

export default {
  name: 'ClientesPage',
  components: { ClienteMapa },

  data () {
    return {
      clientes: [],
      cliente: {},
      dialogCliente: false,
      dialogHistorial: false,
      loading: false,
      loadingPdf: false,
      historialCliente: null,
      historialVentas: [],
      historialPagos: [],
      filter: '',
      pagination: {
        page: 1,
        rowsPerPage: 25,
        sortBy: 'id',
        descending: true
      },
      columns: [
        { name: 'actions', label: 'Acciones', align: 'left' },
        { name: 'nombre', label: 'Nombre', align: 'left', field: 'nombre' },
        { name: 'local', label: 'Local', align: 'left', field: 'local' },
        { name: 'titular', label: 'Titular', align: 'left', field: 'titular' },
        { name: 'tipo', label: 'Tipo', align: 'left', field: 'tipo' },
        { name: 'ci', label: 'CI', align: 'left', field: 'ci' },
        { name: 'telefono', label: 'Telefono', align: 'left', field: 'telefono' },
        { name: 'direccion', label: 'Direccion', align: 'left', field: 'direccion' },
        { name: 'fechanac', label: 'F. Nac.', align: 'left', field: 'fechanac' },
        { name: 'legalidad', label: 'Legalidad', align: 'left', field: 'legalidad' },
        { name: 'categoria', label: 'Categoria', align: 'left', field: 'categoria' },
        { name: 'nit', label: 'NIT', align: 'left', field: 'nit' },
        { name: 'lat', label: 'Lat', align: 'left', field: 'lat' },
        { name: 'lng', label: 'Lng', align: 'left', field: 'lng' },
        { name: 'estado', label: 'Estado', align: 'left', field: 'estado' }
      ],
      colsHistorial: [
        { name: 'id', label: 'Venta', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha', field: 'created_at', align: 'left' },
        { name: 'tipo_pago', label: 'Pago', field: 'tipo_pago', align: 'left' },
        { name: 'total', label: 'Total', field: 'total', align: 'right' },
        { name: 'total_pagado', label: 'Pagado', field: 'total_pagado', align: 'right' },
        { name: 'saldo_pendiente', label: 'Deuda', field: 'saldo_pendiente', align: 'right' },
        { name: 'deuda_oculta', label: 'Estado deuda', field: 'deuda_oculta', align: 'left' }
      ],
      colsPagos: [
        { name: 'nro_cuota', label: 'Cuota', field: 'nro_cuota', align: 'left' },
        { name: 'monto', label: 'Monto', field: 'monto', align: 'right' },
        { name: 'metodo', label: 'Metodo', field: 'metodo', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'fecha_pago', label: 'F. Pago', field: 'fecha_pago', align: 'left' }
      ],
      tipoLocalOptions: [
        { label: 'Propietario', value: 'PROPIETARIO' },
        { label: 'Inquilino', value: 'INQUILINO' }
      ],
      legalidadOptions: [
        { label: 'Con licencia', value: 'CON LICENCIA' },
        { label: 'Sin licencia', value: 'SIN LICENCIA' }
      ],
      categoriaOptions: [
        { label: 'Simplificado', value: 'SIMPLIFICADO' },
        { label: 'General', value: 'GENERAL' },
        { label: 'Sin NIT', value: 'SIN NIT' }
      ]
    }
  },

  computed: {
    tipoCliente () {
      return this.$route.params.tipo === 'local' ? 'local' : 'detalle'
    },
    tituloPagina () {
      return this.tipoCliente === 'local' ? 'Cliente local' : 'Cliente detalle'
    },
    kpi () {
      const total = this.clientes.length
      const activos = this.clientes.filter(c => !!c.estado).length
      return {
        total,
        activos,
        inactivos: total - activos
      }
    }
  },

  watch: {
    '$route.params.tipo' () {
      this.clientesGet()
    }
  },

  mounted () {
    this.clientesGet()
  },

  methods: {
    req (v) {
      return !!v || 'Campo requerido'
    },
    money (n) {
      return Number(n || 0).toFixed(2)
    },
    normalDate (value) {
      if (!value) return '-'
      return String(value)
        .replace('T', ' ')
        .replace('.000000Z', '')
        .replace('.000Z', '')
    },
    chipColor (estado) {
      if (estado === 'PAGADO') return 'positive'
      if (estado === 'PENDIENTE') return 'orange-8'
      if (estado === 'ANULADO') return 'negative'
      return 'grey-7'
    },

    clienteNuevo () {
      this.cliente = {
        nombre: '',
        local: '',
        titular: '',
        tipo: '',
        ci: '',
        telefono: '',
        direccion: '',
        fechanac: null,
        legalidad: '',
        categoria: '',
        razon: '',
        nit: '',
        observacion: '',
        lat: null,
        lng: null,
        estado: true
      }
      this.dialogCliente = true
    },

    clienteEditar (row) {
      this.cliente = {
        ...row,
        lat: row.lat !== null ? Number(row.lat) : null,
        lng: row.lng !== null ? Number(row.lng) : null
      }
      this.dialogCliente = true
    },

    clientesGet () {
      this.loading = true
      this.$axios.get('clientes', { params: { tipo_cliente: this.tipoCliente } })
        .then(res => { this.clientes = res.data })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar clientes'))
        .finally(() => { this.loading = false })
    },

    clientePost () {
      this.loading = true
      this.$axios.post('clientes', this.payloadCliente())
        .then(() => {
          this.dialogCliente = false
          this.$alert.success('Cliente creado')
          this.clientesGet()
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo crear'))
        .finally(() => { this.loading = false })
    },

    clientePut () {
      this.loading = true
      this.$axios.put(`clientes/${this.cliente.id}`, this.payloadCliente())
        .then(() => {
          this.dialogCliente = false
          this.$alert.success('Cliente actualizado')
          this.clientesGet()
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo actualizar'))
        .finally(() => { this.loading = false })
    },

    clienteEliminar (id) {
      this.$alert.dialog('¿Desea eliminar el cliente?')
        .onOk(() => {
          this.loading = true
          this.$axios.delete(`clientes/${id}`)
            .then(() => {
              this.$alert.success('Cliente eliminado')
              this.clientesGet()
            })
            .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo eliminar'))
            .finally(() => { this.loading = false })
        })
    },
    async verHistorial (row) {
      try {
        const res = await this.$axios.get(`clientes/${row.id}/historial`, { params: { tipo_venta: this.tipoCliente } })
        this.historialCliente = row
        this.historialVentas = res.data || []
        this.historialPagos = this.historialVentas.length ? (this.historialVentas[0].pagos || []) : []
        this.dialogHistorial = true
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar historial')
      }
    },
    seleccionarVentaHistorial (evt, row) {
      this.historialPagos = row?.pagos || []
    },

    exportExcel () {
      const headers = ['ID', 'Nombre', 'Local', 'Titular', 'Tipo', 'Tipo cliente', 'CI', 'Telefono', 'Direccion', 'Fecha nac.', 'Legalidad', 'Categoria', 'NIT', 'Razon', 'Observacion', 'Lat', 'Lng', 'Estado']
      const trs = this.clientes.map(r => `
        <tr>
          <td>${r.id ?? ''}</td>
          <td>${r.nombre ?? ''}</td>
          <td>${r.local ?? ''}</td>
          <td>${r.titular ?? ''}</td>
          <td>${r.tipo ?? ''}</td>
          <td>${r.tipo_cliente ?? ''}</td>
          <td>${r.ci ?? ''}</td>
          <td>${r.telefono ?? ''}</td>
          <td>${r.direccion ?? ''}</td>
          <td>${r.fechanac ?? ''}</td>
          <td>${r.legalidad ?? ''}</td>
          <td>${r.categoria ?? ''}</td>
          <td>${r.nit ?? ''}</td>
          <td>${r.razon ?? ''}</td>
          <td>${r.observacion ?? ''}</td>
          <td>${r.lat ?? ''}</td>
          <td>${r.lng ?? ''}</td>
          <td>${r.estado ? 'Activo' : 'Inactivo'}</td>
        </tr>
      `).join('')

      const html = `
        <table border="1">
          <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
          <tbody>${trs}</tbody>
        </table>
      `
      const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8;' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `clientes-${this.tipoCliente}.xls`
      document.body.appendChild(a)
      a.click()
      a.remove()
      URL.revokeObjectURL(url)
    },

    async exportPdf () {
      this.loadingPdf = true
      try {
        const res = await this.$axios.get('clientes/pdf', {
          params: { tipo_cliente: this.tipoCliente },
          responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
        const a = document.createElement('a')
        a.href = url
        a.download = `clientes-${this.tipoCliente}.pdf`
        document.body.appendChild(a)
        a.click()
        a.remove()
        window.URL.revokeObjectURL(url)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo generar PDF')
      } finally {
        this.loadingPdf = false
      }
    },
    payloadCliente () {
      const base = { ...this.cliente, tipo_cliente: this.tipoCliente }
      if (this.tipoCliente === 'detalle') {
        base.nombre = base.titular || base.nombre || ''
      } else {
        base.nombre = base.local || base.nombre || ''
      }
      return base
    }
  }
}
</script>
