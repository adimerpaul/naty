<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <div>
          <div class="text-h6">{{ titulo }}</div>
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
      <div class="col-12 col-md-3"><q-input v-model="filters.search" dense outlined label="Buscar cliente / telefono / venta" @keyup.enter="cargar" /></div>
      <div class="col-12 col-md-2">
        <q-select
          v-model="filters.sort_deuda"
          dense
          outlined
          emit-value
          map-options
          :options="[
            { label: 'Debe mas', value: 'desc' },
            { label: 'Debe menos', value: 'asc' }
          ]"
          label="Orden deuda"
        />
      </div>
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
      <template #body-cell-actions="props">
        <q-td :props="props" class="text-left">
          <q-btn-dropdown dense color="primary" label="Opciones" no-caps>
            <q-list dense>
              <q-item clickable v-close-popup @click="ver(props.row)">
                <q-item-section avatar><q-icon name="visibility" color="primary" /></q-item-section>
                <q-item-section>Ver deuda</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="abrirAmortizar(props.row)">
                <q-item-section avatar><q-icon name="payments" color="positive" /></q-item-section>
                <q-item-section>Amortizar</q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="ocultar(props.row)">
                <q-item-section avatar><q-icon name="visibility_off" color="grey-8" /></q-item-section>
                <q-item-section>Ocultar</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
      <template #body-cell-created_at="props"><q-td :props="props">{{ formatDate(props.row.created_at) }}</q-td></template>
      <template #body-cell-total_pagado="props"><q-td :props="props" class="text-right">{{ money(props.row.total_pagado) }}</q-td></template>
      <template #body-cell-saldo_pendiente="props"><q-td :props="props" class="text-right text-weight-bold text-orange-8">{{ money(props.row.saldo_pendiente) }}</q-td></template>
    </q-table>

    <q-dialog v-model="dialogVer">
      <q-card style="width: 900px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Deuda venta #{{ rowSel?.id }}</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm q-mb-sm">
            <div class="col-6 col-md-3"><b>Cliente:</b> {{ rowSel?.cliente_nombre || '-' }}</div>
            <div class="col-6 col-md-3"><b>Telefono:</b> {{ rowSel?.cliente_telefono || '-' }}</div>
            <div class="col-6 col-md-3"><b>Pagado:</b> {{ money(rowSel?.total_pagado) }}</div>
            <div class="col-6 col-md-3"><b>Deuda:</b> {{ money(rowSel?.saldo_pendiente) }}</div>
          </div>
          <q-table dense flat bordered :rows="rowSel?.pagos || []" :columns="colsPagos" row-key="id" hide-pagination>
            <template #body-cell-estado="props">
              <q-td :props="props">
                <q-chip dense :color="chipColor(props.row.estado)" text-color="white">{{ props.row.estado }}</q-chip>
              </q-td>
            </template>
            <template #body-cell-actions="props">
              <q-td :props="props">
                <q-btn
                  dense
                  flat
                  color="negative"
                  icon="block"
                  label="Anular"
                  no-caps
                  :disable="props.row.estado !== 'PAGADO'"
                  @click="anularPago(props.row)"
                />
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogAmortizar">
      <q-card style="width: 520px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Amortizar deuda #{{ amortForm.id }}</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarAmortizar">
            <q-input v-model="amortForm.cliente" dense outlined readonly label="Cliente" class="q-mb-sm" />
            <q-input v-model.number="amortForm.monto" dense outlined type="number" min="0.01" step="0.01" label="Monto a amortizar" :rules="[req]" class="q-mb-sm" />
            <q-select
              v-model="amortForm.metodo"
              dense
              outlined
              emit-value
              map-options
              :options="[
                { label: 'Efectivo', value: 'efectivo' },
                { label: 'QR', value: 'qr' }
              ]"
              label="Metodo"
              class="q-mb-sm"
            />
            <q-input v-model="amortForm.observacion" dense outlined label="Observacion" class="q-mb-md" />
            <div class="row justify-end q-gutter-sm">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar amortizacion" type="submit" :loading="loadingAmort" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'DeudasPage',
  data () {
    const today = new Date().toISOString().slice(0, 10)
    return {
      loading: false,
      loadingAmort: false,
      dialogVer: false,
      dialogAmortizar: false,
      rows: [],
      rowSel: null,
      amortForm: { id: null, cliente: '', monto: null, metodo: 'efectivo', observacion: '' },
      pagination: { page: 1, rowsPerPage: 25, sortBy: 'saldo_pendiente', descending: true },
      filters: { date_from: today, date_to: today, search: '', sort_deuda: 'desc' },
      columns: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'id', label: 'Venta', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha', field: 'created_at', align: 'left' },
        { name: 'cliente_nombre', label: 'Cliente', field: 'cliente_nombre', align: 'left' },
        { name: 'cliente_telefono', label: 'Telefono', field: 'cliente_telefono', align: 'left' },
        { name: 'total_pagado', label: 'Pagado', field: 'total_pagado', align: 'right' },
        { name: 'saldo_pendiente', label: 'Deuda', field: 'saldo_pendiente', align: 'right' }
      ],
      colsPagos: [
        { name: 'nro_cuota', label: 'Cuota', field: 'nro_cuota', align: 'left' },
        { name: 'monto', label: 'Monto', field: 'monto', align: 'right' },
        { name: 'metodo', label: 'Metodo', field: 'metodo', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'fecha_pago', label: 'F. Pago', field: 'fecha_pago', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' },
        { name: 'actions', label: '', align: 'left' }
      ]
    }
  },
  computed: {
    tipoVenta () { return this.$route.params.tipo === 'local' ? 'local' : 'detalle' },
    titulo () { return this.tipoVenta === 'local' ? 'Deuda local' : 'Deuda detalle' },
    totalDeuda () { return this.rows.reduce((a, b) => a + Number(b.saldo_pendiente || 0), 0) }
  },
  watch: {
    '$route.params.tipo' () { this.cargar() }
  },
  mounted () {
    this.cargar()
  },
  methods: {
    req (v) { return !!v || 'Campo requerido' },
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
      this.$axios.get('ventas/deudas', { params: { ...this.filters, tipo_venta: this.tipoVenta } })
        .then(r => { this.rows = r.data || [] })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar deudas'))
        .finally(() => { this.loading = false })
    },
    chipColor (estado) {
      if (estado === 'PAGADO') return 'positive'
      if (estado === 'PENDIENTE') return 'orange-8'
      if (estado === 'ANULADO') return 'negative'
      return 'grey-7'
    },
    async ver (row) {
      const res = await this.$axios.get(`ventas/${row.id}`)
      this.rowSel = res.data
      this.dialogVer = true
    },
    anularPago (pago) {
      this.$alert.dialog(`Anular pago #${pago.id}?`)
        .onOk(async () => {
          await this.$axios.post(`ventas/${this.rowSel.id}/pagos/${pago.id}/anular`)
          const res = await this.$axios.get(`ventas/${this.rowSel.id}`)
          this.rowSel = res.data
          this.$alert.success('Pago anulado')
          this.cargar()
        })
    },
    abrirAmortizar (row) {
      this.amortForm = {
        id: row.id,
        cliente: row.cliente_nombre || '-',
        monto: Number(row.saldo_pendiente || 0),
        metodo: 'efectivo',
        observacion: ''
      }
      this.dialogAmortizar = true
    },
    async guardarAmortizar () {
      this.loadingAmort = true
      try {
        await this.$axios.post(`ventas/${this.amortForm.id}/amortizar`, {
          monto: this.amortForm.monto,
          metodo: this.amortForm.metodo,
          observacion: this.amortForm.observacion
        })
        this.$alert.success('Amortizacion registrada')
        this.dialogAmortizar = false
        this.cargar()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo amortizar')
      } finally {
        this.loadingAmort = false
      }
    },
    ocultar (row) {
      this.$alert.dialog('Ocultar esta deuda de la lista?')
        .onOk(async () => {
          await this.$axios.post(`ventas/${row.id}/ocultar-deuda`, { oculto: true })
          this.$alert.success('Deuda oculta')
          this.cargar()
        })
    }
  }
}
</script>
