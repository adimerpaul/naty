<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center bg-orange-1">
        <div>
          <div class="text-h6">Resumen de Gastos y Ventas</div>
          <div class="text-caption text-grey-7">Reporte consolidado del periodo</div>
        </div>
        <q-space />
        <q-btn flat no-caps icon="print" color="teal" label="Imprimir" class="q-mr-sm" @click="imprimir" :disable="loading" />
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
    </div>

    <!-- CARDS DE TOTALES -->
    <div class="row q-col-gutter-md q-mb-lg">
      <div class="col-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="q-pa-sm">
            <div class="text-caption text-grey-7">Total ventas</div>
            <div class="text-h6 text-weight-bold text-primary">{{ money(totales.total_ventas) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="q-pa-sm">
            <div class="text-caption text-grey-7">Total ingresado</div>
            <div class="text-h6 text-weight-bold text-positive">{{ money(totales.total_pagado) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="q-pa-sm">
            <div class="text-caption text-grey-7">Total gastos</div>
            <div class="text-h6 text-weight-bold text-negative">{{ money(totales.total_gastos) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-6 col-md-3">
        <q-card flat bordered>
          <q-card-section class="q-pa-sm">
            <div class="text-caption text-grey-7">Efectivo neto</div>
            <div class="text-h6 text-weight-bold text-teal">{{ money(totales.total_pagado - totales.total_gastos) }} Bs</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

<!--    &lt;!&ndash; TABLA VENTAS &ndash;&gt;-->
<!--    <div class="text-subtitle2 text-weight-bold q-mb-sm">Historial de Ventas del Periodo</div>-->
<!--    <q-table-->
<!--      dense-->
<!--      flat-->
<!--      bordered-->
<!--      :rows="ventas"-->
<!--      :columns="colsVentas"-->
<!--      row-key="id"-->
<!--      :loading="loading"-->
<!--      v-model:pagination="paginationVentas"-->
<!--      :rows-per-page-options="[10, 25, 50, 0]"-->
<!--      class="q-mb-lg"-->
<!--    >-->
<!--      <template #body-cell-tipo_venta="props">-->
<!--        <q-td :props="props">-->
<!--          <q-chip-->
<!--            dense-->
<!--            :color="props.row.tipo_venta === 'local' ? 'green-7' : 'blue-7'"-->
<!--            text-color="white"-->
<!--            size="sm"-->
<!--          >-->
<!--            {{ props.row.tipo_venta === 'local' ? 'LOCAL' : 'DETALLE' }}-->
<!--          </q-chip>-->
<!--        </q-td>-->
<!--      </template>-->
<!--      <template #body-cell-total="props">-->
<!--        <q-td :props="props" class="text-right">{{ money(props.row.total) }}</q-td>-->
<!--      </template>-->
<!--      <template #body-cell-total_pagado="props">-->
<!--        <q-td :props="props" class="text-right text-positive">{{ money(props.row.total_pagado) }}</q-td>-->
<!--      </template>-->
<!--      <template #body-cell-saldo_pendiente="props">-->
<!--        <q-td :props="props" class="text-right text-orange-8">{{ money(props.row.saldo_pendiente) }}</q-td>-->
<!--      </template>-->
<!--    </q-table>-->

    <!-- TABLA PAGOS DEL PERIODO -->
    <div class="text-subtitle2 text-weight-bold q-mb-sm">Pagos / Cobros del Periodo</div>
    <q-table
      dense
      flat
      bordered
      :rows="pagos"
      :columns="colsPagos"
      row-key="id"
      :loading="loading"
      v-model:pagination="paginationPagos"
      :rows-per-page-options="[10, 25, 50, 0]"
      class="q-mb-lg"
    >
      <template #body-cell-monto="props">
        <q-td :props="props" class="text-right text-positive text-weight-bold">{{ money(props.row.monto) }}</q-td>
      </template>
      <template #body-cell-tipo_venta="props">
        <q-td :props="props">
          <q-chip dense :color="props.row.tipo_venta === 'local' ? 'green-7' : 'blue-7'" text-color="white" size="sm">
            {{ props.row.tipo_venta === 'local' ? 'LOCAL' : 'DETALLE' }}
          </q-chip>
        </q-td>
      </template>
    </q-table>

    <!-- TABLA GASTOS -->
    <div class="text-subtitle2 text-weight-bold q-mb-sm">Gastos / Egresos de Caja del Periodo</div>
    <q-table
      dense
      flat
      bordered
      :rows="movimientos"
      :columns="colsMovimientos"
      row-key="id"
      :loading="loading"
      v-model:pagination="paginationMovimientos"
      :rows-per-page-options="[10, 25, 50, 0]"
    >
      <template #body-cell-monto_real="props">
        <q-td :props="props" class="text-right text-negative text-weight-bold">{{ money(props.row.monto_real) }}</q-td>
      </template>
    </q-table>

    <div id="myElement" class="hidden"></div>
  </q-page>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'

export default {
  name: 'ResumenGastos',
  data () {
    const today = new Date().toISOString().slice(0, 10)
    return {
      loading: false,
      ventas: [],
      movimientos: [],
      pagos: [],
      totales: { total_ventas: 0, total_pagado: 0, total_saldo: 0, total_gastos: 0 },
      paginationVentas: { page: 1, rowsPerPage: 25 },
      paginationMovimientos: { page: 1, rowsPerPage: 25 },
      paginationPagos: { page: 1, rowsPerPage: 25 },
      filters: {
        date_from: today,
        date_to: today,
        one_day: true
      },
      colsVentas: [
        { name: 'id', label: 'Id', field: 'id', align: 'left', sortable: true },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'tipo_venta', label: 'Tipo', field: 'tipo_venta', align: 'left' },
        { name: 'cliente_nombre', label: 'Cliente', field: 'cliente_nombre', align: 'left' },
        { name: 'total', label: 'Total', field: 'total', align: 'right', sortable: true },
        { name: 'total_pagado', label: 'A cuenta', field: 'total_pagado', align: 'right' },
        { name: 'saldo_pendiente', label: 'Saldo', field: 'saldo_pendiente', align: 'right' },
        { name: 'usuario', label: 'Usuario', field: row => row.user?.name || '-', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' }
      ],
      colsPagos: [
        { name: 'id', label: 'Id', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha/Hora', field: 'created_at', align: 'left', format: v => v ? String(v).slice(0, 16).replace('T', ' ') : '-' },
        { name: 'tipo_venta', label: 'Tipo', field: 'tipo_venta', align: 'left' },
        { name: 'cliente_nombre', label: 'Cliente', field: 'cliente_nombre', align: 'left' },
        { name: 'monto', label: 'Monto', field: 'monto', align: 'right' },
        { name: 'metodo', label: 'Metodo', field: 'metodo', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' },
        { name: 'usuario', label: 'Usuario', field: 'usuario', align: 'left' }
      ],
      colsMovimientos: [
        { name: 'id', label: 'Id', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha/Hora', field: 'created_at', align: 'left' },
        { name: 'monto_real', label: 'Monto', field: 'monto_real', align: 'right' },
        { name: 'observacion', label: 'Glosa / Observacion', field: 'observacion', align: 'left' },
        { name: 'usuario', label: 'Usuario', field: 'usuario', align: 'left' }
      ]
    }
  },
  mounted () {
    this.cargar()
  },
  methods: {
    money (n) { return Number(n || 0).toFixed(2) },
    cargar () {
      this.loading = true
      const dateTo = this.filters.one_day ? this.filters.date_from : this.filters.date_to
      this.$axios.get('cajas/resumen-gastos', { params: { date_from: this.filters.date_from, date_to: dateTo } })
        .then(r => {
          this.ventas = r.data.ventas || []
          this.movimientos = r.data.movimientos || []
          this.pagos = r.data.pagos || []
          this.totales = r.data.totales || { total_ventas: 0, total_pagado: 0, total_saldo: 0, total_gastos: 0 }
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar el resumen'))
        .finally(() => { this.loading = false })
    },
    imprimir () {
      const fecha = this.filters.one_day
        ? `Fecha: ${this.filters.date_from}`
        : `Desde: ${this.filters.date_from} Hasta: ${this.filters.one_day ? this.filters.date_from : this.filters.date_to}`

      const filasVentas = this.ventas.map(v => `
        <tr>
          <td>${v.id}</td>
          <td>${v.fecha || ''}</td>
          <td>${v.tipo_venta === 'local' ? 'LOCAL' : 'DETALLE'}</td>
          <td>${v.cliente_nombre || '-'}</td>
          <td style="text-align:right">${this.money(v.total)}</td>
          <td style="text-align:right">${this.money(v.total_pagado)}</td>
          <td style="text-align:right">${this.money(v.saldo_pendiente)}</td>
          <td>${v.user?.name || '-'}</td>
        </tr>`).join('')

      const filasPagos = this.pagos.map(p => `
        <tr>
          <td>${p.id}</td>
          <td>${p.created_at ? String(p.created_at).slice(0, 16).replace('T', ' ') : ''}</td>
          <td>${p.tipo_venta === 'local' ? 'LOCAL' : 'DETALLE'}</td>
          <td>${p.cliente_nombre || '-'}</td>
          <td style="text-align:right; color:green"><b>${this.money(p.monto)}</b></td>
          <td>${p.metodo || '-'}</td>
          <td>${p.observacion || '-'}</td>
          <td>${p.usuario || '-'}</td>
        </tr>`).join('')

      const filasGastos = this.movimientos.map(m => `
        <tr>
          <td>${m.id}</td>
          <td>${m.created_at ? String(m.created_at).slice(0, 16).replace('T', ' ') : ''}</td>
          <td style="text-align:right; color:red">${this.money(m.monto_real)}</td>
          <td>${m.observacion || '-'}</td>
          <td>${m.usuario || '-'}</td>
        </tr>`).join('')

      const html = `
        <div style="font-family:Arial,sans-serif; font-size:12px; padding:10px;">
          <h2 style="text-align:center; margin-bottom:4px;">RESUMEN DE GASTOS Y VENTAS</h2>
          <p style="text-align:center; margin:0;">${fecha}</p>
          <hr/>
          <h3>VENTAS DETALLE Y LOCAL</h3>
          <table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11px;">
            <thead style="background:#eee;">
              <tr>
                <th>Id</th><th>Fecha</th><th>Tipo</th><th>Cliente</th>
                <th>Total</th><th>A cuenta</th><th>Saldo</th><th>Usuario</th>
              </tr>
            </thead>
            <tbody>${filasVentas || '<tr><td colspan="8" style="text-align:center">Sin ventas</td></tr>'}</tbody>
          </table>
          <br/>
          <h3>PAGOS / COBROS DEL PERIODO</h3>
          <table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11px;">
            <thead style="background:#eee;">
              <tr><th>Id</th><th>Fecha/Hora</th><th>Tipo</th><th>Cliente</th><th>Monto</th><th>Metodo</th><th>Observacion</th><th>Usuario</th></tr>
            </thead>
            <tbody>${filasPagos || '<tr><td colspan="8" style="text-align:center">Sin pagos</td></tr>'}</tbody>
          </table>
          <br/>
          <h3>DETALLE DE GASTOS / EGRESOS DE CAJA</h3>
          <table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11px;">
            <thead style="background:#eee;">
              <tr><th>Id</th><th>Fecha/Hora</th><th>Monto</th><th>Observacion</th><th>Usuario</th></tr>
            </thead>
            <tbody>${filasGastos || '<tr><td colspan="5" style="text-align:center">Sin gastos</td></tr>'}</tbody>
          </table>
          <br/>
          <table border="0" cellpadding="4" width="300" style="font-size:13px; float:right;">
            <tr><td><b>Total ventas:</b></td><td style="text-align:right"><b>${this.money(this.totales.total_ventas)} Bs</b></td></tr>
            <tr><td><b>Total ingresado:</b></td><td style="text-align:right"><b>${this.money(this.totales.total_pagado)} Bs</b></td></tr>
            <tr><td><b>Total gastos:</b></td><td style="text-align:right; color:red"><b>${this.money(this.totales.total_gastos)} Bs</b></td></tr>
            <tr style="border-top:2px solid #333;"><td><b>Efectivo neto:</b></td><td style="text-align:right; color:teal"><b>${this.money(this.totales.total_pagado - this.totales.total_gastos)} Bs</b></td></tr>
          </table>
        </div>`

      document.getElementById('myElement').innerHTML = html
      Imprimir.printTicketHtml(html)
    }
  }
}
</script>
