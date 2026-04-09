<template>
  <q-page class="q-pa-xs">
    <div class="row">
      <div class="col-4 flex flex-center">
        <q-btn @click="providerList" v-if="$store.state.login.editalmacen" :loading="loading" label="Registro de Provedor" color="blue-7" no-caps icon="o_assignment_ind"/>
      </div>
      <div class="col-4 flex flex-center">
        <q-btn @click="materialAdd" v-if="$store.state.login.almacenCrearMateriaPrima" :loading="loading" label="Registro de materia prima" color="green-7" no-caps icon="o_shopping_bag"/>
      </div>
      <div class="col-4 flex flex-center">
        <q-btn @click="compraAdd" v-if="$store.state.login.ingresoMaterial" :loading="loading" label="Ingreso de Material Prima" color="cyan-7" no-caps icon="add_circle_outline"/>
      </div>
      <div class="col-12">
        <q-table
          dense
          title="Lista de Materiales"
          :rows="materials"
          :columns="materialColumns"
          :loading="loading"
          :rows-per-page-options="[0]"
          :filter="almacenFilter"
          flat
          row-class="cursor-pointer"
        >
          <template v-slot:top-right>
             <q-btn dense color="green"  label="EXCEL" @click="exportTable" />

            <q-input outlined dense v-model="almacenFilter" debounce="300" placeholder="Buscar...">
              <template v-slot:append>
                <q-icon name="search" class="cursor-pointer"/>
              </template>
            </q-input>
          </template>
          <template v-slot:body-cell-actions="props">
            <q-td :props="props" auto-width>
              <q-btn-dropdown icon="more_vert" no-caps dense color="primary" label="Opciones">
                <q-list>
                  <q-item clickable v-close-popup @click="materialEdit(props.row)">
                    <q-item-section>Editar</q-item-section>
                  </q-item>
                  <q-item clickable v-close-popup @click="materialDelete(props.row)">
                    <q-item-section>Eliminar</q-item-section>
                  </q-item>
                  <q-item clickable v-close-popup @click="reporte(props.row)">
                    <q-item-section>Reporte</q-item-section>
                  </q-item>
                </q-list>
              </q-btn-dropdown>
            </q-td>
          </template>
<!--          <template v-slot:body-cell-estado="props">-->
<!--            <q-td :props="props" auto-width>-->
<!--            </q-td>-->
<!--          </template>-->
          <template v-slot:body-cell-stock="props" >
            <q-td :props="props">
              <q-linear-progress size="18px" rounded :value="calcular(props.row.stock,props.row.min)" :color="calcular(props.row.stock,props.row.min)<1?'red-7':'green-7'" class="full-width">
                <div class="absolute-full flex flex-center">
                  <q-badge color="white" :text-color="calcular(props.row.stock,props.row.min)<1?'red-7':'green-7'" :label="props.row.stock" />
                </div>
              </q-linear-progress>
            </q-td>
          </template>
        </q-table>
      </div>
      <div class="col-3"><q-input dense outlined v-model="fecha3" label="Fecha Ini" type="date"/></div>
      <div class="col-3"><q-input dense outlined v-model="fecha4" label="Fecha fin" type="date"/></div>
      <!--      <div class="col-4"><q-select dense outlined v-model="material3" :options="materiales" label="Material" /></div>-->
      <div class="col-3 flex flex-center"> <q-btn color="info" label="Consultar"  @click="consultmaterial" no-caps/></div>
      <div class="col-3 flex flex-center"> <q-btn color="green" label="EXCEL"  @click="exportTable2" no-caps/></div>
      <div class="col-12">
        <q-table
          dense
          :rows-per-page-options="[0]"
          title="LISTA DE COMPRAS "
          :rows="comptodo"
          :columns="colcompra"
          :filter="filter"
          flat
          row-key="name">

          <template v-slot:body-cell-estado="props" >
            <q-td key="estado" :props="props" >
              <template v-if="$store.state.login.almacenCostoSubtotal">
                <q-badge :color="props.row.estado=='POR PAGAR'?'red':'green'"  >{{props.row.estado}}</q-badge>
              </template>
            </q-td>
          </template>
          <template v-slot:top-right>
            <q-input outlined dense v-model="filter" debounce="300" placeholder="Buscar...">
              <template v-slot:append>
                <q-icon name="search" class="cursor-pointer"/>
              </template>
            </q-input>
          </template>
          <template v-slot:body-cell-costo="props">
            <q-td :props="props">
              <template v-if="$store.state.login.almacenCostoSubtotal">
              <q-badge v-if="$store.state.login.preciounitario" :color="props.row.subtotal>props.row.deuda?'red':'green'"  >{{props.row.costo}}</q-badge>
              </template>
            </q-td>
          </template>
          <template v-slot:body-cell-saldocant="props">
            <q-td :props="props" :style="props.row.saldocant>=1?'font-weight: bold; font-size: 18px;':''" :color="props.row.saldocant>=1?'black':'red'">
              {{ props.row.saldocant }}
            </q-td>
          </template>
          <template v-slot:body-cell-material="props">
            <q-td :props="props" style="font-weight: bold; " >
              {{ props.row.material.nombre }}
            </q-td>
          </template>
          <template v-slot:body-cell-subtotal="props">
            <q-td :props="props">
              <template v-if="$store.state.login.almacenCostoSubtotal">
              <q-badge v-if="$store.state.login.preciounitario" :color="props.row.subtotal>props.row.deuda?'red':'green'"  >{{props.row.subtotal}}</q-badge>
              </template>
            </q-td>
          </template>
          <template v-slot:body-cell-saldopago="props">
            <q-td :props="props">
              <template v-if="$store.state.login.almacenCostoSubtotal">
              {{props.row.saldopago}}
              </template>
            </q-td>
          </template>
          <template v-slot:body-cell-deuda="props">
            <q-td :props="props">
              <template v-if="$store.state.login.almacenCostoSubtotal">
                {{props.row.deuda}}
              </template>
            </q-td>
          </template>
          <template v-slot:body-cell-comentario="props">
            <q-td :props="props">
               <q-btn icon="list" color="indigo"  dense v-if="props.row.comentario!=null && $store.state.login.user.id==1" @click="verObs(props.row.comentario)"  />
            </q-td>
          </template>
          <template v-slot:body-cell-observacion="props">
            <q-td :props="props">
               <q-btn icon="list" color="purple"  dense v-if="props.row.observacion!=null" @click="verObs(props.row.observacion)"/>
            </q-td>
          </template>
          <template v-slot:body-cell-opcion="props" >
            <q-td key="opcion" :props="props" >
              <q-btn dense round flat color="green" icon="paid" v-if="$store.state.login.pagoalmacen && props.row.deuda<props.row.subtotal" @click="pagarcompra(props.row)">
                <q-tooltip>Realizar Pago</q-tooltip>
              </q-btn>
              <q-btn dense round flat color="purple" icon="point_of_sale" v-if="$store.state.login.almacenHistorialPago && props.row.logcompras.length>0" @click="listpago(props.row)">
                <q-tooltip>Ver Pagos</q-tooltip>
              </q-btn>

              <q-btn dense round flat color="teal" icon="o_download" v-if="$store.state.login.egresoMaterial && props.row.retiro<props.row.cantidad" @click="retirarRow(props.row)">
                <q-tooltip>Realizar retiro</q-tooltip>
              </q-btn>

              <q-btn dense round flat color="purple" icon="list" v-if="$store.state.login.egresoMaterial && props.row.recuentos.length>0" @click="listrecuento(props.row)">
                <q-tooltip>Ver Retiros</q-tooltip>
              </q-btn>
              <q-btn dense round flat color="yellow-9" icon="o_edit" v-if="$store.state.login.editalmacen " @click="editcompra(props.row)">
                <q-tooltip>Editar</q-tooltip>
              </q-btn>
              <q-btn dense round flat color="red"  icon="delete" v-if="$store.state.login.editalmacen && props.row.estado=='POR PAGAR'" @click="delcompra(props.row)">
                <q-tooltip>Eliminar</q-tooltip>
              </q-btn>
              <q-btn dense round flat color="indigo"  icon="info" v-if="$store.state.login.user.id==1" @click="comentarioAdmin(props.row)">
                <q-tooltip>Comentario admin</q-tooltip>
              </q-btn>
            </q-td>
          </template>

        </q-table>
      </div>
      <!--
      <div class="col-12">
        <q-table
          dense
          :rows-per-page-options="[0]"
          title="LISTA DE RETIROS "
          :rows="recutodo"
          :columns="colrecuento"
          :filter="filter"
          flat
          row-key="name">
          <template v-slot:top-right>
            <q-input outlined dense v-model="filter" debounce="300" placeholder="Buscar...">
              <template v-slot:append>
                <q-icon name="search" class="cursor-pointer"/>
              </template>
            </q-input>
          </template>
          <template v-slot:body-cell-opcion="props" >
            <q-td key="opcion" :props="props" >
              <q-btn dense round flat color="accent" icon="edit" v-if="$store.state.login.editalmacen" @click="modrecuento(props.row)"/>
              <q-btn dense round flat color="red"  icon="delete" v-if="$store.state.login.editalmacen" @click="delrecuento(props.row)"/>
            </q-td>
          </template>

        </q-table>
      </div>-->
    </div>

