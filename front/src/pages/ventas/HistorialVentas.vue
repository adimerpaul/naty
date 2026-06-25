<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center bg-purple-1">
        <div>
          <div class="text-h6">Historial de Ventas</div>
          <div class="text-caption text-grey-7">Consulta de ventas por rango de fechas</div>
        </div>
        <q-space />
        <q-btn flat no-caps icon="search" color="primary" label="Buscar" @click="cargar" :loading="loading" />
      </q-card-section>
    </q-card>

    <!-- FILTROS -->
    <div class="row q-col-gutter-md q-mb-md items-center">
      <div class="col-12 col-md-2">
        <q-input
          v-model="filters.date_from"
          type="date"
          dense
          outlined
          :label="filters.one_day ? 'Fecha' : 'Desde'"
        />
      </div>
      <div class="col-12 col-md-2" v-if="!filters.one_day">
        <q-input
          v-model="filters.date_to"
          type="date"
          dense
          outlined
          label="Hasta"
        />
      </div>
      <div class="col-12 col-md-2">
        <q-toggle v-model="filters.one_day" dense label="Un solo dia" />
      </div>
      <div class="col-12 col-md-3">
        <q-input
          v-model="filters.search"
          dense
          outlined
          label="Buscar cliente / id"
          clearable
          @keyup.enter="cargar"
        />
      </div>
      <div class="col-12 col-md-2">
        <q-select
          v-model="filters.tipo_venta"
          dense
          outlined
          emit-value
          map-options
          clearable
          :options="[
            { label: 'Todos', value: '' },
            { label: 'Detalle', value: 'detalle' },
            { label: 'Local', value: 'local' }
          ]"
          label="Tipo venta"
        />
      </div>
    </div>

    <!-- RESUMEN TOTALES -->
    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="q-pa-sm">
            <div class="text-caption text-grey-7">Total ventas</div>
            <div class="text-h6 text-weight-bold">{{ rows.length }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="q-pa-sm">
            <div class="text-caption text-grey-7">Monto total</div>
            <div class="text-h6 text-weight-bold text-primary">{{ money(totalVentas) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="q-pa-sm">
            <div class="text-caption text-grey-7">Total pagado</div>
            <div class="text-h6 text-weight-bold text-positive">{{ money(totalPagado) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="q-pa-sm">
            <div class="text-caption text-grey-7">Saldo pendiente</div>
            <div class="text-h6 text-weight-bold text-orange-8">{{ money(totalSaldo) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- TABLA PRINCIPAL -->
    <q-table
      dense
      flat
      bordered
      :rows="rowsFiltrados"
      :columns="columns"
      row-key="id"
      :loading="loading"
      v-model:pagination="pagination"
      :rows-per-page-options="[10, 25, 50, 100, 0]"
    >
      <template #body-cell-tipo_venta="props">
        <q-td :props="props">
          <q-chip
            dense
            :color="props.row.tipo_venta === 'local' ? 'green-7' : 'blue-7'"
            text-color="white"
            size="sm"
          >
            {{ props.row.tipo_venta === 'local' ? 'LOCAL' : 'DETALLE' }}
          </q-chip>
        </q-td>
      </template>
      <template #body-cell-estado="props">
        <q-td :props="props">
          <q-chip
            dense
            :color="estadoColor(props.row.estado)"
            text-color="white"
            size="sm"
          >
            {{ props.row.estado }}
          </q-chip>
        </q-td>
      </template>
      <template #body-cell-total="props">
        <q-td :props="props" class="text-right">{{ money(props.row.total) }}</q-td>
      </template>
      <template #body-cell-total_pagado="props">
        <q-td :props="props" class="text-right text-positive">{{ money(props.row.total_pagado) }}</q-td>
      </template>
      <template #body-cell-saldo_pendiente="props">
        <q-td :props="props" class="text-right text-weight-bold text-orange-8">{{ money(props.row.saldo_pendiente) }}</q-td>
      </template>
      <template #body-cell-acciones="props">
        <q-td :props="props">
          <q-btn
            dense
            flat
            no-caps
            color="primary"
            icon="visibility"
            label="Detalle"
            size="sm"
            @click="verDetalle(props.row)"
          />
        </q-td>
      </template>
    </q-table>

    <!-- MODAL DETALLE -->
    <q-dialog v-model="dialogDetalle">
      <q-card style="width: 800px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Detalle de Venta #{{ ventaSel?.id }}</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm q-mb-sm">
            <div class="col-6 col-md-3"><b>Fecha:</b> {{ ventaSel?.fecha }}</div>
            <div class="col-6 col-md-3"><b>Cliente:</b> {{ ventaSel?.cliente_nombre || '-' }}</div>
            <div class="col-6 col-md-3"><b>Total:</b> {{ money(ventaSel?.total) }} Bs</div>
            <div class="col-6 col-md-3"><b>Pagado:</b> {{ money(ventaSel?.total_pagado) }} Bs</div>
            <div class="col-6 col-md-3"><b>Saldo:</b> {{ money(ventaSel?.saldo_pendiente) }} Bs</div>
            <div class="col-6 col-md-3"><b>Usuario:</b> {{ ventaSel?.user?.name || '-' }}</div>
            <div v-if="ventaSel?.observacion" class="col-12"><b>Observacion:</b> {{ ventaSel?.observacion }}</div>
          </div>
          <q-table
            dense
            flat
            bordered
            :rows="ventaSel?.detalles || []"
            :columns="colsDetalle"
            row-key="id"
            hide-pagination
            :rows-per-page-options="[0]"
          >
            <template #body-cell-precio="props">
              <q-td :props="props" class="text-right">{{ money(props.row.precio) }}</q-td>
            </template>
            <template #body-cell-subtotal="props">
              <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.subtotal) }}</q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>

    <div id="myElement" class="hidden"></div>
  </q-page>
</template>

<script>
export default {
  name: 'HistorialVentas',
  data () {
    const today = new Date().toISOString().slice(0, 10)
    return {
      loading: false,
      rows: [],
      dialogDetalle: false,
      ventaSel: null,
      pagination: { page: 1, rowsPerPage: 25, sortBy: 'id', descending: true },
      filters: {
        date_from: today,
        date_to: today,
        one_day: true,
        search: '',
        tipo_venta: ''
      },
      columns: [
        { name: 'acciones', label: '', align: 'left', style: 'width: 90px' },
        { name: 'id', label: 'Id', field: 'id', align: 'left', sortable: true },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left', sortable: true },
        { name: 'tipo_venta', label: 'Tipo', field: 'tipo_venta', align: 'left' },
        { name: 'cliente_nombre', label: 'Cliente', field: 'cliente_nombre', align: 'left' },
        { name: 'total', label: 'Total', field: 'total', align: 'right', sortable: true },
        { name: 'total_pagado', label: 'A cuenta', field: 'total_pagado', align: 'right', sortable: true },
        { name: 'saldo_pendiente', label: 'Saldo', field: 'saldo_pendiente', align: 'right', sortable: true },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'user', label: 'Usuario', field: row => row.user?.name || '-', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' }
      ],
      colsDetalle: [
        { name: 'producto_nombre', label: 'Producto', field: 'producto_nombre', align: 'left' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'precio', label: 'Precio', field: 'precio', align: 'right' },
        { name: 'subtotal', label: 'Subtotal', field: 'subtotal', align: 'right' }
      ]
    }
  },
  computed: {
    rowsFiltrados () {
      let data = this.rows
      if (this.filters.tipo_venta) {
        data = data.filter(r => r.tipo_venta === this.filters.tipo_venta)
      }
      if (this.filters.search) {
        const s = this.filters.search.toLowerCase()
        data = data.filter(r =>
          (r.cliente_nombre || '').toLowerCase().includes(s) ||
          String(r.id).includes(s)
        )
      }
      return data
    },
    totalVentas () { return this.rowsFiltrados.reduce((a, b) => a + Number(b.total || 0), 0) },
    totalPagado () { return this.rowsFiltrados.reduce((a, b) => a + Number(b.total_pagado || 0), 0) },
    totalSaldo () { return this.rowsFiltrados.reduce((a, b) => a + Number(b.saldo_pendiente || 0), 0) }
  },
  mounted () {
    this.cargar()
  },
  methods: {
    money (n) { return Number(n || 0).toFixed(2) },
    estadoColor (estado) {
      if (estado === 'ACTIVA') return 'positive'
      if (estado === 'ANULADA') return 'negative'
      return 'grey-7'
    },
    cargar () {
      this.loading = true
      const params = {
        date_from: this.filters.date_from,
        date_to: this.filters.one_day ? this.filters.date_from : this.filters.date_to
      }
      this.$axios.get('ventas', { params })
        .then(r => {
          this.rows = (r.data || []).filter(v => v.tipo_venta === 'detalle' || v.tipo_venta === 'local')
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar el historial'))
        .finally(() => { this.loading = false })
    },
    verDetalle (row) {
      this.$axios.get(`ventas/${row.id}`)
        .then(r => {
          this.ventaSel = r.data
          this.dialogDetalle = true
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar el detalle'))
    }
  }
}
</script>
