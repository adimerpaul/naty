<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <div class="col-12 col-lg-8">
        <q-card flat bordered>
          <q-card-section class="row items-center q-gutter-sm">
            <q-btn flat dense icon="arrow_back" no-caps label="Volver a ventas" @click="volverVentas" />
            <div class="text-h6">{{ tituloPagina }}</div>
            <q-space />
            <q-input v-model="search" dense outlined placeholder="Buscar producto..." style="width: 250px">
              <template #append><q-icon name="search" /></template>
            </q-input>

            <q-btn-toggle
              v-model="filtroGrupo"
              dense
              no-caps
              unelevated
              toggle-color="primary"
              color="grey-3"
              text-color="black"
              :options="[
                { label: 'Todos', value: 'todos', icon: 'apps' },
                { label: 'Chicha', value: 'chicha', icon: 'liquor' },
                { label: 'Garapina', value: 'garapina', icon: 'local_drink' }
              ]"
            />
          </q-card-section>
        </q-card>

        <div class="row q-col-gutter-sm q-mt-sm">
          <div class="col-12 col-sm-6 col-md-3" v-for="p in productosFiltrados" :key="p.id">
            <q-card class="cursor-pointer product-card" flat bordered @click="agregarProducto(p)" :style="{ borderColor: p.color || '#d7dbe0' }">
              <q-img v-if="p.fotografia" :src="imgProducto(p.fotografia)" style="height: 120px" fit="cover">
                <div class="absolute-bottom text-caption text-center" :style="{ background: 'rgba(0,0,0,.35)' }">{{ p.nombre }}</div>
              </q-img>
              <div v-else class="row items-center justify-center" style="height: 120px; background: #f4f5f7;">
                <q-icon name="inventory_2" size="38px" color="grey-7" />
              </div>
              <q-card-section class="q-pa-sm">
                <div class="text-subtitle2 ellipsis">{{ p.nombre }}</div>
                <div class="text-caption text-grey-7">{{ p.grupo }}</div>
                <div class="row items-center q-mt-xs">
                  <div class="text-weight-bold text-primary">{{ money(p.precio) }} Bs</div>
                  <q-space />
                  <div class="color-dot" :style="{ backgroundColor: p.color || '#ffffff' }" />
                </div>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <div class="text-subtitle1 text-weight-bold">Carrito</div>
            <q-space />
            <q-btn flat dense no-caps color="negative" icon="delete_sweep" label="Limpiar" @click="limpiarCarrito" :disable="!carrito.length" />
          </q-card-section>
          <q-separator />
          <q-card-section class="q-pa-sm">
            <q-list separator>
              <q-item v-for="it in carrito" :key="it.uid" dense>
                <q-item-section avatar>
                  <q-avatar rounded size="38px">
                    <q-img v-if="it.fotografia" :src="imgProducto(it.fotografia)" />
                    <q-icon v-else :name="it.producto_id ? 'inventory_2' : 'receipt_long'" />
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ it.nombre }}</q-item-label>
                  <q-item-label caption>
                    <div class="row q-col-gutter-xs">
                      <div class="col-6">
                        <q-input dense outlined type="number" v-model.number="it.precio" label="Precio" @update:model-value="recalcularItem(it)" />
                      </div>
                      <div class="col-6">
                        <q-input dense outlined type="number" min="1" v-model.number="it.cantidad" label="Cant." @update:model-value="recalcularItem(it)" />
                      </div>
                    </div>
                  </q-item-label>
                </q-item-section>
                <q-item-section side>
                  <div class="text-weight-bold">{{ money(it.subtotal) }}</div>
                  <q-btn dense flat round icon="delete" color="negative" @click="quitarItem(it.uid)" />
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
          <q-separator />
          <q-card-section class="row items-center">
            <div class="text-subtitle1 text-weight-bold">Total: {{ money(total) }} Bs</div>
            <q-space />
            <q-btn color="positive" no-caps icon="point_of_sale" label="Realizar venta" :disable="!carrito.length" @click="dialogVenta = true" />
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-dialog v-model="dialogVenta">
      <q-card style="width: 840px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Confirmar venta</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form ref="formVenta" @submit.prevent="guardarVenta">
            <div class="row q-col-gutter-sm q-mb-sm">
              <div class="col-12 col-md-8">
                <div class="row items-center q-col-gutter-sm">
                  <div class="col">
                    <q-select
                      v-model="form.cliente_id"
                      label="Cliente"
                      dense outlined
                      emit-value map-options
                      option-label="nombre"
                      option-value="id"
                      :options="clientes"
                      :rules="[req]"
                      use-input
                      input-debounce="250"
                      @filter="filtrarClientes"
                    />
                  </div>
                  <div class="col-auto">
                    <q-btn dense no-caps color="primary" icon="person_add" label="Nuevo" @click="dialogClienteNuevo = true" />
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <q-select
                  v-model="form.tipo_pago"
                  label="Pago"
                  dense outlined
                  emit-value map-options
                  :options="[
                    { label: 'Contado', value: 'contado' },
                    { label: 'Credito', value: 'credito' }
                  ]"
                />
              </div>
            </div>

            <div class="row q-col-gutter-sm q-mb-sm">
              <div class="col-12 col-md-4">
                <q-select
                  v-model="form.metodo_pago"
                  label="Metodo"
                  dense outlined
                  emit-value map-options
                  :options="[
                    { label: 'Efectivo', value: 'efectivo' },
                    { label: 'QR', value: 'qr' }
                  ]"
                />
              </div>
              <div class="col-12 col-md-4" v-if="form.tipo_pago === 'credito'">
                <q-input v-model.number="form.pago_inicial" type="number" min="0" :max="total" label="Pago inicial" dense outlined />
              </div>
              <div class="col-12 col-md-8">
                <q-input v-model="form.observacion" dense outlined type="textarea" autogrow label="Observacion" />
              </div>
            </div>

            <q-separator class="q-my-sm" />

            <q-table
              dense
              flat
              bordered
              :rows="carrito"
              :columns="resumenCols"
              row-key="uid"
              hide-pagination
              :pagination="{ rowsPerPage: 0 }"
            >
              <template #body-cell-foto="props">
                <q-td :props="props">
                  <q-avatar rounded size="30px">
                    <q-img v-if="props.row.fotografia" :src="imgProducto(props.row.fotografia)" />
                    <q-icon v-else name="inventory_2" />
                  </q-avatar>
                </q-td>
              </template>
              <template #body-cell-precio="props">
                <q-td :props="props" class="text-right">{{ money(props.row.precio) }}</q-td>
              </template>
              <template #body-cell-subtotal="props">
                <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.subtotal) }}</q-td>
              </template>
            </q-table>
            <q-markup-table dense flat bordered class="q-mt-sm">
              <tbody>
                <tr>
                  <td class="text-right text-weight-bold" style="width: 70%">Total</td>
                  <td class="text-right text-weight-bold">{{ money(total) }} Bs</td>
                </tr>
                <tr v-if="form.tipo_pago === 'credito'">
                  <td class="text-right">Pago inicial</td>
                  <td class="text-right">{{ money(form.pago_inicial || 0) }} Bs</td>
                </tr>
                <tr v-if="form.tipo_pago === 'credito'">
                  <td class="text-right">Saldo</td>
                  <td class="text-right">{{ money(total - Number(form.pago_inicial || 0)) }} Bs</td>
                </tr>
              </tbody>
            </q-markup-table>

            <div class="row justify-end q-gutter-sm q-mt-md">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
              <q-btn no-caps color="primary" label="Guardar venta" type="submit" :loading="loadingGuardar" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogClienteNuevo">
      <q-card style="width: 760px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Nuevo cliente</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="crearClienteRapido">
            <div class="row q-col-gutter-sm">
              <div class="col-12">
                <q-input :model-value="tipoVenta === 'local' ? 'Cliente local' : 'Cliente detalle'" dense outlined readonly />
              </div>
              <div class="col-12 col-md-8">
                <q-input v-model="clienteNew.titular" dense outlined label="Titular" :rules="[req]" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="clienteNew.ci" dense outlined label="CI" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="clienteNew.telefono" dense outlined label="Telefono" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="clienteNew.fechanac" dense outlined type="date" label="Fecha nacimiento" />
              </div>
              <div class="col-12 col-md-4">
                <q-input v-model="clienteNew.nit" dense outlined label="NIT" />
              </div>
              <div class="col-12">
                <q-input v-model="clienteNew.direccion" dense outlined label="Direccion" />
              </div>

              <template v-if="tipoVenta === 'local'">
                <div class="col-12 col-md-6">
                  <q-input v-model="clienteNew.local" dense outlined label="Local" :rules="[req]" />
                </div>
                <div class="col-12 col-md-6">
                  <q-select
                    v-model="clienteNew.tipo"
                    dense
                    outlined
                    emit-value
                    map-options
                    label="Tipo"
                    :options="[
                      { label: 'Propietario', value: 'PROPIETARIO' },
                      { label: 'Inquilino', value: 'INQUILINO' }
                    ]"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <q-select
                    v-model="clienteNew.legalidad"
                    dense
                    outlined
                    emit-value
                    map-options
                    label="Legalidad"
                    :options="[
                      { label: 'Con licencia', value: 'CON LICENCIA' },
                      { label: 'Sin licencia', value: 'SIN LICENCIA' }
                    ]"
                  />
                </div>
                <div class="col-12 col-md-6">
                  <q-select
                    v-model="clienteNew.categoria"
                    dense
                    outlined
                    emit-value
                    map-options
                    label="Categoria"
                    :options="[
                      { label: 'Simplificado', value: 'SIMPLIFICADO' },
                      { label: 'General', value: 'GENERAL' },
                      { label: 'Sin NIT', value: 'SIN NIT' }
                    ]"
                  />
                </div>
                <div class="col-12">
                  <q-input v-model="clienteNew.razon" dense outlined label="Razon social" />
                </div>
              </template>

              <div class="col-12">
                <q-input v-model="clienteNew.observacion" dense outlined label="Observacion" />
              </div>
            </div>
            <div class="row justify-end q-gutter-sm">
              <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup @click="resetClienteNew" />
              <q-btn no-caps color="primary" label="Crear cliente" type="submit" :loading="loadingClienteNew" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogGarantiaAsk">
      <q-card style="width: 520px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Garantia de inventario</div>
          <q-space />
          <q-btn flat round dense icon="close" @click="continuarSinGarantia" />
        </q-card-section>
        <q-card-section>
          <div class="text-body1 q-mb-sm">Deseas registrar prestamo o venta de material?</div>
          <div class="text-caption text-grey-7">
            Puedes continuar sin registrar garantia y volver mas tarde desde Inventario.
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat no-caps color="grey-8" label="No, continuar" @click="continuarSinGarantia" />
          <q-btn no-caps color="primary" icon="handshake" label="Si, registrar" @click="abrirDialogGarantia" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialogGarantia">
      <q-card style="width: 760px; max-width: 98vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Registrar prestamo o venta de material</div>
          <q-space />
          <q-btn flat round dense icon="close" v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-6">
              <q-input
                :model-value="ventaCreada?.cliente_nombre || ''"
                dense
                outlined
                readonly
                label="Cliente"
              />
            </div>
            <div class="col-12 col-md-3">
              <q-select
                v-model="garantia.tipo"
                dense
                outlined
                emit-value
                map-options
                label="Tipo"
                :options="[
                  { label: 'Prestamo', value: 'prestamo' },
                  { label: 'Venta material', value: 'venta' }
                ]"
              />
            </div>
            <div class="col-12 col-md-3">
              <q-input v-model.number="garantia.cantidad" type="number" min="1" dense outlined label="Cantidad" />
            </div>

            <div class="col-12 col-md-8">
              <q-select
                v-model="garantia.inventario_id"
                dense
                outlined
                emit-value
                map-options
                option-label="nombre"
                option-value="id"
                label="Inventario"
                :options="inventarios"
                :rules="[req]"
              >
                <template #option="scope">
                  <q-item v-bind="scope.itemProps">
                    <q-item-section>
                      <q-item-label>{{ scope.opt.nombre }}</q-item-label>
                      <q-item-label caption>
                        Disponible: {{ scope.opt.cantidad }} | Precio: {{ money(scope.opt.precio) }} Bs
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
            <div class="col-12 col-md-4">
              <q-input :model-value="selectedInventario ? selectedInventario.cantidad : 0" dense outlined readonly label="Disponible" />
            </div>
            <div class="col-12 col-md-4">
              <q-input :model-value="money(selectedInventario ? selectedInventario.precio : 0)" dense outlined readonly label="Precio referencia" suffix="Bs" />
            </div>
            <div class="col-12 col-md-4" v-if="garantia.tipo === 'venta'">
              <q-input
                v-model.number="garantia.efectivo"
                type="number"
                min="0"
                dense
                outlined
                label="Monto efectivo"
                @update:model-value="garantia.efectivo_manual = true"
              >
                <template #append>
                  <q-btn flat dense no-caps size="sm" icon="calculate" label="Sugerir" @click="aplicarPrecioSugerido" />
                </template>
              </q-input>
            </div>
            <div class="col-12 col-md-4" v-if="garantia.tipo === 'venta'">
              <q-select
                v-model="garantia.metodo_pago"
                dense
                outlined
                emit-value
                map-options
                label="Metodo pago"
                :options="[
                  { label: 'Efectivo', value: 'efectivo' },
                  { label: 'QR', value: 'qr' }
                ]"
              />
            </div>
            <div class="col-12 col-md-4" v-if="garantia.tipo === 'prestamo'">
              <q-input v-model="garantia.fisico" dense outlined label="Fisico recibido" />
            </div>
            <div class="col-12">
              <q-input v-model="garantia.observacion" dense outlined type="textarea" autogrow label="Observacion" />
            </div>
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn flat no-caps color="negative" label="Cancelar" v-close-popup />
          <q-btn no-caps color="primary" label="Guardar" :loading="loadingGarantia" @click="guardarGarantia" />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <div id="myElement" ></div>
  </q-page>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'

