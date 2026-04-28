<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <div class="col-12 col-lg-8">
        <q-card flat bordered>
          <q-card-section class="row items-center q-gutter-sm">
            <q-btn flat dense icon="arrow_back" no-caps label="Volver a ventas" @click="volverVentas" />
            <div class="text-h6">{{ tituloPagina }}</div>
            <q-space />
            <q-input v-model="form.fecha_venta" dense outlined type="date" label="Fecha venta" class="venta-date-input" />
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

        <div class="row q-col-gutter-xs q-mt-sm">
          <div class="col-6 col-sm-4 col-md-2" v-for="p in productosFiltrados" :key="p.id">
            <q-card class="cursor-pointer product-card" flat bordered @click="agregarProducto(p)" :style="{ borderColor: p.color || '#d7dbe0' }">
              <q-img v-if="p.fotografia" :src="imgProducto(p.fotografia)" style="height: 72px" fit="cover">
              </q-img>
              <div v-else class="row items-center justify-center" style="height: 72px; background: #f4f5f7;">
                <q-icon name="inventory_2" size="24px" color="grey-7" />
              </div>
              <q-card-section class="q-pa-xs">
                <div class="text-caption text-weight-medium ellipsis">{{ p.nombre }}</div>
                <div class="row items-center no-wrap">
                  <div class="text-caption text-grey-7 ellipsis">{{ p.grupo }}</div>
                  <q-space />
                  <div class="text-caption text-weight-bold text-primary q-ml-xs">{{ money(p.precio) }} Bs</div>
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
          </q-card-section>
        </q-card>

        <q-card flat bordered class="q-mt-md">
          <q-card-section>
            <div class="text-subtitle1 text-weight-bold">Datos de la venta</div>
          </q-card-section>
          <q-separator />
          <q-card-section>
            <q-form ref="formVenta" @submit.prevent="guardarVenta">
              <div class="row q-col-gutter-sm q-mb-sm">
                <div class="col-12">
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
                        fill-input
                        @filter="filtrarClientes"
                        clearable
                      >
                        <template #selected-item="scope">
<!--                        <div class="ellipsis">-->
<!--                          {{ scope.opt.nombre }} <span class="text-grey-7">CI: {{ scope.opt.ci || '-' }}</span>-->
<!--                        </div>-->
                        </template>
                        <template #option="scope">
                          <q-item v-bind="scope.itemProps">
                            <q-item-section>
                              <q-item-label>{{ scope.opt.nombre }}</q-item-label>
                              <q-item-label caption>CI: {{ scope.opt.ci || '-' }}</q-item-label>
                            </q-item-section>
                          </q-item>
                        </template>
                      </q-select>
                    </div>
                    <div class="col-auto">
                      <q-btn dense no-caps color="primary" icon="person_add" label="Nuevo" @click="dialogClienteNuevo = true" />
                    </div>
                  </div>
                </div>