<!--    <div class="col-12">-->

<!--    </div>-->
    <q-dialog v-model="providerDialog">
      <q-card style="width: 700px; max-width: 90vw;">
        <q-card-section class="q-pb-none row items-center">
          <div class="text-h6">{{providerOptions === 'create' ? 'Registro de Provedor' : providerOptions === 'list' ? 'Lista de Provedores' : 'Editar Provedor'}}</div>
          <q-space/>
          <q-btn flat round dense icon="close" v-close-popup/>
        </q-card-section>
        <q-card-section>
          <q-form v-if="providerOptions=='create' || providerOptions=='edit'" @submit="providerOptions === 'create' ? providerCreate() : providerUpdate()">
            <div class="row">
              <div class="col-5">
                <q-input outlined dense v-model="provider.nombre" label="Nombre" lazy-rules :rules="[val => !!val || 'Campo requerido']"/>
              </div>
              <div class="col-4">
                <q-input outlined dense v-model="provider.razon" label="Razón Social" lazy-rules :rules="[val => !!val || 'Campo requerido']"/>
              </div>
              <div class="col-3">
                <q-input outlined dense v-model="provider.nit" label="CI/NIT" hint="" />
              </div>
              <div class="col-12">
                <q-input outlined dense v-model="provider.direccion" label="Dirección" hint="" />
              </div>
              <div class="col-6">
                <q-input outlined dense v-model="provider.email" label="Email" hint="" />
              </div>
              <div class="col-6">
                <q-input outlined dense v-model="provider.telefono" label="Teléfono" hint="" />
              </div>
            </div>
            <q-card-section class="row justify-end">
              <q-btn :loading="loading" color="red" no-caps icon="o_delete" label="Cancelar" dense @click="providerOptions = 'list'"/>
              <q-btn :loading="loading" no-caps icon="o_save"  dense type="submit" :label="providerOptions === 'create' ? 'Guardar' : 'Actualizar'" :color="providerOptions === 'create' ? 'green' : 'yellow-7'"/>
            </q-card-section>
          </q-form>
          <q-table :loading="loading" :rows-per-page-options="[0]" dense :rows="providers" v-if="providerOptions === 'list'" :columns="providerColumns" row-key="name">
            <template v-slot:top-right>
              <q-btn color="green" icon="add_circle_outline" label="Nuevo" no-caps @click="providerOptions = 'create'; provider = {}"/>
              <q-input outlined dense v-model="filter" debounce="300" placeholder="Buscar...">
                <template v-slot:append>
                  <q-icon name="search" class="cursor-pointer"/>
                </template>
              </q-input>
            </template>
            <template v-slot:body-cell-actions="props" >
              <q-td key="opcion" :props="props" >
                <q-btn dense round flat color="accent" icon="edit" @click="providerOptions = 'edit'; provider = props.row"/>
                <q-btn dense round flat color="red"  icon="delete" @click="delprovider(props.row)"/>
              </q-td>
            </template>
          </q-table>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="materialDialog">
      <q-card style="width: 500px; max-width: 90vw;">
        <q-card-section class="q-pb-none row items-center">
          <div class="text-h6">{{materialOptions === 'create' ? 'Registro de ' : 'Editar '}} de materia prima</div>
          <q-space/>
          <q-btn flat round dense icon="close" v-close-popup/>
        </q-card-section>
        <q-card-section>
          <q-form @submit="materialOptions === 'create' ? materialCreate() : materialUpdate()">
            <div class="row">
              <div class="col-12">
                <q-input outlined dense v-model="material.nombre" label="Nombre" lazy-rules :rules="[val => !!val || 'Campo requerido']"/>
              </div>
