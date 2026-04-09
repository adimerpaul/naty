<template>
  <q-page>
    <div class="row">
      <div class="col-12">
        <div :class="`q-pa-xs bg-${type=='detalle'?'red':'green'} text-center text-bold text-white`">
          {{ type == 'detalle' ? 'VENTA POR DETALLE' : 'VENTA POR LOCAL' }}
        </div>
      </div>
      <div class="col-2">
        <q-input type="date" outlined v-model="fecha" dense label="Fecha"/>
      </div>
      <div class="col-1 flex flex-center">
        <q-btn dense color="green" icon="add_circle" @click="modalregistro=true; newcliente={fechanac:'2000-01-01'}"/>
        <q-btn dense color="yellow" icon="edit" v-if="client " @click="modcliente=client; dialog_mod=true;"/>
      </div>
      <div class="col-5">
        <q-select
          outlined
          dense
          v-model="client"
          use-input
          input-debounce="0"
          label="Cliente"
          :options="clients"
          @filter="filterFn"
          behavior="menu"
        >
          <template v-slot:no-option>
            <q-item>
              <q-item-section class="text-grey">
                No results
              </q-item-section>
            </q-item>
          </template>
        </q-select>
      </div>
      <div class="col-4">
        <q-input dense outlined v-model="obs" label="Observacion"/>
      </div>
      <div class="col-12">
        <q-btn v-for="(grupo,index) in $gruposProductos" :key="index" :label="grupo" color="indigo"
               class="q-ma-xs" @click="getProductsFilter(grupo)" no-caps icon="check_circle" size="10px"/>
