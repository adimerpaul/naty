<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <div class="text-h6">Pagos e historial de personal</div>
          <div class="text-caption text-grey-7">Lista densa del personal activo con registro en dialogo</div>
        </div>
        <div class="col-12 col-md-2">
          <q-input v-model="filters.mes" type="month" dense outlined label="Mes" />
        </div>
        <div class="col-12 col-md-3">
          <q-input v-model="filters.search" dense outlined label="Buscar personal" debounce="250">
            <template #append><q-icon name="search" /></template>
          </q-input>
        </div>
        <div class="col-12 col-md-3 text-right">
          <q-btn color="primary" no-caps icon="refresh" label="Actualizar" :loading="loading" @click="refreshBoard" />
          <q-btn class="q-ml-sm" color="positive" no-caps icon="add" label="Agregar pago" @click="abrirDialogPago()" />
        </div>
      </q-card-section>
    </q-card>

    <q-table
      dense
      flat
      bordered
      :rows="filteredRows"
      :columns="columns"
      row-key="id"
      :loading="loading"
      :pagination="{ rowsPerPage: 0 }"
      :rows-per-page-options="[0]"
      no-data-label="No hay personal activo"
    >
      <template #body="props">
        <q-tr :props="props" :class="selectedPersonalId === props.row.id ? 'bg-blue-1' : ''">
          <q-td key="personal" :props="props">
            <div class="row items-center no-wrap q-gutter-sm">
              <q-avatar size="34px" rounded color="grey-3" text-color="grey-8">
                <q-img v-if="props.row.fotografia" :src="imgPersonal(props.row.fotografia)" />
                <q-icon v-else name="person" />
              </q-avatar>
              <div style="min-width: 0;">
                <div class="text-weight-medium ellipsis">{{ props.row.nombre }}</div>
                <div class="text-caption text-grey-7">CI {{ props.row.ci || '-' }} | {{ props.row.tipo || '-' }}</div>
              </div>
            </div>
          </q-td>
          <q-td key="salario" :props="props" class="text-right text-weight-bold">{{ money(props.row.salario) }}</q-td>
          <q-td key="adelantos" :props="props" class="text-right text-orange">{{ money(props.row.adelantos) }}</q-td>
          <q-td key="extras" :props="props" class="text-right text-positive">{{ money(props.row.extras) }}</q-td>
          <q-td key="descuentos" :props="props" class="text-right text-negative">{{ money(props.row.descuentos) }}</q-td>
          <q-td key="total" :props="props" class="text-right text-primary text-weight-bold">{{ money(props.row.total_calculado) }}</q-td>
          <q-td key="actions" :props="props" class="text-left" style="min-width: 170px;">
            <q-btn dense flat no-caps color="dark" icon="visibility" label="Ver abajo" @click="seleccionarPersonal(props.row)" />
            <q-btn dense flat no-caps color="primary" icon="add" label="Registrar" class="q-ml-xs" @click="abrirDialogPago(props.row)" />
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <q-card flat bordered class="q-mt-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-5">
          <div class="text-subtitle1 text-weight-bold">
            Registro e historial
            <span v-if="selectedPersonal">- {{ selectedPersonal.nombre }}</span>
          </div>
          <div class="text-caption text-grey-7">El detalle del registro se mantiene abajo y el guardado sale desde dialogo</div>
        </div>
        <div class="col-12 col-md-7">
          <div v-if="selectedSummary" class="row q-col-gutter-sm">
            <div class="col-6 col-md-3"><q-banner dense class="bg-orange-1 text-orange-9">Adelantos: {{ money(selectedSummary.adelantos) }}</q-banner></div>
            <div class="col-6 col-md-3"><q-banner dense class="bg-green-1 text-green-9">Extras: {{ money(selectedSummary.extras) }}</q-banner></div>
            <div class="col-6 col-md-3"><q-banner dense class="bg-red-1 text-red-9">Descuentos: {{ money(selectedSummary.descuentos) }}</q-banner></div>
            <div class="col-6 col-md-3"><q-banner dense class="bg-blue-1 text-blue-9">Total: {{ money(selectedSummary.total_calculado) }}</q-banner></div>
          </div>
        </div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <div v-if="selectedPersonal" class="row q-col-gutter-sm q-mb-md">
          <div class="col-12 col-md-2">
            <q-input dense outlined label="Caja" :model-value="dialogCajaLabel" readonly />
          </div>
          <div class="col-12 col-md-2">
            <q-input dense outlined label="Fecha" :model-value="dialogForm.fecha_pago || '-'" readonly />
          </div>
          <div class="col-12 col-md-2">
            <q-input dense outlined label="Registro" :model-value="dialogRegistroLabel" readonly />
          </div>
          <div class="col-12 col-md-2">
            <q-input dense outlined label="Adelanto" :model-value="dialogForm.tipo_registro === 'adelanto' ? money(dialogForm.monto) : '-'" readonly />
          </div>
          <div class="col-12 col-md-2">
            <q-input dense outlined label="Registro extra" :model-value="dialogForm.tipo_registro === 'extra' ? money(dialogForm.monto) : '-'" readonly />
          </div>
          <div class="col-12 col-md-2">
            <q-input dense outlined label="Registro descuento" :model-value="dialogForm.tipo_registro === 'descuento' ? money(dialogForm.monto) : '-'" readonly />
          </div>
        </div>

        <div v-if="!selectedPersonal" class="text-grey-7">Seleccione un personal para ver y gestionar su historial del mes.</div>
        <q-table
          v-else
          dense
          flat
          bordered
          :rows="historyRows"
          :columns="historyColumns"
          row-key="id"
          :loading="loadingHistory"
          :pagination="{ rowsPerPage: 100 }"
        >
          <template #body-cell-tipo_registro="props">
            <q-td :props="props">
              <q-chip dense :color="tipoColor(props.row.tipo_registro)" text-color="white">{{ props.row.tipo_registro }}</q-chip>
            </q-td>
          </template>
          <template #body-cell-estado="props">
            <q-td :props="props">
              <q-chip dense :color="props.row.estado === 'ANULADO' ? 'negative' : 'positive'" text-color="white">{{ props.row.estado }}</q-chip>
            </q-td>
          </template>
          <template #body-cell-monto_pagado="props">
            <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.monto_pagado) }}</q-td>
          </template>
          <template #body-cell-actions="props">
            <q-td :props="props">
              <q-btn-dropdown dense color="primary" label="Opciones" no-caps size="10px">
                <q-list dense>
                  <q-item clickable v-close-popup @click="imprimirPago(props.row)">
                    <q-item-section avatar><q-icon name="print" color="primary" /></q-item-section>
                    <q-item-section>Imprimir</q-item-section>
                  </q-item>
                  <q-item clickable v-close-popup :disable="props.row.tipo_registro !== 'salario'" @click="descargarBoleta(props.row)">
                    <q-item-section avatar><q-icon name="picture_as_pdf" color="negative" /></q-item-section>
                    <q-item-section>Boleta PDF</q-item-section>
                  </q-item>
                  <q-item clickable v-close-popup :disable="props.row.estado === 'ANULADO'" @click="anularPago(props.row)">
                    <q-item-section avatar><q-icon name="block" color="negative" /></q-item-section>
                    <q-item-section>Anular</q-item-section>
                  </q-item>
                </q-list>
              </q-btn-dropdown>
            </q-td>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-dialog v-model="dialogPago" persistent>
      <q-card style="width: 680px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Registrar pago</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarPago">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-6">
                <q-select
                  v-model="dialogForm.personal_id"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="personalOptions"
                  label="Personal"
                  :rules="[req]"
                />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="dialogForm.fecha_pago" dense outlined type="date" label="Fecha" :rules="[req]" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model="dialogForm.mes" dense outlined type="month" label="Mes" :rules="[req]" />
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  v-model="dialogForm.tipo_registro"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="tipoRegistroOptions"
                  label="Registro"
                  :rules="[req]"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  v-model="dialogForm.caja_id"
                  dense
                  outlined
                  emit-value
                  map-options
                  :options="cajaOptions"
                  label="Caja"
                  :disable="!requiereCaja"
                />
              </div>
              <div class="col-12 col-md-4">
                <q-input
                  v-model.number="dialogForm.monto"
                  dense
                  outlined
                  type="number"
                  min="0"
                  step="0.01"
                  :label="dialogMontoLabel"
                  :rules="[reqMonto]"
                />
              </div>
              <div class="col-12">
                <q-input v-model="dialogForm.observacion" dense outlined label="Observacion" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar" type="submit" :loading="savingDialog" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'