<!--              <div class="col-12">-->
<!--                <q-input outlined dense v-model="material.nombre" label="Nombre" lazy-rules :rules="[val => !!val || 'Campo requerido']"/>-->
<!--              </div>-->
              <div class="col-6">
                <q-select outlined dense v-model="material.unid" label="Unidad" :options="unidades" lazy-rules :rules="[val => !!val || 'Campo requerido']"/>
              </div>
              <div class="col-6">
                <q-input outlined type="number" dense v-model="material.min" label="Minimo" hint="" :rules="[val => !!val || 'Campo requerido']">
                  <q-tooltip>
                      Cantidad minima de material en almacen
                  </q-tooltip>
                </q-input>
              </div>
            </div>
            <q-card-section class="row justify-end">
              <q-btn :loading="loading" color="red" no-caps icon="o_delete" label="Cancelar" dense v-close-popup/>
              <q-btn :loading="loading" color="green" no-caps icon="o_save" label="Guardar" dense type="submit"/>
            </q-card-section>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="dialog_add">
      <q-card style="width: 800px; max-width: 80vw;">
        <q-card-section class="bg-green-14 text-white q-pb-none">
          <div class="text-bold text-center">INGRESO DE MATERIA PRIMA</div>
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <div class="row">
            <div class="col-6">
              <q-select dense outlined v-model="material" :options="materials" label="Material" option-label="nombre" option-value="id" />
            </div>
            <div class="col-6">
              <q-select dense outlined v-model="provider" :options="providers" label="Proveedor" option-label="razon" option-value="id" />
            </div>
            <div class="col-2"><q-input dense outlined type="text" v-model="compra.cantidad" label="Cantidad"/></div>
            <div class="col-2">
              <template v-if="$store.state.login.almacenCostoSubtotal">
                <q-input dense outlined type="number" step="0.01" v-model="compra.costo" label="Costo U" v-if="$store.state.login.preciounitario" />
              </template>
            </div>
            <div class="col-1 flex-center flex text-bold text-center" v-if="$store.state.login.preciounitario">
              <template v-if="$store.state.login.almacenCostoSubtotal">
                Subtotal: {{subTotalNumber(compra.costo,compra.cantidad)}}
              </template>
            </div>
            <div class="col-2"><q-input dense outlined  type="text" v-model="compra.lote"  label="Lote"  /></div>
            <div class="col-3"><q-input dense outlined  type="date" v-model="compra.fechaven"  label="Fecha Vencimiento"  /></div>
            <div class="col-3" v-if="$store.state.login.user.id==1"><q-input dense outlined  type="text"  v-model="compra.comentario" label="Obser Admin" /></div>
            <div class="col-3"><q-input dense outlined  type="text"  v-model="compra.observacion" label="Observacion" /></div>
            <div class="col-12 text-right">
              <q-btn label="Añadir" no-caps  color="green" icon="add_circle" @click="agregarcompra" v-if="compraOptions === 'create'"/>
            </div>
<!--            <div class="col-12">-->
<!--              <pre>{{compra}}</pre>-->
<!--            </div>-->
            <div class="col-12" v-if="compraOptions === 'create'">
                            <q-markup-table>
                            <thead>
                            <tr>
                              <th class="text-left" >NRO</th>
                              <th class="text-left">MATERIAL</th>
                              <th class="text-center">CANTIDAD</th>
                              <th v-if="$store.state.login.preciounitario">COSTO</th>
                              <th v-if="$store.state.login.preciounitario">SUBTOTAL</th>
                              <th class="text-center">LOTE</th>
                              <th class="text-left">FEC VEN</th>
                              <th class="text-left">OBS</th>
                              <th class="text-right">OPCION</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(d,index) in compras " :key="index">
                              <td class="text-left">{{index + 1}}</td>
                              <td class="text-left">{{d.material}}</td>
                              <td class="text-center">{{d.cantidad}}</td>
                              <td>{{d.costo}}</td>
                              <td>{{d.subtotal}}</td>
                              <td class="text-center">{{d.lote}}</td>
                              <td class="text-left">{{d.fechaven}}</td>
                              <td class="text-left">{{d.observacion}}</td>
                              <td class="text-right"> <q-btn flat dense color="red" icon="o_delete" @click="eliminar(index)"/>
                              </td>
                            </tr>
                            </tbody>
                          </q-markup-table>
<!--              <pre>{{compras}}</pre>-->
            </div>
            <div>
            </div>
          </div>
          <div>
            <q-btn :loading="loading" label="REGISTRAR" color="positive" icon="add_circle" @click="regcompra"/>
            <q-btn  :loading="loading" label="Cancelar" icon="delete" color="negative" v-close-popup />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="dialoglistpagos" >
      <q-card>
        <q-card-section class="row items-center">
          <q-table title="pagos" :rows="pagos" :columns="colpagos" row-key="name"  :rows-per-page-options="[0]" dense>
            <template v-slot:body-cell-op="props" >
              <q-td key="op" :props="props" >
                <q-btn dense round flat color="info"  icon="print" @click="printpago(props.row)"  />
                <q-btn dense round flat color="red"  icon="delete" @click="anularpago(props.row)"  />
              </q-td>
            </template>
