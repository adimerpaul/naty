<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-5">
          <div class="text-h6">Almacen</div>
          <div class="text-caption text-grey-7">Control de materiales, proveedores, compras, pagos y retiros</div>
        </div>
        <div class="col-12 col-md-7 text-right">
          <q-btn color="primary" no-caps icon="refresh" label="Actualizar" @click="loadAll" :loading="loading" />
        </div>
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-md-4">
        <q-banner rounded class="bg-blue-1 text-blue-9">
          Materiales: <span class="text-weight-bold">{{ materials.length }}</span>
        </q-banner>
      </div>
      <div class="col-12 col-md-4">
        <q-banner rounded class="bg-orange-1 text-orange-9">
          Compras con deuda: <span class="text-weight-bold">{{ comprasPendientes.length }}</span>
        </q-banner>
      </div>
      <div class="col-12 col-md-4">
        <q-banner rounded class="bg-red-1 text-red-9">
          Materiales bajo minimo: <span class="text-weight-bold">{{ materialsBajoMinimo.length }}</span>
        </q-banner>
      </div>
    </div>

    <q-card flat bordered>
      <q-tabs
        v-model="tab"
        dense
        align="left"
        active-color="white"
        indicator-color="transparent"
        class="tabs-almacen"
      >
        <q-tab no-caps name="materials" icon="inventory_2" label="Materiales" />
        <q-tab no-caps name="providers" icon="local_shipping" label="Proveedores" />
        <q-tab no-caps name="compras" icon="shopping_cart" label="Compras" />
        <q-tab no-caps name="pagos" icon="payments" label="Pagos compras" />
        <q-tab no-caps name="recuentos" icon="inventory" label="Retiros" />
      </q-tabs>
      <q-separator />

      <q-tab-panels v-model="tab" animated>
        <q-tab-panel name="materials">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-lg-4">
              <q-card flat bordered>
                <q-card-section class="text-subtitle1 text-weight-bold">
                  {{ materialForm.id ? 'Editar material' : 'Registro de material' }}
                </q-card-section>
                <q-card-section>
                  <q-form @submit.prevent="saveMaterial">
                    <q-input v-model="materialForm.nombre" dense outlined label="Nombre" :rules="[req]" class="q-mb-sm" />
                    <q-input v-model="materialForm.unidad" dense outlined label="Unidad" :rules="[req]" class="q-mb-sm" />
                    <q-input v-model.number="materialForm.minimo" dense outlined type="number" min="0" step="0.01" label="Minimo" class="q-mb-sm" />
                    <q-input v-model.number="materialForm.stock" dense outlined type="number" min="0" step="0.01" label="Stock" class="q-mb-sm" />
                    <div class="row justify-end q-gutter-sm">
                      <q-btn flat no-caps color="negative" label="Limpiar" @click="resetMaterialForm" />
                      <q-btn no-caps color="primary" label="Guardar" type="submit" :loading="savingMaterial" />
                    </div>
                  </q-form>
                </q-card-section>
              </q-card>
            </div>

            <div class="col-12 col-lg-8">
              <q-table
                dense
                flat
                bordered
                :rows="materials"
                :columns="materialColumns"
                row-key="id"
                :pagination="{ rowsPerPage: 100 }"
                :rows-per-page-options="[50, 100]"
              >
                <template #body-cell-actions="props">
                  <q-td :props="props">
                    <q-btn dense color="primary" no-caps icon="edit" label="Editar" @click="editMaterial(props.row)" />
                  </q-td>
                </template>
                <template #body-cell-stock="props">
                  <q-td :props="props" class="text-right">
                    <q-chip dense :color="Number(props.row.stock) <= Number(props.row.minimo) ? 'negative' : 'positive'" text-color="white">
                      {{ amountLabel(props.row.stock) }}
                    </q-chip>
                  </q-td>
                </template>
                <template #body-cell-minimo="props">
                  <q-td :props="props" class="text-right">{{ amountLabel(props.row.minimo) }}</q-td>
                </template>
              </q-table>
            </div>
          </div>
        </q-tab-panel>

        <q-tab-panel name="providers">
          <div class="row q-col-gutter-md">
            <div class="col-12 col-lg-4">
              <q-card flat bordered>
                <q-card-section class="text-subtitle1 text-weight-bold">
                  {{ providerForm.id ? 'Editar proveedor' : 'Registro de proveedor' }}
                </q-card-section>
                <q-card-section>
                  <q-form @submit.prevent="saveProvider">
                    <q-input v-model="providerForm.razon" dense outlined label="Razon social" :rules="[req]" class="q-mb-sm" />
                    <q-input v-model="providerForm.nombre" dense outlined label="Nombre" class="q-mb-sm" />
                    <q-input v-model="providerForm.nit" dense outlined label="NIT" class="q-mb-sm" />
                    <q-input v-model="providerForm.direccion" dense outlined label="Direccion" class="q-mb-sm" />
                    <q-input v-model="providerForm.telefono" dense outlined label="Telefono" class="q-mb-sm" />
                    <q-toggle v-model="providerForm.estado" label="Activo" color="positive" class="q-mb-sm" />
                    <div class="row justify-end q-gutter-sm">
                      <q-btn flat no-caps color="negative" label="Limpiar" @click="resetProviderForm" />
                      <q-btn no-caps color="primary" label="Guardar" type="submit" :loading="savingProvider" />
                    </div>
                  </q-form>
                </q-card-section>
              </q-card>
            </div>

            <div class="col-12 col-lg-8">
              <q-table
                dense
                flat
                bordered
                :rows="providers"
                :columns="providerColumns"
                row-key="id"
                :pagination="{ rowsPerPage: 100 }"
                :rows-per-page-options="[50, 100]"
              >
                <template #body-cell-actions="props">
                  <q-td :props="props">
                    <q-btn dense color="primary" no-caps icon="edit" label="Editar" @click="editProvider(props.row)" />
                  </q-td>
                </template>
                <template #body-cell-estado="props">
                  <q-td :props="props">
                    <q-chip dense :color="props.row.estado ? 'positive' : 'negative'" text-color="white">
                      {{ props.row.estado ? 'ACTIVO' : 'INACTIVO' }}
                    </q-chip>
                  </q-td>
                </template>
              </q-table>
            </div>
          </div>
        </q-tab-panel>

        <q-tab-panel name="compras">
          <q-table
            dense
            flat
            bordered
            :rows="compras"
            :columns="compraColumns"
            row-key="id"
            :pagination="{ rowsPerPage: 100 }"
            :rows-per-page-options="[50, 100]"
          >
            <template #top-right>
              <q-btn color="positive" no-caps icon="add" label="Registrar compra" @click="openCompraDialog" />
            </template>
            <template #body-cell-fecha="props">
              <q-td :props="props">{{ props.row.fecha }} {{ props.row.hora }}</q-td>
            </template>
            <template #body-cell-cantidad="props">
              <q-td :props="props" class="text-right">{{ amountLabel(props.row.cantidad) }}</q-td>
            </template>
            <template #body-cell-retiro="props">
              <q-td :props="props" class="text-right">{{ amountLabel(props.row.retiro) }}</q-td>
            </template>
            <template #body-cell-costo="props">
              <q-td :props="props" class="text-right">{{ money(props.row.costo) }}</q-td>
            </template>
            <template #body-cell-subtotal="props">
              <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.subtotal) }}</q-td>
            </template>
            <template #body-cell-deuda="props">
              <q-td :props="props" class="text-right">
                <q-chip dense :color="Number(props.row.deuda) > 0 ? 'orange' : 'positive'" text-color="white">
                  {{ money(props.row.deuda) }}
                </q-chip>
              </q-td>
            </template>
            <template #body-cell-estado="props">
              <q-td :props="props">
                <q-chip dense :color="compraEstadoColor(props.row.estado)" text-color="white">
                  {{ props.row.estado }}
                </q-chip>
              </q-td>
            </template>
          </q-table>
        </q-tab-panel>

        <q-tab-panel name="pagos">
          <q-table
            dense
            flat
            bordered
            :rows="pagos"
            :columns="pagoColumns"
            row-key="id"
            :pagination="{ rowsPerPage: 100 }"
            :rows-per-page-options="[50, 100]"
          >
            <template #top-right>
              <q-btn color="positive" no-caps icon="payments" label="Registrar pago" @click="openPagoDialog" />
            </template>
            <template #body-cell-fecha="props">
              <q-td :props="props">{{ props.row.fecha }}</q-td>
            </template>
            <template #body-cell-monto="props">
              <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.monto) }}</q-td>
            </template>
            <template #body-cell-caja="props">
              <q-td :props="props" class="text-right">{{ money(props.row.caja) }}</q-td>
            </template>
          </q-table>
        </q-tab-panel>

        <q-tab-panel name="recuentos">
          <q-table
            dense
            flat
            bordered
            :rows="recuentos"
            :columns="recuentoColumns"
            row-key="id"
            :pagination="{ rowsPerPage: 100 }"
            :rows-per-page-options="[50, 100]"
          >
            <template #top-right>
              <q-btn color="negative" no-caps icon="remove_circle" label="Registrar retiro" @click="openRecuentoDialog" />
            </template>
            <template #body-cell-fecha="props">
              <q-td :props="props">{{ props.row.fecha }} {{ props.row.hora }}</q-td>
            </template>
            <template #body-cell-cantidad="props">
              <q-td :props="props" class="text-right text-weight-bold">{{ amountLabel(props.row.cantidad) }}</q-td>
            </template>
          </q-table>
        </q-tab-panel>
      </q-tab-panels>
    </q-card>

    <q-dialog v-model="dialogCompra">
      <q-card style="width: 760px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Registro de compra</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="saveCompra">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4">
                <q-input v-model="compraForm.fecha" dense outlined type="date" label="Fecha" :rules="[req]" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="compraForm.hora" dense outlined type="time" label="Hora" :rules="[req]" />
              </div>
              <div class="col-12 col-md-4">
                <q-input dense outlined readonly :model-value="currentUserName" label="Usuario" />
              </div>
              <div class="col-12 col-md-6">
                <q-select v-model="compraForm.material_id" dense outlined emit-value map-options :options="materialOptions" label="Material" :rules="[req]" />
              </div>
              <div class="col-12 col-md-6">
                <q-select v-model="compraForm.provider_id" dense outlined emit-value map-options :options="providerOptions" label="Proveedor" :rules="[req]" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model.number="compraForm.cantidad" dense outlined type="number" min="0.01" step="0.01" label="Cantidad" :rules="[req]" />
              </div>
              <div class="col-12 col-md-3">
                <q-input v-model.number="compraForm.costo" dense outlined type="number" min="0" step="0.01" label="Costo" :rules="[req]" />
              </div>
              <div class="col-12 col-md-3">
                <q-input dense outlined readonly :model-value="amountLabel(0)" label="Retiro" />
              </div>
              <div class="col-12 col-md-3">
                <q-input dense outlined readonly :model-value="money(compraSubtotal)" label="Subtotal" />
              </div>
              <div class="col-12 col-md-4">
                <q-input dense outlined readonly :model-value="money(compraSubtotal)" label="Deuda" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="compraForm.fechaven" dense outlined type="date" label="Fecha vencimiento" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="compraForm.lote" dense outlined label="Lote" />
              </div>
              <div class="col-12">
                <q-input v-model="compraForm.comentario" dense outlined type="textarea" autogrow label="Comentario" />
              </div>
              <div class="col-12">
                <q-input v-model="compraForm.observacion" dense outlined type="textarea" autogrow label="Observacion" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar compra" type="submit" :loading="savingCompra" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogPago">
      <q-card style="width: 620px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Pago de compra</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="savePago">
            <div class="row q-col-gutter-sm">
              <div class="col-12">
                <q-select v-model="pagoForm.compra_id" dense outlined emit-value map-options :options="compraPendienteOptions" label="Compra" :rules="[req]" />
              </div>
              <div class="col-12 col-md-6">
                <q-input v-model="pagoForm.fecha" dense outlined type="date" label="Fecha" :rules="[req]" />
              </div>
              <div class="col-12 col-md-6">
                <q-input v-model.number="pagoForm.monto" dense outlined type="number" min="0.01" step="0.01" label="Monto" :rules="[req]" />
              </div>
              <div class="col-12">
                <q-banner dense class="bg-orange-1 text-orange-9">
                  Deuda actual: <span class="text-weight-bold">{{ money(selectedCompraPendiente?.deuda || 0) }}</span>
                </q-banner>
              </div>
              <div class="col-12">
                <q-input v-model="pagoForm.observacion" dense outlined type="textarea" autogrow label="Observacion" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar pago" type="submit" :loading="savingPago" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogRecuento">
      <q-card style="width: 620px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Registro de retiro</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="saveRecuento">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-6">
                <q-input v-model="recuentoForm.fecha" dense outlined type="date" label="Fecha" :rules="[req]" />
              </div>
              <div class="col-12 col-md-6">
                <q-input v-model="recuentoForm.hora" dense outlined type="time" label="Hora" :rules="[req]" />
              </div>
              <div class="col-12">
                <q-select v-model="recuentoForm.material_id" dense outlined emit-value map-options :options="materialOptions" label="Material" :rules="[req]" />
              </div>
              <div class="col-12">
                <q-banner dense class="bg-red-1 text-red-9">
                  Stock disponible: <span class="text-weight-bold">{{ amountLabel(selectedMaterialForRecuento?.stock || 0) }}</span>
                </q-banner>
              </div>
              <div class="col-12">
                <q-input v-model.number="recuentoForm.cantidad" dense outlined type="number" min="0.01" step="0.01" label="Cantidad" :rules="[req]" />
              </div>
              <div class="col-12">
                <q-input v-model="recuentoForm.observacion" dense outlined type="textarea" autogrow label="Observacion" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar retiro" type="submit" :loading="savingRecuento" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'AlmacenPage',
  data () {
    return {
      tab: 'materials',
      loading: false,
      savingMaterial: false,
      savingProvider: false,
      savingCompra: false,
      savingPago: false,
      savingRecuento: false,
      dialogCompra: false,
      dialogPago: false,
      dialogRecuento: false,
      materials: [],
      providers: [],
      compras: [],
      pagos: [],
      recuentos: [],
      materialForm: { id: null, nombre: '', unidad: '', minimo: 0, stock: 0 },
      providerForm: { id: null, razon: '', nombre: '', nit: '', direccion: '', telefono: '', estado: true },
      compraForm: {
        fecha: '',
        hora: '',
        cantidad: 0,
        costo: 0,
        fechaven: '',
        lote: '',
        comentario: '',
        observacion: '',
        material_id: null,
        provider_id: null
      },
      pagoForm: {
        fecha: '',
        monto: null,
        observacion: '',
        compra_id: null
      },
      recuentoForm: {
        fecha: '',
        hora: '',
        cantidad: null,
        observacion: '',
        material_id: null
      },
      materialColumns: [
        { name: 'actions', label: '', field: 'id', align: 'left' },
        { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left' },
        { name: 'unidad', label: 'Unidad', field: 'unidad', align: 'left' },
        { name: 'minimo', label: 'Minimo', field: 'minimo', align: 'right' },
        { name: 'stock', label: 'Stock', field: 'stock', align: 'right' }
      ],
      providerColumns: [
        { name: 'actions', label: '', field: 'id', align: 'left' },
        { name: 'razon', label: 'Razon', field: 'razon', align: 'left' },
        { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left' },
        { name: 'nit', label: 'NIT', field: 'nit', align: 'left' },
        { name: 'direccion', label: 'Direccion', field: 'direccion', align: 'left' },
        { name: 'telefono', label: 'Telefono', field: 'telefono', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' }
      ],
      compraColumns: [
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'material', label: 'Material', field: row => row.material?.nombre || '-', align: 'left' },
        { name: 'provider', label: 'Proveedor', field: row => row.provider?.razon || '-', align: 'left' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'retiro', label: 'Retiro', field: 'retiro', align: 'right' },
        { name: 'costo', label: 'Costo', field: 'costo', align: 'right' },
        { name: 'subtotal', label: 'Subtotal', field: 'subtotal', align: 'right' },
        { name: 'deuda', label: 'Deuda', field: 'deuda', align: 'right' },
        { name: 'usuario', label: 'Usuario', field: row => row.user?.name || row.user?.username || '-', align: 'left' },
        { name: 'lote', label: 'Lote', field: 'lote', align: 'left' },
        { name: 'estado', label: 'Estado', field: 'estado', align: 'left' }
      ],
      pagoColumns: [
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'compra', label: 'Compra', field: row => `#${row.compra_id}`, align: 'left' },
        { name: 'material', label: 'Material', field: row => row.compra?.material?.nombre || '-', align: 'left' },
        { name: 'monto', label: 'Monto', field: 'monto', align: 'right' },
        { name: 'caja', label: 'Caja General', field: 'caja', align: 'right' },
        { name: 'usuario', label: 'Usuario', field: row => row.user?.name || row.user?.username || '-', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' }
      ],
      recuentoColumns: [
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', align: 'left' },
        { name: 'material', label: 'Material', field: row => row.material?.nombre || '-', align: 'left' },
        { name: 'compra', label: 'Compra origen', field: row => `#${row.compra_id}`, align: 'left' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'usuario', label: 'Usuario', field: row => row.user?.name || row.user?.username || '-', align: 'left' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' }
      ]
    }
  },
  computed: {
    materialOptions () {
      return this.materials.map(m => ({
        label: `${m.nombre} | ${this.amountLabel(m.stock)} ${m.unidad}`,
        value: m.id
      }))
    },
    providerOptions () {
      return this.providers
        .filter(p => p.estado)
        .map(p => ({ label: p.razon, value: p.id }))
    },
    comprasPendientes () {
      return this.compras.filter(c => Number(c.deuda) > 0)
    },
    compraPendienteOptions () {
      return this.comprasPendientes.map(c => ({
        label: `#${c.id} | ${c.material?.nombre || '-'} | deuda ${this.money(c.deuda)}`,
        value: c.id
      }))
    },
    selectedCompraPendiente () {
      return this.compras.find(c => c.id === this.pagoForm.compra_id) || null
    },
    selectedMaterialForRecuento () {
      return this.materials.find(m => m.id === this.recuentoForm.material_id) || null
    },
    materialsBajoMinimo () {
      return this.materials.filter(m => Number(m.stock) <= Number(m.minimo))
    },
    compraSubtotal () {
      return Number(this.compraForm.cantidad || 0) * Number(this.compraForm.costo || 0)
    },
    currentUserName () {
      return this.$store.user?.name || this.$store.user?.username || '-'
    }
  },
  mounted () {
    this.loadAll()
  },
  methods: {
    req (v) { return v !== null && v !== undefined && v !== '' || 'Campo requerido' },
    money (n) { return `${Number(n || 0).toFixed(2)} Bs` },
    amountLabel (n) { return Number(n || 0).toFixed(2) },
    compraEstadoColor (estado) {
      if (estado === 'PAGADA' || estado === 'PAGADA / CONSUMIDA') return 'positive'
      if (estado === 'CONSUMIDA') return 'deep-orange'
      if (estado === 'PARCIAL') return 'orange'
      return 'primary'
    },
    nowDate () {
      return new Date().toISOString().slice(0, 10)
    },
    nowTime () {
      return new Date().toTimeString().slice(0, 5)
    },
    async loadAll () {
      this.loading = true
      try {
        const [materials, providers, compras, pagos, recuentos] = await Promise.all([
          this.$axios.get('materials'),
          this.$axios.get('providers'),
          this.$axios.get('compras'),
          this.$axios.get('log-compras'),
          this.$axios.get('recuentos')
        ])
        this.materials = materials.data || []
        this.providers = providers.data || []
        this.compras = compras.data || []
        this.pagos = pagos.data || []
        this.recuentos = recuentos.data || []
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar almacen')
      } finally {
        this.loading = false
      }
    },
    resetMaterialForm () {
      this.materialForm = { id: null, nombre: '', unidad: '', minimo: 0, stock: 0 }
    },
    editMaterial (row) {
      this.materialForm = { ...row, minimo: Number(row.minimo || 0), stock: Number(row.stock || 0) }
      this.tab = 'materials'
    },
    async saveMaterial () {
      this.savingMaterial = true
      try {
        if (this.materialForm.id) {
          await this.$axios.put(`materials/${this.materialForm.id}`, this.materialForm)
        } else {
          await this.$axios.post('materials', this.materialForm)
        }
        this.$alert.success('Material guardado')
        this.resetMaterialForm()
        await this.loadAll()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar material')
      } finally {
        this.savingMaterial = false
      }
    },
    resetProviderForm () {
      this.providerForm = { id: null, razon: '', nombre: '', nit: '', direccion: '', telefono: '', estado: true }
    },
    editProvider (row) {
      this.providerForm = { ...row, estado: !!row.estado }
      this.tab = 'providers'
    },
    async saveProvider () {
      this.savingProvider = true
      try {
        if (this.providerForm.id) {
          await this.$axios.put(`providers/${this.providerForm.id}`, this.providerForm)
        } else {
          await this.$axios.post('providers', this.providerForm)
        }
        this.$alert.success('Proveedor guardado')
        this.resetProviderForm()
        await this.loadAll()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar proveedor')
      } finally {
        this.savingProvider = false
      }
    },
    openCompraDialog () {
      this.compraForm = {
        fecha: this.nowDate(),
        hora: this.nowTime(),
        cantidad: 0,
        costo: 0,
        fechaven: '',
        lote: '',
        comentario: '',
        observacion: '',
        material_id: this.materials[0]?.id || null,
        provider_id: this.providers.find(p => p.estado)?.id || null
      }
      this.dialogCompra = true
    },
    async saveCompra () {
      this.savingCompra = true
      try {
        await this.$axios.post('compras', this.compraForm)
        this.$alert.success('Compra registrada')
        this.dialogCompra = false
        await this.loadAll()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar compra')
      } finally {
        this.savingCompra = false
      }
    },
    openPagoDialog () {
      this.pagoForm = {
        fecha: this.nowDate(),
        monto: null,
        observacion: '',
        compra_id: this.comprasPendientes[0]?.id || null
      }
      this.dialogPago = true
    },
    async savePago () {
      this.savingPago = true
      try {
        await this.$axios.post('log-compras', this.pagoForm)
        this.$alert.success('Pago registrado')
        this.dialogPago = false
        await this.loadAll()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar pago')
      } finally {
        this.savingPago = false
      }
    },
    openRecuentoDialog () {
      this.recuentoForm = {
        fecha: this.nowDate(),
        hora: this.nowTime(),
        cantidad: null,
        observacion: '',
        material_id: this.materials[0]?.id || null
      }
      this.dialogRecuento = true
    },
    async saveRecuento () {
      this.savingRecuento = true
      try {
        const r = await this.$axios.post('recuentos', this.recuentoForm)
        const totalRegistros = r.data?.items?.length || 0
        this.$alert.success(`Retiro registrado${totalRegistros > 1 ? ` en ${totalRegistros} compras FIFO` : ''}`)
        this.dialogRecuento = false
        await this.loadAll()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar retiro')
      } finally {
        this.savingRecuento = false
      }
    }
  }
}
</script>

<style scoped>
.tabs-almacen {
  background: linear-gradient(90deg, #0d47a1 0%, #1565c0 45%, #ef6c00 100%);
}
.tabs-almacen :deep(.q-tab--active) {
  background: rgba(255, 255, 255, 0.18);
}
</style>
