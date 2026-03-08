<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <div>
          <div class="text-h6">Deuda detalle</div>
          <div class="text-caption text-grey-7">Clientes con saldo pendiente</div>
        </div>
        <q-space />
        <q-btn flat no-caps icon="search" color="primary" label="Buscar" @click="cargar" :loading="loading" class="q-mr-sm" />
        <q-btn flat no-caps icon="refresh" color="grey-8" label="Actualizar" @click="cargar" :loading="loading" />
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-md-2"><q-input v-model="filters.date_from" type="date" dense outlined label="Desde" /></div>
      <div class="col-12 col-md-2"><q-input v-model="filters.date_to" type="date" dense outlined label="Hasta" /></div>
      <div class="col-12 col-md-4"><q-input v-model="filters.search" dense outlined label="Buscar cliente / telefono / nro venta" @keyup.enter="cargar" /></div>
    </div>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-caption text-grey-7">Total deudores</div>
            <div class="text-h6 text-weight-bold">{{ rows.length }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat bordered>
          <q-card-section>
            <div class="text-caption text-grey-7">Saldo pendiente</div>
            <div class="text-h6 text-weight-bold text-orange-8">{{ money(totalDeuda) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-table
      dense
      flat
      bordered
      :rows="rows"
      :columns="columns"
      row-key="id"
      :loading="loading"
      v-model:pagination="pagination"
      :rows-per-page-options="[10, 25, 50, 100]"
    >
      <template #body-cell-created_at="props"><q-td :props="props">{{ formatDate(props.row.created_at) }}</q-td></template>
      <template #body-cell-total_pagado="props"><q-td :props="props" class="text-right">{{ money(props.row.total_pagado) }}</q-td></template>
      <template #body-cell-saldo_pendiente="props"><q-td :props="props" class="text-right text-weight-bold text-orange-8">{{ money(props.row.saldo_pendiente) }}</q-td></template>
      <template #body-cell-actions="props">
        <q-td :props="props">
          <q-btn dense color="primary" no-caps label="Ver venta" @click="$router.push(`/ventas/detalle`)" />
        </q-td>
      </template>
    </q-table>
  </q-page>
</template>

<script>
export default {
  name: 'DeudasDetallePage',
  data () {
    const today = new Date().toISOString().slice(0, 10)
    return {
      loading: false,
      rows: [],
      pagination: { page: 1, rowsPerPage: 25, sortBy: 'id', descending: true },
      filters: { date_from: today, date_to: today, search: '' },
      columns: [
        { name: 'id', label: 'Venta', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha', field: 'created_at', align: 'left' },
        { name: 'cliente_nombre', label: 'Cliente', field: 'cliente_nombre', align: 'left' },
        { name: 'cliente_telefono', label: 'Telefono', field: 'cliente_telefono', align: 'left' },
        { name: 'total_pagado', label: 'Pagado', field: 'total_pagado', align: 'right' },
        { name: 'saldo_pendiente', label: 'Deuda', field: 'saldo_pendiente', align: 'right' },
        { name: 'actions', label: '', align: 'left' }
      ]
    }
  },
  computed: {
    totalDeuda () { return this.rows.reduce((a, b) => a + Number(b.saldo_pendiente || 0), 0) }
  },
  mounted () {
    this.cargar()
  },
  methods: {
    money (n) { return Number(n || 0).toFixed(2) },
    formatDate (value) {
      if (!value) return '-'
      const date = new Date(value)
      if (Number.isNaN(date.getTime())) return value
      const dd = String(date.getDate()).padStart(2, '0')
      const mm = String(date.getMonth() + 1).padStart(2, '0')
      const yy = date.getFullYear()
      return `${dd}/${mm}/${yy}`
    },
    cargar () {
      this.loading = true
      this.$axios.get('ventas/deudas/detalle', { params: this.filters })
        .then(r => { this.rows = r.data || [] })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar deudas'))
        .finally(() => { this.loading = false })
    }
  }
}
</script>
