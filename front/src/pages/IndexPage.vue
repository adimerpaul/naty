<template>
  <q-page class="dashboard-page q-pa-md">
    <q-card flat bordered class="q-mb-md header-card">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <div class="text-h5 text-weight-bold">Dashboard Naty</div>
          <div class="text-caption text-grey-7">Gestion de compras, ventas, pagos y cajas</div>
        </div>
        <div class="col-12 col-md-3">
          <q-input v-model="dateFrom" type="date" dense outlined label="Desde" />
        </div>
        <div class="col-12 col-md-3">
          <q-input v-model="dateTo" type="date" dense outlined label="Hasta" />
        </div>
        <div class="col-12 col-md-2 text-right">
          <q-btn color="primary" icon="refresh" no-caps label="Actualizar" :loading="loading" @click="loadAll" />
        </div>
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat class="metric-card bg-green-8 text-white">
          <q-card-section class="row items-center">
            <q-avatar color="white" text-color="green-8" icon="trending_up" />
            <div class="q-ml-md">
              <div class="text-caption">Ingresos activos</div>
              <div class="text-h6 text-weight-bold">{{ money(metrics.ingresosActivos) }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat class="metric-card bg-red-7 text-white">
          <q-card-section class="row items-center">
            <q-avatar color="white" text-color="red-7" icon="trending_down" />
            <div class="q-ml-md">
              <div class="text-caption">Egresos activos</div>
              <div class="text-h6 text-weight-bold">{{ money(metrics.egresosActivos) }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat class="metric-card bg-blue-8 text-white">
          <q-card-section class="row items-center">
            <q-avatar color="white" text-color="blue-8" icon="account_balance_wallet" />
            <div class="q-ml-md">
              <div class="text-caption">Saldo neto</div>
              <div class="text-h6 text-weight-bold">{{ money(metrics.saldoNeto) }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <q-card flat class="metric-card bg-indigo-8 text-white">
          <q-card-section class="row items-center">
            <q-avatar color="white" text-color="indigo-8" icon="point_of_sale" />
            <div class="q-ml-md">
              <div class="text-caption">Ventas activas</div>
              <div class="text-h6 text-weight-bold">{{ metrics.ventasActivas }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-4">
        <q-card flat bordered class="kpi-mini">
          <q-card-section>
            <div class="text-caption text-grey-7">Ventas anuladas</div>
            <div class="text-h6 text-weight-bold text-negative">{{ metrics.ventasAnuladas }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-4">
        <q-card flat bordered class="kpi-mini">
          <q-card-section>
            <div class="text-caption text-grey-7">Caja general</div>
            <div class="text-h6 text-weight-bold text-primary">{{ money(metrics.cajaGeneral) }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-4">
        <q-card flat bordered class="kpi-mini">
          <q-card-section>
            <div class="text-caption text-grey-7">Caja chica</div>
            <div class="text-h6 text-weight-bold text-blue-7">{{ money(metrics.cajaChica) }}</div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-lg-8">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Ingresos y egresos por dia</q-card-section>
          <q-separator />
          <q-card-section>
            <apexchart type="area" height="320" :options="seriesDiasOptions" :series="seriesDias" />
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-lg-4">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Metodos de pago</q-card-section>
          <q-separator />
          <q-card-section>
            <apexchart type="donut" height="320" :options="metodosOptions" :series="metodosSeries" />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-lg-6">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Top usuarios que mas vendieron</q-card-section>
          <q-separator />
          <q-card-section>
            <apexchart type="bar" height="320" :options="usuariosOptions" :series="usuariosSeries" />
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-lg-6">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Distribucion por tipo de pago</q-card-section>
          <q-separator />
          <q-card-section>
            <apexchart type="pie" height="320" :options="tipoPagoOptions" :series="tipoPagoSeries" />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-lg-7">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Productos mas vendidos</q-card-section>
          <q-separator />
          <q-card-section class="row q-col-gutter-sm">
            <div class="col-12 col-sm-6 col-md-4" v-for="p in topProductos" :key="p.nombre">
              <q-card flat bordered class="product-card">
                <q-img v-if="p.fotografia" :src="imgProducto(p.fotografia)" style="height: 110px" fit="cover" />
                <q-card-section>
                  <div class="text-subtitle2 ellipsis">{{ p.nombre }}</div>
                  <div class="text-caption text-grey-7">Cantidad: {{ fmtInt(p.cantidad) }}</div>
                  <div class="text-weight-bold text-primary">{{ money(p.monto) }}</div>
                </q-card-section>
              </q-card>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-lg-5">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Ultimos movimientos</q-card-section>
          <q-separator />
          <q-table
            dense
            flat
            bordered
            :rows="ultimosMovimientos"
            :columns="movColumns"
            row-key="id"
            :pagination="{ rowsPerPage: 8 }"
          >
            <template #body-cell-estado="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.estado === 'ACTIVA' ? 'positive' : 'negative'" text-color="white">{{ props.row.estado }}</q-chip>
              </q-td>
            </template>
            <template #body-cell-tipo_movimiento="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.tipo_movimiento === 'ingreso' ? 'green' : 'red'" text-color="white">{{ props.row.tipo_movimiento }}</q-chip>
              </q-td>
            </template>
            <template #body-cell-total="props"><q-td :props="props" class="text-right">{{ money(props.row.total) }}</q-td></template>
            <template #body-cell-created_at="props"><q-td :props="props">{{ fmtDate(props.row.created_at) }}</q-td></template>
          </q-table>
        </q-card>
      </div>
    </div>

    <div class="row q-col-gutter-md">
      <div class="col-12 col-md-4">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Clientes</q-card-section>
          <q-separator />
          <q-card-section>
            <div class="text-h6 text-weight-bold">{{ metrics.totalClientes }}</div>
            <div class="text-caption text-grey-7">Activos: {{ metrics.clientesActivos }} | Inactivos: {{ metrics.clientesInactivos }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Productos</q-card-section>
          <q-separator />
          <q-card-section>
            <div class="text-h6 text-weight-bold">{{ metrics.totalProductos }}</div>
            <div class="text-caption text-grey-7">Detalle: {{ metrics.productosDetalle }} | Local: {{ metrics.productosLocal }}</div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-md-4">
        <q-card flat bordered>
          <q-card-section class="text-subtitle1 text-weight-bold">Personal</q-card-section>
          <q-separator />
          <q-card-section>
            <div class="text-h6 text-weight-bold">{{ metrics.totalPersonal }}</div>
            <div class="text-caption text-grey-7">Activos: {{ metrics.personalActivo }}</div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'IndexPage',
  data () {
    const today = new Date()
    const y = today.getFullYear()
    const m = String(today.getMonth() + 1).padStart(2, '0')
    const d = String(today.getDate()).padStart(2, '0')
    return {
      loading: false,
      dateFrom: `${y}-${m}-01`,
      dateTo: `${y}-${m}-${d}`,
      ventas: [],
      cajas: [],
      clientes: [],
      productos: [],
      personales: [],
      topProductos: [],
      movColumns: [
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha', field: 'created_at', align: 'left' },
        { name: 'tipo_movimiento', label: 'Tipo', field: 'tipo_movimiento', align: 'left' },
        { name: 'tipo_venta', label: 'Origen', field: 'tipo_venta', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'total', label: 'Monto', field: 'total', align: 'right' }
      ]
    }
  },
  computed: {
    ventasActivas () {
      return (this.ventas || []).filter(v => v.estado === 'ACTIVA')
    },
    metrics () {
      const activas = this.ventasActivas
      const ingresosActivos = activas
        .filter(v => v.tipo_movimiento === 'ingreso')
        .reduce((acc, v) => acc + Number(v.total_pagado ?? v.total ?? 0), 0)
      const egresosActivos = activas
        .filter(v => v.tipo_movimiento === 'egreso')
        .reduce((acc, v) => acc + Number(v.total_pagado ?? v.total ?? 0), 0)

      const cajaGeneral = Number((this.cajas.find(c => c.id === 1) || {}).saldo_actual || 0)
      const cajaChica = Number((this.cajas.find(c => c.id === 2) || {}).saldo_actual || 0)

      const totalClientes = this.clientes.length
      const clientesActivos = this.clientes.filter(c => Number(c.estado) === 1).length
      const totalProductos = this.productos.length
      const productosDetalle = this.productos.filter(p => String(p.tipo || '').toLowerCase() === 'detalle').length
      const totalPersonal = this.personales.length
      const personalActivo = this.personales.filter(p => p.estado === 'ACTIVO').length

      return {
        ingresosActivos,
        egresosActivos,
        saldoNeto: ingresosActivos - egresosActivos,
        ventasActivas: activas.length,
        ventasAnuladas: this.ventas.filter(v => v.estado === 'ANULADA').length,
        cajaGeneral,
        cajaChica,
        totalClientes,
        clientesActivos,
        clientesInactivos: totalClientes - clientesActivos,
        totalProductos,
        productosDetalle,
        productosLocal: totalProductos - productosDetalle,
        totalPersonal,
        personalActivo
      }
    },
    ultimosMovimientos () {
      return [...this.ventas]
        .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
        .slice(0, 8)
    },
    seriesDias () {
      const map = {}
      const from = new Date(this.dateFrom)
      const to = new Date(this.dateTo)
      for (let d = new Date(from); d <= to; d.setDate(d.getDate() + 1)) {
        const k = d.toISOString().slice(0, 10)
        map[k] = { ingreso: 0, egreso: 0 }
      }
      this.ventasActivas.forEach(v => {
        const k = String(v.created_at || '').slice(0, 10)
        if (!map[k]) return
        const monto = Number(v.total_pagado ?? v.total ?? 0)
        if (v.tipo_movimiento === 'egreso') map[k].egreso += monto
        else map[k].ingreso += monto
      })
      const labels = Object.keys(map).sort()
      return [
        { name: 'Ingresos', data: labels.map(k => Number(map[k].ingreso.toFixed(2))) },
        { name: 'Egresos', data: labels.map(k => Number(map[k].egreso.toFixed(2))) }
      ]
    },
    seriesDiasOptions () {
      const labels = []
      const from = new Date(this.dateFrom)
      const to = new Date(this.dateTo)
      for (let d = new Date(from); d <= to; d.setDate(d.getDate() + 1)) labels.push(d.toISOString().slice(0, 10))
      return {
        chart: { toolbar: { show: false } },
        stroke: { curve: 'smooth', width: 3 },
        dataLabels: { enabled: false },
        xaxis: { categories: labels },
        legend: { position: 'top' },
        colors: ['#2e7d32', '#c62828'],
        tooltip: { y: { formatter: val => `${Number(val).toFixed(2)} Bs` } }
      }
    },
    metodosSeries () {
      let efectivo = 0
      let qr = 0
      this.ventasActivas.forEach(v => {
        ;(v.pagos || []).forEach(p => {
          if (p.estado !== 'PAGADO') return
          if (p.metodo === 'qr') qr += Number(p.monto || 0)
          else efectivo += Number(p.monto || 0)
        })
      })
      return [Number(efectivo.toFixed(2)), Number(qr.toFixed(2))]
    },
    metodosOptions () {
      return {
        labels: ['Efectivo', 'QR'],
        legend: { position: 'bottom' },
        colors: ['#1976d2', '#00897b']
      }
    },
    tipoPagoSeries () {
      const contado = this.ventasActivas.filter(v => v.tipo_pago === 'contado').length
      const credito = this.ventasActivas.filter(v => v.tipo_pago === 'credito').length
      return [contado, credito]
    },
    tipoPagoOptions () {
      return {
        labels: ['Contado', 'Credito'],
        legend: { position: 'bottom' },
        colors: ['#3949ab', '#fb8c00']
      }
    },
    usuariosSeries () {
      const map = {}
      this.ventasActivas.forEach(v => {
        const key = v.user?.name || v.user?.username || 'Sin usuario'
        map[key] = (map[key] || 0) + Number(v.total_pagado ?? v.total ?? 0)
      })
      const sorted = Object.entries(map)
        .map(([name, total]) => ({ name, total }))
        .sort((a, b) => b.total - a.total)
        .slice(0, 7)
      return [{ name: 'Monto', data: sorted.map(x => Number(x.total.toFixed(2))) }]
    },
    usuariosOptions () {
      const map = {}
      this.ventasActivas.forEach(v => {
        const key = v.user?.name || v.user?.username || 'Sin usuario'
        map[key] = (map[key] || 0) + Number(v.total_pagado ?? v.total ?? 0)
      })
      const sorted = Object.entries(map)
        .map(([name, total]) => ({ name, total }))
        .sort((a, b) => b.total - a.total)
        .slice(0, 7)
      return {
        chart: { toolbar: { show: false } },
        plotOptions: { bar: { borderRadius: 6, horizontal: true } },
        dataLabels: { enabled: false },
        xaxis: { categories: sorted.map(x => x.name) },
        colors: ['#1565c0'],
        tooltip: { y: { formatter: v => `${Number(v).toFixed(2)} Bs` } }
      }
    }
  },
  mounted () {
    this.loadAll()
  },
  methods: {
    async loadAll () {
      this.loading = true
      try {
        const month = String(this.dateFrom || '').slice(0, 7)
        const [ventasRes, cajasRes, clientesRes, productosRes, personalesRes] = await Promise.all([
          this.$axios.get('ventas', { params: { date_from: this.dateFrom, date_to: this.dateTo } }),
          this.$axios.get('cajas/resumen', { params: { month } }),
          this.$axios.get('clientes'),
          this.$axios.get('productos'),
          this.$axios.get('personales')
        ])

        this.ventas = ventasRes.data || []
        this.cajas = cajasRes.data?.cajas || []
        this.clientes = clientesRes.data || []
        this.productos = productosRes.data || []
        this.personales = personalesRes.data || []
        this.calcularTopProductos()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar el dashboard')
      } finally {
        this.loading = false
      }
    },
    calcularTopProductos () {
      const map = {}
      this.ventasActivas.forEach(v => {
        ;(v.detalles || []).forEach(d => {
          const nombre = d.producto_nombre || 'SIN NOMBRE'
          if (!map[nombre]) {
            map[nombre] = {
              nombre,
              cantidad: 0,
              monto: 0,
              fotografia: null
            }
          }
          map[nombre].cantidad += Number(d.cantidad || 0)
          map[nombre].monto += Number(d.subtotal || 0)
        })
      })
      const byName = Object.values(map).sort((a, b) => b.monto - a.monto).slice(0, 6)
      byName.forEach(row => {
        const prod = (this.productos || []).find(p => String(p.nombre || '').trim().toUpperCase() === String(row.nombre || '').trim().toUpperCase())
        row.fotografia = prod?.fotografia || null
      })
      this.topProductos = byName
    },
    imgProducto (foto) {
      return `${this.$url}../../images/productos/${foto}`
    },
    money (n) {
      return `${Number(n || 0).toFixed(2)} Bs`
    },
    fmtInt (n) {
      return Number(n || 0).toLocaleString('es-BO')
    },
    fmtDate (v) {
      if (!v) return '-'
      const d = new Date(v)
      if (Number.isNaN(d.getTime())) return v
      const dd = String(d.getDate()).padStart(2, '0')
      const mm = String(d.getMonth() + 1).padStart(2, '0')
      const yy = d.getFullYear()
      const hh = String(d.getHours()).padStart(2, '0')
      const mi = String(d.getMinutes()).padStart(2, '0')
      return `${dd}/${mm}/${yy} ${hh}:${mi}`
    }
  }
}
</script>

<style scoped>
.dashboard-page {
  background: linear-gradient(135deg, #f7f9fc 0%, #eef3f9 100%);
  min-height: 100%;
}
.header-card {
  border-color: #d9e2f2;
}
.metric-card {
  border-radius: 12px;
}
.kpi-mini {
  border-radius: 10px;
}
.product-card {
  border-radius: 10px;
}
</style>
