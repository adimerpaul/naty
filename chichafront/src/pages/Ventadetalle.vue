<template>
  <q-page class="q-pa-xs">
    <div class="row">
      <div class="col-12">
        <div class="text-subtitle1 bg-blue-9 text-center text-white">Ventas</div>
      </div>
      <div class="col-12">
        <q-form @submit.prevent="guardar">
          <div class="row">
            <div class="col-6 col-sm-6 q-pa-xs"><q-input dense type="date" label="fecha" v-model="fecha" outlined required/></div>
            <div class="col-6 col-sm-6 q-pa-xs"><q-input dense type="text" label="Responsable" v-model="responsable" outlined required/></div>
          </div>
          <div class="row">
            <div class="col-6 col-sm-2 q-pa-xs">
              <div class="row">
                <div class="col-2">
                  <q-btn @click="modalregistro=true" class="full-width full-height" color="green" icon="add_circle" size="xs" />
                  <q-btn @click="modcliente=model;dialog_mod=true;" class="full-width full-height" color="yellow" icon="edit" size="xs" v-if="model"/>
                </div>
                <div class="col-10">
                  <q-select
                    outlined
                    v-model="model"
                    use-input
                    input-debounce="0"
                    label="Seleccionar Cliente"
                    :options="options"
                    @filter="filterFn"
                    @click="generar"

                  />
                </div>

              </div>

            </div>
            <div class="col-6 col-sm-2 q-pa-xs">
              <q-select  v-model="producto" outlined :options="productos" option-label="nombre" option-value="id" label="Producto" required/>
            </div>
            <div class="col-6 col-sm-1 q-pa-xs">
              <q-input

                type="number"
                step="0.1"
                label="precio"
                v-model="producto.precio"
                outlined
                required />
            </div>
            <div class="col-4 col-sm-1 q-pa-xs">
              <!--            <q-input type="number" label="Cantidad" v-model="cantidad" outlined/>-->
              <q-input  label="Cantidad" type="number" step="0.5" v-model="cantidad" outlined/>
            </div>
            <div class="col-4 col-sm-2 q-pa-xs">
              <!--            <q-input type="number" label="Cantidad" v-model="cantidad" outlined/>-->
                            <q-input

                type="text"
                label="Observacion"
                v-model="observacion"
                outlined
                required
                 />
            </div>
            <!--                    <div class="col-6 col-sm-2 q-pa-xs">-->
            <!--            <q-input-->
            <!--            type="number"-->
            <!--            label="Prestamo"-->
            <!--            v-model="cantidadprestamo"-->
            <!--            outlined-->
            <!--            required -->
            <!--            :rules="[val => val>=0 && val<=cantidad || 'Field is required']"-->
            <!--            />-->
            <!--          </div>-->


            <div class="col-6 col-sm-1 q-pa-xs">
              <!--            <q-input type="text" disable label="Subtotal" label-color="white"  bg-color="positive" v-model="subtotal" outlined/>-->
              <q-badge class="full-width full-height" color="positive">Subtotal <br> {{subtotal}}</q-badge>
            </div>
            <div class="col-6 col-sm-1 q-pa-xs">
              <q-input  type="number" label="A cuenta" step="0.1" v-model="acuenta" outlined required/>
            </div>
            <div class="col-6 col-sm-1 q-pa-xs">
              <!--            <q-input type="text" label="Saldo" v-model="saldo" label-color="white" :bg-color="subtotal>acuenta?'negative':'positive'" disable outlined/>-->
              <q-badge class="full-width full-height" :color="subtotal>acuenta?'red-7':'positive'">Subtotal <br> {{saldo}}</q-badge>
            </div>
            <div class="col-6 col-sm-1 q-pa-xs">
              <!--            <q-input type="text" label="Estado" v-model="estado" label-color="white" :bg-color="subtotal>acuenta?'red-7':'positive'" outlined/>-->
              <q-badge class="full-width full-height" :color="subtotal>acuenta?'red-7':'positive'">Subtotal <br> {{estado}}</q-badge>
            </div>
            <div class="col-12 col-sm-12 q-pa-xs flex flex-center">
              <!--          <div v-if="saldo>0||cantidadprestamo>0" class="row">-->
              <!--          <br>-->
              <!--            <div class="col-6 col-sm-2 q-pa-xs">-->
              <!--            <q-input-->
              <!--            type="number"-->
              <!--            label="Efectivo"-->
              <!--            v-model="garantia.efectivo"-->
              <!--            outlined-->

              <!--            :rules="[val => val>=0  || 'valor min 0']"-->
              <!--            />-->
              <!--          </div>-->

              <!--          <div class="col-6 col-sm-2 q-pa-xs">-->
              <!--            <q-input-->
              <!--            type="text"-->
              <!--            label="Fisico"-->
              <!--            v-model="garantia.fisico"-->
              <!--            outlined            />-->
              <!--          </div>-->

              <!--          <div class="col-6 col-sm-2 q-pa-xs">-->
              <!--            <q-input-->
              <!--            type="text"-->
              <!--            label="Observacion"-->
              <!--            v-model="garantia.observacion"-->
              <!--            outlined            />-->
              <!--          </div>-->
              <!--          </div>-->
              <q-btn color="accent" size="lg"  label="Generar Venta" icon="send" type="submit" />
            </div>
          </div>
        </q-form>
      </div>
      <!--    <div class="col-12">-->
      <!--      <q-table-->
      <!--      :columns="columns"-->
      <!--      :rows="detalles"-->
      <!--      >-->
      <!--        <template v-slot:body-cell-action="props">-->
      <!--          <q-td :props="props">-->
      <!--            <q-btn-->
      <!--              color="negative"-->
      <!--              icon-right="delete"-->
      <!--              no-caps-->
      <!--              flat-->
      <!--              dense-->
      <!--              @click="deleteval(detalles.indexOf(props.row))"-->
      <!--            />-->
      <!--          </q-td>-->
      <!--        </template>-->
      <!--      </q-table>-->
      <!--    </div>-->

      <!--    <div class="col-12">-->
      <!--      <q-form @submit.prevent="guardar">-->
      <!--        <div class="row">-->
      <!--          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" label="Total" v-model="total" disable outlined/></div>-->
      <!--          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" label="Acuenta" v-model="acuenta" outlined/></div>-->
      <!--          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" label="Saldo" v-model="saldo" disable outlined/></div>-->
      <!--          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" label="Estado" v-model="estado" outlined/></div>-->
      <!--          <div class="col-6 col-sm-2 q-pa-xs flex flex-center">-->
      <!--            <q-btn color="positive"  label="Venta" icon="shop" type="submit" />-->
      <!--          </div>-->
      <!--        </div>-->
      <!--      </q-form>-->
      <!--    </div>-->

      <div class="col-12">
        <div class="row">
          <div class="col-12 col-sm-12">
            <div class="row">
              <div class="col-12">
                <div class="text-subtitle1 bg-info text-center text-white">Historial de ventas</div>
              </div>
              <template v-if="$store.state.login.historialventadetalle">
              <div class="col-6 col-sm-4 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha2" outlined required/></div>
              <div class="col-6 col-sm-4 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha3" outlined required/></div>
              <div class="col-6 col-sm-4 q-pa-xs flex flex-center">
                <q-btn color="info"  label="Consultar" icon="search" type="submit" @click="misventas" />
              </div>
              </template>
              <div class="col-12">
                <q-table dense
                  :columns="columns2"
                  :rows="ventas"
                  title="Historial de ventas"
                  :filter="filter"
                >
                  <!--        <template v-slot:top-right>-->
                  <!--          <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">-->
                  <!--            <template v-slot:append>-->
                  <!--              <q-icon name="search" />-->
                  <!--            </template>-->
                  <!--          </q-input>-->
                  <!--        </template>-->
                  <template v-slot:body="props">
                    <q-tr :props="props">
                      <q-td key="fecha" :props="props">
                        {{ props.row.fecha }}
                      </q-td>
                      <q-td key="titular" :props="props">
                        {{ props.row.titular }}
                      </q-td>
                      <q-td key="cantidad" :props="props">
                        {{ props.row.cantidad }}
                      </q-td>
                      <q-td key="producto" :props="props">
                        {{ props.row.producto }}
                      </q-td>
                      <q-td key="total" :props="props">
                        {{ props.row.total }}
                      </q-td>
                      <q-td key="acuenta" :props="props">
                        {{ props.row.acuenta }}
                      </q-td>
                      <q-td key="saldo" :props="props">
                        {{ props.row.saldo }}
                      </q-td>
                      <q-td key="estado" :props="props">
                        <q-badge :color="props.row.estado=='CANCELADO'?'positive':'negative'">{{ props.row.estado }}</q-badge>
                      </q-td>
                      <q-td key="user" :props="props">
                        {{ props.row.user }}
                      </q-td>
                      <q-td key="observacion" :props="props">
                        <q-badge :color="props.row.observacion=='CANCELADO'?'positive':'negative'">{{ props.row.observacion }}</q-badge>
                      </q-td>
                      <q-td key="opcion" :props="props">
                        <q-btn-group v-if="props.row.estado!='ANULADO'" >
                          <q-btn icon="delete" color="red" @click="anular(props.row)" size="xs" v-if="$store.state.login.anularventa"/>
                          <q-btn icon="local_shipping" color="info"   @click="clickhojaruta(props.row)" size="xs" v-if="$store.state.login.ruta"/>
                          <template v-if="$store.state.login.reimpresion" >
                            <q-btn dense icon="print" color="info" v-if="props.row.estado!='ANULADO'" @click="impboleta(props.row)" />
                          </template>
                        </q-btn-group>

                      </q-td>
                    </q-tr>
                  </template>
                </q-table>
              </div>
              <!--
              <div class="col-12">
                <q-form>
                  <div class="row">
                    <div class="col-4 q-pa-md">
                      <q-input type="text" label="Venta total" label-color="positive"  v-model="ventat"  outlined/>
                    </div>
                    <div class="col-4 q-pa-md">
                      <q-input type="text" label="Saldo caja" label-color="info"  v-model="porc"  outlined/>
                    </div>
                    <div class="col-4 q-pa-md">
                      <q-input type="text" label="Por cobrar" label-color="negative"  v-model="saldoc"  outlined/>
                    </div>
                  </div>
                </q-form>
              </div>-->
              <div class="col-12">
                <q-btn label="Imprimir" icon="print" color="info" class="full-width" @click="imprimir"/>
              </div>
            </div>
          </div>