export default {
  name: 'VentaNuevaPage',
  data () {
    return {
      productos: [],
      clientes: [],
      inventarios: [],
      clientesBackup: [],
      carrito: [],
      search: '',
      filtroGrupo: 'todos',
      dialogVenta: false,
      dialogClienteNuevo: false,
      dialogGarantiaAsk: false,
      dialogGarantia: false,
      loadingGuardar: false,
      loadingClienteNew: false,
      loadingGarantia: false,
      ventaCreada: null,
      form: {
        cliente_id: null,
        tipo_pago: 'contado',
        metodo_pago: 'efectivo',
        pago_inicial: 0,
        observacion: ''
      },
      clienteNew: {
        local: '',
        titular: '',
        tipo: 'PROPIETARIO',
        ci: '',
        telefono: '',
        direccion: '',
        fechanac: '',
        legalidad: 'CON LICENCIA',
        categoria: 'SIMPLIFICADO',
        razon: '',
        nit: '',
        observacion: ''
      },
      garantia: {
        inventario_id: null,
        cantidad: 1,
        tipo: 'prestamo',
        efectivo: 0,
        efectivo_manual: false,
        metodo_pago: 'efectivo',
        fisico: '',
        observacion: ''
      },
      resumenCols: [
        { name: 'foto', label: '', field: 'fotografia', align: 'left' },
        { name: 'nombre', label: 'Producto / Concepto', field: 'nombre', align: 'left' },
        { name: 'precio', label: 'Precio', field: 'precio', align: 'right' },
        { name: 'cantidad', label: 'Cant.', field: 'cantidad', align: 'right' },
        { name: 'subtotal', label: 'Subtotal', field: 'subtotal', align: 'right' }
      ]
    }
  },
  computed: {
    tipoVenta () { return this.$route.params.tipo === 'local' ? 'local' : 'detalle' },
    tituloPagina () { return this.tipoVenta === 'local' ? 'Nueva venta local' : 'Nueva venta detalle' },
    selectedInventario () { return this.inventarios.find(i => i.id === this.garantia.inventario_id) || null },
    productosFiltrados () {
      const t = this.search.toLowerCase().trim()
      return this.productos.filter(p => {
        const okGrupo = this.filtroGrupo === 'todos' ? true : p.grupo === this.filtroGrupo
        const okText = t === '' ? true : (p.nombre || '').toLowerCase().includes(t)
        return okGrupo && okText
      })
    },
    total () { return this.carrito.reduce((a, b) => a + Number(b.subtotal || 0), 0) }
  },
  mounted () {
    this.cargarTodo()
  },
  watch: {
    'garantia.inventario_id' () {
      this.recalcularPrecioGarantia()
    },
    'garantia.cantidad' () {
      this.recalcularPrecioGarantia()
    },
    'garantia.tipo' () {
      this.recalcularPrecioGarantia()
    }
  },
  methods: {
    req (v) { return !!v || 'Campo requerido' },
    money (n) { return Number(n || 0).toFixed(2) },
    imgProducto (foto) { return `${this.$url}../../images/productos/${foto}` },
    uid () { return `${Date.now()}-${Math.round(Math.random() * 100000)}` },
    async cargarTodo () {
      try {
        const [prod, cli, inv] = await Promise.all([
          this.$axios.get('productos', { params: { tipo_producto: this.tipoVenta } }),
          this.$axios.get('clientes', { params: { tipo_cliente: this.tipoVenta } }),
          this.$axios.get('inventarios')
        ])
        this.productos = prod.data.filter(p => !!p.estado)
        this.clientes = cli.data.filter(c => !!c.estado)
        this.clientesBackup = this.clientes
        this.inventarios = (inv.data || []).filter(i => (i.cantidad || 0) > 0 && (i.estado || '').toUpperCase() === 'ACTIVO')
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar datos de venta')
      }
    },
    filtrarClientes (val, update) {
      update(() => {
        const t = (val || '').toLowerCase()
        this.clientes = !t ? this.clientesBackup : this.clientesBackup.filter(c => (c.nombre || '').toLowerCase().includes(t))
      })
    },
    volverVentas () {
      this.$router.push(`/ventas/${this.tipoVenta}`)
    },
    agregarProducto (p) {
      const idx = this.carrito.findIndex(i => i.producto_id === p.id)
      if (idx >= 0) {
        this.carrito[idx].cantidad += 1
        this.recalcularItem(this.carrito[idx])
      } else {
        this.carrito.push({
          uid: this.uid(),
          producto_id: p.id,
          nombre: p.nombre,
          fotografia: p.fotografia,
          precio: Number(p.precio),
          cantidad: 1,
          subtotal: Number(p.precio)
        })
      }
    },
    recalcularItem (it) {
      it.cantidad = Number(it.cantidad || 1)
      if (it.cantidad < 1) it.cantidad = 1
      it.precio = Number(it.precio || 0)
      if (it.precio < 0) it.precio = 0
      it.subtotal = Number(it.precio) * it.cantidad
    },
    quitarItem (uid) {
      this.carrito = this.carrito.filter(i => i.uid !== uid)
    },
    limpiarCarrito () {
      this.carrito = []
    },
    resetClienteNew () {
      this.clienteNew = {
        local: '',
        titular: '',
        tipo: 'PROPIETARIO',
        ci: '',
        telefono: '',
        direccion: '',
        fechanac: '',
        legalidad: 'CON LICENCIA',
        categoria: 'SIMPLIFICADO',
        razon: '',
        nit: '',
        observacion: ''
      }
    },
    async crearClienteRapido () {
      this.loadingClienteNew = true
      try {
        const payload = {
          ...this.clienteNew,
          tipo_cliente: this.tipoVenta,
          estado: true
        }
        if (this.tipoVenta === 'detalle') {
          payload.local = null
          payload.tipo = null
          payload.legalidad = null
          payload.categoria = null
          payload.razon = null
        }
        const res = await this.$axios.post('clientes', {
          ...payload
        })
        this.clientesBackup.unshift(res.data)
        this.clientes = this.clientesBackup
        this.form.cliente_id = res.data.id
        this.dialogClienteNuevo = false
        this.resetClienteNew()
        this.$alert.success('Cliente creado')
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo crear cliente')
      } finally {
        this.loadingClienteNew = false
      }
    },
    async guardarVenta () {
      if (!this.carrito.length) return
      const ok = await this.$refs.formVenta.validate()
      if (!ok) return
      this.loadingGuardar = true
      try {
        const res = await this.$axios.post('ventas', {
          cliente_id: this.form.cliente_id,
          tipo_venta: this.tipoVenta,
          tipo_movimiento: 'ingreso',
          tipo_pago: this.form.tipo_pago,
          metodo_pago: this.form.metodo_pago,
          pago_inicial: this.form.tipo_pago === 'credito' ? this.form.pago_inicial : 0,
          observacion: this.form.observacion,
          items: this.carrito.map(i => ({
            producto_id: i.producto_id,
            producto_nombre: i.producto_id ? null : i.nombre,
            cantidad: i.cantidad,
            precio: i.precio
          }))
        })
        this.ventaCreada = res.data
        Imprimir.fichaDespacho(res.data)
        this.$alert.success('Venta registrada')
        this.dialogVenta = false
        this.dialogGarantiaAsk = true
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar la venta')
      } finally {
        this.loadingGuardar = false
      }
    },
    continuarSinGarantia () {
      this.dialogGarantiaAsk = false
      this.$router.push(`/ventas/${this.tipoVenta}`)
    },
    abrirDialogGarantia () {
      this.dialogGarantiaAsk = false
      this.garantia = { inventario_id: null, cantidad: 1, tipo: 'prestamo', efectivo: 0, efectivo_manual: false, metodo_pago: 'efectivo', fisico: '', observacion: '' }
      this.dialogGarantia = true
    },
    aplicarPrecioSugerido () {
      const base = Number(this.selectedInventario?.precio || 0)
      const cantidad = Math.max(1, Number(this.garantia.cantidad || 1))
      this.garantia.efectivo = Number((base * cantidad).toFixed(2))
      this.garantia.efectivo_manual = false
    },
    recalcularPrecioGarantia () {
      if (this.garantia.tipo !== 'venta') return
      if (this.garantia.efectivo_manual) return
      this.aplicarPrecioSugerido()
    },
    async guardarGarantia () {
      if (!this.ventaCreada) return
      this.loadingGarantia = true
      try {
        await this.$axios.post('prestamos', {
          venta_id: this.ventaCreada.id,
          cliente_id: this.ventaCreada.cliente_id,
          inventario_id: this.garantia.inventario_id,
          cantidad: this.garantia.cantidad,
          tipo: this.garantia.tipo,
          efectivo: this.garantia.tipo === 'venta' ? this.garantia.efectivo : 0,
          metodo_pago: this.garantia.metodo_pago,
          fisico: this.garantia.fisico,
          observacion: this.garantia.observacion,
          tipo_venta: this.tipoVenta
        })
        this.$alert.success('Garantia registrada')
        this.dialogGarantia = false
        this.$router.push(`/ventas/${this.tipoVenta}`)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar garantia')
      } finally {
        this.loadingGarantia = false
      }
    }
  }
}
</script>

<style scoped>
.product-card {
  transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
}
.product-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(0,0,0,.1);
}
.color-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 1px solid rgba(0,0,0,.2);
}
</style>
