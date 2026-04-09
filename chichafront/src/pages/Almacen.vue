<template>
  <q-page class="q-pa-xs">
    <div class="row">
      <div class="col-12">
        <div class="text-h5 text-center text-bold"  >INVENTARIO DE  MATERIAL</div>
      </div>
      <div class="col-3 flex flex-center">
        <q-btn-dropdown icon="list" class="full-width" color="accent" label="PROVEEDOR" v-if="$store.state.login.editalmacen">
          <q-list>
            <q-item clickable v-close-popup @click="dialog_prov=true; proveedor={}" >
              <q-item-section>
                <q-item-label>REGISTRO</q-item-label>
              </q-item-section>
            </q-item>

            <q-item clickable v-close-popup @click="modif_prov">
              <q-item-section>
                <q-item-label>MODIFICAR</q-item-label>
              </q-item-section>
            </q-item>

            <q-item clickable v-close-popup @click="del_prov">
              <q-item-section>
                <q-item-label>ELIMINAR</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </div>
      <div class="col-6">
        <q-select dense outlined v-model="proveedor" :options="proveedores" label="Provedores" v-if="$store.state.login.editalmacen"/>
      </div>
      <div class="col-3 flex flex-center">
        <q-btn icon="engineering" color="teal" class="full-width" label="Registrar Material" @click="dialog_mat=true; material={}" v-if="$store.state.login.editalmacen"/>
      </div>
        <div class="col-12">
        <div class="q-pa-none">
          <q-table
            dense
            :rows-per-page-options="[0,10,20,50,100]"
            title="LISTA DE MATERIALES "
            :rows="materiales"
            :columns="columns"
            :filter="filter"
            row-key="name">
            <template v-slot:top-right >
               <q-btn color="amber-8" icon="shopping_cart" label="COMPRAS" @click="dialog_add=true; compras=[]" v-if="$store.state.login.editalmacen"/>
              <q-input dense outlined debounce="300" v-model="filter" placeholder="Buscar">
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </template>
            <template v-slot:body-cell-opcion="props" >
              <q-td key="opcion" :props="props" >
                <q-btn dense round flat color="accent" @click="retirarRow(props)" icon="download"></q-btn>
                <q-btn dense round flat color="cyan" @click="reporte(props)" icon="poll"></q-btn>
                <q-btn dense round flat color="yellow" @click="editRow(props)" icon="edit" v-if="$store.state.login.editalmacen"></q-btn>
                <!--<q-btn dense round flat color="red" @click="delRow(props)" icon="delete" v-if="$store.state.login.editalmacen"></q-btn>-->
              </q-td>
            </template>
            <template v-slot:body-cell-stock="props" >
              <q-td :props="props">
                    <q-linear-progress size="25px" :value="calcular(props.row.stock,props.row.min)" :color="calcular(props.row.stock,props.row.min)<1?'negative':'positive'" class="full-width">
                      <div class="absolute-full flex flex-center">
                        <q-badge color="white" :text-color="calcular(props.row.stock,props.row.min)<1?'negative':'positive'" :label="props.row.stock" />
                      </div>
                    </q-linear-progress>
              </q-td>
            </template>
          </q-table>
        </div>

          <div class="row">
              <div class="col-3"><q-input dense outlined v-model="fecha3" label="Fecha Ini" type="date"/></div>
              <div class="col-3"><q-input dense outlined v-model="fecha4" label="Fecha fin" type="date"/></div>
              <div class="col-3"><q-select dense outlined v-model="material3" :options="materiales" label="Material" /></div>
              <div class="col-3"> <q-btn color="info" label="Consultar"  @click="consultmaterial"/>
              </div>
          </div>
        <div class="q-pa-none">
          <q-table
            dense
            :rows-per-page-options="[0,10,20,50,100]"
            title="LISTA DE COMPRAS "
            :rows="comptodo"
            :columns="colcompra"
            :filter="filter"
            row-key="name">

            <template v-slot:body-cell-estado="props" >
              <q-td key="estado" :props="props" >
                  <q-badge :color="props.row.estado=='POR PAGAR'?'red':'green'"  >{{props.row.estado}}</q-badge>


              </q-td>
            </template>
            <template v-slot:body-cell-opcion="props" >
              <q-td key="opcion" :props="props" >
                <q-btn dense round flat color="green" icon="paid" v-if="$store.state.login.pagoalmacen && props.row.deuda>0" @click="pagarcompra(props.row)"/>
                <q-btn dense round flat color="purple" icon="list" v-if="$store.state.login.editalmacen && props.row.logcompras.length>0" @click="listpago(props.row)"/>
                <!--<q-btn dense round flat color="teal" icon="edit" v-if="$store.state.login.editalmacen" @click="modcompra(props.row)"/>-->
                <q-btn dense round flat color="red"  icon="delete" v-if="$store.state.login.editalmacen" @click="delcompra(props.row)"/>
              </q-td>
            </template>

          </q-table>
        </div>

        <div class="q-pa-none">
          <q-table
            dense
            :rows-per-page-options="[0,10,20,50,100]"
            title="LISTA DE RETIROS "
            :rows="recutodo"
            :columns="colrecuento"
            :filter="filter"
            row-key="name">

            <template v-slot:body-cell-opcion="props" >
              <q-td key="opcion" :props="props" >
                <q-btn dense round flat color="accent" icon="edit" v-if="$store.state.login.editalmacen" @click="modrecuento(props.row)"/>
                <q-btn dense round flat color="red"  icon="delete" v-if="$store.state.login.editalmacen" @click="delrecuento(props.row)"/>
              </q-td>
            </template>

          </q-table>
        </div>

      </div>
      <q-dialog v-model="dialog_add">
        <q-card style="width: 800px; max-width: 80vw;">
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">Compra MATERIAL </div>
            <div class="text-h7">Proveedor: {{proveedor.razon}}</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
              <div class="row">
              <div class="col-12"><q-select dense outlined v-model="material" :options="materiales" label="Material" /></div>
              <div class="col-2"><q-input dense outlined type="text" v-model="compra.cantidad" label="Cantidad"/></div>
              <div class="col-2">
                <template v-if="$store.state.login.almacenCostoSubtotal">
                  <q-input dense outlined type="number" step="0.01" v-model="compra.costo" label="Costo U" />
                </template>
              </div>
              <div class="col-2">
                <template v-if="$store.state.login.almacenCostoSubtotal">
                  Subtotal: {{compra.costo * compra.cantidad}}
                </template>
              </div>
              <div class="col-3"><q-input dense outlined  type="text" v-model="compra.lote"  label="Lote"  /></div>
              <div class="col-3"><q-input dense outlined  type="date" v-model="compra.fechaven"  label="Fecha Vencimiento"  /></div>
              <div class="col-3"><q-input dense outlined  type="text"  v-model="compra.observacion" label="Observacion" /></div>
              <div class="col-1"> <q-btn  dense color="green" icon="add_circle" @click="agregarcompra"/>
              </div>
              <table style="width:100%;border-collapse: collapse;">
              <thead>
              <tr>
              <th>NRO</th>
              <th>MATERIAL</th>
              <th>CANTIDAD</th>
              <th>COSTO</th>
              <th>SUBTOTAL</th>
              <th>LOTE</th>
              <th>FEC VEN</th>
              <th>OBS</th>
              <th>OPCION</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(d,index) in compras " :key="index">
                <td>{{index + 1}}</td>
                <td>{{d.material}}</td>
                <td>{{d.cantidad}}</td>
                <td>{{d.costo}}</td>
                <td>{{d.subtotal}}</td>
                <td>{{d.lote}}</td>
                <td>{{d.fechaven}}</td>
                <td>{{d.observacion}}</td>
                <td> <q-btn color="red" icon="delete" @click="eliminar(index)"/>
                </td>
              </tr>
              </tbody>
              </table>
              </div>
              <div>
                <q-btn label="REGISTRAR" color="positive" icon="add_circle" @click="regcompra"/>
                <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
              </div>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialog_remove">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
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

            <q-dialog v-model="dialog_modcompra">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">Modificar Compra material : {{compra2.material.nombre}}</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form             @submit="updatecompra"             class="q-gutter-md"          >
              <q-input outlined type="text" v-model="compra2.cantidad" label="Cantidad"/>
              <q-input outlined type="number" v-model="compra2.costo" label="Costo U"/>
              Subtotal: {{compra2.costo * compra2.cantidad}}
              <q-input outlined type="date" v-model="compra2.fechaven" label="fecha Ven"/>
              <q-input outlined type="text" v-model="compra2.lote" label="Lote"/>
              <q-input outlined  type="text"  v-model="compra2.observacion" label="Observacion" />
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

      <q-dialog v-model="dialog_prov">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">REGISTRO PROVEEDOR</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form @submit="onReg" class="q-gutter-md" >
              <q-input outlined type="text" v-model="proveedor.razon" label="Razon Social"/>
              <q-input outlined type="text" v-model="proveedor.nit" label="NIT / CI"/>
              <q-input outlined type="text" v-model="proveedor.direccion" label="DIRECCION"/>
              <q-input outlined type="text" v-model="proveedor.email" label="Email"/>
              <q-input outlined type="text" v-model="proveedor.telefono" label="telefono"/>
              <div>
                <q-btn label="REGISTRAR" type="submit" color="positive" icon="add_circle"/>
                <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialog_mat">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">REGISTRO MATERIAL</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form @submit="onRegmat" class="q-gutter-md" >
              <q-input outlined type="text" v-model="material.nombre" label="NOMBRE"/>
              <q-input outlined type="text" v-model="material.unid" label="UNIDADES"/>
              <q-input outlined type="text" v-model="material.min" label="MIN STOCK"/>
              <div>
                <q-btn label="REGISTRAR" type="submit" color="positive" icon="add_circle"/>
                <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

            <q-dialog v-model="dialog_modmat">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">MODIFICAR MATERIAL</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form @submit="onModmat" class="q-gutter-md" >
              <q-input outlined type="text" v-model="material2.nombre" label="NOMBRE"/>
              <q-input outlined type="text" v-model="material2.unid" label="UNIDADES"/>
              <q-input outlined type="text" v-model="material2.min" label="MIN STOCK"/>
              <div>
                <q-btn label="MODIFICAR" type="submit" color="positive" icon="add_circle"/>
                <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialogpagar">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">PAGAR POR LA COMPRA</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">

            <q-form @submit="regpago" class="q-gutter-md" >
              <q-input outlined type="text" v-model="pago.monto" label="Monto" step="0.01"
                    lazy-rules
                    :rules="[ val => val && val > 0 && val <=compra2.deuda || 'ingrese otro monto']"
                    />
              <q-input outlined type="text" v-model="pago.observacion" label="Observacion"/>
              <div>
                <q-btn label="Registrar" type="submit" color="positive" icon="add_circle"/>
                <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialog_modprov">
        <q-card>
          <q-card-section class="bg-yellow-14 text-white">
            <div class="text-h7">MODIFICAR PROVEEDOR</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form @submit="onReg" class="q-gutter-md" >
              <q-input outlined type="text" v-model="proveedor2.razon" label="Razon Social"/>
              <q-input outlined type="text" v-model="proveedor2.nit" label="NIT / CI"/>
              <q-input outlined type="text" v-model="proveedor2.direccion" label="DIRECCION"/>
              <q-input outlined type="text" v-model="proveedor2.email" label="Email"/>
              <q-input outlined type="text" v-model="proveedor2.telefono" label="telefono"/>
              <div>
                <q-btn label="MODIFICAR" type="submit" color="positive" icon="add_circle"/>
                <q-btn  label="Cancelar" icon="delete" color="negative" v-close-popup />
              </div>
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




      <q-dialog v-model="dialog_del" >
        <q-card>
          <q-card-section class="row items-center">
            <q-avatar icon="clear" color="red" text-color="white" />
            <span class="q-ml-sm">Seguro de eliminar Registro.</span>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Eliminar" color="deep-orange" @click="onDel"/>
            <q-btn flat label="Cancelar" color="primary" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>


      <q-dialog v-model="dialoglistpagos" >
        <q-card>
          <q-card-section class="row items-center">
          <q-table title="pagos" :rows="pagos" :columns="colpagos" row-key="name" />

          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Cancelar" color="primary" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>

    </div>
  </q-page>