const currentMonth = new Date().toISOString().slice(0, 7)
const today = new Date().toISOString().slice(0, 10)

export default {
  name: 'PersonalPagosBoard',
  data () {
    return {
      loading: false,
      loadingHistory: false,
      savingDialog: false,
      personales: [],
      cajas: [],
      resumenRows: [],
      historyRows: [],
      selectedPersonalId: null,
      dialogPago: false,
      dialogForm: {
        personal_id: null,
        mes: currentMonth,
        fecha_pago: today,
        tipo_registro: 'adelanto',
        caja_id: null,
        monto: null,
        observacion: ''
      },
      filters: {
        mes: currentMonth,
        search: ''
      },
      columns: [
        { name: 'personal', label: 'Personal', align: 'left' },
        { name: 'salario', label: 'Salario', align: 'right' },
        { name: 'adelantos', label: 'Adelantos', align: 'right' },
        { name: 'extras', label: 'Extras', align: 'right' },
        { name: 'descuentos', label: 'Descuentos', align: 'right' },
        { name: 'total', label: 'Total mes', align: 'right' },
        { name: 'actions', label: 'Acciones', align: 'left' }
      ],
      historyColumns: [
        { name: 'actions', label: '', field: 'id', align: 'left' },
        { name: 'fecha_pago', label: 'Fecha', field: 'fecha_pago', align: 'left' },
        { name: 'tipo_registro', label: 'Tipo', field: 'tipo_registro', align: 'left' },
        { name: 'monto_pagado', label: 'Monto', field: 'monto_pagado', align: 'right' },
        { name: 'caja', label: 'Caja', field: row => row.caja?.nombre || '-', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' }
      ]
    }
  },
  computed: {
    cajaOptions () {
      return this.cajas.map(c => ({ label: c.nombre, value: c.id }))
    },
    personalOptions () {
      return this.activePersonales.map(p => ({ label: `${p.nombre} (${p.ci || '-'})`, value: p.id }))
    },
    tipoRegistroOptions () {
      return [
        { label: 'Adelanto', value: 'adelanto' },
        { label: 'Extra', value: 'extra' },
        { label: 'Descuento', value: 'descuento' },
        { label: 'Pago salario', value: 'salario' }
      ]
    },
    activePersonales () {
      return (this.personales || []).filter(p => p.estado === 'ACTIVO')
    },
    mergedRows () {
      const resumenById = new Map((this.resumenRows || []).map(r => [r.personal_id, r]))
      return this.activePersonales.map(personal => {
        const resumen = resumenById.get(personal.id) || {}
        return {
          ...personal,
          adelantos: Number(resumen.adelantos || 0),
          extras: Number(resumen.extras || 0),
          descuentos: Number(resumen.descuentos || 0),
          total_calculado: Number(resumen.total_calculado || personal.salario || 0)
        }
      })
    },
    filteredRows () {
      const term = String(this.filters.search || '').trim().toLowerCase()
      if (!term) return this.mergedRows
      return this.mergedRows.filter(row => {
        const full = `${row.nombre || ''} ${row.ci || ''} ${row.tipo || ''}`.toLowerCase()
        return full.includes(term)
      })
    },
    selectedPersonal () {
      return this.activePersonales.find(p => p.id === this.selectedPersonalId) || null
    },
    selectedSummary () {
      if (!this.selectedPersonalId) return null
      return this.mergedRows.find(r => r.id === this.selectedPersonalId) || null
    },
    requiereCaja () {
      return ['adelanto', 'salario'].includes(this.dialogForm.tipo_registro)
    },
    dialogMontoLabel () {
      if (this.dialogForm.tipo_registro === 'adelanto') return 'Registro adelanto'
      if (this.dialogForm.tipo_registro === 'extra') return 'Registro extra'
      if (this.dialogForm.tipo_registro === 'descuento') return 'Registro descuento'
      return 'Pago salario'
    },
    dialogRegistroLabel () {
      const opt = this.tipoRegistroOptions.find(t => t.value === this.dialogForm.tipo_registro)
      return opt?.label || '-'
    },
    dialogCajaLabel () {
      const opt = this.cajaOptions.find(c => c.value === this.dialogForm.caja_id)
      return opt?.label || '-'
    }
  },
  watch: {
    'filters.mes' () {
      this.refreshBoard()
    },
    'dialogForm.tipo_registro' (value) {
      if (!this.requiereCaja) {
        this.dialogForm.caja_id = null
      } else if (!this.dialogForm.caja_id) {
        this.dialogForm.caja_id = this.cajas[0]?.id || null
      }
      if (value === 'salario' && this.selectedPersonal) {
        this.dialogForm.monto = Number(this.selectedPersonal.salario || 0)
      }
    }
  },
  async mounted () {
    await this.loadRefs()
    await this.refreshBoard()
  },
  methods: {
    req (v) {
      return !!v || 'Campo requerido'
    },
    reqMonto (v) {
      return Number(v || 0) > 0 || 'Ingrese un monto valido'
    },
    money (n) {
      return Number(n || 0).toFixed(2) + ' Bs'
    },
    imgPersonal (foto) {
      return `${this.$url}../../images/personales/${foto}`
    },
    tipoColor (tipo) {
      if (tipo === 'salario') return 'primary'
      if (tipo === 'extra') return 'positive'
      if (tipo === 'adelanto') return 'orange'
      if (tipo === 'descuento') return 'negative'
      return 'grey-7'
    },
    async loadRefs () {
      const [personalesRes, cajasRes] = await Promise.all([
        this.$axios.get('personales'),
        this.$axios.get('cajas')
      ])
      this.personales = personalesRes.data || []
      this.cajas = cajasRes.data || []
      if (!this.dialogForm.caja_id) this.dialogForm.caja_id = this.cajas[0]?.id || null
    },
    async refreshBoard () {
      this.loading = true
      try {
        const resumenRes = await this.$axios.get('personal-pagos/resumen-mensual', {
          params: { mes: this.filters.mes }
        })
        this.resumenRows = resumenRes.data || []
        if (!this.selectedPersonalId && this.filteredRows.length) {
          this.selectedPersonalId = this.filteredRows[0].id
        }
        if (this.selectedPersonalId) {
          await this.loadHistory(this.selectedPersonalId)
        }
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar resumen de pagos')
      } finally {
        this.loading = false
      }
    },
    async seleccionarPersonal (row) {
      this.selectedPersonalId = row.id
      await this.loadHistory(row.id)
    },
    abrirDialogPago (row = null) {
      const target = row || this.selectedPersonal || this.filteredRows[0] || null
      if (!target) {
        this.$alert.error('No hay personal activo para registrar pago')
        return
      }
      this.selectedPersonalId = target.id
      this.dialogForm = {
        personal_id: target.id,
        mes: this.filters.mes,
        fecha_pago: today,
        tipo_registro: 'adelanto',
        caja_id: this.cajas[0]?.id || null,
        monto: null,
        observacion: ''
      }
      this.dialogPago = true
      this.loadHistory(target.id)
    },
    async loadHistory (personalId) {
      this.loadingHistory = true
      try {
        const historyRes = await this.$axios.get('personal-pagos', {
          params: {
            mes: this.filters.mes,
            personal_id: personalId
          }
        })
        this.historyRows = historyRes.data || []
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar historial del personal')
      } finally {
        this.loadingHistory = false
      }
    },
    async guardarPago () {
      if (this.requiereCaja && !this.dialogForm.caja_id) {
        this.$alert.error('Seleccione una caja')
        return
      }
      this.savingDialog = true
      try {
        const personal = this.activePersonales.find(p => p.id === this.dialogForm.personal_id)
        const payload = {
          personal_id: this.dialogForm.personal_id,
          mes: this.dialogForm.mes,
          fecha_pago: this.dialogForm.fecha_pago,
          tipo_registro: this.dialogForm.tipo_registro,
          caja_id: this.requiereCaja ? this.dialogForm.caja_id : null,
          observacion: this.dialogForm.observacion || null
        }

        if (this.dialogForm.tipo_registro === 'salario') {
          payload.sueldo = Number(personal?.salario || this.dialogForm.monto || 0)
        } else {
          payload.monto = Number(this.dialogForm.monto || 0)
        }

        await this.$axios.post('personal-pagos', payload)
        this.$alert.success('Registro guardado')
        this.dialogPago = false
        this.selectedPersonalId = this.dialogForm.personal_id
        await this.refreshBoard()
        await this.loadHistory(this.dialogForm.personal_id)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar el pago')
      } finally {
        this.savingDialog = false
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
        return
      }
      Imprimir.pagoPersonalMovimiento(row)
    },
    anularPago (row) {
      this.$alert.dialog('Desea anular este pago?').onOk(async () => {
        try {
          await this.$axios.post(`personal-pagos/${row.id}/anular`)
          this.$alert.success('Pago anulado')
          if (this.selectedPersonalId) await this.loadHistory(this.selectedPersonalId)
          await this.refreshBoard()
        } catch (e) {
          this.$alert.error(e.response?.data?.message || 'No se pudo anular el pago')
        }
      })
    }
  }
}
</script>
