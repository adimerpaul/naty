<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <div class="text-h6">Cumpleaños</div>
          <div class="text-caption text-grey-7">Control de cumpleaños de personal y clientes</div>
        </div>
        <div class="col-12 col-md-2">
          <q-input v-model="filters.month" type="number" min="1" max="12" dense outlined label="Mes (1-12)" />
        </div>
        <div class="col-12 col-md-2">
          <q-input v-model.number="filters.include_past_days" type="number" min="0" dense outlined label="Pasados (días)" />
        </div>
        <div class="col-12 col-md-2">
          <q-input v-model.number="filters.include_next_days" type="number" min="0" dense outlined label="Próximos (días)" />
        </div>
        <div class="col-12 col-md-2 text-right">
          <q-btn color="primary" no-caps icon="refresh" label="Actualizar" :loading="loading" @click="getData" />
        </div>
      </q-card-section>
    </q-card>

    <q-table
      dense
      flat
      bordered
      :rows="rows"
      :columns="columns"
      row-key="row_key"
      :filter="search"
      :loading="loading"
      :pagination="{ rowsPerPage: 100 }"
      :rows-per-page-options="[50, 100]"
    >
      <template #top-right>
        <q-input v-model="search" dense outlined debounce="250" label="Buscar" style="width: 280px">
          <template #append><q-icon name="search" /></template>
        </q-input>
      </template>

      <template #body-cell-actions="props">
        <q-td :props="props" class="text-left">
          <q-btn-dropdown dense color="primary" label="Opciones" no-caps size="10px">
            <q-list dense>
              <q-item clickable v-close-popup @click="verHistorial(props.row)">
                <q-item-section avatar><q-icon name="history" color="primary" /></q-item-section>
                <q-item-section>Ver historial</q-item-section>
              </q-item>
              <q-item clickable v-close-popup :disable="!props.row.whatsapp_url" @click="abrirWhatsapp(props.row)">
                <q-item-section avatar><q-icon name="phone" color="positive" /></q-item-section>
                <q-item-section>Enviar WhatsApp</q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>

      <template #body-cell-origen="props">
        <q-td :props="props">
          <q-chip dense :color="props.row.origen === 'personal' ? 'indigo' : 'teal'" text-color="white">
            {{ props.row.origen === 'personal' ? 'Personal' : `Cliente ${props.row.tipo_cliente || ''}` }}
          </q-chip>
        </q-td>
      </template>

      <template #body-cell-dias_cumple="props">
        <q-td :props="props">
          <q-chip dense :color="colorCumple(props.row)" text-color="white">
            {{ labelDias(props.row.dias_cumple) }}
          </q-chip>
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialogHistorial">
      <q-card style="width: 980px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            Historial - {{ rowSel?.nombre || '' }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-table
            v-if="rowSel?.origen === 'cliente'"
            dense
            flat
            bordered
            :rows="historialRows"
            :columns="colsVentas"
            row-key="id"
            :pagination="{ rowsPerPage: 100 }"
          >
            <template #body-cell-created_at="props"><q-td :props="props">{{ normalDate(props.row.created_at) }}</q-td></template>
            <template #body-cell-total="props"><q-td :props="props" class="text-right">{{ money(props.row.total) }}</q-td></template>
            <template #body-cell-total_pagado="props"><q-td :props="props" class="text-right">{{ money(props.row.total_pagado) }}</q-td></template>
            <template #body-cell-saldo_pendiente="props"><q-td :props="props" class="text-right">{{ money(props.row.saldo_pendiente) }}</q-td></template>
          </q-table>

          <q-table
            v-else
            dense
            flat
            bordered
            :rows="historialRows"
            :columns="colsPagosPersonal"
            row-key="id"
            :pagination="{ rowsPerPage: 100 }"
          >
            <template #body-cell-monto_pagado="props"><q-td :props="props" class="text-right">{{ money(props.row.monto_pagado) }}</q-td></template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'CumpleaniosPage',
  data () {
    return {
      loading: false,
      rows: [],
      search: '',
      dialogHistorial: false,
      rowSel: null,
      historialRows: [],
      filters: {
        month: null,
        include_past_days: 30,
        include_next_days: 60
      },
      columns: [
        { name: 'actions', label: '', field: 'id', align: 'left' },
        { name: 'origen', label: 'Tipo', field: 'origen', align: 'left' },
        { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left' },
        { name: 'ci', label: 'CI', field: 'ci', align: 'left' },
        { name: 'telefono', label: 'Telefono', field: 'telefono', align: 'left' },
        { name: 'fechanac', label: 'Fecha nac.', field: 'fechanac', align: 'left' },
        { name: 'dias_cumple', label: 'Estado', field: 'dias_cumple', align: 'left' }
      ],
      colsVentas: [
        { name: 'id', label: 'Venta', field: 'id', align: 'left' },
        { name: 'created_at', label: 'Fecha', field: 'created_at', align: 'left' },
        { name: 'tipo_venta', label: 'Tipo', field: 'tipo_venta', align: 'left' },
        { name: 'tipo_pago', label: 'Pago', field: 'tipo_pago', align: 'left' },
        { name: 'total', label: 'Total', field: 'total', align: 'right' },
        { name: 'total_pagado', label: 'Pagado', field: 'total_pagado', align: 'right' },
        { name: 'saldo_pendiente', label: 'Deuda', field: 'saldo_pendiente', align: 'right' }
      ],
      colsPagosPersonal: [
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'mes', label: 'Mes', field: 'mes', align: 'left' },
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
    normalDate (value) {
      if (!value) return '-'
      return String(value).replace('T', ' ').replace('.000000Z', '').replace('.000Z', '')
    },
    rowKey (r) {
      return `${r.origen}-${r.id}`
    },
    labelDias (dias) {
      const d = Number(dias || 0)
      if (d === 0) return 'Hoy es su cumpleaños'
      if (d < 0) return `Ya pasó hace ${Math.abs(d)} día(s)`
      return `Falta ${d} día(s)`
    },
    colorCumple (row) {
      if (row.estado_cumple === 'hoy') return 'positive'
      if (row.estado_cumple === 'pasado') return 'negative'
      return 'primary'
    },
    async getData () {
      this.loading = true
      try {
        const r = await this.$axios.get('cumpleanios', { params: this.filters })
        this.rows = (r.data || []).map(x => ({ ...x, row_key: this.rowKey(x) }))
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar cumpleaños')
      } finally {
        this.loading = false
      }
    },
    abrirWhatsapp (row) {
      if (!row.whatsapp_url) {
        this.$alert.error('No tiene número de WhatsApp')
        return
      }
      window.open(row.whatsapp_url, '_blank')
    },
    async verHistorial (row) {
      this.rowSel = row
      try {
        const r = await this.$axios.get(`cumpleanios/${row.origen}/${row.id}/historial`)
        this.historialRows = r.data?.historial || []
        this.dialogHistorial = true
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar historial')
      }
    }
  }
}
</script>