<!--          <div class="col-12 col-sm-6">-->
<!--            <div class="row">-->
<!--              <div class="col-12">-->
<!--                <div class="text-subtitle1 bg-accent text-center text-white">Historial de prestamos</div>-->
<!--              </div>-->
<!--              <div class="col-6 col-sm-4 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha4" outlined required/></div>-->
<!--              <div class="col-6 col-sm-4 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha5" outlined required/></div>-->
<!--              <div class="col-6 col-sm-4 q-pa-xs flex flex-center">-->
<!--                <q-btn color="info"  label="Consultar" icon="search" type="submit" @click="generar" />-->
<!--              </div>-->
<!--              <div class="row">-->
<!--                <q-form-->
<!--                  @submit="regprestamo"-->
<!--                  @reset="onReset"-->
<!--                  class="q-gutter-md"-->
<!--                >-->
<!--                  <div class="row">-->

<!--                    <div class="col-2">-->
<!--                      <q-select v-model="inventario" outlined :options="inventarios" option-label="nombre" option-value="id" label="Material" required/>-->
<!--                    </div>-->
<!--                    <div class="col-2">-->
<!--                      <q-input-->
<!--                        outlined-->
<!--                        type="number"-->
<!--                        v-model="prestamo.cantidad"-->
<!--                        label="Cantidad"-->
<!--                        required-->
<!--                      />-->
<!--                    </div>-->

