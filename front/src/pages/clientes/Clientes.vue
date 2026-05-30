<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center tipo-banner" :class="tipoThemeClass">
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

    <div class="row items-center q-mb-sm q-gutter-xs">
      <q-btn
        no-caps dense unelevated
        :color="filtroEstado === 'todos' ? 'primary' : 'grey-3'"
        :text-color="filtroEstado === 'todos' ? 'white' : 'grey-8'"
        label="Todos"
        @click="filtroEstado = 'todos'"
      />
      <q-btn
        no-caps dense unelevated
        :color="filtroEstado === 'activos' ? 'positive' : 'grey-3'"
        :text-color="filtroEstado === 'activos' ? 'white' : 'grey-8'"
        label="Activos"
        @click="filtroEstado = 'activos'"
      />
      <q-btn
        no-caps dense unelevated
        :color="filtroEstado === 'inactivos' ? 'negative' : 'grey-3'"
        :text-color="filtroEstado === 'inactivos' ? 'white' : 'grey-8'"
        label="Inactivos"
        @click="filtroEstado = 'inactivos'"
      />
    </div>

    <q-table
      :rows="clientesFiltrados"
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
        <q-btn-dropdown
          color="teal"
          label="Reportes"
          icon="description"
          no-caps
          class="q-mr-sm"
          :loading="loading || loadingPdf"
        >
          <q-list dense>
            <q-item clickable v-close-popup @click="exportExcel">
              <q-item-section avatar>
                <q-icon name="table_view" color="indigo" />
              </q-item-section>
              <q-item-section>Excel</q-item-section>
            </q-item>
            <q-item clickable v-close-popup @click="exportPdf">
              <q-item-section avatar>
                <q-icon name="picture_as_pdf" color="deep-orange" />
              </q-item-section>
              <q-item-section>PDF</q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
        <q-btn
          color="brown"
          label="Ubicaciones"
          icon="map"
          no-caps
          @click="dialogUbicaciones = true"
        />
      </template>

      <template #body-cell-fechanac="props">
        <q-td :props="props">{{ formatDateOnly(props.row.fechanac) }}</q-td>
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
              <div class="col-12 col-md-4">
                <q-input v-model="cliente.razon_social" label="Razon social" dense outlined />
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

            <q-toggle v-model="cliente.estado" label="Activo" color="positive" class="q-mb-md" :trueValue="1" :falseValue="0" />
<!--            <pre>{{cliente}}</pre>-->

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
              <q-td :props="props">{{ formatDateTime(props.row.created_at) }}</q-td>
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
                <q-td :props="props">{{ formatDateOnly(props.row.fecha_pago) }}</q-td>
              </template>
            </q-table>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogUbicaciones" maximized>
      <q-card>
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            Ubicaciones de clientes
            <span class="text-caption text-grey-7 q-ml-sm">({{ clientesConUbicacion.length }} con coordenadas)</span>
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section style="height: calc(100vh - 70px); padding: 8px 16px;">
          <div class="row items-center q-gutter-sm q-mb-sm">
            <q-select
              v-model="clienteSeleccionadoUbicacion"
              :options="opcionesFiltradas"
              label="Ir a cliente..."
              dense
              outlined
              clearable
              use-input
              hide-selected
              fill-input
              input-debounce="150"
              style="min-width: 280px"
              no-error-icon
              @filter="filtrarOpcionesUbicacion"
              @update:model-value="irACliente"
            />
            <q-btn-toggle
              v-model="mapaCapaUbicaciones"
              no-caps
              dense
              rounded
              toggle-color="primary"
              :options="[
                { label: 'Mapa', value: 'calle', icon: 'map' },
                { label: 'Satelite', value: 'satelite', icon: 'satellite_alt' }
              ]"
            />
          </div>
          <l-map
            ref="ubicacionesMap"
            v-if="dialogUbicaciones"
            style="height: calc(100% - 52px); width: 100%; border-radius: 8px;"
            :zoom="13"
            :center="ubicacionesCenter"
            :use-global-leaflet="false"
          >
            <l-tile-layer
              :url="tileUrlUbicaciones"
              layer-type="base"
              name="Mapa"
            />
            <l-marker
              v-for="c in clientesConUbicacion"
              :key="c.id"
              :lat-lng="[Number(c.lat), Number(c.lng)]"
            >
              <l-popup>
                <strong>{{ c.nombre }}</strong><br>
                <span v-if="c.razon_social">R. Social: {{ c.razon_social }}<br></span>
                <span v-if="c.telefono">Tel: {{ c.telefono }}<br></span>
                <span v-if="c.direccion">Dir: {{ c.direccion }}<br></span>
              </l-popup>
            </l-marker>
          </l-map>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import ClienteMapa from 'src/components/ClienteMapa.vue'