</template>
<script>
import {date} from 'quasar'
import {jsPDF} from "jspdf";
import $ from "jquery";
import moment from 'moment'

export default {
  data(){
    return{
        proveedor:{},
        proveedor2:{},
        dialog_prov:false,
        dialog_modprov:false,
        dialog_mat:false,
        dialog_modmat:false,
        dialog_reporte:false,
        dialog_modcompra:false,
        dialog_modret:false,
        checkgasto:'CAJA',
        material:{},
        materiales:[],
        material2:{},
        material3:{},
        proveedores:[],
        compras:[],
        comptodo:[],
        compra:{},
        compra2:{},
        recutodo:[],
      crear:false,
      recuento:{},
      recuento2:{},
      filter:'',
      dialog_mod:false,
      dialog_del:false,
      dialog_add:false,
      dialog_remove:false,
      dialog_log:false,
      dialogpagar:false,
      dialoglistpagos:false,
      pago:{},
      agregar:0,
      disminuir:0,
      producto:{},
      color:'',
      dato:{},
      fecha1:date.formatDate( Date.now(),'YYYY-MM-DD'),
      fecha2:date.formatDate( Date.now(),'YYYY-MM-DD'),
      fecha3:date.formatDate( Date.now(),'YYYY-MM-DD'),
      fecha4:date.formatDate( Date.now(),'YYYY-MM-DD'),
      columns : [

  { name: 'nombre', align: 'center', label: 'Nombre', field: 'nombre', sortable: true },
  { name: 'unid', align: 'center', label: 'unidad', field: 'unid', sortable: true },
  { name: 'min', align: 'center', label: 'Minimo', field: 'min', sortable: true },
  { name: 'stock', align: 'center', label: 'stock', field: 'stock', sortable: true },
  { name: 'opcion', label: 'OPCIONES', field: 'opcion' }
],

      colcompra : [

  { name: 'opcion', label: 'OPCIONES', field: 'opcion' },
  { name: 'estado', align: 'center', label: 'ESTADO', field: 'estado' },
  { name: 'fecha', align: 'center', label: 'FECHA', field: 'fecha', sortable: true },
  { name: 'id', align: 'center', label: 'ID', field: 'id', sortable: true },
  { name: 'cantidad', align: 'center', label: 'CANTIDAD', field: 'cantidad', sortable: true },
  { name: 'costo', align: 'center', label: 'COSTO', field: 'costo', sortable: true },
  { name: 'subtotal', align: 'center', label: 'SUBTOTAL', field: 'subtotal', sortable: true },
  { name: 'deuda', align: 'center', label: 'DEUDA', field: 'deuda', sortable: true },
  { name: 'lote', align: 'center', label: 'LOTE', field: 'lote', sortable: true },
  { name: 'fechaven', align: 'center', label: 'FECHA VEN', field: 'fechaven', sortable: true },
  { name: 'material', align: 'center', label: 'MATERIAL', field: row=>row.material.nombre, sortable: true },
  { name: 'provider', align: 'center', label: 'PROVEEDOR', field: row=>row.provider.razon, sortable: true },
  { name: 'observacion', align: 'center', label: 'OBSERVACION', field: 'observacion', sortable: true },
],

      colrecuento : [

  { name: 'opcion', label: 'OPCIONES', field: 'opcion' },
  { name: 'fecha', align: 'center', label: 'FECHA', field: row=>moment(row.fecha).format('DD/MM/YYYY'), sortable: true },
  { name: 'cantidad', align: 'center', label: 'CANTIDAD', field: 'cantidad', sortable: true },
  { name: 'material', align: 'center', label: 'MATERIAL', field: row=>row.material.nombre, sortable: true },
  { name: 'observacion', align: 'center', label: 'OBSERVACION', field: 'observacion', sortable: true },
],
      colpagos : [

  { name: 'fecha', align: 'center', label: 'FECHA', field: row=>moment(row.fecha).format('DD/MM/YYYY'), sortable: true },
  { name: 'monto', align: 'center', label: 'MONTO', field: 'monto', sortable: true },
  { name: 'observacion', align: 'center', label: 'OBSERVACION', field: 'observacion', sortable: true },
],

  rows:[],
  montogeneral:0,
  totalcompra:0,
  montocaja:0,
  pagos:[]

  }},
  created() {
      this.misproveedores()
      this.mismateriales()
      this.totalgeneral()
      this.totalcaja()
  },
  methods: {
    listpago(compra){
      this.pagos=compra.logcompras
      this.dialoglistpagos=true
    },
          totalcaja(){
      this.$axios.post(process.env.API + "/totalcaja").then((res) => {
          this.montocaja=res.data.monto;
      })
      },
    regpago(){
      if(this.$store.state.login.user.id==1){this.checkgasto='GASTO'}
      else{this.checkgasto='CAJA'}
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
      this.pago.compra_id=this.compra2.id
      this.pago.checktipo=this.checkgasto
      this.$axios.post(process.env.API + "/logcompra",this.pago).then((res) => {
                let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
                        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
        this.dialogpagar=false
                this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'info',
          message: 'registrado '
        });
        this.checkgasto='CAJA'
        this.consultmaterial()


      })

    },
                      totalgeneral(){
      this.$axios.post(process.env.API + "/totalgeneral").then((res) => {
          this.montogeneral=parseFloat( res.data.monto);
      })
      },
    updatecompra(){
      this.$axios.put(process.env.API+'/compra/'+this.compra2.id,this.compra2).then(res=>{
                this.mismateriales()
        this.consultmaterial()
        this.dialog_modcompra=false
                this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'info',
          message: 'modificado '
        });
      })

    },
    modrecuento(recuento){
      this.recuento2=recuento;
      this.dialog_modret=true

    },

        updateretiro(){
      this.$axios.put(process.env.API+'/recuento/'+this.recuento2.id,this.recuento2).then(res=>{
        this.mismateriales()
        this.consultmaterial()
        this.dialog_modret=false
                this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'info',
          message: 'modificado '
        });
      })

    },
    modcompra(compra){
      this.compra2=compra;
      this.dialog_modcompra=true

    },
    pagarcompra(compra){
      this.compra2=compra
      this.pago={}
      if(this.$store.state.login.user.id==1){this.checkgasto='GASTO'}
      else{this.checkgasto='CAJA'}

      this.dialogpagar=true

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
              this.$axios.delete(process.env.API+'/compra/'+compra.id).then(res=>{
        this.mismateriales()
        this.consultmaterial()
                this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'info',
          message: 'Eliminado '
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
        this.mismateriales()
        this.consultmaterial()
                this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'info',
          message: 'Eliminado '
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
    consultmaterial(){
      this.$axios.post(process.env.API+'/consultar',{material_id:this.material3.id,fecha1:this.fecha3,fecha2:this.fecha4}).then(res=>{
        console.log(res.data)
        this.comptodo=res.data
      })

      this.$axios.post(process.env.API+'/consulrecuento',{material_id:this.material3.id,fecha1:this.fecha3,fecha2:this.fecha4}).then(res=>{
        this.recutodo=res.data
      })


    },
    genreporte(){
      console.log(date.formatDate(new Date(),'DD/MM/YYYY HH:mm:ss'))
      this.$axios.post(process.env.API+'/repalmacen',{id:this.material2.id,fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
          console.log(res.data)
      let mc=this

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
      })
      window.open(doc.output('bloburl'), '_blank');
      })

    },
    reporte(props){
      this.material2=props.row
      this.dialog_reporte=true
    },
    regcompra(){
      if(this.compras.length==0){
                this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'info',
          message: 'Debe registrar '
        });
        return false}
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
      this.$axios.post(process.env.API+'/compra',{provider_id:this.proveedor.id,user_id:this.$store.getters["login/user"].id,compras:this.compras}).then(res=>{
        //console.log(res.data)
        this.dialog_add=false
        this.mismateriales();
      })

    },
    eliminar(index){
      this.compras.splice(this.compras.indexOf(index),1);
    },
    agregarcompra(){
      if(this.compra.cantidad=='' || this.compra.cantidad==0 || this.compra.cantidad==undefined)
        return false
      if(this.compra.costo==undefined || this.compra.costo=='' || this.compra.costo==0) return false
      if(this.compra.fechaven==undefined || this.compra.fechaven=='') this.compra.fechaven=''
      if(this.compra.observacion==undefined || this.compra.observacion=='') this.compra.observacion=''
      if(this.compra.lote==undefined || this.compra.lote=='') this.compra.lote=''
      this.compra.material_id=this.material.id
      this.compra.subtotal=parseFloat(this.compra.cantidad) * parseFloat(this.compra.costo)
      this.compra.material=this.material.nombre
      this.compras.push(this.compra)
      this.compra={}
    },
    onRegRecuento(){
      //console.log
      this.recuento.material_id=this.material2.id
      this.recuento.user_id=this.$store.getters["login/user"].id
      this.$axios.post(process.env.API+'/recuento',this.recuento).then(res=>{
        this.recuento={}
        this.dialog_add=false
        this.mismateriales()
      })
    },
    onRegRecuento2(){
      //console.log
      if(this.material2.stock<this.recuento.cantidad)
        return false
      this.recuento.material_id=this.material2.id
      this.recuento.tipo='RETIRAR'
      this.recuento.user_id=this.$store.getters["login/user"].id
      this.$axios.post(process.env.API+'/recuento',this.recuento).then(res=>{
        this.recuento={}
        this.dialog_remove=false
        this.mismateriales()
      })
    },
    agregarRow(props){
      this.material2=props.row
      this.dialog_add=true
    },
        retirarRow(props){
      this.material2=props.row
      this.dialog_remove=true
    },
      modif_prov(){
        this.proveedor2=this.proveedor
        this.dialog_modprov=true
      },
      onReg(){
      this.$axios.post(process.env.API+'/provider',this.proveedor).then(res=>{
        this.dialog_prov=false
          this.misproveedores()
          this.provider={}
      })

      },

      onRegmat(){
      this.$q.loading.show()
      this.$axios.post(process.env.API+'/material',this.material).then(res=>{
          this.$q.loading.hide()
          this.dialog_mat=false
          this.mismateriales()
          this.material={}
        })
      },
      onModmat(){
      this.$q.loading.show()
      this.$axios.put(process.env.API+'/material/'+this.material2.id,this.material2).then(res=>{
          this.$q.loading.hide()
          this.dialog_modmat=false
          this.mismateriales()
          this.material={}
        })
      },

      misproveedores(){
          this.proveedores=[]
      this.$axios.get(process.env.API+'/provider').then(res=>{
          res.data.forEach( r=> {
              r.label=r.razon
              this.proveedores.push(r)
          });
          this.proveedor=this.proveedores[0]
      })

      },

       mismateriales(){
          this.materiales=[]
      this.$axios.get(process.env.API+'/material').then(res=>{
          res.data.forEach( r=> {
              r.label=r.nombre
              this.materiales.push(r)
          });
          this.material=this.materiales[0]
          this.material3=this.materiales[0]
      })

      },

    registrar(){
      this.producto.nombre=  this.producto.nombre.toUpperCase()

      // console.log(this.producto.observacion)
      if (this.producto.observacion!=undefined){
        this.producto.observacion=  this.producto.observacion.toUpperCase()
      }

        this.$axios.post(process.env.API+'/producto', this.producto).then(res=>{
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Creado correctamente'
        });
        this.crear=false;
        this.onReset();
        this.listado();
      }).catch(err=>{

          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: 'Error al registrar'
          });
        })
      },


      activar(props){
        this.dato=props.row;
        this.$axios.post(process.env.API+'/activarprod', this.dato).then(res=>{
          this.listado();
        });

      },
    calcular(stock,min){
      let porcentaje=((stock/min*100)/100)
      // console.log(porcentaje)
      if (porcentaje>1){
        return 1
      }else{
        return porcentaje
      }

    },
  editRow(props){
    this.material2=props.row;
    this.dialog_modmat=true;
  },
    logRow(props){
    this.dato=props.row;
    this.dialog_log=true;
  },
  delRow(props){
    this.material2=props.row;
    this.dialog_del=true;
  },
        addRow(props){
        this.dato= props.row;
        this.dialog_add=true;
    },
    substractRow(props){
        this.dato= props.row;
        this.dialog_sub=true;
    },

    onDel(){
        this.$q.loading.show();
        this.$axios.delete(process.env.API+'/material/'+this.material2.id).then(res=>{
         this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Eliminado correctamente'
        });
        this.dialog_del=false;
        this.mismateriales();
        })
        this.$q.loading.hide();
    },

        del_prov(){
        this.$q.dialog({
        title:'Seguro de eliminar?',
        cancel:true,
        }).onOk(()=>{
          this.$q.loading.show();
          this.$axios.delete(process.env.API+'/provider/'+this.proveedor.id).then(res=>{
          this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Eliminado correctamente'
          });
        this.misproveedores();
        })
        this.$q.loading.hide();
        })
    },


  onReset(){
    this.producto={};
  },

  computed:{


  }
  },



}
</script>
<style>
</style>