<!--                    <div class="col-2">-->
<!--                      <q-input-->
<!--                        outlined-->
<!--                        type="text"-->
<!--                        v-model="prestamo.efectivo"-->
<!--                        label="Efectivo"-->
<!--                      />-->
<!--                    </div>-->
<!--                    <div class="col-2">-->
<!--                      <q-input-->
<!--                        outlined-->
<!--                        type="text"-->
<!--                        v-model="prestamo.fisico"-->
<!--                        label="Fisico"-->
<!--                      />-->
<!--                    </div>-->
<!--                    <div class="col-2">-->
<!--                      <q-input-->
<!--                        outlined-->
<!--                        type="text"-->
<!--                        v-model="prestamo.observacion"-->
<!--                        label="Observacion"-->
<!--                      />-->
<!--                    </div>-->
<!--                    <div class="col-2 flex flex-center">-->
<!--                      <q-btn  type="submit" color="primary" icon="send" />-->
<!--                    </div>-->
<!--                  </div>-->
<!--                </q-form>-->
<!--              </div>-->
<!--              <div class="col-12">-->
<!--                <q-table-->
<!--                  :columns="columns3"-->
<!--                  :rows="prestamos"-->
<!--                  title="Historial de Prestamo"-->
<!--                >-->
<!--                  &lt;!&ndash;        <template v-slot:top-right>&ndash;&gt;-->
<!--                  &lt;!&ndash;          <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">&ndash;&gt;-->
<!--                  &lt;!&ndash;            <template v-slot:append>&ndash;&gt;-->
<!--                  &lt;!&ndash;              <q-icon name="search" />&ndash;&gt;-->
<!--                  &lt;!&ndash;            </template>&ndash;&gt;-->
<!--                  &lt;!&ndash;          </q-input>&ndash;&gt;-->
<!--                  &lt;!&ndash;        </template>&ndash;&gt;-->
<!--                  <template v-slot:body-cell-opcion="props" >-->
<!--                    <q-td key="opcion" :props="props" >-->
<!--                      <q-btn dense round flat color="red" @click="devolver1(props)" icon="undo" v-if="props.row.estado=='PRESTAMO'"></q-btn>-->
<!--                    </q-td>-->
<!--                  </template>-->
<!--                </q-table>-->
<!--              </div>-->

<!--              <div class="col-12">-->
<!--                <q-btn label="Imprimir prestamos" icon="print" color="accent" class="full-width" @click="impprestamos"/>-->
<!--              </div>-->
<!--              <q-dialog v-model="dialog_garantia" >-->
<!--                <q-card style="min-width: 350px">-->
<!--                  <q-card-section>-->
<!--                    <div class="text-h6">Devolver Material Prestado</div>-->
<!--                  </q-card-section>-->

<!--                  <q-card-section class="q-pt-none">-->
<!--                    <q-input dense v-model="garantia.observacion" autofocus />-->
<!--                  </q-card-section>-->