<!--          <pre>{{pagos}}</pre>-->
</q-table>

        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="dialoglistretiros" full-width>
      <q-card>
        <q-card-section class="row items-center">
          <q-table :title="'RETIROS TOTAL: ' + totalRetiro" :rows="retiros" :columns="colretiros" row-key="name" flat :rows-per-page-options="[0]" >
            <template v-slot:body-cell-op="props" >
              <q-td key="op" :props="props" >
                <q-btn dense round flat color="red"  icon="delete"  v-if="$store.state.login.almacenHistorialPago" @click="delrecuento(props.row)"/>
              </q-td>
            </template>
          </q-table>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancelar" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-dialog v-model="dialogpagar">
      <q-card style="width: 700px; max-width: 90vw;">
        <q-card-section class="bg-green-14 text-white">
          <div class="text-h7 text-bold text-center">PAGAR POR LA COMPRA</div>
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form @submit="regpago" class="q-gutter-md" >
            <div class="row">
              <div v-if="$store.state.login.user.id==1">
              <div class="col-5">
                <q-input dense outlined v-model="compra2.provider.razon" readonly label="proveedor"/>
              </div>
              <div class="col-5">
                <q-input dense outlined v-model="compra2.material.nombre" readonly label="material"/>
              </div>
              <div class="col-2">
                <q-input dense outlined v-model="compra2.lote" readonly label="lote"/>
              </div>
              <div class="col-4">
                <q-input dense outlined v-model="compra2.cantidad" readonly label="cantidad"/>
              </div>
              <div class="col-4">
                <q-input dense outlined v-model="compra2.costo" readonly label="costo"/>
              </div>
              <div class="col-4">
                <q-input dense outlined v-model="compra2.subtotal" readonly label="subtotal"/>
              </div>
            </div>
              <div class="col-6">
                <q-input dense outlined type="number" v-model="pago.monto" label="Monto" step="0.01"
                         lazy-rules
                         :rules="[ val => val && val > 0 && val <=(compra2.subtotal - compra2.deuda) || 'ingrese otro monto']"
                />
              </div>
              <div class="col-6">
                <q-input dense outlined type="text" v-model="pago.observacion" label="Observacion"/>
              </div>
            </div>
            <div class="col-12">
              <q-btn label="Registrar" type="submit" color="positive" icon="add_circle"/>
              <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
            </div>
<!--            <pre>{{compra2}}</pre>-->
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="dialog_reporte" >
      <q-card>
        <q-card-section class="bg-cyan-14 text-white">
          <div class="text-h7">GENERAR REPORTE</div>
        </q-card-section>
        <q-card-section class="row items-center">
          <q-input dense type="date" outlined v-model="fecha1" label="Fecha Inicio" />
          <q-input dense type="date" outlined v-model="fecha2" label="Fecha Fin" />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="GENERAR" color="deep-orange" @click="genreporte"/>
          <q-btn flat label="Cancelar" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
    <q-dialog v-model="dialog_remove">
      <q-card style="width: 350px; max-width: 90vw;">
        <q-card-section class="bg-green-8 text-white text-bold">
          <div class="text-h7">RETIRAR MATERIAL </div>
          <div class="text-h7">Material: {{material2.nombre}}</div>
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form             @submit="onRegRecuento2"             class="q-gutter-md"          >
            <q-input outlined type="text" v-model="recuento.cantidad" label="Cantidad"/>
            <q-input outlined  type="text"  v-model="recuento.observacion" label="Observacion" />
            <div>
              <q-btn label="Modificar" type="submit" color="positive" icon="add_circle"/>
              <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
    <q-dialog v-model="dialog_modret">
      <q-card>
        <q-card-section class="bg-green-14 text-white">
          <div class="text-h7">Modificar Retiro material : {{recuento2.material.nombre}}</div>
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form             @submit="updateretiro"             class="q-gutter-md"          >
            <q-input outlined type="text" v-model="recuento2.cantidad" label="Cantidad"/>
            <q-input outlined  type="text"  v-model="recuento2.observacion" label="Observacion" />
            <div>
              <q-btn label="Modificar" type="submit" color="positive" icon="add_circle"/>
              <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>
<script>
import {date} from "quasar";
import moment from "moment/moment";
import {jsPDF} from "jspdf";
import { Console } from 'console';
import xlsx from "json-as-xlsx"

