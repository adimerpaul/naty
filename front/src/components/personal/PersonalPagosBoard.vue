<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <div class="text-h6">Pagos e historial de personal</div>
          <div class="text-caption text-grey-7">Formato consolidado para registrar adelantos, extras, descuentos y pago mensual</div>
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
          <q-td key="caja" :props="props" style="min-width: 140px;">
            <q-select
              v-model="formFor(props.row.id).caja_id"
              dense
              outlined
              emit-value
              map-options
              :options="cajaOptions"
              label="Caja"
              :disable="savingId === props.row.id"
            />
          </q-td>
          <q-td key="fecha_pago" :props="props" style="min-width: 128px;">
            <q-input
              v-model="formFor(props.row.id).fecha_pago"
              dense
              outlined
              type="date"
              :disable="savingId === props.row.id"
            />
          </q-td>
          <q-td key="adelanto_input" :props="props" style="min-width: 132px;">
            <q-input
              v-model.number="formFor(props.row.id).adelanto"
              dense
              outlined
              type="number"
              min="0"
              step="0.01"
              label="Adelanto"
              :disable="savingId === props.row.id"
            />
          </q-td>
          <q-td key="extra_input" :props="props" style="min-width: 120px;">
            <q-input
              v-model.number="formFor(props.row.id).extra"
              dense
              outlined
              type="number"
              min="0"
              step="0.01"
              label="Extra"
              :disable="savingId === props.row.id"
            />
          </q-td>
          <q-td key="descuento_input" :props="props" style="min-width: 138px;">
            <q-input
              v-model.number="formFor(props.row.id).descuento"
              dense
              outlined
              type="number"
              min="0"
              step="0.01"
              label="Descuento"
              :disable="savingId === props.row.id"
            />
          </q-td>
          <q-td key="observacion" :props="props" style="min-width: 180px;">
            <q-input
              v-model="formFor(props.row.id).observacion"
              dense
              outlined
              label="Observacion"
              :disable="savingId === props.row.id"
            />
          </q-td>
          <q-td key="actions" :props="props" style="min-width: 252px;">
            <div class="row q-col-gutter-xs">
              <div class="col-auto">
                <q-btn
                  dense
                  no-caps
                  color="orange"
                  label="+ Adel."
                  :loading="savingId === props.row.id && savingTipo === 'adelanto'"
                  @click="registrarMovimiento(props.row, 'adelanto')"
                />
              </div>
              <div class="col-auto">
                <q-btn
                  dense
                  no-caps
                  color="positive"
                  label="+ Extra"
                  :loading="savingId === props.row.id && savingTipo === 'extra'"
                  @click="registrarMovimiento(props.row, 'extra')"
                />
              </div>
              <div class="col-auto">
                <q-btn
                  dense
                  no-caps
                  color="negative"
                  label="+ Desc."
                  :loading="savingId === props.row.id && savingTipo === 'descuento'"
                  @click="registrarMovimiento(props.row, 'descuento')"
                />
              </div>
              <div class="col-auto">
                <q-btn
                  dense
                  no-caps
                  color="primary"
                  label="Pagar"
                  :loading="savingId === props.row.id && savingTipo === 'salario'"
                  @click="registrarSalario(props.row)"
                />
              </div>
              <div class="col-auto">
                <q-btn dense flat no-caps color="dark" icon="history" label="Historial" @click="seleccionarPersonal(props.row)" />
              </div>
            </div>
          </q-td>
        </q-tr>
      </template>
    </q-table>

    <q-card flat bordered class="q-mt-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-5">
          <div class="text-subtitle1 text-weight-bold">
            Historial del mes
            <span v-if="selectedPersonal">- {{ selectedPersonal.nombre }}</span>
          </div>
          <div class="text-caption text-grey-7">Se muestra en la misma pantalla para seguir el formato del sistema anterior</div>
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
                  <q-item
                    clickable
                    v-close-popup
                    :disable="props.row.tipo_registro !== 'salario'"
                    @click="descargarBoleta(props.row)"
                  >
                    <q-item-section avatar><q-icon name="picture_as_pdf" color="negative" /></q-item-section>
                    <q-item-section>Boleta PDF</q-item-section>
                  </q-item>
                  <q-item
                    clickable
                    v-close-popup
                    :disable="props.row.estado === 'ANULADO'"
                    @click="anularPago(props.row)"
                  >
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
      savingId: null,
      savingTipo: null,
      personales: [],
      cajas: [],
      resumenRows: [],
      historyRows: [],
      selectedPersonalId: null,
      quickForms: {},
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
        { name: 'caja', label: 'Caja', align: 'left' },
        { name: 'fecha_pago', label: 'Fecha', align: 'left' },
        { name: 'adelanto_input', label: 'Registrar adelanto', align: 'left' },
        { name: 'extra_input', label: 'Registrar extra', align: 'left' },
        { name: 'descuento_input', label: 'Registrar descuento', align: 'left' },
        { name: 'observacion', label: 'Observacion', align: 'left' },
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
    }
  },
  watch: {
    'filters.mes' () {
      this.refreshBoard()
    }
  },
  async mounted () {
    await this.loadRefs()
    await this.refreshBoard()
  },
  methods: {
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
    defaultQuickForm () {
      return {
        caja_id: this.cajas[0]?.id || null,
        fecha_pago: today,
        adelanto: null,
        extra: null,
        descuento: null,
        observacion: ''
      }
    },
    formFor (personalId) {
      if (!this.quickForms[personalId]) {
        this.quickForms[personalId] = this.defaultQuickForm()
      }
      return this.quickForms[personalId]
    },
    resetQuickField (personalId, tipo) {
      const form = this.formFor(personalId)
      form[tipo] = null
    },
    async loadRefs () {
      const [personalesRes, cajasRes] = await Promise.all([
        this.$axios.get('personales'),
        this.$axios.get('cajas')
      ])
      this.personales = personalesRes.data || []
      this.cajas = cajasRes.data || []
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
    async registrarMovimiento (row, tipo) {
      const form = this.formFor(row.id)
      const monto = Number(form[tipo] || 0)
      if (monto <= 0) {
        this.$alert.error(`Ingrese un monto valido para ${tipo}`)
        return
      }
      const payload = {
        personal_id: row.id,
        mes: this.filters.mes,
        tipo_registro: tipo,
        monto,
        observacion: form.observacion || null,
        fecha_pago: form.fecha_pago || today,
        caja_id: tipo === 'adelanto' ? form.caja_id : null
      }

      this.savingId = row.id
      this.savingTipo = tipo
      try {
        await this.$axios.post('personal-pagos', payload)
        this.$alert.success('Registro guardado')
        this.resetQuickField(row.id, tipo)
        await this.seleccionarPersonal(row)
        await this.refreshBoard()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar el movimiento')
      } finally {
        this.savingId = null
        this.savingTipo = null
      }
    },
    async registrarSalario (row) {
      const form = this.formFor(row.id)
      if (!form.caja_id) {
        this.$alert.error('Seleccione una caja para pagar salario')
        return
      }
      this.savingId = row.id
      this.savingTipo = 'salario'
      try {
        await this.$axios.post('personal-pagos', {
          personal_id: row.id,
          caja_id: form.caja_id,
          mes: this.filters.mes,
          tipo_registro: 'salario',
          sueldo: Number(row.salario || 0),
          observacion: form.observacion || null,
          fecha_pago: form.fecha_pago || today
        })
        this.$alert.success('Pago de salario registrado')
        await this.seleccionarPersonal(row)
        await this.refreshBoard()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar salario')
      } finally {
        this.savingId = null
        this.savingTipo = null
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
          if (this.selectedPersonalId) {
            await this.loadHistory(this.selectedPersonalId)
          }
          await this.refreshBoard()
        } catch (e) {
          this.$alert.error(e.response?.data?.message || 'No se pudo anular el pago')
        }
      })
    }
  }
}
</script>