<!--        btn todo-->
        <q-btn  label="Todo" color="indigo"
               class="q-ma-xs" @click="getProductsFilter('Todo')" no-caps icon="check_circle" size="10px"/>
      </div>
      <div class="col-7">
        <div class="row">
          <div class="col-2" v-for="p in products" :key="p.id" style="padding:5px;">
            <q-card style="height: 80px;" @click="saleAdd(p)" class="no_selection"
                    :style="'background-color: '+p.color ">
              <q-card-section class="row items-center q-pb-none">
                <div class="text-bold text-grey-9">{{ p.precio }}Bs.</div>
                <q-space/>
                <div v-if="p.cantidad>0">
                  <q-chip color="primary" class=" q-ma-none text-bold text-white" dense :label="p.cantidad"/>
                </div>
              </q-card-section>
              <q-card-section class="q-pa-none">
                <div class="text-subtitle2 flex-center flex text-center">{{ p.nombre }}</div>
              </q-card-section>
            </q-card>
          </div>
        </div>
      </div>
      <div class="col-5">
        <q-card>
          <q-card-section class="row items-center q-pb-none">


            <!--       <div class="col-2">
                      <div class="text-bold text-grey">Total: <span class="text-red text-h5">{{total}}Bs.</span> </div>
                    </div>-->

            <!-- <div class="col-1">
               <q-btn icon="delete_outline" dense  color="negative" @click="saleClear" />
             </div>-->
          </q-card-section>
          <q-card-section class="q-pa-none">
            <div class="text-subtitle2 flex-center flex text-center bg-primary text-white">PRODUCTOS</div>
          </q-card-section>
          <q-card-section class="q-pa-none">
            <div class="row items-center bg-primary text-white">
              <div class="col-2 text-center text-bold">Cantidad</div>
              <div class="col-5 text-center text-bold">Nombre</div>
              <div class="col-2 text-center text-bold">Precio</div>
              <div class="col-2 text-center text-bold">Subtotal</div>
              <div class="col-1 text-center text-bold">Op</div>
            </div>
            <div class="row items-center" v-for="(p,index) in productSales" :key="p.id">
              <div class="col-2 text-center text-bold">
                <q-input v-model="p.cantidad" dense type="number" step="0.25"/>
              </div>
              <div class="col-5 text-grey">{{ p.nombre }}</div>
              <div class="col-2 text-right text-grey">
                <q-input v-model="p.precio" dense type="number" step="0.1">
                  <template v-slot:append>
                    <div class="text-subtitle1">Bs.</div>
                  </template>
                </q-input>
              </div>
              <div class="col-2 text-right q-pr-xs text-bold">{{ p.precio * p.cantidad }}Bs.</div>
              <div class="col-1">
                <q-btn color="red" icon="delete" dense @click="delProduct(index,p.id)"/>
              </div>
            </div>
          </q-card-section>
        </q-card>
        <q-form>
          <div class="row">
            <div class="col-3">
              <q-input v-model="monto" dense outlined label="Monto" type="number" step="0.1">
                <template v-slot:append>
                  <div class="text-subtitle1">Bs.</div>
                </template>
              </q-input>
            </div>
            <div class="col-3">
              <div class="text-bold text-grey">Total: <span class="text-black text-h5">{{ total }}Bs.</span></div>
            </div>
            <div class="col-3">
              <div class="text-bold text-grey">Saldo: <span
                :class="porCobrar>0?'text-red text-h5':'text-green text-h5'">{{ porCobrar }}Bs.</span></div>
            </div>
            <div class="col-3">
              <div
                :class="`bg-${porCobrar>0?'red':'green'}-3 text-${porCobrar>0?'red':'green'}-8 text-bold q-ma-xs q-pa-xs`"
                :style="`border: 1px solid ${porCobrar>0?'red':'green'};border-radius: 5px`">
                {{ porCobrar > 0 ? 'POR COBRAR' : 'CANCELADO' }}
              </div>
            </div>
            <div class="col-12" align="center">
              <q-btn color="green" icon="check_circle" label="Generar Venta" @click="saleSave"/>
            </div>
            <div class="col-12">
            </div>
          </div>

        </q-form>
      </div>
      <q-dialog v-model="modalregistro">
        <q-card style="width: 700px;min-width: 80vw">
          <q-card-section>
            <div class="text-h6">Registro de cliente</div>
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit.prevent="agregarcliente">
              <div class="row">
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input outlined label="ci" v-model="newcliente.ci"/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input required outlined label="titular" style="text-transform: uppercase"
                           v-model="newcliente.titular"/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input outlined label="telefono" v-model="newcliente.telefono"/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input outlined label="direccion" style="text-transform: uppercase" v-model="newcliente.direccion"/>
                </div>
                <template v-if="type=='local'">
                  <div class="col-12 col-md-6 q-pa-xs">
                    <q-input outlined label="Local" v-model="newcliente.local"/>
                  </div>
                  <div class="col-12 col-md-6 q-pa-xs">
                    <q-input outlined label="Fecha Nac" type="date" v-model="newcliente.fechanac"/>
                  </div>
                  <div class="col-12 col-md-6 q-pa-xs">
                    <q-select outlined v-model="newcliente.tipo" :options="['PROPIETARIO','INQUILINO']" label="Tipo"/>
                  </div>
                  <div class="col-12 col-md-6 q-pa-xs">
                    <q-select outlined v-model="newcliente.legalidad" :options="['CON LICENCIA','SIN LICENCIA']"
                              label="Legalidad"/>
                  </div>
                  <div class="col-12 col-md-6 q-pa-xs">
                    <q-select outlined v-model="newcliente.categoria" :options="['GENERAL','SIMPLIFICADO','SIN NIT']"
                              label="Categoria"/>
                  </div>
                  <div class="col-12 col-md-6 q-pa-xs">
                    <q-input outlined label="Razon Social" v-model="newcliente.razon"/>
                  </div>
                  <div class="col-12 col-md-6 q-pa-xs">
                    <q-input outlined label="NIT" v-model="newcliente.nit"/>
                  </div>
                </template>
                <div class="col-12  q-pa-xs flex flex-center">
                  <q-btn type="submit" class="full-width" color="primary" icon="add_circle" label="Registrar"/>
                </div>

              </div>
            </q-form>
          </q-card-section>
          <q-card-section align="right" class="">
            <q-btn flat label="Cerrar" icon="delete" color="negative" v-close-popup/>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialogGarantia" >
        <q-card>
          <q-card-section>

            <div class="text-h6">Garantia?</div>
          </q-card-section>
          <q-card-section>
            Desea registrar préstamo o venta de material?
          </q-card-section>
          <q-card-actions align="center" >
            <div class="botones">
              <q-btn flat label="Cerrar" color="red" @click="garantiaFin" />
              <q-btn flat label="IMPRIMIR" color="accent" @click="garantiaCancel" />
              <q-btn flat label="SI" color="green" @click="garantiaOk" />
            </div>
          </q-card-actions>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialogGarantia2" persistent>
        <q-card>
          <q-card-section>

            <div class="text-h6">Garantia?</div>
          </q-card-section>
          <q-card-section>
            Desea registrar préstamo o venta de material?
          </q-card-section>
          <q-card-actions align="center">
            <q-btn flat label="FINALIZAR" color="accent" @click="garantiaFin2"/>
            <q-btn flat label="OK" color="green" @click="garantiaOk2"/>
            <q-btn flat label="IMPRIMIR" color="red" @click="garantiaCancel2"/>
          </q-card-actions>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialog_mod">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">MODIFICAR REGISTRO CLIENTE</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form
              @submit="onMod"
              class="q-gutter-md">
              <q-input
                outlined
                type="text"
                v-model="modcliente.ci"
                label="Cedula Identidad"
                style="text-transform: uppercase"
              />
              <q-input
                outlined
                v-model="modcliente.titular"
                type="text"
                label="Nombre Completo"
                style="text-transform: uppercase"
                lazy-rules
                :rules="[ val => val && val.length > 0 || 'Por favor ingresar dato']"
              />
              <q-input
                outlined
                type="text"
                v-model="modcliente.telefono"
                label="Telefono o Celular"
                style="text-transform: uppercase"
              />
              <q-input
                type="text"
                outlined
                v-model="modcliente.direccion"
                label="Direccion*"
                style="text-transform: uppercase"
              />
              <q-input
                outlined
                v-model="modcliente.observacion"
                label="Observacion"
                type="text"
                style="text-transform: uppercase"
              />
              <div>
                <template v-if="type=='local'">
                  <q-input outlined label="Local" v-model="modcliente.local"/>
                  <q-input outlined label="Fecha Nac" type="date" v-model="modcliente.fechanac"/>
                  <q-select outlined v-model="modcliente.tipo" :options="['PROPIETARIO','INQUILINO']" label="Tipo"/>
                  <q-select outlined v-model="modcliente.legalidad" :options="['CON LICENCIA','SIN LICENCIA']"
                            label="Legalidad"/>
                  <q-select outlined v-model="modcliente.categoria" :options="['GENERAL','SIMPLIFICADO','SIN NIT']"
                            label="Categoria"/>
                  <q-input outlined label="Razon Social" v-model="modcliente.razon"/>
                  <q-input outlined label="NIT" v-model="modcliente.nit"/>
                </template>
                <q-btn label="Modificar" type="submit" color="positive" icon="add_circle"/>
                <q-btn label="Cancelar" icon="delete" color="negative" v-close-popup/>
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>
      <div class="col-12">
        <!--    <pre>{{clients}}</pre>-->
        <div class="row">
          <div class="col-3">
            <q-input outlined v-model="fecha1" label="Fecha Inicial" dense type="date"/>
          </div>
          <div class="col-3">
            <q-input outlined v-model="fecha2" label="Fecha Final" dense type="date" v-if="rango=='RANGO'"/>
          </div>
          <div class="col-1">
            <q-toggle v-model="rango" true-value="RANGO" false-value="DIA" :label="rango +' FECHA'" style="width:100%"/>
          </div>
          <div class="col-3">
            <q-btn color="info" label="CONSULTAR" icon="search" dense @click="consultaVenta(type)"/>
          </div>
          <div class="col-12 flex flex-center">
            <q-pagination v-model="pagination.page" :max="pagination.last_page" :min="1"
                          :total-pages="Math.ceil(pagination.rowsNumber / pagination.rowsPerPage)"
                          @click="consultaVenta(type)"
                          :max-pages="6"
                          boundary-numbers
            />
          </div>
          <!-- <div class="col-3"> <q-btn color="info" label="IMPRIMIR" icon="print"  dense @click="impresion"/></div>-->
        </div>
        <div class="col-12">
          <q-table v-model:pagination="pagination" title="Ventas" :rows="ventas" :columns="columnas" row-key="name"
                   :filter="filter" :rows-per-page-options="[0]">
            <template v-slot:top-right>
              <q-input outlined dense debounce="300" v-model="filter" placeholder="Search"
                       @update:modelValue="consultaVenta(type)">
                <template v-slot:append>
                  <q-icon name="search"/>
                </template>
              </q-input>
            </template>

            <template v-slot:body-cell-cliente="props">
              <q-td key="cliente" :props="props">
                {{ props.row.cliente.local }} {{ props.row.cliente.titular }}
              </q-td>
            </template>
            <template v-slot:body-cell-observacion="props">
              <q-td key="cliente" :props="props">

                <q-btn color="purple" icon="subject" v-if="props.row.observacion!=''" @click="verObs(props.row)"/>

              </q-td>
            </template>
            <template v-slot:body-cell-user="props">
              <q-td key="user" :props="props">
                {{ props.row.user.name }}
              </q-td>
            </template>
            <template v-slot:body-cell-estado="props">
              <q-td key="estado" :props="props">
                <q-badge :color="props.row.estado=='POR COBRAR' || props.row.estado=='ANULADO'?'red':'green'"
                         :label="props.row.estado"/>

              </q-td>
            </template>
            <template v-slot:body-cell-opcion="props">
              <q-td key="opcion" :props="props">
                <q-btn-group>
                  <q-btn icon="delete" color="red" @click="anular(props.row)" size="xs"
                         v-if="$store.state.login.anularventa && props.row.estado!='ANULADO'">
                    <q-tooltip>Anular</q-tooltip>
                  </q-btn>
                  <q-btn icon="local_shipping" color="indigo-3" @click="printruta(props.row)" size="xs"
                         v-if="props.row.fechaentrega!=null && props.row.fechaentrega!='' && props.row.estado!='ANULADO'">
                    <q-tooltip>Imprimir Ruta</q-tooltip>
                  </q-btn>
                  <q-btn icon="local_shipping" color="accent" @click="clickhojaruta(props.row)" size="xs"
                         v-if="$store.state.login.ruta && props.row.estado!='ANULADO'">
                    <q-tooltip>Hoja de Ruta</q-tooltip>
                  </q-btn>
                  <q-btn icon="shopping_cart" color="orange" @click="clickcompra(props.row)" size="xs">
                    <q-tooltip>Detalle Venta</q-tooltip>
                  </q-btn>
                  <template v-if="$store.state.login.reimpresion && props.row.estado!='ANULADO'">
                    <q-btn dense icon="print" color="info" v-if="props.row.estado!='ANULADO'"
                           @click="impboleta(props.row)">
                      <q-tooltip>Imprimir Boleta</q-tooltip>
                    </q-btn>
                  </template>
                </q-btn-group>

              </q-td>
            </template>
          </q-table>
        </div>
      </div>
      <q-dialog v-model="modalgarantia" persistent>
        <q-card style="width: 700px;min-width: 80vw">
          <q-card-section>
            <div class="text-h6">Garantia</div>
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit.prevent="agregargarantia">
              <div class="row">
                <div class="col-12 col-md-4 q-pa-xs">
                  <q-input required type="text" outlined label="Cliente" v-model="client.label" disable/>
                </div>
                <div class="col-12 col-md-4 q-pa-xs">
                  <q-input required type="text" outlined label="telefono" v-model="client.telefono" disable/>
                </div>
                <div class="col-12 col-md-4 q-pa-xs">
                  <q-input required type="text" outlined label="direccion" v-model="client.direccion" disable/>
                </div>
                <div class="col-12 col-md-4 q-pa-xs">
                  <q-select required outlined label="inventario" v-model="inventario" :options="inventarios"
                            option-label="nombre" @update:model-value="calcular"/>
                </div>
                <div class="col-12 col-md-4 q-pa-xs">
                  <q-input required type="number" outlined label="cantidad" v-model="newgarantia.cantidad"
                           @update:model-value="calcular"/>
                </div>
                <div class="col-12 col-md-4 q-pa-xs">
                  <q-input outlined type="number" step="0.01" label="efectivo" v-model="newgarantia.efectivo"/>
                </div>
                <div class="col-12 col-md-4 q-pa-xs">
                  <q-input outlined label="fisico" v-model="newgarantia.fisico" style="text-transform: uppercase"/>
                </div>
                <div class="col-12 col-md-4 q-pa-xs">
                  <q-input outlined label="observacion" v-model="newgarantia.observacion"
                           style="text-transform: uppercase"/>
                </div>
                <div class="col-12 col-md-4 q-pa-xs flex flex-center">
                  <input style="margin-right: 0.5em; height:35px; width:35px; " type="radio" value="EN PRESTAMO"
                         v-model="newgarantia.tipo"/><b> EN PRESTAMO </b>
                  <input style="margin-left: 1em;margin-right: 0.5em;height:35px; width:35px; " type="radio"
                         value="VENTA" v-model="newgarantia.tipo"/><b> VENTA </b>
                </div>
                <div class="col-12  q-pa-xs flex flex-center ">
                  <div class="col-6">
                    <q-btn type="submit" color="primary" icon="add_circle" label="Registrar"/>
                  </div>
                  <div class="col-6">
                    <q-btn color="red" icon="cancel" label="Cancelar" @click="cancelarGarantia"/>
                  </div>
                </div>
              </div>
            </q-form>
          </q-card-section>
          <!-- <q-card-section align="right" class="">
             <q-btn flat label="Cerrar" icon="delete" color="negative" v-close-popup/>
           </q-card-section>-->
        </q-card>
      </q-dialog>
      <q-dialog v-model="modalhojaruta">
        <q-card style="width: 700px;min-width: 80vw">
          <q-card-section>
            <div class="text-h6">Hoja ruta {{ venta.titular }} {{ venta.direccion }}</div>
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit.prevent="actualizarventa">
              <div class="row">
                <div class="col-12  q-pa-xs">
                  <q-input type="date" outlined label="fechaentrega" v-model="venta.fechaentrega"/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-select required :options="['MAÑANA','TARDE','NOCHE']" outlined label="turno"
                            v-model="venta.turno"/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input required type="text" style="text-transform: uppercase;" outlined label="hora"
                           v-model="venta.hora"/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input outlined type="text" style="text-transform: uppercase;" label="telefono1"
                           v-model="venta.telefono1"/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input outlined type="text" style="text-transform: uppercase;" label="telefono2"
                           v-model="venta.telefono2"/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input outlined label="direccion" style="text-transform: uppercase;" v-model="venta.direccion"/>
                </div>

                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input required outlined label="envase" style="text-transform: uppercase;" v-model="venta.envase"/>
                </div>

                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input required outlined label="acuenta" v-model="venta.acuenta" disable/>
                </div>
                <div class="col-12 col-md-6 q-pa-xs">
                  <q-input required outlined label="saldo" v-model="venta.saldo" disable/>
                </div>
                <div class="col-12  q-pa-xs">
                  <q-input outlined label="observacion" style="text-transform: uppercase;" v-model="venta.observacion"/>
                </div>
                <div class="col-12  q-pa-xs flex flex-center">
                  <q-btn type="submit" class="full-width" color="primary" icon="add_circle" label="Imprimir"/>
                </div>
              </div>
            </q-form>
          </q-card-section>
          <q-card-section align="right" class="">
            <q-btn flat label="Cerrar" icon="delete" color="negative" v-close-popup/>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="modalDialog">
        <q-card style="width: 700px; max-width: 80vw;">
          <q-card-section>
            <div class="text-h6">DETALLE DE COMPRA</div>
          </q-card-section>
          <q-card-section>
            <div><b>Obs:</b>{{ venta.observacion }}</div>
            <div>
              <table style="width:100%">
                <tr>
                  <th>PRODUCTO</th>
                  <th>CANTIDAD</th>
                  <th>PRECIO</th>
                  <th>SUBTOTAL</th>
                </tr>
                <tr v-for=" d in venta.detalles " :key="d">
                  <td>{{ d.nombreproducto }}</td>
                  <td>{{ d.cantidad }}</td>
                  <td>{{ d.precio }}</td>
                  <td>{{ d.subtotal }}</td>
                </tr>
              </table>
            </div>
          </q-card-section>
          <q-card-actions align="right">
            <q-btn flat label="OK" color="primary" v-close-popup/>
          </q-card-actions>
        </q-card>
      </q-dialog>


    </div>
  </q-page>