import { LMap, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet'

export default {
  name: 'ClientesPage',
  components: { ClienteMapa, LMap, LTileLayer, LMarker, LPopup },

  data () {
    return {
      clientes: [],
      cliente: {},
      dialogCliente: false,
      dialogHistorial: false,
      dialogUbicaciones: false,
      filtroEstado: 'todos',
      clienteSeleccionadoUbicacion: null,
      mapaCapaUbicaciones: 'calle',
      opcionesFiltradas: [],
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
        // { name: 'local', label: 'Local', align: 'left', field: 'local' },
        { name: 'titular', label: 'Titular', align: 'left', field: 'titular' },
        // { name: 'tipo', label: 'Tipo', align: 'left', field: 'tipo' },
        { name: 'ci', label: 'CI', align: 'left', field: 'ci' },
        { name: 'telefono', label: 'Telefono', align: 'left', field: 'telefono' },
        { name: 'direccion', label: 'Direccion', align: 'left', field: 'direccion' },
        { name: 'fechanac', label: 'F. Nac.', align: 'left', field: 'fechanac' },
        // { name: 'legalidad', label: 'Legalidad', align: 'left', field: 'legalidad' },
        // { name: 'categoria', label: 'Categoria', align: 'left', field: 'categoria' },
        { name: 'razon_social', label: 'Razon social', align: 'left', field: 'razon_social' },
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
    tipoThemeClass () {
      return this.tipoCliente === 'local' ? 'tipo-local' : 'tipo-detalle'
    },
    kpi () {
      const total = this.clientes.length
      const activos = this.clientes.filter(c => !!c.estado).length
      return {
        total,
        activos,
        inactivos: total - activos
      }
    },
    clientesFiltrados () {
      if (this.filtroEstado === 'activos') return this.clientes.filter(c => Number(c.estado) === 1)
      if (this.filtroEstado === 'inactivos') return this.clientes.filter(c => Number(c.estado) !== 1)
      return this.clientes
    },
    clientesConUbicacion () {
      return this.clientes.filter(c => c.lat && c.lng)
    },
    opcionesClientesUbicacion () {
      return this.clientesConUbicacion.map(c => ({
        label: c.nombre,
        value: c.id,
        lat: Number(c.lat),
        lng: Number(c.lng)
      }))
    },
    tileUrlUbicaciones () {
      return this.mapaCapaUbicaciones === 'satelite'
        ? 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
        : 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    },
    ubicacionesCenter () {
      const lista = this.clientesConUbicacion
      if (lista.length === 0) return [-17.9647, -67.1060]
      const sumLat = lista.reduce((s, c) => s + Number(c.lat), 0)
      const sumLng = lista.reduce((s, c) => s + Number(c.lng), 0)
      return [sumLat / lista.length, sumLng / lista.length]
    }
  },

  watch: {
    '$route.params.tipo' () {
      this.clientesGet()
    },
    dialogUbicaciones (val) {
      if (!val) {
        this.clienteSeleccionadoUbicacion = null
        this.mapaCapaUbicaciones = 'calle'
      } else {
        this.opcionesFiltradas = this.opcionesClientesUbicacion.slice()
      }
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
    formatDateTime (v) {
      if (!v) return '-'
      const d = new Date(v)
      if (Number.isNaN(d.getTime())) return v
      const dd = String(d.getDate()).padStart(2, '0')
      const mm = String(d.getMonth() + 1).padStart(2, '0')
      const yy = d.getFullYear()
      const hh = String(d.getHours()).padStart(2, '0')
      const mi = String(d.getMinutes()).padStart(2, '0')
      return `${dd}/${mm}/${yy} ${hh}:${mi}`
    },
    formatDateOnly (v) {
      if (!v) return '-'
      const parts = String(v).split('T')[0].split('-')
      if (parts.length !== 3) return v
      return `${parts[2]}/${parts[1]}/${parts[0]}`
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
        razon_social: '',
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
      const exportCols = this.columns.filter(c => c.name !== 'actions' && c.field)
      const headers = exportCols.map(c => c.label)
      const trs = this.clientesFiltrados.map(r => {
        const cells = exportCols.map(col => {
          if (col.name === 'estado') return Number(r.estado) === 1 ? 'Activo' : 'Inactivo'
          if (col.name === 'fechanac') return this.formatDateOnly(r.fechanac)
          return r[col.field] ?? ''
        })
        return `<tr>${cells.map(v => `<td>${v}</td>`).join('')}</tr>`
      }).join('')

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
          params: { tipo_cliente: this.tipoCliente, filtro_estado: this.filtroEstado },
          responseType: 'blob',
          timeout: 90000
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
    irACliente (opcion) {
      if (!opcion) return
      const map = this.$refs.ubicacionesMap?.leafletObject
      if (map) {
        map.flyTo([opcion.lat, opcion.lng], 17)
      }
    },
    filtrarOpcionesUbicacion (val, update) {
      update(() => {
        const v = val.toLowerCase()
        this.opcionesFiltradas = v
          ? this.opcionesClientesUbicacion.filter(o => o.label.toLowerCase().includes(v))
          : this.opcionesClientesUbicacion.slice()
      })
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
