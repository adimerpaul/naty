<template>
  <q-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)">
    <q-card style="width: 720px; max-width: 96vw;">
      <q-card-section class="row items-center q-pb-none">
        <div>
          <div class="text-subtitle1 text-weight-bold">Hoja de ruta #{{ venta?.id || row?.id }}</div>
          <div class="text-caption text-grey-7">{{ venta?.cliente_nombre || row?.cliente_nombre || '-' }}</div>
        </div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>
      <q-card-section>
        <q-form @submit.prevent="guardarEImprimir">
          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-4">
              <q-input v-model="form.hoja_fecha_entrega" type="date" dense outlined label="Fecha entrega" />
            </div>
            <div class="col-12 col-md-4">
              <q-select
                v-model="form.hoja_turno"
                dense
                outlined
                emit-value
                map-options
                :options="turnoOptions"
                label="Turno"
              />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="form.hoja_hora" type="text" dense outlined label="Hora" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.hoja_telefono_1" dense outlined label="Telefono 1" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.hoja_telefono_2" dense outlined label="Telefono 2" />
            </div>
            <div class="col-12">
              <q-input v-model="form.hoja_direccion" dense outlined label="Direccion" />
            </div>
            <div class="col-12">
              <q-input v-model="form.hoja_envases" dense outlined type="textarea" autogrow label="Envases" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model.number="form.hoja_cuenta" type="number" min="0" step="0.01" dense outlined label="A cuenta" suffix="Bs" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model.number="form.hoja_saldo" type="number" min="0" step="0.01" dense outlined label="Saldo" suffix="Bs" />
            </div>
            <div class="col-12">
              <q-input v-model="form.hoja_observaciones" dense outlined type="textarea" autogrow label="Observaciones" />
            </div>
          </div>
          <div class="row justify-end q-gutter-sm q-mt-md">
            <q-btn flat no-caps label="Cancelar" v-close-popup />
            <q-btn color="primary" no-caps icon="print" label="Guardar e imprimir" type="submit" :loading="loading" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'

export default {
  name: 'HojaRutaDialog',
  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    row: {
      type: Object,
      default: null
    }
  },
  emits: ['update:modelValue', 'saved'],
  data () {
    return {
      loading: false,
      venta: null,
      turnoOptions: [
        { label: 'Mañana', value: 'mañana' },
        { label: 'Tarde', value: 'tarde' },
        { label: 'Noche', value: 'noche' }
      ],
      form: this.emptyForm()
    }
  },
  watch: {
    modelValue (value) {
      if (value) this.cargar()
    }
  },
  methods: {
    emptyForm () {
      return {
        hoja_fecha_entrega: '',
        hoja_turno: 'mañana',
        hoja_hora: '',
        hoja_telefono_1: '',
        hoja_telefono_2: '',
        hoja_direccion: '',
        hoja_envases: '',
        hoja_cuenta: 0,
        hoja_saldo: 0,
        hoja_observaciones: ''
      }
    },
    async cargar () {
      if (!this.row?.id) return
      this.loading = true
      try {
        const res = await this.$axios.get(`ventas/${this.row.id}`)
        this.venta = res.data
        this.form = this.buildForm(this.venta)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar la hoja de ruta')
        this.$emit('update:modelValue', false)
      } finally {
        this.loading = false
      }
    },
    buildForm (venta) {
      const fecha = venta.hoja_fecha_entrega || venta.fecha || new Date().toISOString().slice(0, 10)
      const hora = venta.hoja_hora ? String(venta.hoja_hora).slice(0, 5) : (venta.hora ? String(venta.hora).slice(0, 5) : '')
      return {
        hoja_fecha_entrega: String(fecha).slice(0, 10),
        hoja_turno: venta.hoja_turno || 'mañana',
        hoja_hora: hora,
        hoja_telefono_1: venta.hoja_telefono_1 || venta.cliente_telefono || '',
        hoja_telefono_2: venta.hoja_telefono_2 || '',
        hoja_direccion: venta.hoja_direccion || venta.cliente_direccion || '',
        hoja_envases: venta.hoja_envases || this.envasesDefault(venta),
        hoja_cuenta: Number(venta.hoja_cuenta ?? venta.total_pagado ?? 0),
        hoja_saldo: Number(venta.hoja_saldo ?? venta.saldo_pendiente ?? venta.total ?? 0),
        hoja_observaciones: venta.hoja_observaciones || venta.observacion || ''
      }
    },
    envasesDefault (venta) {
      return (venta.prestamos || [])
        .map(p => `${p.cantidad || 0} ${p.inventario?.nombre || 'material'}`)
        .join(', ')
    },
    async guardarEImprimir () {
      if (!this.venta?.id) return
      this.loading = true
      try {
        const res = await this.$axios.put(`ventas/${this.venta.id}/hoja-ruta`, this.form)
        this.venta = res.data
        this.form = this.buildForm(this.venta)
        this.$emit('saved', this.venta)
        this.$emit('update:modelValue', false)
        Imprimir.hojaRuta(this.venta)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar la hoja de ruta')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
