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
      <template #body-cell-actions="props">
        <q-td :props="props" class="text-left">
          <q-btn-dropdown dense color="primary" label="Opciones" no-caps size="10px">
            <q-list dense>
              <q-item clickable v-close-popup @click="verPagosMes(props.row)">
                <q-item-section avatar><q-icon name="history" color="primary" /></q-item-section>
                <q-item-section>Ver pagos</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="descargarBoletaResumen(props.row)">
                <q-item-section avatar><q-icon name="picture_as_pdf" color="negative" /></q-item-section>
                <q-item-section>Boleta PDF</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
      <template #body-cell-sueldo="props"><q-td :props="props" class="text-right">{{ money(props.row.sueldo) }}</q-td></template>
      <template #body-cell-extras="props"><q-td :props="props" class="text-right text-positive">{{ money(props.row.extras) }}</q-td></template>
      <template #body-cell-adelantos="props"><q-td :props="props" class="text-right text-orange">{{ money(props.row.adelantos) }}</q-td></template>
      <template #body-cell-descuentos="props"><q-td :props="props" class="text-right text-negative">{{ money(props.row.descuentos) }}</q-td></template>
      <template #body-cell-total_calculado="props"><q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.total_calculado) }}</q-td></template>
      <template #body-cell-total_pagado_salario="props"><q-td :props="props" class="text-right text-weight-bold text-primary">{{ money(props.row.total_pagado_salario) }}</q-td></template>
    </q-table>

    <q-dialog v-model="dialogPagos">
      <q-card style="width: 980px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            Pagos de {{ rowSel?.personal_nombre || '-' }} - {{ filters.mes }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-table
            dense
            flat
            bordered
            :rows="pagosRows"
            :columns="pagosCols"
            row-key="id"
            :pagination="{ rowsPerPage: 100 }"
          >
            <template #body-cell-actions="props">
              <q-td :props="props" class="text-left">
                <q-btn-dropdown dense color="primary" label="Opciones" no-caps size="10px">
                  <q-list dense>
                    <q-item clickable v-close-popup @click="imprimirPago(props.row)">
                      <q-item-section avatar><q-icon name="print" color="primary" /></q-item-section>
                      <q-item-section>Imprimir</q-item-section>
                    </q-item>
                    <q-item
                      clickable
                      v-close-popup
                      :disable="props.row.tipo_registro !== 'salario'"
                      @click="descargarBoleta(props.row)"
                    >
                      <q-item-section avatar><q-icon name="picture_as_pdf" color="negative" /></q-item-section>
                      <q-item-section>Boleta PDF</q-item-section>
                    </q-item>
                  </q-list>
                </q-btn-dropdown>
              </q-td>
            </template>
            <template #body-cell-estado="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.estado === 'ANULADO' ? 'negative' : 'positive'" text-color="white">{{ props.row.estado }}</q-chip>
              </q-td>
            </template>
            <template #body-cell-monto_pagado="props"><q-td :props="props" class="text-right">{{ money(props.row.monto_pagado) }}</q-td></template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'

export default {
  name: 'HistorialPagosPersonalPage',
  data () {
    return {
      loading: false,
      rows: [],
      dialogPagos: false,
      pagosRows: [],
      rowSel: null,
      filters: {
        mes: new Date().toISOString().slice(0, 7)
      },
      columns: [
        { name: 'actions', label: '', field: 'personal_id', align: 'left' },
        { name: 'mes', label: 'Mes', field: 'mes', align: 'left' },
        { name: 'ci', label: 'CI', field: 'ci', align: 'left' },
        { name: 'personal_nombre', label: 'Personal', field: 'personal_nombre', align: 'left' },
        { name: 'sueldo', label: 'Sueldo', field: 'sueldo', align: 'right' },
        { name: 'extras', label: 'Extras', field: 'extras', align: 'right' },
        { name: 'adelantos', label: 'Adelantos', field: 'adelantos', align: 'right' },
        { name: 'descuentos', label: 'Descuentos', field: 'descuentos', align: 'right' },
        { name: 'total_calculado', label: 'Total calculado', field: 'total_calculado', align: 'right' },
        { name: 'total_pagado_salario', label: 'Pagado salario', field: 'total_pagado_salario', align: 'right' }
      ],
      pagosCols: [
        { name: 'actions', label: '', field: 'id', align: 'left' },
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'tipo_registro', label: 'Tipo', field: 'tipo_registro', align: 'left' },
        { name: 'monto_pagado', label: 'Monto', field: 'monto_pagado', align: 'right' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'fecha_pago', label: 'Fecha pago', field: 'fecha_pago', align: 'left' }
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
    },
    async verPagosMes (row) {
      this.rowSel = row
      try {
        const r = await this.$axios.get('personal-pagos', {
          params: {
            mes: this.filters.mes,
            personal_id: row.personal_id
          }
        })
        this.pagosRows = r.data || []
        this.dialogPagos = true
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar pagos del mes')
      }
    },
    async descargarBoletaResumen (row) {
      try {
        const r = await this.$axios.get('personal-pagos', {
          params: {
            mes: this.filters.mes,
            personal_id: row.personal_id,
            tipo_registro: 'salario',
            estado: 'ACTIVO'
          }
        })
        const salario = (r.data || [])[0]
        if (!salario?.id) {
          this.$alert.error('No existe boleta de salario para ese mes')
          return
        }
        await this.descargarBoleta(salario)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo obtener boleta del mes')
      }
    },
    async descargarBoleta (row) {
      try {
        const res = await this.$axios.get(`personal-pagos/${row.id}/boleta-pdf`, { responseType: 'blob' })
        const blob = new Blob([res.data], { type: 'application/pdf' })
        const url = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = url
        a.download = `boleta-personal-${row.id}.pdf`
        document.body.appendChild(a)
        a.click()
        a.remove()
        window.URL.revokeObjectURL(url)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo descargar boleta')
      }
    },
    imprimirPago (row) {
      if (row.tipo_registro === 'salario') {
        this.descargarBoleta(row)
      } else {
        Imprimir.pagoPersonalMovimiento(row)
      }
    }
  }
}
</script>