<!--              <div class="col-12 col-md-4">-->
<!--                <q-select-->
<!--                  v-model="form.tipo_pago"-->
<!--                  label="Pago"-->
<!--                  dense outlined-->
<!--                  emit-value map-options-->
<!--                  :options="[-->
<!--                    { label: 'Contado', value: 'contado' },-->
<!--                    { label: 'Credito', value: 'credito' }-->
<!--                  ]"-->
<!--                />-->
<!--              </div>-->
                <div class="col-12 col-sm-6">
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
                <div class="col-12 col-sm-6" v-if="form.tipo_pago === 'credito'">
                  <q-input v-model.number="form.pago_inicial" type="number" min="0" :max="total" label="Pago inicial" dense outlined />
                </div>
                <div class="col-12">
                  <q-checkbox v-model="form.facturado" label="Facturado SIAT" dense />
                </div>
                <div class="col-12">
                  <q-input v-model="form.observacion" dense outlined type="textarea" autogrow label="Observacion" />
                </div>
              </div>

              <q-separator class="q-my-sm" />

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
                <q-btn no-caps color="positive" icon="point_of_sale" label="Guardar venta" type="submit" :disable="!carrito.length" :loading="loadingGuardar" />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-card flat bordered class="q-mt-md">
      <q-card-section class="row items-center q-col-gutter-sm">
        <div class="col-12 col-md-4">
          <div class="text-subtitle1 text-weight-bold">Historial de ventas</div>
          <div class="text-caption text-grey-7">Ventas realizadas entre fechas para esta misma pantalla</div>
        </div>
        <div class="col-12 col-md-2">
          <q-input v-model="historial.date_from" dense outlined type="date" label="Desde" />
        </div>
        <div class="col-12 col-md-2">
          <q-input v-model="historial.date_to" dense outlined type="date" label="Hasta" />
        </div>
        <div class="col-12 col-md-4 text-right">
          <q-btn color="primary" no-caps icon="refresh" label="Actualizar historial" :loading="loadingHistorial" @click="cargarHistorial" />
        </div>
      </q-card-section>
      <q-separator />
      <q-card-section>
        <q-table
          dense
          flat
          bordered
          :rows="historialRows"
          :columns="historialCols"
          row-key="id"
          :loading="loadingHistorial"
          :rows-per-page-options="[0]"
          :table-row-class-fn="historialRowClass"
        >
          <template #body-cell-fecha="props">
            <q-td :props="props">{{ formatDateOnly(props.row.fecha) }} {{ props.row.hora ? props.row.hora.slice(0, 5) : '' }}</q-td>
          </template>
          <template #body-cell-actions="props">
            <q-td :props="props">
              <venta-actions-menu
                :row="props.row"
                @view="verVenta"
                @route-sheet="abrirHojaRuta"
                @print="imprimirVenta"
                @print-tax="imprimirImpuestos"
                @cancel="anularVenta"
              />
            </q-td>
          </template>
          <template #body-cell-prestamo="props">
            <q-td :props="props">
              <q-chip dense :color="props.row.tiene_prestamo ? 'orange' : 'grey-5'" text-color="white">
                {{ props.row.tiene_prestamo ? `Si (${props.row.prestamos_count || 0})` : 'No' }}
              </q-chip>
            </q-td>
          </template>
          <template #body-cell-facturado="props">
            <q-td :props="props">
              <q-chip dense :color="props.row.facturado ? 'positive' : 'grey-5'" text-color="white">
                {{ props.row.facturado ? 'Si' : 'No' }}
              </q-chip>
            </q-td>
          </template>
          <template #body-cell-total="props">
            <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.total) }} Bs</q-td>
          </template>
          <template #body-cell-user="props">
            <q-td :props="props">{{ props.row.user?.name || props.row.user?.username || '-' }}</q-td>
          </template>
          <template #bottom-row>
            <q-tr>
              <q-td colspan="5" class="text-right text-weight-bold">Total periodo</q-td>
              <q-td class="text-right text-weight-bold text-primary">{{ money(totalHistorial) }} Bs</q-td>
              <q-td />
            </q-tr>
          </template>
        </q-table>
      </q-card-section>
    </q-card>

    <q-dialog v-model="dialogDetalle">
      <q-card style="width: 920px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">Venta #{{ ventaSel?.id }}</div>
          <q-space />
          <venta-actions-menu
            v-if="ventaSel"
            :row="ventaSel"
            class="q-mr-sm"
            @view="verVenta"
            @route-sheet="abrirHojaRuta"
            @print="imprimirVenta"
            @print-tax="imprimirImpuestos"
            @cancel="anularVenta"
          />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>
        <q-card-section>
          <div class="row q-col-gutter-sm q-mb-sm">
            <div class="col-12 col-md-4"><b>Cliente:</b> {{ ventaSel?.cliente_nombre || '-' }}</div>
            <div class="col-6 col-md-2"><b>Fecha:</b> {{ formatDateOnly(ventaSel?.fecha) }}</div>
            <div class="col-6 col-md-2"><b>Estado:</b> {{ ventaSel?.estado || '-' }}</div>
            <div class="col-6 col-md-2"><b>Facturado:</b> {{ ventaSel?.facturado ? 'Si' : 'No' }}</div>
            <div class="col-6 col-md-2"><b>Total:</b> {{ money(ventaSel?.total) }} Bs</div>
            <div class="col-12 col-md-4"><b>Usuario:</b> {{ ventaSel?.user?.name || ventaSel?.user?.username || '-' }}</div>
            <div class="col-12 col-md-4"><b>Telefono:</b> {{ ventaSel?.cliente_telefono || '-' }}</div>
            <div class="col-12 col-md-4"><b>Direccion:</b> {{ ventaSel?.cliente_direccion || '-' }}</div>
            <div class="col-12"><b>Observacion:</b> {{ ventaSel?.observacion || '-' }}</div>
          </div>

          <div class="text-subtitle2 q-mb-xs">Productos</div>
          <q-table dense flat bordered :rows="ventaSel?.detalles || []" :columns="colsDetalle" row-key="id" hide-pagination>
            <template #body-cell-precio="props">
              <q-td :props="props" class="text-right">{{ money(props.row.precio) }}</q-td>
            </template>
            <template #body-cell-subtotal="props">
              <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.subtotal) }}</q-td>
            </template>
          </q-table>

          <div class="text-subtitle2 q-mt-md q-mb-xs">Prestamos / material</div>
          <q-table dense flat bordered :rows="ventaSel?.prestamos || []" :columns="colsPrestamos" row-key="id" hide-pagination>
            <template #body-cell-inventario="props">
              <q-td :props="props">{{ props.row.inventario?.nombre || '-' }}</q-td>
            </template>
            <template #body-cell-fisico_recibido="props">
              <q-td :props="props" class="text-right text-weight-bold">{{ money(props.row.fisico_recibido) }}</q-td>
            </template>
            <template #body-cell-monto_pendiente="props">
              <q-td :props="props" class="text-right text-negative text-weight-bold">{{ money(props.row.monto_pendiente ?? props.row.efectivo_actual) }}</q-td>
            </template>
          </q-table>
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
                :model-value="clienteSeleccionado?.nombre || ''"
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
            <div class="col-12 col-md-4">
              <q-input
                v-model.number="garantia.efectivo"
                type="number"
                min="0"
                dense
                outlined
                :label="garantia.tipo === 'venta' ? 'Monto efectivo' : 'Fisico recibido'"
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
    <hoja-ruta-dialog
      v-model="dialogHojaRuta"
      :row="hojaRutaRow"
      @saved="hojaRutaGuardada"
    />
    <div id="myElement" class="hidden"></div>
  </q-page>