<!--                  <q-card-actions align="right" class="text-primary">-->
<!--                    <q-btn flat label="Cancel" v-close-popup />-->
<!--                    <q-btn flat label="Devolver" @click="devolver" />-->
<!--                  </q-card-actions>-->
<!--                </q-card>-->
<!--              </q-dialog>-->
<!--            </div>-->
<!--          </div>-->
        </div>


      </div>

    </div>
    <q-dialog v-model="modalregistro">
      <q-card style="width: 700px;min-width: 80vw">
        <q-card-section ><div class="text-h6">Registro de cliente</div></q-card-section>
        <q-card-section class="q-pt-none">
          <q-form @submit.prevent="agregarcliente">
            <div class="row">
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input outlined label="ci" v-model="newcliente.ci"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input required outlined label="titular" style="text-transform: uppercase" v-model="newcliente.titular"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input  outlined label="telefono" v-model="newcliente.telefono"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input  outlined label="direccion" style="text-transform: uppercase" v-model="newcliente.direccion"/>
              </div>

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

    <q-dialog v-model="dialog_mod">
      <q-card>
        <q-card-section class="bg-green-14 text-white">
          <div class="text-h7">MODIFICAR REGISTRO CLIENTE</div>
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form
            @submit="onMod"
            class="q-gutter-md"  >
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
                    :rules="[ val => (val && val.length > 0) || 'Por favor ingresar dato']"
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
              <q-btn label="Modificar" type="submit" color="positive" icon="add_circle"/>
                <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="modalgarantia">
      <q-card style="width: 700px;min-width: 80vw">
        <q-card-section ><div class="text-h6">Garantia</div></q-card-section>
        <q-card-section class="q-pt-none">
          <q-form @submit.prevent="agregargarantia">
            <div class="row">
<!--              {{model}}-->

              <div class="col-12 col-md-4 q-pa-xs">
                <q-input required type="text" outlined label="Cliente" v-model="model.label" disable/>
              </div>
              <div class="col-12 col-md-4 q-pa-xs">
                <q-input required type="text"  outlined label="telefono" v-model="model.telefono" disable/>
              </div>
              <div class="col-12 col-md-4 q-pa-xs">
                <q-input required type="text" outlined label="direccion" v-model="model.direccion" disable/>
              </div>
              <div class="col-12 col-md-4 q-pa-xs">
                <!--                <q-input required outlined label="ci" v-model="newgarantia.ci"/>-->
                <q-select required outlined label="inventario" v-model="inventario" :options="inventarios" option-label="nombre"/>
              </div>
              <div class="col-12 col-md-4 q-pa-xs">
                <q-input required type="number" outlined label="cantidad" v-model="newgarantia.cantidad"/>
              </div>
              <div class="col-12 col-md-4 q-pa-xs">
                <q-input  outlined type="number" label="efectivo" v-model="newgarantia.efectivo"/>
              </div>
              <div class="col-12 col-md-4 q-pa-xs">
                <q-input  outlined label="fisico" v-model="newgarantia.fisico" style="text-transform: uppercase"/>
              </div>
              <div class="col-12 col-md-4 q-pa-xs">
                <q-input  outlined label="observacion" v-model="newgarantia.observacion" style="text-transform: uppercase"/>
              </div>
              <div class="col-12 col-md-4 q-pa-xs flex flex-center">
                <input type="radio" value="EN PRESTAMO" v-model="newgarantia.tipo"/><b> EN PRESTAMO </b>
                <input type="radio" value="VENTA" v-model="newgarantia.tipo"/><b> VENTA </b>
              </div>
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

    <q-dialog v-model="modalhojaruta">
      <q-card style="width: 700px;min-width: 80vw">
        <q-card-section ><div class="text-h6">Hoja ruta {{venta.titular }} {{venta.direccion }}</div></q-card-section>
        <q-card-section class="q-pt-none">
          <q-form @submit.prevent="actualizarventa">
            <div class="row">
<!--              <div class="col-12 col-md-6 q-pa-xs">-->
<!--                                <q-input required outlined label="cantidad" v-model="venta.cantidad"/>-->
<!--&lt;!&ndash;                <q-select required outlined label="inventario" v-model="inventario" :options="inventarios" option-label="nombre"/>&ndash;&gt;-->
<!--              </div>-->
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input required type="text" style="text-transform: uppercase;" outlined label="turno" v-model="venta.turno"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input required type="text"  style="text-transform: uppercase;" outlined label="hora" v-model="venta.hora"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input  outlined type="text" style="text-transform: uppercase;" label="telefono1" v-model="venta.telefono1"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input  outlined type="text" style="text-transform: uppercase;" label="telefono2" v-model="venta.telefono2"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input  outlined label="direccion" style="text-transform: uppercase;" v-model="venta.direccion"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input required outlined label="observacion" style="text-transform: uppercase;" v-model="venta.observacion"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input required outlined label="envase" style="text-transform: uppercase;" v-model="venta.envase"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input type="date" outlined label="fechaentrega" v-model="venta.fechaentrega"/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input required outlined label="precio" v-model="venta.precio"  disable/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input required outlined label="acuenta" v-model="venta.acuenta" disable/>
              </div>
              <div class="col-12 col-md-6 q-pa-xs">
                <q-input required outlined label="saldo" v-model="venta.saldo" disable/>
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
  </q-page>
</template>

<script>
import {date} from 'quasar'
import { jsPDF } from "jspdf";
import moment from 'moment'