export default {
  data () {
    return {
      providerColumns: [
        { name: 'razon', label: 'Razon Social', field: 'razon', align: 'left', sortable: true },
        { name: 'nombre', label: 'Nombre', field: 'nombre', align: 'left', sortable: true },
        { name: 'nit', label: 'NIT', field: 'nit', align: 'left', sortable: true },
        { name: 'telefono', label: 'Telefono', field: 'telefono', align: 'left', sortable: true },
        { name: 'direccion', label: 'Direccion', field: 'direccion', align: 'left', sortable: true },
        { name: 'email', label: 'Email', field: 'email', align: 'left', sortable: true },
        { name: 'actions', label: 'Acciones', field: 'actions', align: 'center' }
      ],
      dialog_modret:false,
      dialog_remove: false,
      providers: [],
      provider: {},
      dialogpagar: false,
      dialog_reporte: false,
      filter: '',
      compraOptions: 'create',
      pago:{},
      recuento:{},
      recuento2:{},
      pagos:[],
      compra2:{},
      providerOptions: 'create',
      providerDialog: false,
      loading: false,
      almacenFilter: '',
      materials: [],
      material: '',
      dialog_add: false,
      //inicio de anio
      fecha3:moment().subtract(1, 'year').format('YYYY-MM-DD'),
      fecha4:moment().format('YYYY-MM-DD'),
      fecha1:date.formatDate( Date.now(),'YYYY-MM-DD'),
      fecha2:date.formatDate( Date.now(),'YYYY-MM-DD'),
      unidades: ['Kilogramos', 'Litros', 'Unidad', 'Metros', 'Metros Cuadrados', 'Metros Cubicos', 'Caja', 'Bolsa', 'Paquete','Quintal','Lata', 'Otro'],
      materialDialog: false,
      compra: {
        cantidad: 0,
        costo: 0,
        lote: '',
        fechaven: '',
        observacion: ''
      },
      comp:{},
      comptodo:[],
      excelcompra:[],
      colrecuento : [

        { name: 'opcion', label: 'OPCIONES', field: 'opcion' },
        { name: 'fecha', align: 'center', label: 'FECHA', field: 'fecha', sortable: true },
        { name: 'cantidad', align: 'center', label: 'CANTIDAD', field: 'cantidad', sortable: true },
        { name: 'material', align: 'center', label: 'MATERIAL', field: row=>row.material.nombre, sortable: true },
        { name: 'observacion', align: 'center', label: 'OBSERVACION', field: 'observacion', sortable: true },
      ],
      colcompra : [
      ],
      colpagos : [

        { name: 'op', align: 'center', label: 'OP', field: 'op', sortable: true },
        { name: 'fecha', align: 'center', label: 'FECHA', field: row=>moment(row.fecha).format('DD/MM/YYYY'), sortable: true },
        { name: 'monto', align: 'center', label: 'MONTO', field: 'monto', sortable: true },
        { name: 'observacion', align: 'center', label: 'OBSERVACION', field: 'observacion', sortable: true },
      ],
      colretiros : [

      { name: 'op', align: 'center', label: 'OP', field: 'op' },
      { name: 'fecha', align: 'center', label: 'FECHA', field: row=>moment(row.fecha).format('DD/MM/YYYY'), sortable: true },
      { name: 'cantidad', align: 'center', label: 'CANTIDAD', field: 'cantidad', sortable: true },
      { name: 'observacion', align: 'center', label: 'OBSERVACION', field: 'observacion', sortable: true },
      ],
      recutodo:[],
      compras: [],
      retiros:[],
      material2: {},
      materialOptions: 'create',
      materialColumns: [
        { name: 'nombre', label: 'Material', align: 'left', field: 'nombre', sortable: true },
        { name: 'unid', label: 'Unidad', align: 'left', field: 'unid', sortable: true },
        { name: 'stock', label: 'Stock', align: 'center', field: 'stock', sortable: true },
        { name: 'actions', label: 'Acciones', align: 'center', field: 'actions', sortable: true },
        { name: 'min', label: 'Minimo', align: 'center', field: 'min', sortable: true }
      ],
      dialoglistpagos:false,
      dialoglistretiros:false,
    }
  },
  created() {
    const user = JSON.parse(localStorage.getItem('userchi'))
    // console.log(user)
    this.colcompra = [
      { name: 'opcion', label: 'OPCIONES', field: 'opcion' },
      { name: 'material', align: 'center', label: 'MATERIAL', field: row=>row.material.nombre, sortable: true },
      { name: 'provider', align: 'center', label: 'PROVEEDOR', field: row=>row.provider.razon, sortable: true },
      // { name: 'id', align: 'center', label: 'ID', field: 'id', sortable: true },
      // { name: 'estado', align: 'center', label: 'ESTADO', field: 'estado', sortable: true },
      { name: 'fecha', align: 'center', label: 'FECHA', field: row=>moment(row.fecha).format('DD/MM/YYYY'), sortable: true },
      { name: 'cantidad', align: 'center', label: 'CANTIDAD', field: row=> row.cantidad+' - '+ row.tretiro, sortable: true },
      { name: 'saldocant', align: 'center', label: 'SALDO CANT', field: 'saldocant', sortable: true },
      // { name: 'costo', align: 'center', label: 'COSTO', field: 'costo', sortable: true },
      // { name: 'subtotal', align: 'center', label: 'SUBTOTAL', field: 'subtotal', sortable: true },
      // { name: 'saldopago', align: 'center', label: 'SALDO PAGO', field: 'saldopago', sortable: true },
      { name: 'lote', align: 'center', label: 'LOTE', field: 'lote', sortable: true },
      { name: 'fechaven', align: 'center', label: 'FECHA VEN', field: row=>moment(row.fechaven).format('DD/MM/YYYY'), sortable: true },
      // { name: 'comentario', align: 'center', label: 'COMENTARIO', field: 'comentario', sortable: true },
      { name: 'observacion', align: 'center', label: 'OBSERVACION', field: 'observacion', sortable: true }
    ]
    if (user.id==1){
      this.colcompra.push({ name: 'observacionAdmin', align: 'center', label: 'OBSERVACION ADMIN', field: 'observacionAdmin', sortable: true })
    }
    this.providersGet()
    this.materialsGet()
    // this.fecha3=this.principioMesYmd()
    this.consultmaterial()
  },
  methods: {
   verObs(cadena){
    this.$q.dialog({
        title: 'Observacion',
        message: cadena
      }).onOk(() => {
        // console.log('OK')
      }).onCancel(() => {
        // console.log('Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
   },
    exportTable () {
      let datacaja = [
        {
          sheet: "Almacen",
          columns: [
            { label: "Material", value: 'nombre' }, // Top level data
            { label: "Cantidad", value: "stock" }, // Top level data
          ],
          content: this.materials
        },

      ]

      let settings = {
        fileName: "Almacen", // Name of the resulting spreadsheet
        extraLength: 7, // A bigger number means that columns will be wider
        writeOptions: {}, // Style options from https://github.com/SheetJS/sheetjs#writing-options
      }
      xlsx(datacaja, settings) // Will download the excel file
      },
      exportTable2 () {
      let datacaja = [
        {
          sheet: "Compras",
          columns: [
            { label: "id", value: "id" }, // Top level data
            { label: "Material", value: row=>row.material.nombre }, // Top level data
            { label: "Proveedor", value: row=>row.provider.razon}, // Top level data
            { label: "Lote", value: "lote" }, // Top level data
            { label: "Cantidad", value: "cantidad" }, // Top level data
            { label: "Saldo", value: "saldocant" }, // Top level data
          ],
          content: this.excelcompra
        },

      ]

      let settings = {
        fileName: "Compras", // Name of the resulting spreadsheet
        extraLength: 7, // A bigger number means that columns will be wider
        writeOptions: {}, // Style options from https://github.com/SheetJS/sheetjs#writing-options
      }
      xlsx(datacaja, settings) // Will download the excel file
      },

    delrecuento(recuento){
      //console.log(recuento)
      this.$q.dialog({
        title: 'ADVERTENCIA',
        message: 'Esta seguro de eliminar registro?',
        cancel: true,
        persistent: false
      }).onOk(() => {
        // console.log('>>>> OK')
        this.$axios.delete(process.env.API+'/recuento/'+recuento.id).then(res=>{
          this.dialoglistretiros=false
          this.materialsGet()
          this.consultmaterial()
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'info',
            message: 'Eliminado ',
            position: 'top'
          });
        })
      }).onOk(() => {
        // console.log('>>>> second OK catcher')
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })

    },
    modrecuento(recuento){
      this.recuento2=recuento;
      this.dialog_modret=true

    },
    updateretiro(){
      this.$axios.put(process.env.API+'/recuento/'+this.recuento2.id,this.recuento2).then(res=>{
        this.materialsGet()
        this.consultmaterial()
        this.dialog_modret=false
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'info',
          message: 'modificado ',
          position: 'top'
        });
      })

    },
    onRegRecuento2(){
      if((this.comp.cantidad - this.comp.retiro) < this.recuento.cantidad){
        this.$q.notify({
          color: 'negative',
          textColor: 'white',
          icon: 'report_problem',
          message: 'No se puede retirar mas de lo que hay en registro',
          position: 'top'
        })
        return false
      }
      this.$q.loading.show()

      this.recuento.material_id=this.material2.id
      this.recuento.compra_id=this.comp.id
      this.recuento.tipo='RETIRAR'
      this.recuento.user_id=this.$store.getters["login/user"].id
      console.log(this.recuento)
      this.$api.post(process.env.API+'/recuento',this.recuento).then(res=>{
        this.recuento={}
        this.dialog_remove=false
        this.materialsGet()
        this.consultmaterial()
      this.$q.loading.hide()

      })
    },
    principioMesYmd(){
      let date = moment().startOf('month').format('YYYY-MM-DD')
      return date
    },
    reporte(props){
      this.material2=props
      this.recuento.material_id=props.id
      this.recuento.cantidad=''
      this.recuento.observacion=''
      this.dialog_reporte=true
    },
    genreporte(){
      //console.log(date.formatDate(new Date(),'DD/MM/YYYY HH:mm:ss'))
      this.$api.post(process.env.API+'/repalmacen',{id:this.material2.id,fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
        let mc=this
       /*console.log(res.data)

        function header(){
          var img = new Image()
          img.src = 'logo.png'
          doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
          doc.setFont(undefined,'bold')
          doc.text(5, 1, 'INVENTARIO MATERIAL : ' + mc.material2.nombre)
          doc.text(5, 1.5,  'DE '+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY'))
          doc.text(1, 3, 'TIPO')
          doc.text(2.5, 3, 'PROVEEDOR')
          doc.text(5, 3, 'MATERIAL')
          doc.text(7, 3, 'FECHA')
          doc.text(9.5, 3, 'COSTO')
          doc.text(11, 3, 'CANTIDAD')
          doc.text(13, 3, 'FECHA VEN')
          doc.text(15, 3, 'OBSERVACION')
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
        res.data.forEach(r=>{
          y+=0.5
          doc.text(1, y+3, r.tipo)
          doc.text(2.5, y+3, r.razon)
          doc.text(5, y+3, r.nombre)
          doc.text(7, y+3, date.formatDate(new Date(r.fecha),'DD/MM/YYYY'))
          doc.text(9.5, y+3, r.costo==null?'':r.costo+'')
          doc.text(11, y+3, r.cantidad+'')
          doc.text(13, y+3, r.fechaven==null?'':r.fechaven)
          doc.text(15, y+3, r.observacion==null?'':r.observacion)
          if (y+3>25){
            doc.addPage();
            header()
            y=0
          }
        })*/
        let fec1=mc.fecha1
        let fec2=mc.fecha2
        this.fecha1=date.formatDate( Date.now(),'YYYY-MM-DD')
        this.fecha2=date.formatDate( Date.now(),'YYYY-MM-DD')
        let cadena="<style>\
        *{font-size:10px}\
        table{width:100%;border-collapse: collapse;}\
        table, th, td {  border: 1px solid;}\
        .titulo1{text-align:center; font-weight: bold;}\
        </style>\
        <div><img src='logo.png' style='width:150px; height:75px;'></div>\
        <div class='titulo1'>INVENTARIO MATERIAL :  "+ mc.material2.nombre +"<br>DE "+moment(fec1).format('DD/MM/YYYY')+' AL '+moment(fec2).format('DD/MM/YYYY')+"</div><br>\
        <table>\
        <tr><th>TIPO</th><th>PROVEEDOR</th><th>MATERIAL</th><th>FECHA</th><th>COSTO</th><th>CANTIDAD</th><th>FECHA VEN</th><th>OBSERVACION</th></tr>"
        res.data.forEach(r=>{
          cadena+="<tr><td>"+r.tipo+"</td><td>"+ r.razon+"</td><td>"+r.nombre+"</td><td>"+moment(r.fecha).format('DD/MM/YYYY')+"</td><td>"+r.costo+"</td><td>"+r.cantidad+"</td><td>"+r.fechaven+"</td><td>"+r.observacion+"</td></tr>"
        })
        //window.open(doc.output('bloburl'), '_blank');
        let myWindow = window.open("_blank");
        myWindow.document.write(cadena);
        myWindow.document.close();
      })

    },
    editcompra(compra){
      // console.log(compra)
      this.dialog_add = true
      this.compra=compra
      this.material=compra.material
      this.provider=compra.provider
      this.compraOptions = 'edit'

    },
    regpago(){
      //if(this.$store.state.login.user.id==1){
        this.checkgasto='GASTO'
      //}
      //else{this.checkgasto='CAJA'}
      if(this.montogeneral<this.pago.monto && this.checkgasto=='GASTO'){
        this.$q.notify({
          color: 'red',
          icon: 'info',
          message: 'No ay suficiente en Cja General '
        });
        return false
      }

      if(this.montocaja<this.pago.monto && this.checkgasto=='CAJA'){
        this.$q.notify({
          color: 'red',
          icon: 'info',
          message: 'No ay suficiente en Cja Chica '
        });
        return false
      }
      this.$q.loading.show()

      this.pago.compra_id=this.compra2.id
      this.pago.checktipo=this.checkgasto
      this.$api.post(process.env.API + "/logcompra",this.pago).then((res) => {
        console.log(res.data)
      this.$q.loading.hide()
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000")
        myWindow.document.write(res.data)
        myWindow.document.close()
        myWindow.print()
        myWindow.close()
        this.consultmaterial()
        this.dialogpagar=false

        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'info',
          message: 'registrado '
        })
      })
    },
    onReg(){
      this.$api.post(process.env.API+'/provider',this.proveedor).then(res=>{
        this.dialog_prov=false
        this.misproveedores()
        this.provider={}
      })

    },
    listpago(compra){
      this.pagos=compra.logcompras
      this.dialoglistpagos=true
    },
    listrecuento(compra){
      this.retiros=compra.recuentos
      console.log(this.retiros)
      this.dialoglistretiros=true
    },
    retirarRow(props) {
      console.log(props.material)
      this.material2=props.material
      this.comp=props
      this.dialog_remove=true
    },
    comentarioAdmin(compra){
      this.$q.dialog({
        title: 'Observacion Admin',
        // message: compra.observacionAdmin,
        prompt: {
          model: compra.observacionAdmin,
          type: 'text',
        }
      }).onOk((data) => {
        this.$api.post(process.env.API+'/observacionAdmin',{
          id:compra.id,
          observacionAdmin:data
        }).then(res=>{
          this.materialsGet()
          this.consultmaterial()
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'info',
            message: 'modificado '
          })
        })
      })
    },
    delcompra(compra){
      //console.log(compra)
      this.$q.dialog({
        title: 'ADVERTENCIA',
        message: 'Esta seguro de eliminar registro?',
        cancel: true,
        persistent: false
      }).onOk(() => {
        // console.log('>>>> OK')
        this.$api.delete(process.env.API+'/compra/'+compra.id).then(res=>{
          this.materialsGet()
          this.consultmaterial()
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'info',
            message: 'Eliminado '
          })
        }).catch(err=>{
          console.log(err.response)
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: err.response.data.error
        });
        })
      }).onOk(() => {
        // console.log('>>>> second OK catcher')
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })

    },
    pagarcompra(compra){
      this.compra2=compra
      this.pago={}
     // if(this.$store.state.login.user.id==1){
        this.checkgasto='GASTO'
     // }
     // else{this.checkgasto='CAJA'}

      this.dialogpagar=true

    },
    consultmaterial(){
      this.comptodo=[]
      this.excelcompra=[]
      this.$api.post(process.env.API+'/consultar2',{fecha1:this.fecha3,fecha2:this.fecha4}).then(res=>{
        res.data.forEach(r => {
          r.saldocant = r.cantidad - r.retiro
          r.saldopago = r.subtotal - r.deuda
          r.tretiro=this.calculoRetiro(r.recuentos)
          this.comptodo.push(r)
          if(r.saldocant>0)
            this.excelcompra.push(r)
        });
        // console.log(res.data)
      })

      this.$api.post(process.env.API+'/consulrecuento2',{fecha1:this.fecha3,fecha2:this.fecha4}).then(res=>{
        this.recutodo=res.data
      })
    },
    calculoRetiro(ret){
      let res=0
      ret.forEach(element => {
        res+=parseInt(element.cantidad)
      })
      return res
    },
    subTotalNumber(costo,cantidad){
      if(costo==undefined || costo=='' || costo==0) return 0
      if(cantidad==undefined || cantidad=='' || cantidad==0) return 0
      return parseFloat(costo) * parseFloat(cantidad)
    },
    eliminar(index){
      this.compras.splice(this.compras.indexOf(index),1);
    },
    agregarcompra(){
      if(this.compra.cantidad=='' || this.compra.cantidad==0 || this.compra.cantidad==undefined){
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'warning',
          message: 'Ingrese la cantidad',
          position: 'top'
        })
        return false
      }
      // if(this.compra.costo==undefined || this.compra.costo=='' || this.compra.costo==0) return false
      if(this.compra.fechaven==undefined || this.compra.fechaven=='') this.compra.fechaven=''
      if(this.compra.observacion==undefined || this.compra.observacion=='') this.compra.observacion=''
      if(this.compra.lote==undefined || this.compra.lote=='') this.compra.lote=''
      this.compra.material_id=this.material.id
      this.compra.costo=this.compra.costo=='' || this.compra.costo==undefined?0:this.compra.costo
      this.compra.subtotal=parseFloat(this.compra.cantidad) * parseFloat(this.compra.costo)
      this.compra.material=this.material.nombre
      if (this.provider.id== undefined || this.provider.id=='' || this.provider.id==0){
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'info',
          message: 'Debe seleccionar proveedor'
        });
        return false
      }
      this.compra.provider_id=this.provider.id
      this.compras.push(this.compra)
      this.compra={
        cantidad: '',
        costo: '',
        lote: '',
        fechaven: '2023-01-01',
        observacion: ''
      }
    },
    regcompra(){
      if (this.compraOptions=='create'){
        if(this.compras.length==0){
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'info',
            message: 'Debe registrar '
          });
          return false
        }
        this.totalgeneral();
        this.totalcompra=0
        this.compras.forEach(r => {
          this.totalcompra+=r.subtotal
        });
        if(parseFloat(this.montogeneral) < parseFloat(this.totalcompra)){
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'info',
            message: 'No puede exeder monto'
          });
          return false
        }
        this.loading=true
        this.$api.post(process.env.API+'/compra2',{
          // provider_id:this.proveedor.id,
          user_id:this.$store.getters["login/user"].id,
          compras:this.compras}).then(res=>{
          //console.log(res.data)
          this.dialog_add=false
          this.materialsGet();
          this.consultmaterial();
          this.loading=false
        })
        ///////al modificar
      }else{
        if(parseFloat(this.compra.cantidad) <  parseFloat(this.compra.retiro)){
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'info',
            message: 'la cantidad No puede ser menor al retiro'
          })
          return false
        }
        if((parseFloat(this.compra.cantidad) * parseFloat(this.compra.costo)) < parseFloat(this.compra.deuda)){
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'info',
            message: 'la subtotal No puede ser menor a lo pagado'
          })
          return false
        }
        this.loading=true
        this.compra.material_id=this.material.id
        this.compra.provider_id=this.provider.id
        this.$q.dialog({
        dark: true,
        title: 'Confirmar',
        message: 'Esta seguro de Modificar?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        // console.log('>>>> OK')
        this.$api.post(process.env.API+'/compraModificar',this.compra).then(res=>{
          this.dialog_add=false
          this.materialsGet();
          this.consultmaterial()
          this.loading=false
        })
      }).onOk(() => {
        // console.log('>>>> second OK catcher')
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })

      }

    },
    totalgeneral(){
      this.$api.post(process.env.API + "/totalgeneral").then((res) => {
        this.montogeneral=parseFloat( res.data.monto);
      })
    },
    calcular(stock,min){
      let porcentaje=((stock/min*100)/100)
      if (porcentaje>1){
        return 1
      }else{
        return porcentaje
      }
    },
    compraAdd() {
      this.compraOptions = 'create'
      this.dialog_add = true
      this.compras=[]
      this.compra={
        cantidad: '',
        costo: '',
        lote: '',
        fechaven: '2023-01-01',
        observacion: ''
      }
      this.material=''
      this.provider=''
    },
    materialAdd() {
      this.material = {}
      this.materialDialog = true
      this.materialOptions = 'create'
    },
    providerList() {
      this.providerDialog = true
      this.providerOptions = 'list'
    },
    providerAdd() {
      this.provider = {}
      this.providerDialog = true
      this.providerOptions = 'create'
    },
    providersGet() {
      this.$api.get('provider').then(res => {
          this.providers = res.data
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        this.loading = false
      })
    },
    providerUpdate() {
      this.loading = true
      this.$api.put('provider/' + this.provider.id, this.provider).then(res => {
        this.providerOptions = 'list'
        this.providersGet()
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        // this.loading = false
      })
    },
    providerCreate() {
      this.loading = true
      this.$api.post('provider', this.provider).then(res => {
        this.providerOptions = 'list'
        this.providersGet()
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        // this.loading = false
      })
    },
    materialUpdate() {
      this.loading = true
      this.$api.put('material/' + this.material.id, this.material).then(res => {
        this.materialDialog = false
        this.materialsGet()
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        this.loading = false
      })
    },
    delprovider(provider) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea eliminar el proveedor?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.delete('provider/' + provider.id).then(res => {
          this.providersGet()
        }).catch(e => {
          this.$q.notify({
            color: 'red',
            textColor: 'white',
            icon: 'warning',
            message: 'No se puede eliminar el proveedor, tiene registros asociados',
            position: 'top'
          })
        }).finally(() => {
          this.loading = false
        })
      })
    },
    materialCreate() {
      this.loading = true
      this.$api.post('material', this.material).then(res => {
        this.materialDialog = false
        this.materialsGet()
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        this.loading = false
      })
    },
    materialEdit(material) {
      this.material = material
      this.materialDialog = true
      this.materialOptions = 'update'
    },
    materialDelete(material) {
      this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea eliminar el material?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.delete('material/' + material.id).then(res => {
          this.materialsGet()
        }).catch(e => {
          this.$q.notify({
            color: 'red',
            textColor: 'white',
            icon: 'info',
            message: 'No se puede eliminar el material, ya que esta siendo utilizado',
            position: 'top'
          });
        }).finally(() => {
          this.loading = false
        })
      })
    },
    materialsGet() {
      this.loading = true
      this.$api.get('material').then(res => {
          this.materials = res.data
        console.log(this.materials)
      }).catch(e => {
        console.log(e)
      }).finally(() => {
        this.loading = false
      })
    },
    printpago(pago){
      this.$api.get('impPagoAlmacen/'+ pago.id).then(res => {
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
      })

    },
  anularpago(pago){
    this.$q.dialog({
        title: 'Confirmar',
        message: '¿Desea eliminar el pago?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.loading = true
        this.$api.post('anularLogcompra', pago).then(res => {
          this.dialoglistpagos=false
          this.consultmaterial()
        }).catch(e => {
          this.$q.notify({
            color: 'red',
            textColor: 'white',
            icon: 'info',
            message: 'No se puede eliminar el material, ya que esta siendo utilizado',
            position: 'top'
          });
        }).finally(() => {
          this.loading = false
        })
      })
  }
  }
  ,
  computed:{
    totalRetiro(){
      let result=0
      this.retiros.forEach(element => {
        result+=parseInt(element.cantidad)
      })
      return result
    }
  }
}
</script>
<style scoped>

</style>
