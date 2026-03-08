<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-6">
          <div class="text-h6">Historial de pagos (personal activo)</div>
          <div class="text-caption text-grey-7">Resumen mensual de sueldos, extras, adelantos y descuentos</div>
        </div>
        <div class="col-12 col-md-3">
          <q-input v-model="filters.mes" type="month" dense outlined label="Mes" />
        </div>
        <div class="col-12 col-md-3 text-right">
          <q-btn color="primary" no-caps icon="refresh" label="Actualizar" @click="getData" :loading="loading" />
        </div>
      </q-card-section>
    </q-card>

    <q-table
      dense
      flat
      bordered
      :rows="rows"
      :columns="columns"
      row-key="personal_id"
      :pagination="{ rowsPerPage: 100 }"
      :rows-per-page-options="[50, 100]"
      :loading="loading"
    >
      <template #body-cell-sueldo="props"><q-td :props="props" class="text-right">{{ money(props.row.sueldo) }}</q-td></template>
      <template #body-cell-extras="props"><q-td :props="props" class="text-right text-positive">{{ money(props.row.extras) }}</q-td></template>
      <template #body-cell-adelantos="props"><q-td :props="props" class="text-right text-orange">{{ money(props.row.adelantos) }}</q-td></template>
      <template #body-cell-descuentos="props"><q-td :props="props" class="text-right text-negative">{{ money(props.row.descuentos) }}</q-td></template>
      <template #body-cell-total_calculado="props"><q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.total_calculado) }}</q-td></template>
      <template #body-cell-total_pagado_salario="props"><q-td :props="props" class="text-right text-weight-bold text-primary">{{ money(props.row.total_pagado_salario) }}</q-td></template>
    </q-table>
  </q-page>
</template>

<script>
export default {
  name: 'HistorialPagosPersonalPage',
  data () {
    return {
      loading: false,
      rows: [],
      filters: {
        mes: new Date().toISOString().slice(0, 7)
      },
      columns: [
        { name: 'mes', label: 'Mes', field: 'mes', align: 'left' },
        { name: 'ci', label: 'CI', field: 'ci', align: 'left' },
        { name: 'personal_nombre', label: 'Personal', field: 'personal_nombre', align: 'left' },
        { name: 'sueldo', label: 'Sueldo', field: 'sueldo', align: 'right' },
        { name: 'extras', label: 'Extras', field: 'extras', align: 'right' },
        { name: 'adelantos', label: 'Adelantos', field: 'adelantos', align: 'right' },
        { name: 'descuentos', label: 'Descuentos', field: 'descuentos', align: 'right' },
        { name: 'total_calculado', label: 'Total calculado', field: 'total_calculado', align: 'right' },
        { name: 'total_pagado_salario', label: 'Pagado salario', field: 'total_pagado_salario', align: 'right' }
      ]
    }
  },
  mounted () {
    this.getData()
  },
  methods: {
    money (n) { return Number(n || 0).toFixed(2) + ' Bs' },
    async getData () {
      this.loading = true
      try {
        const r = await this.$axios.get('personal-pagos/resumen-mensual', { params: this.filters })
        this.rows = r.data || []
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar historial')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