export default {
  name: "Venta",
  data(){
    return{
      modalregistro:false,
      dialog_mod:false,
      fecha:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha2:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha3:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha4:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha5:date.formatDate(new Date(),'YYYY-MM-DD'),
      responsable:'',
      newgarantia:{tipo:'EN PRESTAMO'},
      newcliente:{},
      clientes:[],
      clientes2:[],
      modcliente:{},
      cliente:'',
      productos:[],
      inventarios:[],
      inventario:{},
      producto:'',
      observacion:'NINGUNA',
      cantidad:1,
      cantidades:[],
      acuenta:0,
      detalles:[],
      garantia:{},
      dialog_garantia:false,
      cantidadprestamo:0,
      prestamo:{},
      modalgarantia:false,
      modalhojaruta:false,
      columns:[
        {name:'nombreproducto', label:'Nombre producto', field:'nombreproducto'},
        {name:'precio',label:'Precio',field:'precio'},
        {name:'cantidad',label:'Cantidad',field:'cantidad'},
        {name:'subtotal',label:'Subtotal',field:'subtotal'},
        {name: 'action', label: 'Borrar', field: 'action' }
      ],
      columns2:[
        {name:'fecha',label:'Fecha',field:'fecha'},
        {name:'titular',label:'Cliente',field:'titular'},
        {name:'cantidad',label:'cantidad',field:'cantidad'},
        {name:'producto',label:'Producto',field:'producto'},
        {name:'total',label:'Total',field:'total'},
        {name:'acuenta',label:'A cuenta',field:'acuenta'},
        {name:'saldo',label:'Saldo',field:'saldo'},
        {name:'estado',label:'Estado',field:'estado'},
        {name:'user',label:'Usuario',field:'user'},
        {name:'observacion',label:'Observacion',field:'observacion'},
        {name:'opcion',label:'Opcion',field:'opcion'},
      ],
      columns3:[
        {name:'fecha',label:'fecha',field:'fecha'},
        {name:'nombre',label:'Material',field:'nombre'},
        {name:'efectivo',label:'efectivo',field:'efectivo'},
        {name:'fisico',label:'fisico',field:'fisico'},
        {name:'observacion',label:'observacion',field:'observacion'},
        {name:'estado',label:'Estado',field:'estado'},
        {name:'user',label:'Usuario',field:'name'},
        {name:'opcion',label:'opcion',field:'opcion'},
      ],
      ventas:[],
      respuesta:{},
      venta:{},
      prestamos:[],
      filter:'',
      options:[],
      model:''
    }
  },
  mounted() {

    for (let i=1;i<=500;i++){
      this.cantidades.push(i)
    }
    this.misventas()
    this.misclientes()
    // console.log(this.$store.state.login)

    this.$axios.get(process.env.API+'/listaproducto').then(res=>{
      // this.productos=res.data
      res.data.forEach(r=>{
        if(r.tipo=='DETALLE')
          this.productos.push(r);
      })
      if (this.producto.length>0)
      this.producto=this.productos[0]
    })
    this.$axios.get(process.env.API+'/listainventario').then(res=>{
      this.inventarios=res.data
      this.inventario=res.data[0]
    })

    this.$axios.get(process.env.API+'/listainventario').then(res=>{
      this.inventarios=res.data
      this.inventario=res.data[0]
    })
    // this.$axios.post(process.env.API+'/misventas',{fecha:this.fecha2}).then(res=>{
    //   this.venta=res.data
    //   // console.log(this.venta)
    //   // this.producto=res.data[0]
    // })
    this.$axios.post(process.env.API+'/me').then(res=>{
      // console.log(res.data)
      this.responsable=res.data.name
    })
    // this.responsable=this.$store.getters["login/user"].name

    // this.$axios.post(process.env.API+'/misventas',{fecha:this.fecha2}).then(res=>{
    //   this.venta=res.data
    //   // console.log(this.venta)
    //   // this.producto=res.data[0]
    // })
    // this.responsable=this.$store.getters["login/user"].name
  },
  methods:{
    onMod(){
        this.$q.loading.show();
        this.$axios.put(process.env.API+'/cliente/'+this.modcliente.id,this.modcliente).then(res=>{
         this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Modificado correctamente'
        });
        this.dialog_mod=false;
        this.modcliente={}
        this.misclientes()
       }).catch(err=>{

          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: 'Error al Modificar'
          });
        })
        this.$q.loading.hide();
    },
    misclientes(){
      this.$axios.get(process.env.API+'/listacliente').then(res=>{
        // this.clientes=res.data
        // this.clientes2=res.data
        // this.cliente=res.data[0]
        // console.log(res.data)
        this.clientes=[]
        res.data.forEach(r=>{
          if (r.tipocliente==2){
            r.label=r.ci+' '+r.titular,

            this.clientes.push(r)}
        })
      })
    },
    agregarcliente(){
      this.$q.loading.show()
      this.newcliente.tipocliente=2
      this.$axios.post(process.env.API+'/agregarcliente',this.newcliente).then(()=>{
        this.$q.loading.hide()
        this.misclientes()
        this.modalregistro=false
        this.newcliente={}
      }).catch(err=>{
        this.$q.loading.hide()
        this.$q.notify({
          message:err.response.data.message,
          color:'red',
          position:'center',
          icon:'error'
        })
      })
    },
    actualizarventa(){
      this.$q.loading.show()
      // console.log(this.venta)
      this.$axios.put(process.env.API+'/venta/'+this.venta.id,{
        turno:this.venta.turno,
        hora:this.venta.hora,
        telefono1:this.venta.telefono1,
        telefono2:this.venta.telefono2,
        envase:this.venta.envase,
        direccion:this.venta.direccion,
        // total:this.venta.total,
        acuenta:this.venta.acuenta,
        saldo:this.venta.saldo,
        observacion:this.venta.observacion,
        fechaentrega:this.venta.fechaentrega,
      }).then(res=>{
        // console.log(res.data)
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();

        this.$q.loading.hide()
        // this.misclientes()
        this.misventas()
        this.modalhojaruta=false
        // this.newcliente={}
      })
    },
    agregargarantia(){
      this.$q.loading.show()
      this.newcliente.tipocliente=2
      this.$axios.post(process.env.API+'/prestamo',{
        inventario_id:this.inventario.id,
        cantidad:this.newgarantia.cantidad,
        efectivo:this.newgarantia.efectivo,
        fisico:this.newgarantia.fisico,
        observacion:this.newgarantia.observacion,
        cliente_id:this.model.id,
        tipo:this.newgarantia.tipo,
        fecha:date.formatDate(new Date(),'YYYY-MM-DD')
      }).then((res)=>{
        // console.log(res.data)
        // return false
        /*let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(this.respuesta);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();*/
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
        this.$q.loading.hide()
        this.misclientes()
        this.modalregistro=false
        this.newcliente={}
        this.subtotal=''
        this.acuenta=0
        this.saldo=''
        this.estado=''
        this.model=''
        this.producto=''
        this.cantidad=1
        this.subtotal=0
        this.fecha=date.formatDate(new Date(),'YYYY-MM-DD');
        this.modalgarantia=false
        this.respuesta={}
      }).catch(err=>{
        this.$q.loading.hide()
        this.$q.notify({
          message:err.response.data.message.error,
          color:'red',
          position:'center',
          icon:'error'
        })
      })
    },
        impboleta(detalle)
    {
      console.log(detalle);
      this.$axios.post(process.env.API+'/impresiondetalle/'+detalle.id).then(res=>{
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.focus();
        setTimeout(function(){
          myWindow.print();
          myWindow.close();
          // this.comanda(sale_id);
          //    impAniv(response);
        },500);
      })

    },
    impruta(venta){
      this.$axios.post(process.env.API+'/ruta/'+venta.id).then(res=>{
          // let myWindow = window.open("", "Imprimir", "width=200,height=100");
          // myWindow.document.write(res.data);
          // myWindow.document.close();
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.focus();
        setTimeout(function(){
          myWindow.print();
          myWindow.close();
          // this.comanda(sale_id);
          //    impAniv(response);
        },500);
      })
    },
    clickhojaruta(venta){
      this.venta=venta
      this.modalhojaruta=true
    },
    anular(venta){
      // console.log(venta)
      this.$q.dialog({
        title: 'Anular Venta?',
        message: 'Motivo?',
        cancel: true,
        prompt:{
          model:'',
          type:'text',
        }
      }).onOk((data) => {
      this.$axios.post(process.env.API+'/anular',{id:venta.id,observacion:data})
        .then(res=>{
        this.misventas();

          this.$q.notify({
            message:'Venta Anulado correctamente',
            color:'green',
          position:'center',

            icon:'info'
          })})
      }).onOk(() => {
        // console.log('>>>> second OK catcher')
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    impprestamos(){
      if (this.model==''){
        this.$q.notify({
          message:'<h5>Tienes que seleccionar cliente</h5>',
          color:'red',
          position:'center',
          icon:'error',
          html:true
        })
        return false;
      }
      let mc=this
      console.log(this.model)
      function header(){
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
        doc.setFont(undefined,'bold')
        doc.text(3, 1, 'Historial de prestamos '+ mc.model.label)
        doc.text(5, 1.5,  'DE '+moment(mc.fecha4).format('DD/MM/YYYY')+' AL '+moment(mc.fecha5).format('DD/MM/YYYY'))

        doc.text(1, 3, 'Fecha')
        doc.text(3, 3, 'Material')
        doc.text(5, 3, 'Efectivo')
        doc.text(7, 3, 'Fisico')
        doc.text(9.5, 3, 'Observacion')
        doc.text(13.5, 3, 'Estado')
        doc.text(18.5, 3, 'Usuario')
        doc.setFont(undefined,'normal')
      }
      var doc = new jsPDF('p','cm','letter')
      // console.log(dat);
      doc.setFont("courier");
      doc.setFontSize(9);
      // var x=0,y=
      header()
      // let xx=x
      // let yy=y
      let y=0
      this.prestamos.forEach(r=>{
        // xx+=0.5
        y+=0.5
        doc.text(1, y+3, date.formatDate(r.fecha,'DD/MM/YYYY').toString())
        doc.text(3, y+3, r.nombre.toString())
        doc.text(5, y+3, r.efectivo==null?'':''+r.efectivo)
        doc.text(7, y+3, r.fisico==null?'':''+r.fisico)
        doc.text(9.5, y+3, r.observacion==null?'':''+r.observacion)
        doc.text(13.5, y+3, r.estado.toString())
        doc.text(18.5, y+3, r.name.toString())
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })
      // doc.text(2, y+4, 'Ventas totales: ')
      // doc.text(5, y+4, this.ventat+'Bs')
      // doc.text(7, y+4, 'Por cobrar totales: ')
      // doc.text(11, y+4, this.ventat+'Bs')
      // doc.text(14, y+4, 'Saldo totales: ')
      // doc.text(17, y+4, this.ventat+'Bs')
      // doc.save("Pago"+date.formatDate(Date.now(),'DD-MM-YYYY')+".pdf");
      window.open(doc.output('bloburl'), '_blank');
    },
    imprimir(){


      let mc=this
      function header(){
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
        doc.setFont(undefined,'bold')
        doc.text(5, 1, 'Historial de ventas')

        doc.text(5, 1.5,  'DE '+moment(mc.fecha2).format('DD/MM/YYYY')+' AL '+moment(mc.fecha3).format('DD/MM/YYYY'))
        doc.text(1, 3, 'Total')
        doc.text(3, 3, 'A cuenta')
        doc.text(5, 3, 'Saldo')
        doc.text(7, 3, 'Estado')
        doc.text(11.5, 3, 'Titular')
        doc.text(18.5, 3, 'Usuario')
        doc.setFont(undefined,'normal')
      }
      var doc = new jsPDF('p','cm','letter')
      // console.log(dat);
      doc.setFont("courier");
      doc.setFontSize(9);
      // var x=0,y=
      header()
      // let xx=x
      // let yy=y
      let y=0
      this.ventas.forEach(r=>{
        // xx+=0.5
        y+=0.5
        doc.text(1, y+3, r.total.toString())
        doc.text(3, y+3, r.acuenta.toString())
        doc.text(5, y+3, r.saldo.toString())
        doc.text(7, y+3, r.estado.toString())
        doc.text(9.5, y+3, r.local.toString())
        doc.text(11.5, y+3, r.titular.toString())
        doc.text(18.5, y+3, r.user.toString())
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })
        doc.setFont(undefined,'bold')
      doc.text(2, y+4, 'Ventas totales: ')
        doc.setFont(undefined,'normal')
      doc.text(5, y+4, this.ventat+'Bs')
        doc.setFont(undefined,'bold')
      doc.text(7, y+4, 'Por cobrar totales: ')
        doc.setFont(undefined,'normal')
      doc.text(11, y+4, this.porc+'Bs')
        doc.setFont(undefined,'bold')
      doc.text(14, y+4, 'Saldo totales: ')
        doc.setFont(undefined,'normal')
      doc.text(17, y+4, this.saldoc+'Bs')
      // doc.save("Pago"+date.formatDate(Date.now(),'DD-MM-YYYY')+".pdf");
      window.open(doc.output('bloburl'), '_blank');
    },
    filterFn (val, update) {
      if (val === '') {
        update(() => {
          this.options = this.clientes
          // with Quasar v1.7.4+
          // here you have access to "ref" which
          // is the Vue reference of the QSelect
        })
        return
      }
      update(() => {
        const needle = val.toLowerCase()
        this.options = this.clientes.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
      })
    },
    misventas(){
      this.$q.loading.show()
      this.$axios.post(process.env.API+'/misventas',{fecha1:this.fecha2,fecha2:this.fecha3}).then(res=>{
        // this.ventas=res.data
        // console.log(res.data)
        this.$q.loading.hide()
        this.ventas=[]
        res.data.forEach(r=>{
          if (r.tipo=='DETALLE')
            // console.log(r)
            this.ventas.push({
              id:r.id,
              fecha:r.fecha,
              precio:r.total,
              total:r.total,
              acuenta:r.acuenta,
              saldo:r.saldo,
              estado:r.estado,
              local:r.cliente.local,
              titular:r.cliente.titular,
              direccion:r.direccion,
              envase:r.envase,
              telefono1:r.cliente.telefono,
              fechaentrega:date.formatDate(Date.now(),'YYYY-MM-DD'),
              telefono2:r.telefono2,
              user:r.user.name,
              turno:r.turno,
              hora:r.hora,
              observacion:r.observacion,
              producto:r.detalle.nombreproducto,
              cantidad:r.detalle.cantidad,
            })
        })
        console.log(this.ventas)
      })
    },
    agregar(){
      // console.log('a')
      // this.detalles.push({
      //   nombreproducto:this.producto.nombre,
      //   precio:this.producto.precio,
      //   producto_id:this.producto.id,
      //   cantidad:this.cantidad,
      //   subtotal:this.subtotal,
      // })
    },
    guardar(){
      if (this.model==0){
        this.$q.notify({
          position:'center',
          message:'<h5>Tienes que seleccionar cliente</h5>',
          color:'red',
          spinnerSize:'100px',
          icon:'error',
          html:true

        })
        return false;
      }
      if (this.fecha==undefined){
        this.$q.notify({
          message:'<h5>Tienes que seleccionar fecha</h5>',
          color:'red',
          position:'center',
          html:true,
          icon:'error'
        })
        return false;
      }
      if (this.acuenta>this.subtotal || this.acuenta<0){
        this.$q.notify({
          message:'<h5>Ingrese monto acuenta correcto</h5>',
          color:'red',
          position:'center',
          html:true,
          icon:'error'
        })
        return false;
      }
      // console.log(this.detalles)
      // return  false
      // return false
      this.$q.loading.show()
      // console.log({
      //   nombreproducto:this.producto.nombre,
      //   precio:this.producto.precio,
      //   producto_id:this.producto.id,
      //   cantidad:this.cantidad,
      //   subtotal:this.subtotal,
      // })
      // return false

      this.$axios.post(process.env.API+'/venta',{
        fecha:this.fecha,
        total:this.subtotal,
        acuenta:this.acuenta,
        saldo:this.saldo,
        estado:this.estado,
        observacion:this.observacion,
        tipo:'DETALLE',
        cliente_id:this.model.id,
        detalles:[{
          nombreproducto:this.producto.nombre,
          precio:this.producto.precio,
          producto_id:this.producto.id,
          cantidad:this.cantidad,
          subtotal:this.subtotal,
        }],

      }).then(res=>{
        // console.log(res.data)
        // let myWindow = window.open("", "Imprimir", "width=200,height=100");
        // myWindow.document.write(res.data);
        // myWindow.document.close();
        //this.respuesta=res.data
         let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.focus();
        // setTimeout(function(){
          myWindow.print();
          myWindow.close();
        // },500);

        this.$q.notify({
          message:'Venta exitosa',
          color:'green',
          position:'center',

          icon:'info'
        })
        this.misventas()
        this.$q.dialog({
          message:'Deseas registrar garantia?',
          title:'Garantia?',
          cancel: true,
        }).onOk(()=>{
          this.newgarantia.cantidad=1,
          this.newgarantia.efectivo=0,
          this.modalgarantia=true
        }).onCancel(()=>{
         /* let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
          myWindow.document.write(this.respuesta);
          myWindow.document.close();
          myWindow.focus();
          // setTimeout(function(){
            myWindow.print();
            myWindow.close();*/
          this.subtotal=''
          this.acuenta=0
          this.saldo=''
          this.estado=''
          this.model=''
          this.producto=''
          this.observacion='NINGUNA'
          this.cantidad=1
          this.subtotal=0
          this.respuesta={}
        })
      }).catch(err=>{
        this.$q.loading.hide()
        // console.error(err)
        this.$q.notify({
          message:err.response.data.message,
          color:'red',
          position:'center',

          icon:'error'
        })
      })
    },
    deleteval(index){
      this.detalles.splice(index, 1);
    },
    generar(){
      if (this.model==0){
        this.$q.notify({
          message:'<h5>Tienes que seleccionar cliente</h5>',
          color:'red',
          position:'center',
          html:true,
          icon:'error'
        })
        return false;
      }

      console.log(this.model.id);
      if(this.model!=''){
        this.$axios.post(process.env.API+'/listaprestamo',{fecha1:this.fecha4,fecha2:this.fecha5,cliente_id:this.model.id
        }).then(res=>{
          console.log(res.data)
          this.prestamos=res.data;

        })}
    },
    regprestamo(){

      if(this.model!=''){
        // console.log('a')
        this.prestamo.cliente_id=this.model.id;
        this.prestamo.inventario_id=this.inventario.id;
        this.prestamo.fecha=date.formatDate(new Date(),'YYYY-MM-DD');
        this.$axios.post(process.env.API+'/garantia',this.prestamo).then(res=>{
          console.log(res.data)


          this.$q.notify({
            message:'Prestamo exitosa',
            color:'green',
          position:'center',

            icon:'info'
          })
          this.generar()
        }).catch(err=>{
          this.$q.loading.hide()
          console.error(err)
          this.$q.notify({
            message:err.response.data.message,
            color:'red',
            position:'center',
            icon:'error'
          })
        })
      }else{
        this.$q.notify({
          message:'<h5>Debes seleccionar cliente</h5>',
          color:'red',
          position:'center',
          html:true,

          icon:'error'
        })
      }
    },
    devolver1(props){
      this.garantia=props.row;
      console.log(this.garantia);
      this.dialog_garantia=true;
    },
    devolver(){
      console.log(this.garantia);
      this.$axios.post(process.env.API+'/devolver',this.garantia).then(res=>{
        this.$q.notify({
          message:'Devolucion exitosa',
          color:'green',
          icon:'info'
        })
      })
      this.dialog_garantia=false;

    },
    onReset(){},

  },
  computed:{
    subtotal(){
      if (this.producto.precio!=undefined && this.producto.precio!=NaN){
        return parseFloat(this.producto.precio) * parseFloat(this.cantidad)
      }
      else{
        return 0
      }
    },
    total(){
      let total=0;
      this.detalles.forEach(r=>{
        total+= parseFloat(r.subtotal)
      })
      return total
    },
    estado(){
      if (this.acuenta<this.subtotal)
        return 'POR COBRAR'
      else
        return 'CANCELADO'
    },
    saldo(){
      return this.subtotal-this.acuenta
    },
    ventat(){
      let total=0;
      this.ventas.forEach(r=>{
        total+= parseFloat(r.total)
      })
      return total
    },
    porc(){
      let total=0;
      this.ventas.forEach(r=>{
        total+= parseFloat(r.acuenta)
      })
      return total
    },
    saldoc(){
      let total=0;
      this.ventas.forEach(r=>{
        total+= parseFloat(r.saldo)
      })
      return total
    },

  }
}
</script>

<style scoped>

</style>
