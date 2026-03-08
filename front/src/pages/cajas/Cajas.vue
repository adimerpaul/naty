<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-5">
          <div class="text-h6">Cajas</div>
          <div class="text-caption text-grey-7">Caja general y caja chica con control mensual y diario</div>
        </div>
        <div class="col-12 col-md-3">
          <q-input v-model="month" type="month" dense outlined label="Mes" @update:model-value="onMonthChange" />
        </div>
        <div class="col-12 col-md-4 text-right">
          <q-btn color="primary" no-caps icon="refresh" label="Actualizar" @click="loadAll" :loading="loading" />
        </div>
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-md-6" v-for="c in cajas" :key="c.id">
        <q-card flat :class="c.id === 1 ? 'card-general' : 'card-chica'">
          <q-card-section class="row items-center">
            <div>
              <div class="text-subtitle1 text-weight-bold">{{ c.nombre }}</div>
              <div class="text-caption">{{ c.descripcion }}</div>
            </div>
            <q-space />
            <q-btn
              :color="selectedCajaId === c.id ? 'white' : 'grey-2'"
              :text-color="selectedCajaId === c.id ? (c.id === 1 ? 'negative' : 'primary') : 'black'"
              dense
              no-caps
              label="Ver detalle"
              @click="selectCaja(c.id)"
            />
          </q-card-section>
          <q-separator color="white" />
          <q-card-section>
            <div class="row">
              <div class="col-4">
                <div class="text-caption">Ingresos mes</div>
                <div class="text-h6 text-weight-bold">{{ money(c.ingresos_mes) }}</div>
              </div>
              <div class="col-4">
                <div class="text-caption">Egresos mes</div>
                <div class="text-h6 text-weight-bold">{{ money(c.egresos_mes) }}</div>
              </div>
              <div class="col-4">
                <div class="text-caption">Saldo actual</div>
                <div class="text-h6 text-weight-bold">{{ money(c.saldo_actual) }}</div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-card flat bordered class="q-mb-md" :class="selectedCajaId === 1 ? 'card-general' : 'card-chica'">
      <q-card-section class="row items-center q-gutter-sm">
        <div class="text-subtitle1 text-weight-bold">{{ selectedCaja?.nombre || 'Caja' }}</div>
        <q-space />
        <q-btn color="positive" no-caps icon="add" label="Agregar monto" @click="openManual('ingreso')" />
        <q-btn color="negative" no-caps icon="remove" label="Quitar monto" @click="openManual('egreso')" />
        <q-btn color="primary" no-caps icon="swap_horiz" label="Transferir entre cajas" @click="openTransfer" />
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md">
      <div class="col-12 col-lg-5">
        <q-card flat bordered>
          <q-card-section class="row items-center" :class="selectedCajaId === 1 ? 'panel-red-header' : 'panel-blue-header'">
            <div class="text-subtitle2 text-weight-bold">Resumen por dia</div>
            <q-space />
            <q-chip dense outline color="primary">{{ month }}</q-chip>
          </q-card-section>
          <q-separator />
          <q-table
            dense
            flat
            bordered
            :rows="diario"
            :columns="colsDiario"
            row-key="fecha"
            :pagination="{ rowsPerPage: 100 }"
            :rows-per-page-options="[50, 100]"
          >
            <template #body-cell-fecha="props">
              <q-td :props="props">
                <q-btn flat dense no-caps color="primary" :label="props.row.fecha" @click="filtrarFecha(props.row.fecha)" />
              </q-td>
            </template>
            <template #body-cell-ingresos="props"><q-td :props="props" class="text-right">{{ money(props.row.ingresos) }}</q-td></template>
            <template #body-cell-egresos="props"><q-td :props="props" class="text-right">{{ money(props.row.egresos) }}</q-td></template>
            <template #body-cell-neto="props"><q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.neto) }}</q-td></template>
          </q-table>
        </q-card>
      </div>

      <div class="col-12 col-lg-7">
        <q-card flat bordered>
          <q-card-section class="row items-center" :class="selectedCajaId === 1 ? 'panel-red-header' : 'panel-blue-header'">
            <div class="text-subtitle2 text-weight-bold">Movimientos</div>
            <q-space />
            <q-chip v-if="selectedDate" dense color="primary" text-color="white">Fecha: {{ selectedDate }}</q-chip>
            <q-chip v-else dense color="primary" text-color="white">Mes completo</q-chip>
            <q-btn v-if="selectedDate" flat dense no-caps icon="close" label="Quitar filtro" @click="filtrarFecha(null)" />
          </q-card-section>
          <q-separator />
          <q-table
            dense
            flat
            bordered
            :rows="movimientos"
            :columns="colsMov"
            row-key="id"
            :pagination="{ rowsPerPage: 100 }"
            :rows-per-page-options="[50, 100]"
          >
            <template #body-cell-actions="props">
              <q-td :props="props" class="text-left">
                <q-btn-dropdown dense color="primary" label="Opciones" no-caps size="10px">
                  <q-list dense>
                    <q-item clickable v-close-popup @click="imprimirMovimiento(props.row)">
                      <q-item-section avatar><q-icon name="print" color="primary" /></q-item-section>
                      <q-item-section>Imprimir</q-item-section>
                    </q-item>
                    <q-item clickable v-close-popup :disable="props.row.tipo_venta !== 'caja' || props.row.estado !== 'ACTIVA'" @click="anularMovimiento(props.row)">
                      <q-item-section avatar><q-icon name="block" color="negative" /></q-item-section>
                      <q-item-section>Anular</q-item-section>
                    </q-item>
                  </q-list>
                </q-btn-dropdown>
              </q-td>
            </template>
            <template #body-cell-created_at="props"><q-td :props="props">{{ formatDate(props.row.created_at) }}</q-td></template>
            <template #body-cell-tipo_movimiento="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.tipo_movimiento === 'egreso' ? 'negative' : 'positive'" text-color="white">
                  {{ props.row.tipo_movimiento }}
                </q-chip>
              </q-td>
            </template>
            <template #body-cell-estado="props">
              <q-td :props="props">
                <q-chip dense :color="props.row.estado === 'ACTIVA' ? 'positive' : 'negative'" text-color="white">
                  {{ props.row.estado }}
                </q-chip>
              </q-td>
            </template>
            <template #body-cell-monto_real="props"><q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.monto_real) }}</q-td></template>
          </q-table>
          <q-separator />
          <q-card-section class="row q-col-gutter-sm">
            <div class="col-4"><q-banner dense class="bg-green-1 text-green-9">Ingresos: {{ money(totales.ingresos) }}</q-banner></div>
            <div class="col-4"><q-banner dense class="bg-red-1 text-red-9">Egresos: {{ money(totales.egresos) }}</q-banner></div>
            <div class="col-4"><q-banner dense class="bg-blue-1 text-blue-9">Saldo: {{ money(totales.saldo) }}</q-banner></div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-dialog v-model="dialogMov">
      <q-card style="width: 560px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">{{ movForm.modo === 'transferencia' ? 'Transferir entre cajas' : (movForm.tipo_movimiento === 'ingreso' ? 'Agregar monto' : 'Quitar monto') }}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="guardarMovimiento">
            <div class="row q-col-gutter-sm">
              <template v-if="movForm.modo === 'transferencia'">
                <div class="col-12 col-md-6">
                  <q-select v-model="movForm.origen_caja_id" dense outlined emit-value map-options label="Caja origen" :options="cajaOptions" :rules="[req]" />
                </div>
                <div class="col-12 col-md-6">
                  <q-select v-model="movForm.destino_caja_id" dense outlined emit-value map-options label="Caja destino" :options="cajaOptions" :rules="[req]" />
                </div>
              </template>
              <template v-else>
                <div class="col-12 col-md-6">
                  <q-select v-model="movForm.caja_id" dense outlined emit-value map-options label="Caja" :options="cajaOptions" :rules="[req]" />
                </div>
                <div class="col-12 col-md-6">
                  <q-input :model-value="movForm.tipo_movimiento" dense outlined readonly label="Tipo" />
                </div>
              </template>
              <div class="col-12 col-md-6">
                <q-input v-model.number="movForm.monto" type="number" min="1" step="0.01" dense outlined label="Monto" :rules="[req]" />
              </div>
              <div class="col-12">
                <q-input v-model="movForm.observacion" dense outlined type="textarea" autogrow label="Observacion" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar" type="submit" :loading="loadingMov" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
  <div id="myElement" ></div>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'