</template>

<script>
import { Imprimir } from 'src/addons/Imprimir'
import HojaRutaDialog from 'src/components/ventas/HojaRutaDialog.vue'
import VentaActionsMenu from 'src/components/ventas/VentaActionsMenu.vue'
import { useCounterStore } from 'stores/example-store'

export default {
  name: 'VentaNuevaPage',
  components: {
    HojaRutaDialog,
    VentaActionsMenu
  },
  data () {
    return {
      productos: [],
      clientes: [],
      inventarios: [],
      clientesBackup: [],
      carrito: [],
      search: '',
      filtroGrupo: 'todos',
      dialogDetalle: false,
      dialogHojaRuta: false,
      dialogClienteNuevo: false,
      dialogGarantiaAsk: false,
      dialogGarantia: false,
      loadingGuardar: false,
      loadingClienteNew: false,
      loadingGarantia: false,
      loadingHistorial: false,
      ventaCreada: null,
      ventaSel: null,
      hojaRutaRow: null,
      historial: {
        date_from: new Date().toISOString().slice(0, 10),
        date_to: new Date().toISOString().slice(0, 10)
      },
      historialRows: [],
      form: {
        cliente_id: null,
        tipo_pago: 'contado',
        metodo_pago: 'efectivo',
        fecha_venta: new Date().toISOString().slice(0, 10),
        facturado: false,
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
      historialCols: [
        { name: 'actions', label: '', field: 'actions', align: 'left' },
        { name: 'id', label: 'ID', field: 'id', align: 'left' },
        { name: 'fecha', label: 'Fecha', field: row => `${row.fecha || ''} ${row.hora || ''}`.trim(), align: 'left' },
        { name: 'cliente_nombre', label: 'Cliente', field: 'cliente_nombre', align: 'left' },
        { name: 'prestamo', label: 'Prestamo', field: row => row.tiene_prestamo, align: 'left' },
        { name: 'facturado', label: 'Facturado', field: 'facturado', align: 'left' },
        { name: 'total', label: 'Total', field: 'total', align: 'right' },
        { name: 'user', label: 'Usuario', field: row => row.user?.name || '-', align: 'left' }
      ],
      colsDetalle: [
        { name: 'producto_nombre', label: 'Producto / Concepto', field: 'producto_nombre', align: 'left' },
        { name: 'precio', label: 'Precio', field: 'precio', align: 'right' },
        { name: 'cantidad', label: 'Cantidad', field: 'cantidad', align: 'right' },
        { name: 'subtotal', label: 'Subtotal', field: 'subtotal', align: 'right' }
      ],
      colsPrestamos: [
        { name: 'inventario', label: 'Material', field: row => row.inventario?.nombre || '-', align: 'left' },
        { name: 'tipo', label: 'Tipo', field: 'tipo', align: 'left' },
        { name: 'cantidad', label: 'Cant. prestada', field: 'cantidad', align: 'right' },
        { name: 'cantidad_actual', label: 'Cant. pendiente', field: 'cantidad_actual', align: 'right' },
        { name: 'fisico_recibido', label: 'Monto recibido', field: 'fisico_recibido', align: 'right' },
        { name: 'monto_pendiente', label: 'Monto pendiente', field: 'monto_pendiente', align: 'right' },
        { name: 'observacion', label: 'Observacion', field: 'observacion', align: 'left' }
      ]
    }
  },
  computed: {
    tipoVenta () { return this.$route.params.tipo === 'local' ? 'local' : 'detalle' },
    tituloPagina () { return this.tipoVenta === 'local' ? 'Nueva venta local' : 'Nueva venta detalle' },
    selectedInventario () { return this.inventarios.find(i => i.id === this.garantia.inventario_id) || null },
    clienteSeleccionado () { return this.clientesBackup.find(c => c.id === this.form.cliente_id) || null },
    productosFiltrados () {
      const t = this.search.toLowerCase().trim()
      return this.productos.filter(p => {
        const okGrupo = this.filtroGrupo === 'todos' ? true : p.grupo === this.filtroGrupo
        const okText = t === '' ? true : (p.nombre || '').toLowerCase().includes(t)
        return okGrupo && okText
      })
    },
    total () { return this.carrito.reduce((a, b) => a + Number(b.subtotal || 0), 0) },
    totalHistorial () { return this.historialRows.reduce((acc, row) => acc + Number(row.total || 0), 0) }
  },
  mounted () {
    this.cargarTodo()
  },
  watch: {
    'garantia.inventario_id' () {
      this.garantia.efectivo_manual = false
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
    formatDateOnly (v) {
      if (!v) return '-'
      const parts = String(v).split('T')[0].split('-')
      if (parts.length !== 3) return v
      return `${parts[2]}/${parts[1]}/${parts[0]}`
    },
    historialRowClass (row) {
      return row.estado === 'ANULADA' ? 'historial-row-anulada' : ''
    },
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
        this.cargarHistorial()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar datos de venta')
      }
    },
    async cargarInventarios () {
      const inv = await this.$axios.get('inventarios')
      this.inventarios = (inv.data || []).filter(i => (i.cantidad || 0) > 0 && (i.estado || '').toUpperCase() === 'ACTIVO')
    },
    async cargarHistorial () {
      this.loadingHistorial = true
      try {
        const res = await this.$axios.get('ventas', {
          params: {
            tipo_venta: this.tipoVenta,
            date_from: this.historial.date_from,
            date_to: this.historial.date_to
          }
        })
        this.historialRows = res.data || []
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar historial de ventas')
      } finally {
        this.loadingHistorial = false
      }
    },
    filtrarClientes (val, update) {
      update(() => {
        const t = (val || '').toLowerCase()
        this.clientes = !t
          ? this.clientesBackup
          : this.clientesBackup.filter(c =>
            (c.nombre || '').toLowerCase().includes(t) ||
            String(c.ci || '').toLowerCase().includes(t)
          )
      })
    },
    volverVentas () {
      this.$router.push(`/ventas/${this.tipoVenta}`)
    },
    async verVenta (row) {
      try {
        const res = await this.$axios.get(`ventas/${row.id}`)
        this.ventaSel = res.data
        this.dialogDetalle = true
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar detalle')
      }
    },
    abrirHojaRuta (row) {
      this.hojaRutaRow = row
      this.dialogHojaRuta = true
    },
    hojaRutaGuardada (venta) {
      if (this.ventaSel?.id === venta.id) {
        this.ventaSel = venta
      }
      this.cargarHistorial()
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
      this.dialogGarantiaAsk = true
    },
    async crearVenta () {
      this.loadingGuardar = true
      try {
        const res = await this.$axios.post('ventas', {
          cliente_id: this.form.cliente_id,
          tipo_venta: this.tipoVenta,
          tipo_movimiento: 'ingreso',
          tipo_pago: this.form.tipo_pago,
          metodo_pago: this.form.metodo_pago,
          fecha_venta: this.form.fecha_venta,
          facturado: this.form.facturado,
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
        const fact = res.data?.facturacion_resultado
        if (this.form.facturado && fact?.ok) {
          this.$alert.success('Venta registrada y factura enviada a SIAT')
        } else if (this.form.facturado && fact && !fact.ok) {
          this.$alert.warning((fact.mensajes && fact.mensajes[0]) || 'Venta registrada, pero la factura no se valido en SIAT')
        } else {
          this.$alert.success('Venta registrada')
        }
        this.cargarHistorial()
        return this.ventaCreada
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar la venta')
        return null
      } finally {
        this.loadingGuardar = false
      }
    },
    async continuarSinGarantia () {
      this.dialogGarantiaAsk = false
      const venta = await this.crearVenta()
      if (!venta) return
      this.carrito = []
      await this.imprimirVentaCompleta(venta.id)
      this.resetVentaNueva()
    },
    async abrirDialogGarantia () {
      this.garantia = { inventario_id: null, cantidad: 1, tipo: 'prestamo', efectivo: 0, efectivo_manual: false, metodo_pago: 'efectivo', fisico: '', observacion: '' }
      try {
        await this.cargarInventarios()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo actualizar inventario')
        return
      }
      this.dialogGarantiaAsk = false
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
      if (!this.garantia.inventario_id) {
        this.$alert.error('Debe seleccionar inventario')
        return
      }
      if (Number(this.garantia.cantidad || 0) < 1) {
        this.$alert.error('Debe registrar cantidad valida')
        return
      }
      if (this.selectedInventario && Number(this.garantia.cantidad || 0) > Number(this.selectedInventario.cantidad || 0)) {
        this.$alert.error('La cantidad supera el inventario disponible')
        return
      }
      if (this.garantia.tipo === 'venta' && Number(this.garantia.efectivo || 0) <= 0) {
        this.$alert.error('Debe registrar monto efectivo para venta de material')
        return
      }
      this.loadingGarantia = true
      try {
        const venta = await this.crearVenta()
        if (!venta) return
        await this.$axios.post('prestamos', {
          venta_id: venta.id,
          cliente_id: venta.cliente_id,
          inventario_id: this.garantia.inventario_id,
          cantidad: this.garantia.cantidad,
          tipo: this.garantia.tipo,
          efectivo: this.garantia.efectivo,
          metodo_pago: this.garantia.metodo_pago,
          fisico: '',
          observacion: this.garantia.observacion,
          tipo_venta: this.tipoVenta
        })
        this.$alert.success('Garantia registrada')
        this.dialogGarantia = false
        await this.imprimirVentaCompleta(venta.id)
        this.resetVentaNueva()
        this.cargarHistorial()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo registrar garantia')
      } finally {
        this.loadingGarantia = false
      }
    },
    async imprimirVentaCompleta (ventaId) {
      if (!ventaId) return
      try {
        const res = await this.$axios.get(`ventas/${ventaId}`)
        Imprimir.fichaDespacho(res.data)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo imprimir la ficha de despacho')
      }
    },
    async getVenta (id) {
      const res = await this.$axios.get(`ventas/${id}`)
      return res.data
    },
    async imprimirVenta (row) {
      try {
        const venta = await this.getVenta(row.id)
        Imprimir.fichaDespacho(venta)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo imprimir')
      }
    },
    anularVenta (row) {
      this.$alert.dialog('Desea anular la venta?')
        .onOk(async () => {
          try {
            await this.$axios.post(`ventas/${row.id}/anular`)
            this.$alert.success('Venta anulada')
            this.cargarHistorial()
          } catch (e) {
            this.$alert.error(e.response?.data?.message || 'No se pudo anular la venta')
          }
        })
    },
    imprimirImpuestos (row) {
      const env = useCounterStore().env || {}
      if (!row?.cuf) {
        this.$alert.error('La venta no tiene CUF de impuestos')
        return
      }
      if (!env.url2 || !env.nit) {
        this.$alert.error('Falta la configuracion de URL/NIT para abrir impuestos')
        return
      }
      const baseUrl = String(env.url2).endsWith('/') ? env.url2 : `${env.url2}/`
      const url = `${baseUrl}consulta/QR?nit=${env.nit}&cuf=${row.cuf}&numero=${row.id}&t=2`
      window.open(url, '_blank', 'noopener')
    },
    resetVentaNueva () {
      this.ventaCreada = null
      this.dialogGarantiaAsk = false
      this.dialogGarantia = false
      this.carrito = []
      this.search = ''
      this.form = {
        cliente_id: null,
        tipo_pago: 'contado',
        metodo_pago: 'efectivo',
        fecha_venta: new Date().toISOString().slice(0, 10),
        facturado: false,
        pago_inicial: 0,
        observacion: ''
      }
      this.garantia = {
        inventario_id: null,
        cantidad: 1,
        tipo: 'prestamo',
        efectivo: 0,
        efectivo_manual: false,
        metodo_pago: 'efectivo',
        fisico: '',
        observacion: ''
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
.venta-date-input {
  width: 170px;
  min-width: 160px;
}
:deep(.historial-row-anulada) {
  background: #ffebee;
  color: #b71c1c;
}
:deep(.historial-row-anulada td) {
  border-color: #ffcdd2;
}
.color-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 1px solid rgba(0,0,0,.2);
}
@media (max-width: 599px) {
  .venta-date-input {
    width: 100%;
  }
}
</style>
