<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <div class="text-h6">Pagos de personal</div>
          <div class="text-caption text-grey-7">Registro y control por mes</div>
        </div>
        <div class="col-12 col-md-3">
          <q-input v-model="filters.mes" type="month" dense outlined label="Mes" />
        </div>
        <div class="col-12 col-md-3">
          <q-select v-model="filters.personal_id" dense outlined emit-value map-options clearable :options="personalOptions" label="Personal" />
        </div>
        <div class="col-12 col-md-2 text-right">
          <q-btn color="primary" no-caps icon="refresh" label="Buscar" @click="getData" :loading="loading" />
        </div>
      </q-card-section>
    </q-card>

    <q-table
      dense
      flat
      bordered
      :rows="rows"
      :columns="columns"
      row-key="id"
      :pagination="{ rowsPerPage: 100 }"
      :rows-per-page-options="[50, 100]"
      :loading="loading"
    >
      <template #top-right>
        <q-btn color="positive" no-caps icon="add" label="Registrar pago" @click="nuevo" />
      </template>
      <template #body-cell-estado="props">
        <q-td :props="props">
          <q-chip dense :color="props.row.estado === 'ANULADO' ? 'negative' : 'positive'" text-color="white">{{ props.row.estado }}</q-chip>
        </q-td>
      </template>
      <template #body-cell-tipo_registro="props">
        <q-td :props="props">
          <q-chip dense :color="tipoColor(props.row.tipo_registro)" text-color="white">{{ props.row.tipo_registro }}</q-chip>
        </q-td>
      </template>
      <template #body-cell-monto_pagado="props"><q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.monto_pagado) }}</q-td></template>
      <template #body-cell-actions="props">
        <q-td :props="props">
          <q-btn-dropdown dense color="primary" label="Opciones" no-caps size="10px">
            <q-list dense>
              <q-item clickable v-close-popup :disable="props.row.tipo_registro !== 'salario'" @click="descargarBoleta(props.row)">
                <q-item-section avatar><q-icon name="picture_as_pdf" color="negative" /></q-item-section>
                <q-item-section>Boleta PDF</q-item-section>
              </q-item>
              <q-item clickable v-close-popup :disable="props.row.estado === 'ANULADO'" @click="anular(props.row)">
                <q-item-section avatar><q-icon name="block" color="negative" /></q-item-section>
                <q-item-section>Anular</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialog">
      <q-card style="width: 640px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Registrar pago</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardar">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-6"><q-select v-model="form.personal_id" dense outlined emit-value map-options :options="personalOptions" label="Personal" :rules="[req]" /></div>
              <div class="col-12 col-md-3"><q-input v-model="form.mes" type="month" dense outlined label="Mes" :rules="[req]" /></div>
              <div class="col-12 col-md-3" v-if="['salario', 'adelanto'].includes(form.tipo_registro)">
                <q-select v-model="form.caja_id" dense outlined emit-value map-options :options="cajaOptions" label="Caja" :rules="[req]" />
              </div>
              <div class="col-12 col-md-3" v-else>
                <q-input dense outlined readonly model-value="No aplica" label="Caja" />
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  v-model="form.tipo_registro"
                  dense outlined emit-value map-options label="Tipo"
                  :options="[
                    { label: 'Pago salario', value: 'salario' },
                    { label: 'Extra', value: 'extra' },
                    { label: 'Adelanto', value: 'adelanto' },
                    { label: 'Descuento', value: 'descuento' }
                  ]"
                />
              </div>
              <div class="col-12 col-md-4" v-if="form.tipo_registro === 'salario'">
                <q-input v-model.number="form.sueldo" dense outlined type="number" min="0" step="0.01" label="Sueldo base" />
              </div>
              <div class="col-12 col-md-4" v-if="form.tipo_registro !== 'salario'">
                <q-input v-model.number="form.monto" dense outlined type="number" min="0.01" step="0.01" label="Monto" :rules="[req]" />
              </div>
              <div class="col-12 col-md-4">
                <q-banner dense class="bg-blue-1 text-blue-9">Total estimado: {{ money(totalEstimado) }}</q-banner>
              </div>
              <div class="col-12">
                <div class="row q-col-gutter-sm">
                  <div class="col-6 col-md-3"><q-banner dense class="bg-grey-2 text-black">Salario: {{ money(resumenPreview.sueldo) }}</q-banner></div>
                  <div class="col-6 col-md-3"><q-banner dense class="bg-green-1 text-green-9">Extras: {{ money(resumenPreview.extras) }}</q-banner></div>
                  <div class="col-6 col-md-3"><q-banner dense class="bg-orange-1 text-orange-9">Adelantos: {{ money(resumenPreview.adelantos) }}</q-banner></div>
                  <div class="col-6 col-md-3"><q-banner dense class="bg-red-1 text-red-9">Descuentos: {{ money(resumenPreview.descuentos) }}</q-banner></div>
                </div>
              </div>
              <div class="col-12">
                <q-banner dense class="bg-blue-1 text-blue-9 text-weight-bold">
                  Total final a pagar salario (mes {{ form.mes }}): {{ money(resumenPreview.total) }}
                </q-banner>
              </div>
              <div class="col-12"><q-input v-model="form.observacion" dense outlined label="Observacion" /></div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar" type="submit" :loading="loadingSave" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'PagosPersonalPage',
  data () {
    return {
      loading: false,
      loadingSave: false,
      dialog: false,
      rows: [],
      personales: [],
      cajas: [],
      filters: {
        mes: new Date().toISOString().slice(0, 7),
        personal_id: null
      },
      form: {
        personal_id: null,
        caja_id: 1,
        mes: new Date().toISOString().slice(0, 7),
        tipo_registro: 'salario',
        sueldo: 0,
        monto: null,
        observacion: ''
      },
      resumenBase: {
        sueldo: 0,
        extras: 0,
        adelantos: 0,
        descuentos: 0,
        total: 0
      },
      columns: [
        { name: 'actions', label: '', field: 'id', align: 'left' },
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'mes', label: 'Mes', field: 'mes', align: 'left' },
        { name: 'personal', label: 'Personal', field: row => row.personal?.nombre, align: 'left' },
        { name: 'tipo_registro', label: 'Tipo', field: 'tipo_registro', align: 'left' },
        { name: 'monto_pagado', label: 'Monto', field: 'monto_pagado', align: 'right' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'fecha_pago', label: 'Fecha', field: 'fecha_pago', align: 'left' }
      ]
    }
  },
  computed: {
    personalOptions () {
      return this.personales.map(p => ({ label: `${p.nombre} (${p.ci})`, value: p.id, salario: Number(p.salario || 0) }))
    },
    cajaOptions () {
      return this.cajas.map(c => ({ label: c.nombre, value: c.id }))
    },
    totalEstimado () {
      if (this.form.tipo_registro === 'salario') return Number(this.form.sueldo || 0)
      return Number(this.form.monto || 0)
    },
    resumenPreview () {
      const base = { ...this.resumenBase }
      const tipo = this.form.tipo_registro
      const monto = Number(this.form.monto || 0)
      const sueldoDraft = Number(this.form.sueldo || 0)

      if (tipo === 'salario') {
        base.sueldo = sueldoDraft
      } else if (tipo === 'extra') {
        base.extras += monto
      } else if (tipo === 'adelanto') {
        base.adelantos += monto
      } else if (tipo === 'descuento') {
        base.descuentos += monto
      }
      base.total = Math.max(0, base.sueldo + base.extras - base.adelantos - base.descuentos)
      return base
    }
  },
  watch: {
    'form.personal_id' (v) {
      const p = this.personales.find(x => x.id === v)
      if (p && this.form.tipo_registro === 'salario') this.form.sueldo = Number(p.salario || 0)
      this.cargarResumenPrevio()
    },
    'form.tipo_registro' (v) {
      if (['extra', 'descuento'].includes(v)) {
        this.form.caja_id = null
      } else if (!this.form.caja_id) {
        this.form.caja_id = 1
      }
      this.cargarResumenPrevio()
    },
    'form.mes' () {
      this.cargarResumenPrevio()
    },
    dialog (v) {
      if (v) this.cargarResumenPrevio()
    }
  },
  mounted () {
    this.loadRefs()
    this.getData()
  },
  methods: {
    req (v) { return !!v || 'Campo requerido' },
    money (n) { return Number(n || 0).toFixed(2) + ' Bs' },
    tipoColor (t) {
      if (t === 'salario') return 'primary'
      if (t === 'extra') return 'positive'
      if (t === 'adelanto') return 'orange'
      if (t === 'descuento') return 'negative'
      return 'grey-7'
    },
    async loadRefs () {
      const [p, c] = await Promise.all([this.$axios.get('personales'), this.$axios.get('cajas')])
      this.personales = p.data || []
      this.cajas = c.data || []
    },
    async getData () {
      this.loading = true
      try {
        const r = await this.$axios.get('personal-pagos', { params: this.filters })
        this.rows = r.data || []
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar pagos')
      } finally {
        this.loading = false
      }
    },
    nuevo () {
      this.form = {
        personal_id: null,
        caja_id: 1,
        mes: this.filters.mes || new Date().toISOString().slice(0, 7),
        tipo_registro: 'salario',
        sueldo: 0,
        monto: null,
        observacion: ''
      }
      this.resumenBase = { sueldo: 0, extras: 0, adelantos: 0, descuentos: 0, total: 0 }
      this.dialog = true
    },
    async cargarResumenPrevio () {
      if (!this.dialog || !this.form.personal_id || !this.form.mes) return
      const p = this.personales.find(x => x.id === this.form.personal_id)
      const sueldoBase = Number(this.form.sueldo || p?.salario || 0)
      try {
        const r = await this.$axios.get('personal-pagos/resumen-mensual', {
          params: { mes: this.form.mes, personal_id: this.form.personal_id }
        })
        const row = (r.data || [])[0]
        this.resumenBase = {
          sueldo: Number(row?.sueldo || sueldoBase),
          extras: Number(row?.extras || 0),
          adelantos: Number(row?.adelantos || 0),
          descuentos: Number(row?.descuentos || 0),
          total: Number(row?.total_calculado || Math.max(0, sueldoBase))
        }
      } catch {
        this.resumenBase = { sueldo: sueldoBase, extras: 0, adelantos: 0, descuentos: 0, total: Math.max(0, sueldoBase) }
      }
    },
    async guardar () {
      this.loadingSave = true
      try {
        await this.$axios.post('personal-pagos', this.form)
        this.$alert.success('Pago registrado')
        this.dialog = false
        this.getData()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar pago')
      } finally {
        this.loadingSave = false
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
        this.$alert.error(e.response?.data?.message || 'No se pudo descargar PDF')
      }
    },
    anular (row) {
      this.$alert.dialog('Desea anular este pago?').onOk(async () => {
        try {
          await this.$axios.post(`personal-pagos/${row.id}/anular`)
          this.$alert.success('Pago anulado')
          this.getData()
        } catch (e) {
          this.$alert.error(e.response?.data?.message || 'No se pudo anular')
        }
      })
    }
  }
}
</script>