export default {
  name: 'CajasPage',
  data () {
    return {
      loading: false,
      loadingMov: false,
      month: new Date().toISOString().slice(0, 7),
      cajas: [],
      selectedCajaId: 1,
      selectedDate: null,
      diario: [],
      movimientos: [],
      totales: { ingresos: 0, egresos: 0, saldo: 0 },
      dialogMov: false,
      movForm: {
        modo: 'manual',
        tipo_movimiento: 'ingreso',
        caja_id: null,
        origen_caja_id: null,
        destino_caja_id: null,
        monto: null,
        observacion: ''
      },
      colsDiario: [
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'ingresos', label: 'Ingresos', field: 'ingresos', align: 'right' },
        { name: 'egresos', label: 'Egresos', field: 'egresos', align: 'right' },
        { name: 'neto', label: 'Neto', field: 'neto', align: 'right' }
      ],
      colsMov: [
        { name: 'actions', label: '', field: 'id', align: 'left' },
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha', field: 'created_at', align: 'left' },
        { name: 'tipo_movimiento', label: 'Tipo', field: 'tipo_movimiento', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' },
        { name: 'tipo_venta', label: 'Origen', field: 'tipo_venta', align: 'left' },
        { name: 'usuario', label: 'Usuario', field: 'usuario', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' },
        { name: 'monto_real', label: 'Monto', field: 'monto_real', align: 'right' }
      ]
    }
  },
  computed: {
    selectedCaja () {
      return this.cajas.find(c => c.id === this.selectedCajaId) || null
    },
    cajaOptions () {
      return this.cajas.map(c => ({ label: c.nombre, value: c.id }))
    }
  },
  mounted () {
    this.loadAll()
  },
  methods: {
    req (v) { return !!v || 'Campo requerido' },
    money (n) { return Number(n || 0).toFixed(2) + ' Bs' },
    formatDate (v) {
      if (!v) return '-'
      const d = new Date(v)
      if (Number.isNaN(d.getTime())) return v
      const dd = String(d.getDate()).padStart(2, '0')
      const mm = String(d.getMonth() + 1).padStart(2, '0')
      const yy = d.getFullYear()
      const hh = String(d.getHours()).padStart(2, '0')
      const mi = String(d.getMinutes()).padStart(2, '0')
      return `${dd}/${mm}/${yy} ${hh}:${mi}`
    },
    onMonthChange () {
      this.selectedDate = null
      this.loadAll()
    },
    async loadAll () {
      this.loading = true
      try {
        await this.loadResumen()
        await this.loadMovimientos()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar cajas')
      } finally {
        this.loading = false
      }
    },
    async loadResumen () {
      const r = await this.$axios.get('cajas/resumen', { params: { month: this.month } })
      this.cajas = r.data?.cajas || []
      if (!this.cajas.find(c => c.id === this.selectedCajaId) && this.cajas.length) {
        this.selectedCajaId = this.cajas[0].id
      }
    },
    async loadMovimientos () {
      if (!this.selectedCajaId) return
      const r = await this.$axios.get(`cajas/${this.selectedCajaId}/movimientos`, {
        params: { month: this.month, date: this.selectedDate || undefined }
      })
      this.diario = r.data?.diario || []
      this.movimientos = r.data?.movimientos || []
      this.totales = r.data?.totales || { ingresos: 0, egresos: 0, saldo: 0 }
    },
    async selectCaja (id) {
      this.selectedCajaId = id
      this.selectedDate = null
      await this.loadMovimientos()
    },
    async filtrarFecha (fecha) {
      this.selectedDate = fecha
      await this.loadMovimientos()
    },
    openManual (tipo) {
      this.movForm = {
        modo: 'manual',
        tipo_movimiento: tipo,
        caja_id: this.selectedCajaId,
        origen_caja_id: null,
        destino_caja_id: null,
        monto: null,
        observacion: ''
      }
      this.dialogMov = true
    },
    openTransfer () {
      const origen = this.selectedCajaId || 1
      const destino = origen === 1 ? 2 : 1
      this.movForm = {
        modo: 'transferencia',
        tipo_movimiento: 'egreso',
        caja_id: null,
        origen_caja_id: origen,
        destino_caja_id: destino,
        monto: null,
        observacion: ''
      }
      this.dialogMov = true
    },
    async guardarMovimiento () {
      if (this.movForm.modo === 'transferencia' && this.movForm.origen_caja_id === this.movForm.destino_caja_id) {
        this.$alert.error('Origen y destino no pueden ser iguales')
        return
      }
      this.loadingMov = true
      try {
        await this.$axios.post('cajas/movimientos', this.movForm)
        this.$alert.success('Movimiento registrado')
        this.dialogMov = false
        await this.loadAll()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar movimiento')
      } finally {
        this.loadingMov = false
      }
    },
    anularMovimiento (row) {
      this.$alert.dialog('Desea anular este movimiento?').onOk(async () => {
        try {
          await this.$axios.post(`ventas/${row.id}/anular`)
          this.$alert.success('Movimiento anulado')
          await this.loadAll()
        } catch (e) {
          this.$alert.error(e.response?.data?.message || 'No se pudo anular')
        }
      })
    },
    imprimirMovimiento (row) {
      Imprimir.movimientoCaja(row, this.selectedCaja?.nombre || 'Caja')
    }
  }
}
</script>

<style scoped>
.card-general {
  background: #b71c1c;
  color: #fff;
}
.card-chica {
  background: #0d47a1;
  color: #fff;
}
.panel-red-header {
  background: #b71c1c;
  color: #fff;
}
.panel-blue-header {
  background: #0d47a1;
  color: #fff;
}
</style>