</template>

<script>
import {date} from 'quasar';
import moment from 'moment';

export default {
  name: `Sale`,
  data() {
    return {
      monto: 0,
      dialogGarantia: false,
      dialogGarantia2: false,
      printboleta: '',
      prestamolista: [],
      fecha: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fecha1: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fecha2: date.formatDate(new Date(), 'YYYY-MM-DD'),
      products: [],
      productsAll: [],
      modalregistro: false,
      newcliente: {fechanac: '2000-01-01'},
      filter: '',
      product: {},
      clients2: [],
      clients: [],
      client: '',
      productSales: [],
      sale: {},
      ventas: [],
      obs: '',
      inventarios: [],
      inventario: {},
      newgarantia: {},
      modalgarantia: false,
      modalhojaruta: false,
      modalDialog: false,
      dialog_mod: false,
      rango: 'RANGO',
      venta: {},
      type: this.$route.params.type,
      pagination: {
        // No longer using sort. if needed, this can now be done using the backend!
        // sortBy: 'name',
        // descending: false,
        page: 1,
        rowsPerPage: 15,
        // When using server side pagination, QTable needs
        // to know the "rows number" (total number of rows).
        // Why?
        // Because Quasar has no way of knowing what the last page
        // will be without this information!
        // Therefore, we now need to supply it with a "rowsNumber" ourself.
        rowsNumber: 0,
        last_page: 0
      },
      columnas: [
        {label: 'opcion', name: 'opcion', field: 'opcion'},
        {label: 'id', name: 'id', field: 'id'},
        {label: 'fecha', name: 'fecha', field: row => moment(row.fecha).format('DD/MM/YYYY')},
        {label: 'hora', name: 'hora', field: row => moment(row.created_at).format('HH:mm')},
        {label: 'cliente', name: 'cliente', field: row => row.cliente.local + ' ' + row.cliente.titular, align: 'left'},
        {label: 'total', name: 'total', field: 'total'},
        {label: 'acuenta', name: 'acuenta', field: 'acuenta'},
        {label: 'saldo', name: 'saldo', field: 'saldo'},
        {label: 'estado', name: 'estado', field: 'estado'},
        {label: 'user', name: 'user', field: 'user'},
        {label: 'observacion', name: 'observacion', field: 'observacion'},
      ]
    }
  },
  created() {
    console.log(this.type)

    this.$watch(
      () => this.$route.params,
      (toParams) => {
        console.log(toParams)
        console.log(toParams)
        // console.log('nuevo', toParams)
        // console.log('antiguo', previousParams)
        if (toParams == undefined) {
          return false
        }
        //this.datosGet(toParams)
        this.type = toParams.type
        //this.consultaVenta(this.type)
        //this.calcular()
        this.datosGet(toParams.type)

        this.consultaVenta(toParams.type)
        this.calcular()
        this.$axios.get(process.env.API + '/listainventario').then(res => {
          this.inventarios = res.data
          this.inventario = res.data[0]
        })
      }
    )
    this.datosGet(this.type)

    this.consultaVenta(this.type)
    this.calcular()
    this.$axios.get(process.env.API + '/listainventario').then(res => {
      this.inventarios = res.data
      this.inventario = res.data[0]
    })
  },
  methods: {
    getProductsFilter(grupo) {
      if (grupo == 'Todo') {
        this.products = this.productsAll
        return false
      }
      this.products = this.productsAll.filter(p => p.grupo == grupo)
    },
    cancelarGarantia() {
      this.modalgarantia = false
      this.monto = 0
      this.obs = ''
      this.sale = {}
      this.productSales = []
      this.client = {label: ''}
    },
    onRequest(props) {
      this.datosGet(this.type, props.pagination.page)
      // this.getUsers()
    },
    delProduct(p, id) {
      console.log(p)

      this.productSales.splice(p, 1);
      find = this.products.find(ps => ps.id == id)
      find.cantidad = 0
    },
    verObs(vent) {
      this.$q.dialog({
        title: 'Observacion',
        message: vent.observacion
      }).onOk(() => {
        // console.log('OK')
      }).onCancel(() => {
        // console.log('Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    calcular() {
      this.newgarantia.efectivo = parseFloat(this.newgarantia.cantidad) * parseFloat(this.inventario.precio)
    },
    onMod() {
      this.$q.loading.show();
      this.$axios.put(process.env.API + '/cliente/' + this.modcliente.id, this.modcliente).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Modificado correctamente'
        });
        this.dialog_mod = false;
        this.modcliente = {}
        this.datosGet(this.type)
      }).catch(err => {

        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: 'Error al Modificar'
        });
      })
      this.$q.loading.hide();
    },
    agregarcliente() {
      this.$q.loading.show()
      this.newcliente.tipocliente = 2
      if (this.type == 'local') {
        this.newcliente.tipocliente = 1
      } else
        this.newcliente.fechanac = null
      this.$axios.post(process.env.API + '/agregarcliente', this.newcliente).then(() => {
        this.$q.notify({
          message: 'Cliente Registrado',
          color: 'green',
          position: 'top',
          icon: 'info'
        })
        this.$q.loading.hide()
        this.datosGet(this.type)
        this.modalregistro = false
        this.newcliente = {fechanac: '2000-01-01'}
      }).catch(err => {
        this.$q.loading.hide()
        this.$q.notify({
          message: err.response.data.message,
          color: 'red',
          position: 'top',

          icon: 'error'
        })
      })
    },
    impresion() {
      if (this.ventas.length == 0) {
        return false
      }
      let sumtotal = 0
      let sumcuenta = 0
      let sumsaldo = 0
      let cadena = `<style>
      *{
      }
      table{width:100%;border-collapse: collapse;}
      table, th, td {
        //border: 1px solid;
      }
      </style>
      <div style='padding:5px'>
      <div style='text-align:center'>HISTORIAL DE VENTAS DE ` + this.fecha1 + ` AL ` + this.fecha2 + `</div>
      <hr>
      <table>
        <tr><th>TOTAL</th><th>A CUENTA</th><th>SALDO</th><th>TITULAR</th><th>USUARIO</th><tr>`
      this.ventas.forEach(v => {
        sumtotal += v.total
        sumcuenta += v.acuenta
        sumsaldo += v.saldo
        cadena += '<tr><td>' + v.total + '</td><td>' + v.acuenta + '</td><td>' + v.saldo + '</td><td>' + v.cliente.titular + '</td><td>' + v.user.name + '</td></tr>'

      });

      cadena += '</table>\
      <div><b>Total Venta </b>' + sumtotal + '</div>\
      <div><b>Total A Cuenta </b>' + sumcuenta + '</div>\
      <div><b>Total Saldo </b>' + sumsaldo + '</div>\
      </div>'
      let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
      myWindow.document.write(cadena);
      myWindow.document.close();
      myWindow.focus();
      setTimeout(function () {
        myWindow.print();
        myWindow.close();
        // this.comanda(sale_id);
        //    impAniv(response);
      }, 500);
    },
    impboleta(detalle) {
      //console.log(detalle);
      this.$axios.post(process.env.API + '/impresionVenta/' + detalle.id).then(res => {
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.focus();
        setTimeout(function () {
          myWindow.print();
          myWindow.close();
          // this.comanda(sale_id);
          //    impAniv(response);
        }, 500);
      })

    },
    clickcompra(compra) {
      console.log(compra)
      this.venta = compra
      this.modalDialog = true;
    },
    actualizarventa() {
      this.$q.loading.show()
      // console.log(this.venta)
      this.$axios.post(process.env.API + '/updateRuta', {
        id: this.venta.id,
        turno: this.venta.turno,
        hora: this.venta.hora,
        telefono1: this.venta.telefono1,
        telefono2: this.venta.telefono2,
        envase: this.venta.envase,
        direccion: this.venta.direccion,
        // total:this.venta.total,
        acuenta: this.venta.acuenta,
        saldo: this.venta.saldo,
        observacion: this.venta.observacion,
        fechaentrega: this.venta.fechaentrega
      }).then(res => {
        this.modalhojaruta = false
        // console.log(res.data)
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();

        let myWindow2 = window.open("", "Imprimir", "width=1000,height=1000");

        myWindow2.document.write(res.data);
        myWindow2.document.close();
        myWindow2.print();
        myWindow2.close();
        this.$q.loading.hide()
        // this.misclientes()
        this.consultaVenta(this.type)
        // this.newcliente={}
      })
    },
    printruta(ruta) {
      this.$axios.post(process.env.API + '/impresionruta/' + ruta.id).then(res => {
        let myWindow = window.open("_blank");
        myWindow.document.write(res.data);
        myWindow.document.close();
      })

    },
    clickhojaruta(venta) {
      this.venta = venta
      this.modalhojaruta = true
    },
    anular(venta) {
      // console.log(venta)
      this.$q.dialog({
        title: 'Anular Venta?',
        message: 'Motivo?',
        cancel: true,
        prompt: {
          model: '',
          type: 'text',
        }
      }).onOk((data) => {
        this.$axios.post(process.env.API + '/anular', {id: venta.id, observacion: data})
          .then(res => {
            this.consultaVenta(this.type)

            this.$q.notify({
              message: 'Venta Anulado ',
              color: 'green',
              position: 'top',

              icon: 'info'
            })
          })
      }).onOk(() => {
        // console.log('>>>> second OK catcher')
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    consultaVenta(tipo1) {
      //console.log(this.fecha1)
      //console.log(this.fecha2)
      if (this.rango == 'DIA') {
        this.fecha2 = this.fecha1
      }
      this.$api.post('listSale', {
        tipo: tipo1 == 'detalle' ? 'DETALLE' : 'LOCAL', ini: this.fecha1, fin: this.fecha2,
        page: this.pagination.page,
        filter: this.filter
      }).then(res => {
        console.log(res.data)
        this.pagination.rowsNumber = res.data.total
        this.pagination.page = res.data.current_page
        this.pagination.last_page = res.data.last_page

        res.data.data.forEach(r => {
          r.telefono1 = r.cliente.telefono
        });
        this.ventas = res.data.data
      })

    },
    saleSave() {
      if (this.fecha == '' || this.fecha == undefined) {
        this.$q.notify({
          message: 'Ingrese la fecha',
          color: 'red',
          position: 'top',
          icon: 'error'
        })
        return false
      }
      if (this.productSales.length == 0) {
        console.log('sin prod')
        this.$q.notify({
          message: 'Seleccione Productos',
          color: 'red',
          position: 'top',
          icon: 'error'
        })
        return false

      }
      if (this.client.id == undefined || this.client.id == '') {
        console.log('sin cliente')
        this.$q.notify({
          message: 'Debe Seleccionar Cliente',
          color: 'red',
          position: 'top',
          icon: 'error'
        })
        return false

      }
      if (this.monto < 0 || this.monto > this.total || this.monto == undefined) {
        console.log('sin monto')
        this.$q.notify({
          message: 'Verifique el monto',
          color: 'red',
          position: 'top',
          icon: 'error'
        })
        return false

      }
      console.log(this.client.id)
      this.$q.loading.show()
      this.productSales.forEach(r => {
        r.subtotal = r.precio * r.cantidad
      })
      this.sale.tipo = this.type == 'detalle' ? 'DETALLE' : 'LOCAL'
      this.sale.fecha = this.fecha
      this.sale.cliente_id = this.client.id
      this.sale.total = this.total
      this.sale.acuenta = this.monto
      this.sale.saldo = this.porCobrar
      this.sale.estado = this.porCobrar > 0 ? 'POR COBRAR' : 'CANCELADO'
      this.sale.detalles = this.productSales
      this.sale.observacion = this.obs
      console.log(this.sale)
      this.printboleta = ''
      this.$api.post('sale', this.sale).then(res => {
        this.printboleta = res.data
        this.dialogGarantia = true
        this.$q.loading.hide()
        this.consultaVenta(this.type)
        this.saleClear()
        //let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        //myWindow.document.write(res.data);
        //myWindow.document.close();
        //myWindow.focus();
        // setTimeout(function(){
        //myWindow.print();
        //myWindow.close();
        // },500);

        this.$q.notify({
          message: 'Venta exitosa',
          color: 'green',
          position: 'top',
          icon: 'info'
        })
        /*       this.$q.dialog({
                 message:'Desea registrar préstamo o venta de material?',
                 title:'Garantia?',
                 cancel: true,
                 persistent:true
               }).onOk(()=>{
                 this.prestamolista=[]
                 this.newgarantia={}
                 this.newgarantia.cantidad=1,
                 this.newgarantia.efectivo=0,
                 this.newgarantia.tipo='EN PRESTAMO'
                 this.calcular
                 this.modalgarantia=true
               }).onCancel(()=>{
                 this.monto=0
                 this.obs=''
                 this.sale={}
                 this.productSales=[]
                 this.client={label:''}
                 let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
                 myWindow.document.write(this.printboleta);
                 myWindow.document.close();
                 myWindow.focus();
                 myWindow.print();
                 myWindow.close();
               })*/
      })
    },
    garantiaOk() {
      this.prestamolista = []
      this.newgarantia = {}
      this.newgarantia.cantidad = 1,
        this.newgarantia.efectivo = 0,
        this.newgarantia.tipo = 'EN PRESTAMO'
      this.dialogGarantia = false
      this.calcular
      this.modalgarantia = true
    },
    garantiaFin() {
      this.monto = 0
      this.obs = ''
      this.sale = {}
      this.productSales = []
      this.client = {label: ''}
      this.client = {label: ''}
      this.dialogGarantia = false

    },
    garantiaCancel() {
      this.dialogGarantia = false
      this.monto = 0
      this.obs = ''
      this.sale = {}
      this.productSales = []
      this.client = {label: ''}
      let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
      myWindow.document.write(this.printboleta);
      myWindow.document.close();
      myWindow.focus();
      myWindow.print();
      myWindow.close();
    },
    datosGet(type, page = 1) {
      this.products = []
      this.productsAll = []
      this.$q.loading.show()
      this.$api.get(`/listaproducto/${type}`).then(res => {
        res.data.forEach(p => {
          p.cantidad = 0
          this.products.push(p)
          this.productsAll.push(p)
        })

        this.clients = []
        this.$api.get(`/listacliente2/${type}`).then(res => {
          res.data.forEach(c => {
            c.label = c.ci + ' ' + c.local + ' ' + c.titular
            this.clients.push(c)
          })
          this.clients2 = this.clients
        })
        this.$q.loading.hide()
      })
    },
    filterFn(val, update) {
      if (val === '') {
        update(() => {
          this.clients = this.clients2
        })
        return
      }
      update(() => {
        const needle = val.toLowerCase()
        this.clients = this.clients2.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
      })
    },
    saleClear() {
      this.productSales = []
      this.products.forEach(p => {
        p.cantidad = 0
      })
    },
    saleAdd(p) {
      p.cantidad++
      let find = this.productSales.find(ps => ps.id == p.id)
      if (find) {
        find.cantidad++
      } else {
        this.productSales.push({
          id: p.id,
          nombre: p.nombre,
          precio: p.precio,
          cantidad: 1
        })
      }
    },
    agregargarantia() {
      this.monto = 0
      this.obs = ''
      this.sale = {}
      this.productSales = []
      this.$q.loading.show()
      this.$axios.post(process.env.API + '/prestamo', {
        inventario_id: this.inventario.id,
        cantidad: this.newgarantia.cantidad,
        efectivo: this.newgarantia.efectivo,
        fisico: this.newgarantia.fisico,
        observacion: this.newgarantia.observacion,
        cliente_id: this.client.id,
        tipo: this.newgarantia.tipo,
        fecha: date.formatDate(new Date(), 'YYYY-MM-DD')
      }).then((res) => {
        this.prestamolista.push({
          'cantidad': this.newgarantia.cantidad,
          'nombre': this.inventario.nombre,
          'efectivo': this.newgarantia.efectivo,
          'tipo': this.newgarantia.tipo
        })
        //let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        //myWindow.document.write(res.data);
        //myWindow.document.close();
        //myWindow.print();
        //myWindow.close();

        this.$q.loading.hide()
        this.producto = ''
        this.cantidad = 1
        //this.fecha=date.formatDate(new Date(),'YYYY-MM-DD');
        this.newgarantia = {}
        this.calcular
        this.modalgarantia = false
        this.dialogGarantia2 = true
        /*this.$q.dialog({
          message:'Deseas registrar garantia?',
          title:'Garantia?',
          cancel: true,
          persistent:true
        }).onOk(()=>{
          this.newgarantia={}
          this.newgarantia.cantidad=1,
          this.newgarantia.efectivo=0,
          this.newgarantia.tipo='EN PRESTAMO'
          this.modalgarantia=true
        }).onCancel(()=>{
          this.printboleta+="<hr><div><table><tr><th>CANT</th><th>MATERIAL</th><th>MONTO</th><th>TIPO</th></tr>"
          this.prestamolista.forEach(p => {
            this.printboleta+="<tr><td>"+p.cantidad+"</td><td>"+p.nombre+"</td><td>"+p.efectivo+"</td><td>"+p.tipo+"</td></tr>"
          })
          this.printboleta+="</table></div>"
          this.printboleta+="<br><hr><div><b>Cliente </b>"+this.client.local + ' - '+this.client.titular+"</div><div><b>Fecha: </b>"+this.fecha+"</div><div><b>Usuario: </b>"+this.$store.state.login.user.name +"</div><div><table><tr><th>CANT</th><th>MATERIAL</th><th>MONTO</th><th>TIPO</th></tr>"
          this.prestamolista.forEach(p => {
            this.printboleta+="<tr><td>"+p.cantidad+"</td><td>"+p.nombre+"</td><td>"+p.efectivo+"</td><td>"+p.tipo+"</td></tr>"
          })
          this.printboleta+="</table></div><div class='leyenda'><b>* SOLO SE RECIBIRA EL ENVASE SI ESTA LIMPIO Y EN BUEN ESTADO<br>* TIEMPO MAXIMO DE DEVOLUCION 5 DIAS, CASO CONTRARIO SE DARA DE BAJA <br>* HORARIOS LUN - VIER DE 8:30 A 17:00<b></div>"
          let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
          myWindow.document.write(this.printboleta);
          myWindow.document.close();
          myWindow.focus();
          myWindow.print();
          myWindow.close();
          this.monto=0
          this.obs=''
          this.sale={}
          this.productSales=[]
          this.client={label:''}

        })*/
      }).catch(err => {
        this.$q.loading.hide()
        console.log(err)
        this.$q.notify({
          message: err.message.error,
          color: 'red',
          position: 'top',
          icon: 'error'
        })
      })
    },
    garantiaOk2() {
      this.dialogGarantia2 = false

      this.newgarantia = {}
      this.newgarantia.cantidad = 1,
        this.newgarantia.efectivo = 0,
        this.newgarantia.tipo = 'EN PRESTAMO'
      this.modalgarantia = true
    },
    garantiaCancel2() {
      this.dialogGarantia2 = false
      let prt = false
      this.printboleta += "<div><table><tr><th>CANT</th><th>MATERIAL</th><th>MONTO</th><th>TIPO</th></tr>"
      this.prestamolista.forEach(p => {
        if (p.tipo == 'EN PRESTAMO') {
          p.tipo = "PRESTAMO"
          prt = true
        }
        this.printboleta += "<tr><td>" + p.cantidad + "</td><td>" + p.nombre + "</td><td>" + p.efectivo + "</td><td>" + p.tipo + "</td></tr>"
      })
      this.printboleta += "</table></div>"
      this.printboleta += "<hr style=' border: 4px dashed;'><div style='text-align:center'>Detalle de Prestamo o venta de material</div><div><b>Cliente </b>" + this.client.local + ' - ' + this.client.titular + "</div><div><table><tr><th>CANT</th><th>MATERIAL</th><th>MONTO</th><th>TIPO</th></tr>"
      this.prestamolista.forEach(p => {
        if (p.tipo == 'EN PRESTAMO') {
          p.tipo = "PRESTAMO"
          prt = true
        }
        this.printboleta += "<tr><td>" + p.cantidad + "</td><td>" + p.nombre + "</td><td>" + p.efectivo + "</td><td>" + p.tipo + "</td></tr>"
      })
      this.printboleta += "</table></div>"
      if (prt) {
        this.printboleta += "<br><hr style=' border: 4px dashed;'><div style='text-align:center'>copia para archivo</div><div><b>Cliente </b>" + this.client.local + ' - ' + this.client.titular + "</div><div><b>Fecha: </b>" + this.fecha + "</div><div><b>Usuario: </b>" + this.$store.state.login.user.name + "</div><div><table><tr><th>CANT</th><th>MATERIAL</th><th>MONTO</th><th>TIPO</th></tr>"
        this.prestamolista.forEach(p => {
          if (p.tipo == 'EN PRESTAMO') p.tipo = "PRESTAMO"

          this.printboleta += "<tr><td>" + p.cantidad + "</td><td>" + p.nombre + "</td><td>" + p.efectivo + "</td><td>" + p.tipo + "</td></tr>"
        })
        this.printboleta += "</table></div><br><br><div class='leyenda' style='text-align:center'><b>FIRMA</div><div class='leyenda'>* Accepto todas las condiciones y terminos de prestamo de envases <b></div>"
      }
      let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
      myWindow.document.write(this.printboleta);
      myWindow.document.close();
      myWindow.focus();
      myWindow.print();
      myWindow.close();
      this.monto = 0
      this.obs = ''
      this.sale = {}
      this.productSales = []
      this.client = {label: ''}
    },
    garantiaFin2() {
      this.dialogGarantia2 = false
      this.monto = 0
      this.obs = ''
      this.sale = {}
      this.productSales = []
      this.client = {label: ''}
    }
  },
  computed: {
    total() {
      let total = 0
      this.productSales.forEach(p => {
        total += p.precio * p.cantidad
      })
      return total
    },
    porCobrar() {
      return this.total - this.monto
    }
  }
}
</script>

<style scoped>
.no_selection {
  -webkit-user-select: none;
  -moz-user-select: none;
  -khtml-user-select: none;
  -ms-user-select: none;
  cursor: pointer;
}
.botones {
  display: flex;
  justify-content: center;
  gap: 50px; /* espacio entre botones */
}
</style>
