<template>
  <q-page class="q-pa-xs">
  <div class="row">
    <div class="col-12">
      <div class="text-subtitle1 bg-red text-center text-white">CONTROL DE GASTOS</div>
    </div>
    <div class="col-6 col-sm-2 q-pa-xs"><q-input dense type="date" label="fecha" v-model="fecha1" outlined required/></div>
    <div class="col-6 col-sm-2 q-pa-xs" v-if="rango=='RANGO'"><q-input dense type="date" label="fecha" v-model="fecha2" outlined required/></div>
    <div class="col-6 col-sm-2 q-pa-xs"><q-toggle v-model="rango" true-value="RANGO" false-value="DIA" :label="rango +' FECHA'"/></div>
    <div class="col-6 col-sm-3 q-pa-xs" v-if="$store.state.login.gastoreporteuser"><q-select dense v-model="user" :options="users" label="Usuarios" outlined /></div>
    <div class="col-6 col-sm-3 q-pa-xs flex flex-center">
      <q-btn color="info"  label="Consultar" icon="search" type="submit" @click="misgastos" />
    </div>

    <q-dialog v-model="dialogGasto">
    <q-card>
    <q-card-section>
    <div class="text-h6">REGISTRO DE GASTO</div>
    </q-card-section>
    <q-card-section>
      <div class="col-12">
        <q-form @submit.prevent="agregar">
          <div class="row">
            <div class="col-12 q-pa-xs ">
            <q-input dense outlined type="number" step="0.1" label="Precio" v-model="empleado.precio" required :rules="[val => val>0  && val<= montogeneral || 'No debe exceder el monto']"/></div>
            <div class="col-10 q-pa-xs ">
              <q-select outlined v-model="glosa" :options="glosas" label="Glosa" use-input @filter="filterFn"     dense  >
                <template v-slot:no-option>
                  <q-item>
                    <q-item-section class="text-grey">
                      No results
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
            <div class="col-2 q-pa-xs ">
               <q-btn color="green" icon="add_circle"  @click="dialogaddglosa=true" v-if="$store.state.login.editglosa"/>

            </div>
            <div class="col-12 q-pa-xs "><q-input dense outlined label="Observacion" v-model="empleado.observacion" style="text-transform: uppercase" required/></div>
            <div class="col-12 q-pa-xs "><q-input dense outlined label="Fecha " type="date" v-model="empleado.fecha" required/></div>
            <div class="col-12 q-pa-xs  flex flex-center">
              <q-btn icon="send" :label="boolcrear?'CREAR':'MODIFICAR'" type="submit" :color="boolcrear?'positive':'warning'"/>
            </div>
          </div>
        </q-form>

      </div>

    </q-card-section>
    <q-card-actions align="right">
    <q-btn flat label="cerrar" color="primary" v-close-popup />
    </q-card-actions>
    </q-card>
    </q-dialog>
    <div class="col-12 q-pa-xs  flex flex-center">
      <!--<div class="col-4"><q-select dense outlined label="Empleado" v-model="empleadopago" :options="empleados"/></div>
<q-btn icon="send" label="Adelanto" type="button" color="teal" @click="cargarPagos"/>-->
<q-btn icon="price_change" label="REGISTRO PAGO" type="button" color="cyan" @click="dialogGasto=true"/>
<q-btn icon="price_change" label="Caja Chica" type="button" color="accent" @click="cajachica=true"/>
</div>

<!--    <div class="col-3">-->
<!--      <q-btn label="Imprimir mis gastos" icon="print" color="info" class="full-width" @click="imprimir(user)" dense no-caps/>-->
<!--    </div>-->
<!--    <div class="col-3 ">-->
<!--        <q-btn label="Imprimir mis ventas Detalle" icon="print" color="teal" style="width:100%"  @click="imprimirmisventasdetalle(user)" dense no-caps/>-->
<!--    </div>-->
<!--    <div class="col-3 ">-->
<!--        <q-btn label="Imprimir mis ventas Local" icon="print" color="warning" style="width:100%"  @click="imprimirmisventaslocal(user)" dense no-caps/>-->
<!--    </div>-->
    <div class="col-3">
      <q-btn label="Imprimir mis ventas y gastos" icon="print" color="accent" class="full-width" @click="impresionventagasto(user)" dense no-caps/>
    </div>
    <div class="col-12">
<!--      <q-table-->
<!--      :columns="columns"-->
<!--      :rows="gastos"-->
<!--      :rows-per-page-options="[50,100,0]"-->
<!--      :filter="filter"-->
<!--      >-->
<!--        <template v-slot:top-right>-->
<!--          <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">-->
<!--            <template v-slot:append>-->
<!--              <q-icon name="search" />-->
<!--            </template>-->
<!--          </q-input>-->
<!--        </template>-->

<!--        <template v-slot:body-cell-action="props">-->
<!--          <q-td :props="props">-->
<!--            <q-btn-->
<!--              color="warning"-->
<!--              icon-right="edit"-->
<!--              no-caps-->
<!--              label="Modificar"-->
<!--              flat-->
<!--              dense-->
<!--              @click="updateval(props.row)"-->
<!--            />-->
<!--            <q-btn-->
<!--            v-if="$store.state.login.eliminargasto"-->
<!--              color="negative"-->
<!--              icon-right="delete"-->
<!--              no-caps-->
<!--              label="Eliminar"-->
<!--              flat-->
<!--              dense-->
<!--              @click="deleteval(props.row)"-->
<!--            />-->
<!--&lt;!&ndash;            <q-btn&ndash;&gt;-->
<!--&lt;!&ndash;              color="info"&ndash;&gt;-->
<!--&lt;!&ndash;              icon-right="list"&ndash;&gt;-->
<!--&lt;!&ndash;              no-caps&ndash;&gt;-->
<!--&lt;!&ndash;              label="Sueldos"&ndash;&gt;-->
<!--&lt;!&ndash;              flat&ndash;&gt;-->
<!--&lt;!&ndash;              dense&ndash;&gt;-->
<!--&lt;!&ndash;              @click="pagosval(props.row)"&ndash;&gt;-->
<!--&lt;!&ndash;            />&ndash;&gt;-->

<!--            &lt;!&ndash;              @click="deleteval(detalles.indexOf(props.row))"&ndash;&gt;-->
<!--          </q-td>-->
<!--        </template>-->
<!--      </q-table>-->
      <div class="text-h5" align="center">HISTORIAL DE CAJA DE VENTAS</div>
      <table id="example" style="width:100%" class="cell-border">
        <thead>
        <tr>
          <th>id</th>
          <th>precio</th>
          <th>glosa</th>
          <th>observacion</th>
          <th>fecha</th>
          <th>hora</th>
          <th>user</th>
          <th>action</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="g in gastos" :key="g.i">
          <td>{{g.id}}</td>
          <td>{{g.precio}}</td>
          <td>{{g.glosa}}</td>
          <td>{{g.observacion}}</td>
          <td>{{g.fecha}}</td>
          <td>{{g.hora}}</td>
          <td>{{g.user}}</td>
          <td>
              <q-td >
                <q-btn hidden
                  color="warning"
                  icon-right="edit"
                  no-caps

                  flat
                  size="xs"
                  dense
                  @click="updateval(g)"
                ><q-tooltip>Modificar</q-tooltip></q-btn>
                <q-btn
                v-if="$store.state.login.eliminargasto"
                  color="negative"
                  icon-right="delete"
                  no-caps
                   flat
                  size="xs"
                  dense
                  @click="deleteval(g)"
                ><q-tooltip>Eliminar</q-tooltip></q-btn>
                <q-btn
                v-if="$store.state.login.user.id==1"
                  color="info"
                  icon-right="print"
                  no-caps
                   flat
                  size="xs"
                  dense
                  @click="printgasto(g)"
                ><q-tooltip>Impresion</q-tooltip></q-btn>
              </q-td>
          </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
          <th colspan="7" style="text-align:right">Total:</th>
          <th></th>
        </tr>
        </tfoot>
      </table>
      <!--
      <q-table title="Gastos Caja Chica" :rows="chica" :columns="columns4" row-key="name" :filter="filtercaja">
            <template v-slot:top-right>
                          <q-input dense outlined debounce="300" v-model="filtercaja" placeholder="Buscar">
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
             <q-btn color="deep-purple-6" label="Exportar Excel"  @click="exportTable"/>

            </template>
                        <template v-slot:body-cell-opcion="props" >
                <q-td key="opcion" :props="props" >
                    <q-btn size="xs" @click="modifcaja(props.row)" v-if="$store.state.login.user.id==1" color="yellow" icon="edit"/>
                    <q-btn size="xs" @click="printcaja(props.row)" v-if="$store.state.login.user.id==1" color="primary" icon="print"/>
                </q-td>
            </template>
    </q-table>
-->
<div class="text-h5" align="center">HISTORIAL DE GASTOS DE CAJA CHICA</div>

    <table id="example2" style="width:100%" class="cell-border">
        <thead>
        <tr>
          <th>id</th>
          <th>Monto</th>
          <th>glosa</th>
          <th>Motivo</th>
          <th>fecha</th>
          <th>hora</th>
          <th>usuario</th>
          <th>opcion</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="c in chica" :key="c.id">
          <td>{{c.id}}</td>
          <td>{{c.monto}}</td>
          <td>{{c.glosa}}</td>
          <td>{{c.motivo}}</td>
          <td>{{c.fecha}}</td>
          <td>{{c.hora}}</td>
          <td>{{c.user}}</td>
          <td>
              <!--<q-btn size="xs" @click="modifcaja(c)" v-if="$store.state.login.user.id==1" color="yellow" icon="edit"><q-tooltip>Modificar</q-tooltip></q-btn>-->
              <q-btn size="xs" @click="printcaja(c)" v-if="$store.state.login.user.id==1" color="primary" icon="print" ><q-tooltip>Impresion</q-tooltip></q-btn>

          </td>
        </tr>
        </tbody>
      </table>
<!--      <div class="row">-->
<!--        <div class="col-12">-->
<!--&lt;!&ndash;          <div class="text-subtitle1 bg-accent text-center text-white">Historial de pagos</div>&ndash;&gt;-->
<!--        </div>-->

<!--        <div class="col-12">-->
<!--          <q-table-->
<!--            :columns="columns3"-->
<!--            :rows="gastos"-->
<!--            title="Historial de pagos"-->

<!--          >-->
<!--&lt;!&ndash;            :filter="filter"&ndash;&gt;-->

<!--            &lt;!&ndash;        <template v-slot:top-right>&ndash;&gt;-->
<!--            &lt;!&ndash;          <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">&ndash;&gt;-->
<!--            &lt;!&ndash;            <template v-slot:append>&ndash;&gt;-->
<!--            &lt;!&ndash;              <q-icon name="search" />&ndash;&gt;-->
<!--            &lt;!&ndash;            </template>&ndash;&gt;-->
<!--            &lt;!&ndash;          </q-input>&ndash;&gt;-->
<!--            &lt;!&ndash;        </template>&ndash;&gt;-->
<!--&lt;!&ndash;            <template v-slot:body="props">&ndash;&gt;-->
<!--&lt;!&ndash;              <q-tr :props="props">&ndash;&gt;-->
<!--&lt;!&ndash;                <q-td key="total" :props="props">&ndash;&gt;-->
<!--&lt;!&ndash;                  {{ props.row.total }}&ndash;&gt;-->
<!--&lt;!&ndash;                </q-td>&ndash;&gt;-->
<!--&lt;!&ndash;                <q-td key="acuenta" :props="props">&ndash;&gt;-->
<!--&lt;!&ndash;                  {{ props.row.acuenta }}&ndash;&gt;-->
<!--&lt;!&ndash;                </q-td>&ndash;&gt;-->
<!--&lt;!&ndash;                <q-td key="saldo" :props="props">&ndash;&gt;-->
<!--&lt;!&ndash;                  {{ props.row.saldo }}&ndash;&gt;-->
<!--&lt;!&ndash;                </q-td>&ndash;&gt;-->
<!--&lt;!&ndash;                <q-td key="estado" :props="props">&ndash;&gt;-->
<!--&lt;!&ndash;                  <q-badge :color="props.row.estado=='CANCELADO'?'positive':'negative'">{{ props.row.estado }}</q-badge>&ndash;&gt;-->
<!--&lt;!&ndash;                </q-td>&ndash;&gt;-->
<!--&lt;!&ndash;                <q-td key="local" :props="props">&ndash;&gt;-->
<!--&lt;!&ndash;                  {{ props.row.local }}&ndash;&gt;-->
<!--&lt;!&ndash;                </q-td>&ndash;&gt;-->
<!--&lt;!&ndash;                <q-td key="titular" :props="props">&ndash;&gt;-->
<!--&lt;!&ndash;                  {{ props.row.titular }}&ndash;&gt;-->
<!--&lt;!&ndash;                </q-td>&ndash;&gt;-->
<!--&lt;!&ndash;                <q-td key="user" :props="props">&ndash;&gt;-->
<!--&lt;!&ndash;                  {{ props.row.user }}&ndash;&gt;-->
<!--&lt;!&ndash;                </q-td>&ndash;&gt;-->
<!--&lt;!&ndash;              </q-tr>&ndash;&gt;-->
<!--&lt;!&ndash;            </template>&ndash;&gt;-->
<!--          </q-table>-->
<!--        </div>-->
<!--        <div class="col-12">-->
<!--          <q-btn label="Imprimir" icon="print" color="info" class="full-width" @click="imprimir"/>-->
<!--        </div>-->
<!--      </div>-->


      <q-dialog v-model="pagos" full-width>
        <q-card>
          <q-card-section class="row items-center q-pb-none">
            <div class="text-h6">Historial de {{empleadopago.nombre}}</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit.prevent="agregarpago">
              <div class="row">

                <div class="col-3">
                  <q-input outlined label="Monto" type="number" step="0.01" v-model="pago.monto" required/>
                </div>
                <div class="col-3">
                  <q-select outlined label="Tipo" v-model="pago.tipo" :options="['DESCUENTO','ADELANTO','EXTRA']"/>
                </div>
                <div class="col-3">
                  <q-input outlined label="Observacion" type="text" v-model="pago.observacion" />
                </div>
                <div class="col-3">
                  <q-input outlined label="Fecha" type="date" v-model="pago.fecha" />
                </div>
                <div class="col-3" v-if="$store.state.login.user.id==1">
                  <q-radio v-model="checkgasto" val="GASTO" label="GASTO" />
                  <q-radio v-model="checkgasto" val="CAJA" label="CAJA CHICA" />
                </div>
                <div class="col-3 flex flex-center">
                  <q-btn label="Agregar" type="submit" icon="send" color="info"/>
                </div>
              </div>
            </q-form>
            <q-table
            title="Hitorial de pagos"
            :rows-per-page-options="[50,100,0]"
            :columns="columns2"
            :rows="listpagos"
            v-if="$store.state.login.reportepago">
        <template v-slot:body-cell-opcion="props" >
                <q-td key="opcion" :props="props" >
                <q-btn dense round flat color="teal" @click="printRow(props)" icon="print"></q-btn>
                </q-td>
            </template>
            </q-table>
          </q-card-section>
          <q-card-actions align="right" class="bg-white text-teal">
            <q-btn flat label="OK" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>

            <q-dialog v-model="cajachica">
        <q-card>
          <q-card-section>
            <div class="text-h6">Registro Gasto de Caja Chica {{montocajachica}} Bs</div>
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit.prevent="agregarcjchica">
              <div class="row">
            <div class="col-12">
            <q-select outlined v-model="glosa" :options="glosas" label="Glosa" use-input @filter="filterFn"     dense  >
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                    No results
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
                <div class="col-12">
                  <q-input outlined label="Monto" type="number" step="0.1" v-model="cchica.precio"
                    :rules="[
                    val => val>0 && val<= montocajachica  || 'No debe exceder el monto',
                    ]"
                lazy-rules/>
                </div>
                 <div class="col-12">
                  <q-input outlined label="Observacion" type="text" v-model="cchica.observacion" />
                </div>
                <div class="col-3 flex flex-center">
                  <q-btn label="Registrar" type="submit" icon="send" color="info" :loading="loading"/>
                </div>
              </div>
            </q-form>
          </q-card-section>
          <q-card-actions align="right" class="bg-white text-teal">
            <q-btn flat label="Cerrar" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>

            <q-dialog v-model="modifcajachica">
        <q-card>
          <q-card-section>
            <div class="text-h6">Modificar Gasto de Caja Chica {{montocajachica}} Bs</div>
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit.prevent="modificarcjchica">
              <div class="row">
            <div class="col-12">
            <q-select outlined v-model="glosa" :options="glosas" label="Glosa" use-input @filter="filterFn"     dense  >
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                    No results
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
                <div class="col-12">
                  <q-input outlined label="Monto" type="number" step="0.1" v-model="cchica2.monto"
                    :rules="[
                    val => val>0 && val<= montocajachica  || 'No debe exceder el monto',
                    ]"
                lazy-rules/>
                </div>
                 <div class="col-12">
                  <q-input outlined label="Observacion" type="text" v-model="cchica2.motivo" />
                </div>
                <div class="col-3 flex flex-center">
                  <q-btn label="Registrar" type="submit" icon="send" color="info"/>
                </div>
              </div>
            </q-form>
          </q-card-section>
          <q-card-actions align="right" class="bg-white text-teal">
            <q-btn flat label="Cerrar" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>


            <q-dialog v-model="dialogaddglosa">
        <q-card>
          <q-card-section>
            <div class="text-h6">Registro / Mod  Glosa</div>
          </q-card-section>
          <q-card-section class="q-pt-none">
            <q-form @submit.prevent="regglosa">
              <div class="row">
                <div class="col-6">
                  <q-input outlined label="Nombre" v-model="glosa.nombre"
                    :rules="[
                    val => val.length>0  || 'INGRESE NOMBRE',
                    ]"
                lazy-rules/>
                </div>
                <div class="col-3">
                  <q-input outlined label="N orden" v-model="glosa.orden" type="number"
                    :rules="[
                    val => val>=0  || 'INGRESE nummero',
                    ]"
                lazy-rules/>
                </div>
                <div class="col-3 ">
                  <q-btn label="Reg/Mod" type="submit" icon="send" color="info" dense/>
                </div>
              </div>
            </q-form>
          </q-card-section>
          <q-card-actions align="right" class="bg-white text-teal">
            <q-btn flat label="Cerrar" v-close-popup />
          </q-card-actions>
        </q-card>
      </q-dialog>
    </div>

  </div>
</q-page>
</template>

<script>
import {date} from 'quasar'
import {jsPDF} from "jspdf";
import $ from "jquery";
import xlsx from "json-as-xlsx"
import moment from 'moment'

export default {
  name: "Venta",
  data(){
    return{
      filter:'',
      loading:false,
      filtercaja:'',
      cchica:{},
      cchica2:{},
      glosa:{},
      glosas:[],
      chica:[],
      checkgasto:'CAJA',
      dialogaddglosa:false,
      pagos:false,
      cajachica:false,
      modifcajachica:false,
      dialogGasto:false,
      pago:{fecha:date.formatDate( Date.now(),'YYYY-MM-DD')},
      empleados:[],
      empleadohistorial:{},
      boolcrear:true,
      gastos:[],
      anulados:[],
      rango:'RANGO',
      empleadopago:{},
      rpagos:[],
      reporteventa:[],
      prestamoventa:[],
      users:[],
      user:{},
      gl:[],
      filterglosa:[],
      listpagos:[],
      pageTotal:'',
      tot:'',
      resumenplanilla:0,
      empleado:{fecha:date.formatDate( Date.now(),'YYYY-MM-DD')},
      fecha1:date.formatDate( Date.now(),'YYYY-MM-DD'),
      fecha2:date.formatDate( Date.now(),'YYYY-MM-DD'),
      montocajachica:0,
      montogeneral:0,
      columns:[
        {name:'id',label:'Id',field:'id'},
        {name:'precio',label:'Precio',field:'precio'},
        {name:'glosa',label:'Glosa',field:row=>row.glosa.nombre},
        {name:'observacion',label:'Observacion',field:'observacion'},
        {name:'fecha',label:'Fecha',field:'fecha'},
        {name:'hora',label:'Hora',field:'hora'},
        {name:'user',label:'Usuario',field:'user'},
        {name:'action',label:'Action',field:'action'},
      ],
      columns2:[
        {name:'opcion',label:'Opcion',field:'opcion'},
        {name:'fecha',label:'Fecha',field:'fecha',sortable: true },
        {name:'hora',label:'Hora',field:'hora'},
        {name:'tipo',label:'Tipo',field:'tipo',sortable: true },
        // {name:'detalle',label:'detalle',field:'detalle'},
        {name:'monto',label:'Monto',field:'monto'},
        {name:'observacion',label:'observacion',field:'observacion'},
      ],
      columns3:[
        {name:'nombre',label:'Empleado',field:'nombre'},
        {name:'salario',label:'Sueldo',field:'salario'},
        {name:'pago',label:'Pago',field:'pago'},
        {name:'adelanto',label:'Adelanto',field:'adelanto'},
        // {name:'detalle',label:'detalle',field:'detalle'},
        {name:'descuento',label:'Descuento',field:'descuento'},
      ],
      columns4:[
        {name:'id',label:'Id',field:'id'},
        {name:'monto',label:'Monto',field:'monto'},
        {name:'glosa',label:'Glosa',field:'glosa'},
        {name:'motivo',label:'Motivo',field:'motivo'},
        {name:'fecha',label:'Fecha',field:'fecha'},
        {name:'hora',label:'Hora',field:'hora'},
        {name:'user',label:'Usuario',field:'user'},
        {name:'opcion',label:'Opcion'},
      ],
      encabezado:{"id":'id',"Monto":'monto',"Glosa":'glosa',"Motivo":'glosa',"Fecha":"fecha","Hora":"hora","Usuario":'user'},
      totalCajaChicaIngreso:0,
      totalPagoAlmacen:0,
    }
  },
  mounted() {
    console.log(this.$store.state.login.user)

    $('#example').DataTable( )
    $('#example2').DataTable( )

    // this.misempleados()
    // console.log(this.$store.state.login)
    // this.$axios.get(process.env.API+'/cliente').then(res=>{
    //   this.clientes=res.data
    //   this.cliente=res.data[0]
    // })
    // this.$axios.get(process.env.API+'/producto').then(res=>{
    //   this.productos=res.data
    //   this.producto=res.data[0]
    // })
    // this.$axios.post(process.env.API+'/misventas',{fecha:this.fecha2}).then(res=>{
    //   this.venta=res.data
    //   // console.log(this.venta)
    //   // this.producto=res.data[0]
    // })
    // this.$axios.post(process.env.API+'/me').then(res=>{
    //   // console.log(res.data)
    //   this.responsable=res.data.name
    // })
    // this.responsable=this.$store.getters["login/user"].name
    this.misglosa()
    this.listadog()
    this.misempleados()
    this.totalcaja()
    this.totalgeneral()
        this.misuser()
        if(!this.$store.state.login.gastoreporteuser) this.user={label:this.$store.state.login.user.name,id:this.$store.state.login.user.id}
    this.misgastos()
  },

  methods:{
    cargarPagos(){
      console.log(this.empleadopago)
      this.$axios.get(process.env.API + "/empleado/"+this.empleadopago.id).then((res) => {
        console.log(res.data)
        this.listpagos=res.data
        this.pagos=true
      })
    },
    modificarcjchica(){
      if(this.glosa.id==undefined)
      {
        return false
      }
      this.cchica2.glosa_id=this.glosa.id
      //console.log(this.cchica)
      //  return false
      this.$axios.put(process.env.API + "/logcaja/"+this.cchica2.id,this.cchica2).then((res) => {
        console.log(res.data)
        this.modifcajachica=false;
        this.glosa={label:''}
        this.misgastos()
        this.totalcaja()
                this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Modificado correctamente'
        });
      })

    },
          exportTable () {
let datacaja = [
  {
    sheet: "Caja Chica",
    columns: [
      { label: "id", value: "id" }, // Top level data
      { label: "Monto", value: "monto" }, // Top level data
      { label: "Glosa", value: "glosa" }, // Top level data
      { label: "Motivo", value: "motivo" }, // Top level data
      { label: "fecha", value: "fecha" }, // Top level data
      { label: "Hora", value: "hora" }, // Top level data
      { label: "Usuario", value: "user" }, // Top level data
    ],
    content: this.chica
  },

]

let settings = {
  fileName: "CajaChica", // Name of the resulting spreadsheet
  extraLength: 7, // A bigger number means that columns will be wider
  writeOptions: {}, // Style options from https://github.com/SheetJS/sheetjs#writing-options
}

xlsx(datacaja, settings) // Will download the excel file
      },
                  totalgeneral(){
      this.$axios.post(process.env.API + "/totalgeneral").then((res) => {
          this.montogeneral=parseFloat( res.data.monto);
      })
      },
    regglosa(){
      if(this.glosa.id==undefined){
        this.$axios.post(process.env.API+'/glosa',{nombre:this.glosa.nombre,orden:this.glosa.orden==undefined?0:this.glosa.orden}).then(res=>{
          this.misglosa()
          this.dialogaddglosa=false;
                          this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Registrado correctamente'
        });
        })
      }
      else{
            this.glosa.orden==undefined?0:this.glosa.orden
                this.$axios.put(process.env.API+'/glosa/'+this.glosa.id,this.glosa).then(res=>{
          this.misglosa()
          this.dialogaddglosa=false;
                          this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Modificado correctamente'
        });
      })}

    },
          filterFn (val, update) {
        if (val === '') {
          update(() => {
            this.glosas = this.filterglosa
          })
          return
        }

        update(() => {
          const needle = val.toLowerCase()
          this.glosas = this.filterglosa.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
        })
      },

    printRow(props){
      this.$axios.get(process.env.API + "/impade/"+props.row.id).then((res) => {
                let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
      })

    },
    modifcaja(log){

      this.cchica2=log
      let glosa=log.gl
      glosa.label=glosa.nombre
      this.glosa=glosa
      this.modifcajachica=true
        console.log(this.cchica2)
    },
    printcaja(caja){
        let cadena="<style>   .textcnt{   text-align:center;        }        table{width:100%;}        td{vertical-align:top;}        </style>"
        cadena+="<div class='textcnt'> DETALLE GASTO CAJA CHICA</div>"
        cadena+="<div class='textcnt'>Nro "+caja.id+"</div>        <hr>"
        cadena+="<div>Nombre: "+caja.user+"</div>"
        cadena+="<div>Fecha: "+moment(caja.fecha).format('DD/MM/YYYY')+"</div>        <hr>"
        cadena+="<table>        <tr><td>Glosa: </td><td><b>"+caja.glosa+"</b></td></tr>"
        cadena+="<tr><td>Costo: </td><td><b>"+caja.monto+"</b></td></tr>"
        cadena+="<tr><td>Observacion: </td><td><b>"+caja.motivo+"</b></td></tr>        </table>"
        cadena+="    <br>"
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(cadena);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
    },
    agregarcjchica(){
      if(this.glosa.id==undefined)
      {
        return false
      }
      if(this.montocajachica < this.cchica.precio){
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'info',
          message: 'No Existe suficiente en Caja Chica'
        })
        return false
      }
      this.loading=true

      this.cchica.glosa_id=this.glosa.id
      //console.log(this.cchica)
      //  return false
      this.$q.loading.show()

      this.$axios.post(process.env.API + "/gastocaja",this.cchica).then((res) => {
                        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        this.$q.loading.hide()
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
        this.cchica.precio=0;
        this.cchica.observacion='';
        this.cajachica=false;
        this.glosa={}
        this.misgastos()
        this.totalcaja()
        this.loading=false
                this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Registrado correctamente'
        })
      })
    },
      totalcaja(){
      this.$axios.post(process.env.API + "/totalcaja").then((res) => {
          this.montocajachica=res.data.monto;
      })
    },



    listadog(){
      this.$axios.post(process.env.API+'/listglosa').then(res=>{
        this.gl=res.data
      })

    },
    misuser(){
      this.$axios.post(process.env.API+'/listuser').then(res=>{
        this.users=[{label:'TODOS',id:0}]
        res.data.forEach(element => {
          this.users.push({label:element.name,id:element.id});

        });
        this.user=this.users[0];
      })
    },
        misglosa(){
          this.glosas=[{label:''}]
      this.$axios.get(process.env.API+'/glosa').then(res=>{
        //console.log(res.data)
        res.data.forEach(r => {
          r.label=r.nombre
          this.glosas.push(r);

        });
        this.filterglosa=this.glosas
        this.glosa=this.glosas[0];
      })
    },
    async impresionventagasto(us){
      let mc=this
          if(!this.$store.state.login.gastoreporteuser)

        us.label=this.$store.state.login.user.name;
        let ventas=0
        let vtotal=0
        let vsaldo=0
        let ventasruta=0
        let vrtotal=0
        let vrsaldo=0
        let ccpago=0
        let color1="style='color:red'"
        let cadena="<style>\
        *{font-size:10px}\
        table{width:100%;border-collapse: collapse;}\
        table, th, td {  border: 1px solid;}\
        .titulo1{text-align:center; font-weight: bold;}\
        </style>\
        <div><img src='logo.png' style='width:150px; height:75px;'></div>\
        <div class='titulo1'>HISTORIAL DE INGRESOS Y GASTOS DE "+us.label+" "+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY') +"</div><br>"
        if(this.ventas.length>0){
        cadena+="<div>VENTAS DETALLE Y LOCAL</div> \
        <table>\
          <tr><th>TIPO</th><th>DETALLE</th><th>TOTAL</th><th>ACUENTA</th><th>SALDO</th><th>LOCAL</th><th>TITULAR</th><th>ESTADO</th></tr>"
          this.ventas.forEach(r => {
            if(r.fechaentrega==null || r.fechaentrega==''){
              ventas=ventas+r.acuenta
               vtotal=vtotal+r.total
               vsaldo=vsaldo+r.saldo
              cadena+="<tr><td>"+ r.tipo.substring(0,1)+'-'+r.id+"</td><td>"
              r.detalles.forEach(d => {
                  cadena+=" "+d.cantidad+" - "+d.nombreproducto+"<br>"
              });
              cadena+="</td><td>"+ r.total +" Bs</td><td>"+r.acuenta+" Bs</td><td>"+ r.saldo +" Bs</td><td>"+ r.local+"</td><td>"+ r.titular+"</td><td "
        if(r.estado=='POR COBRAR')  cadena+=color1
              cadena+=">"+r.estado+"</td></tr>"

      }
          });
        cadena+="</table><div><b>TOTAL VENTAS:</b>"+vtotal+"</div><br>"
        if(this.totalruta){
        cadena+="<div>VENTAS CON RUTA</div> \
        <table>\
          <tr><th>TIPO</th><th>DETALLE</th><th>TOTAL</th><th>ACUENTA</th><th>SALDO</th><th>LOCAL</th><th>TITULAR</th><th>ESTADO</th></tr>"
          this.ventas.forEach(r => {
            if(r.fechaentrega!=null && r.fechaentrega!=''){
              ventasruta=ventasruta+r.acuenta
              vrtotal=vrtotal+r.total
              vrsaldo=vrsaldo+r.saldo
              cadena+="<tr><td>"+ 'R-'+r.id+"</td><td>"
              r.detalles.forEach(d => {
                  cadena+=" "+d.cantidad+" - "+d.nombreproducto+"<br>"
              });
              cadena+="</td><td>"+ r.total +" Bs</td><td>"+r.acuenta+" Bs</td><td>"+ r.saldo +" Bs</td><td>"+ r.local+"</td><td>"+ r.titular+"</td><td "
        if(r.estado=='POR COBRAR')  cadena+=color1
              cadena+=">"+r.estado+"</td></tr>"
      }
          });
          //ventas=ventas+ventasruta
        cadena+="</table><div><b>TOTAL VENTAS RUTA:</b>"+vrtotal+"</div><br>\
        "}
      }
        let panulado=0
      if(this.totalanulado>0){
        panulado=panulado+this.totalanulado;
        cadena+="<div>INGRESOS DE ANULADOS PRESTAMOS</div>\
        <table>\
          <tr><th>CANTIDAD</th><th>MATERIAL</th><th>EFECTIVO</th><th>LOCAL</th><th>TITULAR</th></tr>"
          this.anulados.forEach(element => {
            element.cliente.local=element.cliente.local!=null?element.cliente.local.toString():''
            cadena+="<tr><td>"+element.cantidad+"</td><td>"+element.inventario.nombre+"</td><td>"+element.efectivo+" Bs</td>\
              <td>"+element.cliente.local+"</td><td>"+ element.cliente.titular+"</td></tr>"
              });
        cadena+="</table><div><b>TOTAL ANULADO:</b>"+this.totalanulado+"</div><br>"
        }
        let matventa=0
        if(this.totalpresventa>0){
        cadena+="<div>INGRESOS DE VENTA MATERIAL</div>\
        <table><tr><th>CANTIDAD</th><th>MATERIAL</th><th>EFECTIVO</th><th>LOCAL</th><th>TITULAR</th></tr>"

        this.prestamoventa.forEach(r=>{
          r.cliente.local=r.cliente.local!=null?r.cliente.local:''
          cadena+="<tr><td>"+r.cantidad+"</td><td>"+r.inventario.nombre+"</td><td>"+r.efectivo+" Bs.</td><td>"+r.cliente.local+"</td><td>"+r.cliente.titular+"</td></tr>"
          matventa+=parseFloat(r.efectivo)

        })
      cadena+="</table><div><b>TOTAL VENTA MATERIAL: </b> "+this.totalpresventa+" Bs</div><br>"}

      if(this.rpagos.length>0){
        let pendiente=''
        cadena+="<div>INGRESOS DE PENDIENTES DE PAGO</div>\
        <table><tr><th>TIPO</th><th>DETALLE</th><th>MONTO</th><th>LOCAL</th><th>TITULAR</th></tr>"
      this.rpagos.forEach(r=>{
        ccpago+=r.monto
        pendiente=r.venta.fechaentrega!=null&&r.venta.fechaentrega!=''?'R-'+r.venta.id:r.venta.tipo.substring(0,1)+'-'+r.venta.id
        r.venta.cliente.local=r.venta.cliente.local!=null?r.venta.cliente.local.toString():''
        cadena+="<tr><td>"+ pendiente +"</td><td>"
        r.venta.detalles.forEach(d => {
          cadena+=d.cantidad+" - "+d.nombreproducto+"<br>"
        });
        cadena+="</td><td>"+r.monto+" Bs </td><td>"+r.venta.cliente.local+"</td><td>"+r.venta.cliente.titular+"</td></tr>"
      })
      cadena+="</table><div><b>T.V. Pago: </b>"+this.totalpagos+"</div><br>"
      }
        let caja=0
        let gastos=0
      if(this.gastos.length>0){
      cadena+="<div>DETALLE DE GASTO</div>\
      <table><tr><th>MONTO</th><th>GLOSA</th><th>OBSERVACION</th></tr>"
      this.gastos.forEach(r=>{
        cadena+="<tr><td>"+r.precio+" Bs</td><td>"+r.glosa+"</td><td>"+r.observacion+"</td></tr>"
        if(r.glosa=='CAJA CHICA')
        caja+=parseFloat(r.precio!=null?r.precio:0)
        else
        gastos+=parseFloat(r.precio!=null?r.precio:0)

      })
        cadena+="</table><div><b>TOTAL GASTOS: </b>"+gastos+" Bs</div><br>"}
        if(this.chica.length>0){
        cadena+="<div>DETALLE DE GASTO CAJA CHICA</div>\
        <table><tr><th>MONTO</th><th>GLOSA</th><th>MOTIVO</th></tr>"

        this.chica.forEach(r=>{
        cadena+="<tr><td>"+r.monto+" Bs</td><td>"+r.glosa+"</td><td>"+r.motivo+"</td></tr>"
        caja+=parseFloat(r.monto!=null?r.monto:0)
      })
      cadena+="</table><div><b>TOTAL GASTO CAJA CHICA: </b>"+ caja+" Bs</div><br>"}

      cadena+="<table><tr><td>"
      if(vtotal>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL VENTAS CHICHA: </span>"+ vtotal+" Bs</div>"
      if(vrtotal>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL VENTAS RUTA: </span>"+ vrtotal+" Bs</div>"
      if(matventa>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL VENTA MATERIAL: </span>"+ matventa+" Bs</div>"
      cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL VENTAS : </span>"+ (vtotal + vrtotal + matventa)+" Bs</div>"
      cadena+="<hr>"
      if((ventas + ventasruta)>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL A CUENTA : </span>"+ (ventas + ventasruta ) +" Bs</div>"
      if(ccpago>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL COBRO DEUDA: </span>"+ ccpago+" Bs</div>"
      if(panulado>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL GARANTIAS ANULADOS: </span>"+ panulado+" Bs</div>"
      if(matventa>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL VENTA MATERIAL: </span>"+ matventa+" Bs</div>"
      cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL INGRESO : </span>"+ (ventas + ventasruta + matventa + ccpago + panulado)+" Bs</div>"
      cadena+="</td><td>"
        if((vsaldo + vrsaldo)>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL SALDO POR PAGAR: </span>"+ (vsaldo + vrsaldo)+" Bs</div>"
        if(this.totalPagoAlmacen>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL PAGO ALMACEN : </span>"+ this.totalPagoAlmacen+" Bs</div>"
        if(gastos>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL GASTOS : </span>"+ gastos+" Bs</div>"
      cadena+="</td><td>"
        if(this.totalCajaChicaIngreso>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL INGRESO CAJA CHICA : </span>"+ this.totalCajaChicaIngreso+" Bs</div>"
        if(caja>0) cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL GASTOS CAJA CHICA : </span>"+ caja+" Bs</div>"
        if(this.$store.state.login.user.id==1 && this.resumenplanilla>0 && this.user.id==0){
          cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>SALARIOS : </span>"+ this.resumenplanilla+" Bs</div>"
        }
        else{
          this.resumenplanilla=0
        }

      cadena+="</td></tr></table>"
      //cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL TOTAL ACUENTA-GASTO: </span>"+ ( ventas + ventasruta - gastos) +" Bs</div>"
      cadena+="<div style='font-size:16px'><span style='font-weight: bold;'>TOTAL EFECTIVO ENTREGADO: </span>"+ ( ventas + ventasruta + matventa  + ccpago + panulado - gastos - this.resumenplanilla) +" Bs</div>"

        let myWindow = window.open("_blank");
        myWindow.document.write(cadena);
        myWindow.document.close();
       /* setTimeout(function(){
              myWindow.print();
              myWindow.close();
            },500);*/
    },

    imprimirmisventasygastos(us){
      let mc=this
          if(!this.$store.state.login.gastoreporteuser)

      us.label=this.$store.state.login.user.name;

      function header(){
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
        doc.setFont(undefined,'bold')
        doc.text(5, 1, 'HISTORIAL DE INGRESOS Y GASTOS '+us.label)
        doc.text(5, 1.5,  'DE '+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY'))
        // doc.text(1, 3, 'Total')
        doc.text(1, 3, 'Tipo V')
        doc.text(2.5, 3, 'Cant')
        doc.text(3.5, 3, 'Producto')
        doc.text(7, 3,'Total' )
        doc.text(8.5, 3, 'Acuenta')
        doc.text(10, 3, 'Saldo')
        doc.text(11.5, 3, 'Titular')
        doc.text(15.5, 3, 'Local')
        doc.text(19, 3, 'Estado')
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
      let ventas=0
      let ventasruta=0
      let ccpago=0

      this.ventas.forEach(r=>{
        if(r.fechaentrega==null || r.fechaentrega==''){
        y+=0.5
        // console.log(r)
       doc.setFontSize(6);
        doc.text(1, y+3, r.tipo.substring(0,1)+'-'+r.id)
        doc.setFontSize(9);

        //doc.text(2.5, y+3, r.cantidad!=null?r.cantidad.toString():'')
        //doc.text(3.5, y+3, r.detalle!=null?r.detalle.toString():'')
        doc.text(7, y+3, r.total!=null?r.total.toString():''+' Bs.')
        doc.text(8.5, y+3, r.acuenta!=null?r.acuenta.toString():''+' Bs.')
        ventas+=parseFloat(r.acuenta!=null?r.acuenta:0)
        doc.text(10, y+3, r.saldo!=null?r.saldo.toString():''+' Bs.')
        doc.setFontSize(6);
        doc.text(11.5, y+3, r.titular!=null?r.titular.substring(0,20):'')
        doc.text(15.5, y+3, r.local!=null?r.local.toString():'')
        doc.setFontSize(9);
        if(r.estado=='POR COBRAR')  doc.setTextColor(255,0,0);
        doc.text(19, y+3, r.estado+'')
        doc.setTextColor(0,0,0);

      }
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })
      y+=0.5
        doc.text(1, y+3, 'T VENTA:')
        doc.text(3, y+3, ventas+' Bs.')
      y+=0.5

        doc.setFont(undefined,'bold')
        doc.text(1, y+3, '-------------------------------------------------')
      y+=0.5
        doc.text(1, y+3, 'REGISTRO DE VENTAS CON RUTA')
        doc.setFont(undefined,'normal')

      this.ventas.forEach(r=>{
        if(r.fechaentrega!=null && r.fechaentrega!=''){
        y+=0.5
        // console.log(r)
       doc.setFontSize(6);
        doc.text(1, y+3, 'R-'+r.id)
        doc.setFontSize(9);
        //doc.text(2.5, y+3, r.cantidad!=null?r.cantidad.toString():'')
        //doc.text(3.5, y+3, r.detalle!=null?r.detalle.toString():'')
        doc.text(7, y+3, r.total!=null?r.total.toString():''+' Bs.')
        doc.text(8.5, y+3, r.acuenta!=null?r.acuenta.toString():''+' Bs.')
        ventasruta+=parseFloat(r.acuenta!=null?r.acuenta:0)
        doc.text(10, y+3, r.saldo!=null?r.saldo.toString():''+' Bs.')
        doc.setFontSize(6);
        doc.text(11.5, y+3, r.titular!=null?r.titular.substring(0,20):'')
        doc.text(15.5, y+3, r.local!=null?r.local.toString():'')
        doc.setFontSize(9);
        if(r.estado=='POR COBRAR')  doc.setTextColor(255,0,0);
        doc.text(19, y+3, r.estado+'')
        doc.setTextColor(0,0,0);
      }
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })

      y+=0.5
        doc.text(1, y+3, 'TR VENTA:')
        doc.text(3, y+3, ventasruta+' Bs.')
      let gastos=0
      let caja=0
      ventas=ventas+ventasruta
            y+=0.5
        doc.setFont(undefined,'bold')
        doc.text(1, y+3, '-------------------------------------------------------')
      y+=0.5

        doc.text(1, y+3, 'DETALLE DE GASTO')
        doc.setFont(undefined,'normal')
      this.gastos.forEach(r=>{
        y+=0.5
        // console.log(r)
        doc.text(6.5, y+3, r.precio!=null?r.precio+' Bs':'0 Bs.')
        if(r.glosa=='CAJA CHICA')
        caja+=parseFloat(r.precio!=null?r.precio:0)
        else
        gastos+=parseFloat(r.precio!=null?r.precio:0)
        doc.text(10, y+3, r.glosa+': '+r.observacion)
        // doc.text(9, y+3, r.fecha!=null?r.fecha.toString():'')
        // doc.text(11.5, y+3, r.hora!=null?r.hora.toString():'')
        //doc.text(18.5, y+3, r.user!=null?r.user.toString():'')
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })
            y+=0.5
        doc.text(1, y+3, 'T GASTO:')
        doc.text(3, y+3, gastos+'Bs.')
        doc.setFont(undefined,'bold')
                  y+=0.5
        doc.text(1, y+3, '-------------------------------------------------------')
      y+=0.5
        doc.text(1, y+3, 'DETALLE DE GASTO CAJA CHICA')
        doc.setFont(undefined,'normal')
            this.chica.forEach(r=>{
        y+=0.5
        // console.log(r)
        doc.text(6.5, y+3, r.monto!=null?r.monto+' Bs':'0 Bs.')
        caja+=parseFloat(r.monto!=null?r.monto:0)
        doc.text(10, y+3, r.glosa==null?'GASTO':r.glosa+' : '+r.motivo)
        // doc.text(9, y+3, r.fecha!=null?r.fecha.toString():'')
        // doc.text(11.5, y+3, r.hora!=null?r.hora.toString():'')
        //doc.text(18.5, y+3, r.user!=null?r.user.toString():'')
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })

      y+=0.5
        doc.text(1, y+3, 'Gasto Caja Chica:')
        doc.text(6, y+3, caja+'Bs.')

      y+=0.5
        doc.text(1, y+3, '-------------------------------------------------------')
      y+=0.5
      let panulado=0
        doc.setFont(undefined,'bold')
        doc.text(1, y+3, 'INGRESOS DE ANULADOS PRESTAMOS')
        doc.setFont(undefined,'normal')
                if (y+3>25){
          doc.addPage();
          header()
          y=0
        }

      if(this.totalanulado>0){
        panulado=panulado+this.totalanulado;
              this.anulados.forEach(element => {
              y+=0.5
        doc.text(2.5, y+3, element.cantidad+'')
        doc.text(3.5, y+3, element.inventario.nombre)
        doc.text(7, y+3, element.efectivo+' Bs')
        doc.text(11.5, y+3, element.cliente.titular!=null?element.cliente.titular.substring(0,20):'')
        doc.text(15.5, y+3, element.cliente.local!=null?element.cliente.local.toString():'')
                if (y+3>25){
          doc.addPage();
          header()
          y=0
        }

              });
      y+=0.5
        doc.text(1, y+3, 'T. Anulado')
        doc.text(3, y+3, this.totalanulado+' Bs')

      }
      //if(this.totalpresventa>0){
      doc.setFont(undefined,'bold')
      y+=0.5
        doc.text(1, y+3, '-------------------------------------------------------')
      y+=0.5
        doc.text(1, y+3, 'INGRESOS DE VENTA MATERIAL')
        doc.setFont(undefined,'normal')
                if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
        let matventa=0
      this.prestamoventa.forEach(r=>{
        y+=0.5
        // console.log(r)
        doc.text(1, y+3, '')
        doc.text(2.5, y+3, r.cantidad+'')
        doc.setFontSize(6);
        doc.text(3.5, y+3, r.inventario.nombre)
       doc.setFontSize(9);
        doc.text(7, y+3, r.efectivo+' Bs.')
        matventa+=parseFloat(r.efectivo)
        doc.text(11.5, y+3, r.cliente.titular!=null?r.cliente.titular.substring(0,20):'')
        doc.text(15.5, y+3, r.cliente.local!=null?r.cliente.local.toString():'')

        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })
             y+=0.5
        doc.text(1, y+3, 'T MATERIAL')
        doc.text(3, y+3, this.totalpresventa+' Bs.')

      if(this.totalpagos>0){
        y+=0.5
      doc.setFont(undefined,'bold')
        doc.text(1, y+3, '-------------------------------------------------------')
        y+=0.5

        doc.text(1, y+3, 'INGRESOS DE PENDIENTES DE PAGO')
        doc.setFont(undefined,'normal')
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }

      this.rpagos.forEach(r=>{
        y+=0.5
        doc.setFontSize(6);
        doc.text(1, y+3, r.fechaentrega!=null&&r.fechaentrega!=''?'R-'+r.id:r.tipo.substring(0,1)+'-'+r.id)
        doc.setFontSize(9);
        doc.text(3.5, y+3, r.nombreproducto!=null?r.nombreproducto.toString():'')
        doc.text(7, y+3, r.monto!=null?r.monto+' Bs':' 0 Bs.')
        //ventas+=r.monto;
        ccpago+=r.monto
        doc.text(11.5, y+3, r.titular!=null?r.titular.substring(0,20):'')
        doc.text(15.5, y+3, r.local!=null?r.local.toString():'')
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })

      y+=0.5
        if (y+4>25){
          doc.addPage();
          header()
          y=0
        }
             doc.text(1, y+3, 'T.V. Pago:')
        doc.text(3, y+3, this.totalpagos+' Bs.')
      }
        if (y+4>25){
          doc.addPage();
          header()
          y=0
        }


      doc.setFontSize(11);
        doc.setFont(undefined,'normal')

      doc.text(2, y+4, 'T Ing Venta: ')
        doc.setFont(undefined,'bold')
      doc.text(5.5, y+4, ventas+'Bs')
        doc.setFont(undefined,'normal')

      doc.text(2, y+5, 'T Prest A: ')
        doc.setFont(undefined,'bold')
      doc.text(5.5, y+5, panulado+'Bs')
      doc.setFont(undefined,'normal')

          doc.text(2, y+5.5, 'T Mat Vent: ')
        doc.setFont(undefined,'bold')
      doc.text(5.5, y+5.5, matventa+'Bs')

        doc.setFont(undefined,'normal')
      doc.text(8, y+4, 'Total gasto: ')
        doc.setFont(undefined,'bold')
      doc.text(11, y+4, gastos+'Bs')
        doc.setFont(undefined,'normal')
      doc.text(14, y+4, 'Total saldo: ')
        doc.setFont(undefined,'bold')
      doc.text(17, y+4, (ventas-gastos)+' Bs')
        doc.setFont(undefined,'normal')
      doc.text(2, y+4.5, 'T. CxC pagos: ')
        doc.setFont(undefined,'bold')
      doc.text(5.5, y+4.5, ccpago+'Bs')
      if(this.$store.state.login.user.id==1){
        doc.setFont(undefined,'normal')
        doc.text(8, y+4.5, 'Salarios:')
        doc.setFont(undefined,'bold')
        doc.text(11, y+4.5, this.resumenplanilla+'Bs.')}
      // doc.save("Pago"+date.formatDate(Date.now(),'DD-MM-YYYY')+".pdf");
      window.open(doc.output('bloburl'), '_blank');
    },

    imprimirmisventasdetalle(us){
      /*let mc=this
                if(!this.$store.state.login.gastoreporteuser)

      us.label=this.$store.state.login.user.name;

      function header(){
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
        doc.setFont(undefined,'bold')
        doc.text(5, 1, 'Historial de ventas DETALLE ' +us.label)
        doc.text(5, 1.5,  'DE '+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY'))
        doc.text(1, 3, 'Usuario')
        doc.text(3, 3, 'Fecha')
        doc.text(5.5, 3, 'Total')
        doc.text(6.7, 3, 'Acuenta')
        doc.text(8.2, 3, 'Saldo')
        doc.text(9.5, 3, 'Producto')
        doc.text(12, 3, 'Estado')
        doc.text(14, 3, 'Hora')
        doc.text(16.5 , 3, 'Titular')
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
      let tsaldo=0
      let tacuenta=0
      let total=0
      let caja=0
      this.ventas.forEach(r=>{
        console.log(r)
        if(r.tipo=='DETALLE'){
        tsaldo=tsaldo+r.saldo;
        tacuenta=tacuenta+r.acuenta;
        total=total+r.total;
        // xx+=0.5
        y+=0.5
        doc.text(1, y+3, r.user!=null?r.user.substring(0,8 ):'')
        doc.text(3, y+3, date.formatDate(r.fecha,'DD/MM/YYYY')+'')
        doc.text(5.5, y+3, r.total!=null?r.total.toString():'')
        doc.text(6.7, y+3, r.acuenta!=null?r.acuenta.toString():'')
        doc.text(8.2, y+3, r.saldo!=null?r.saldo.toString():'')
        //doc.text(9.5, y+3, r.producto.substring(0,12)+'')
        doc.text(12, y+3, r.estado!=null?r.estado.toString():'')
        doc.text(14, y+3, r.hora)
        doc.text(16.5, y+3, r.titular!=null?r.titular.substring(0,25):'')
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }}
      })
      caja=total-tsaldo;
        doc.setFont(undefined,'bold')
      doc.text(2, y+4, 'Ventas totales: ')
        doc.setFont(undefined,'normal')
      doc.text(5, y+4, total+'Bs')

        doc.setFont(undefined,'bold')
      doc.text(7, y+4, 'Por Cobrar: ')
        doc.setFont(undefined,'normal')
      doc.text(10, y+4, tsaldo+' Bs')

        doc.setFont(undefined,'bold')
      doc.text(14, y+4, 'En Caja totales: ')
        doc.setFont(undefined,'normal')
      doc.text(18, y+4, caja+'Bs')


      // doc.save("Pago"+date.formatDate(Date.now(),'DD-MM-YYYY')+".pdf");
      window.open(doc.output('bloburl'), '_blank');*/
      let mc=this
          if(!this.$store.state.login.gastoreporteuser)
                us.label=this.$store.state.login.user.name;
        let ventas=0
        let color1="style='color:red'"
        let cadena="<style>\
        *{font-size:10px}\
        table{width:100%;border-collapse: collapse;}\
        table, th, td {  border: 1px solid;}\
        .titulo1{text-align:center; font-weight: bold;}\
        </style>\
        <div><img src='logo.png' style='width:150px; height:75px;'></div>\
        <div class='titulo1'>HISTORIAL VENTA DETALLE DE "+us.label+" "+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY') +"</div><br>"
        if(this.ventas.length>0){
        cadena+="<table>\
          <tr><th>TIPO</th><th>DETALLE</th><th>TOTAL</th><th>ACUENTA</th><th>SALDO</th><th>TITULAR</th><th>LOCAL</th><th>ESTADO</th></tr>"
          this.ventas.forEach(r => {
            if((r.fechaentrega==null || r.fechaentrega=='') && r.tipo=='DETALLE'){
              ventas=ventas+r.acuenta
              cadena+="<tr><td>"+ r.tipo.substring(0,1)+'-'+r.id+"</td><td>"
              r.detalles.forEach(d => {
                  cadena+=" "+d.cantidad+" - "+d.nombreproducto+"<br>"
              });
              cadena+="</td><td>"+ r.total +" Bs</td><td>"+r.acuenta+" Bs</td><td>"+ r.saldo +" Bs</td><td>"+ r.titular+"</td><td>"+ r.local+"</td><td "
        if(r.estado=='POR COBRAR')  cadena+=color1
              cadena+=">"+r.estado+"</td></tr>"
      }
          });
        cadena+="</table><div><b>TOTAL VENTAS:</b>"+ventas+"</div><br>"

        let myWindow = window.open("_blank");
        myWindow.document.write(cadena);
        myWindow.document.close();
        }
    },

    imprimirmisventaslocal(us){
    /*  let mc=this
                if(!this.$store.state.login.gastoreporteuser)

      us.label=this.$store.state.login.user.name;

      function header(){
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
        doc.setFont(undefined,'bold')
        doc.text(5, 1, 'Historial de ventas LOCAL '+ us.label)
        doc.text(5, 1.5,  'DE '+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY'))
        doc.text(1, 3, 'Usuario')
        doc.text(2.5, 3, 'Fecha Hora')
        doc.text(6, 3, 'Producto')
        doc.text(8.5, 3, 'Total')
        doc.text(10, 3, 'ACuenta')
        doc.text(11.5, 3, 'Saldo')
        doc.text(13, 3, 'Estado')
        doc.text(15, 3, 'Local')
        doc.text(18, 3, 'Titular')
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
      let tsaldo=0
      let tacuenta=0
      let caja=0
      let total=0
      this.ventas.forEach(r=>{
        if(r.tipo=='LOCAL'){
        tsaldo=tsaldo+r.saldo;
        tacuenta=tacuenta+r.acuenta;
        total=total+r.total;
        // xx+=0.5
        y+=0.5
        doc.text(1, y+3, r.user!=null?r.user.substring(0,8):'')
        doc.text(2.5, y+3, r.fecha+' '+r.hora)
        doc.text(6, y+3, r.producto.substring(0,12 ))
        doc.text(8.5, y+3, r.total!=null?r.total.toString():'')
        doc.text(10, y+3, r.acuenta!=null?r.acuenta.toString():'')
        doc.text(11.5, y+3, r.saldo!=null?r.saldo.toString():'')
        doc.text(13, y+3, r.estado!=null?r.estado.toString():'')
        doc.text(15, y+3, r.local!=null?r.local.toString():'')
        doc.text(18, y+3, r.titular!=null?r.titular.substring(0,6 ):'')

        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }}
      })
      caja=total-tsaldo
        doc.setFont(undefined,'bold')
      doc.text(2, y+4, 'Ventas totales: ')
        doc.setFont(undefined,'normal')
      doc.text(5, y+4, total+'Bs')

        doc.setFont(undefined,'bold')
      doc.text(7, y+4, 'Por Cobrar: ')
        doc.setFont(undefined,'normal')
      doc.text(10, y+4, tsaldo+' Bs')

        doc.setFont(undefined,'bold')
      doc.text(14, y+4, 'En Caja totales: ')
        doc.setFont(undefined,'normal')
      doc.text(18, y+4, caja+'Bs')


      // doc.save("Pago"+date.formatDate(Date.now(),'DD-MM-YYYY')+".pdf");
      window.open(doc.output('bloburl'), '_blank');*/
      let mc=this
          if(!this.$store.state.login.gastoreporteuser)
                us.label=this.$store.state.login.user.name;
        let ventas=0
        let color1="style='color:red'"
        let cadena="<style>\
        *{font-size:10px}\
        table{width:100%;border-collapse: collapse;}\
        table, th, td {  border: 1px solid;}\
        .titulo1{text-align:center; font-weight: bold;}\
        </style>\
        <div><img src='logo.png' style='width:150px; height:75px;'></div>\
        <div class='titulo1'>HISTORIAL VENTA LOCAL DE "+us.label+" "+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY') +"</div><br>"
        if(this.ventas.length>0){
        cadena+="<table>\
          <tr><th>TIPO</th><th>DETALLE</th><th>TOTAL</th><th>ACUENTA</th><th>SALDO</th><th>TITULAR</th><th>LOCAL</th><th>ESTADO</th></tr>"
          this.ventas.forEach(r => {
            if((r.fechaentrega==null || r.fechaentrega=='') && r.tipo=='LOCAL'){
              ventas=ventas+r.acuenta
              cadena+="<tr><td>"+ r.tipo.substring(0,1)+'-'+r.id+"</td><td>"
              r.detalles.forEach(d => {
                  cadena+=" "+d.cantidad+" - "+d.nombreproducto+"<br>"
              });
              cadena+="</td><td>"+ r.total +" Bs</td><td>"+r.acuenta+" Bs</td><td>"+ r.saldo +" Bs</td><td>"+ r.titular+"</td><td>"+ r.local+"</td><td "
        if(r.estado=='POR COBRAR')  cadena+=color1
              cadena+=">"+r.estado+"</td></tr>"
      }
          });
        cadena+="</table><div><b>TOTAL VENTAS:</b>"+ventas+"</div><br>"

        let myWindow = window.open("_blank");
        myWindow.document.write(cadena);
        myWindow.document.close();
        }
    },

    imprimir(us){
 /*     let mc=this
                if(!this.$store.state.login.gastoreporteuser)

      us.label=this.$store.state.login.user.name;

      function header(){
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
        doc.setFont(undefined,'bold')
        doc.text(5, 1, 'Historial de gastos '+ us.label)
        doc.text(5, 1.5,  'DE '+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY'))
        doc.text(1.5, 3, 'Usuario')
        doc.text(4, 3, 'Precio')
        doc.text(6.5, 3, 'Observacion')
        doc.text(11, 3, 'Glosa')
        doc.text(15.5, 3, 'Fecha')
        doc.text(17.5, 3, 'Hora')
        doc.setFont(undefined,'normal')
      }
      var doc = new jsPDF('p','cm','letter')
      doc.setFont("courier");
      doc.setFontSize(9);
      header()
      let y=0
      let cont=1
      let sumgasto=0
      let caja=0

      this.gastos.forEach(r=>{
        if(r.glosa=='CAJA CHICA')
        caja+=parseFloat(r.precio)
        else
        sumgasto+=parseFloat(r.precio)
        console.log(r)
        // xx+=0.5
        y+=0.5
        doc.text(1.5, y+3, r.user)
        doc.text(4, y+3, r.precio+'')
        doc.text(6.5, y+3, r.observacion)
        doc.text(11, y+3, r.glosa)
        doc.text(15.5, y+3, moment(r.fecha).format('DD/MM/YYYY')+'')
        doc.text(17.5, y+3, r.hora)

        cont++
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })
              doc.setFont(undefined,'bold')
                  y+=0.5
        doc.text(1, y+3, '-------------------------------------------------------')
      y+=0.5
        doc.text(1, y+3, 'DETALLE DE GASTO CAJA CHICA')
        y+=0.5
        doc.setFont(undefined,'normal')
            this.chica.forEach(r=>{
        caja+=parseFloat(r.monto)
        console.log(r)
        // xx+=0.5
        y+=0.5
        doc.text(1.5, y+3, r.user.name)
        doc.text(4, y+3, r.monto+'')
        doc.text(6.5, y+3, r.motivo)
        doc.text(11, y+3, r.glosa==null?'GASTO':r.glosa+' : '+r.motivo)
        doc.text(15.5, y+3, moment(r.fecha).format('DD/MM/YYYY'))
        doc.text(17.5, y+3, r.hora)

        cont++
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })
      y+=0.5
        doc.setFont(undefined,'bold')
              doc.text(1, y+3, 'TOTAL:')
        doc.setFont(undefined,'normal')
        doc.text(3, y+3, sumgasto+' Bs')
      y+=0.5
              if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      y+=0.5
          doc.setFont(undefined,'bold')
          doc.text(1, y+3, 'caja chica:')
        doc.setFont(undefined,'normal')
        doc.text(3.5, y+3, caja+' Bs')
                      if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      y+=0.5
                if(this.$store.state.login.user.id==1){
                doc.setFont(undefined,'bold')
          doc.text(1, y+3, 'SALARIOS:')
        doc.setFont(undefined,'normal')
        doc.text(3.5, y+3, this.resumenplanilla+' Bs')}
      // doc.text(2, y+4, 'Ventas totales: ')
      // doc.text(5, y+4, this.ventat+'Bs')
      // doc.text(7, y+4, 'Por cobrar totales: ')
      // doc.text(11, y+4, this.porc+'Bs')
      // doc.text(14, y+4, 'Saldo totales: ')
      // doc.text(17, y+4, this.saldoc+'Bs')
      // doc.save("Pago"+date.formatDate(Date.now(),'DD-MM-YYYY')+".pdf");
      window.open(doc.output('bloburl'), '_blank');*/
      let mc=this
          if(!this.$store.state.login.gastoreporteuser)

        us.label=this.$store.state.login.user.name;
        let cadena="<style>\
        *{font-size:10px}\
        table{width:100%;border-collapse: collapse;}\
        table, th, td {  border: 1px solid;}\
        .titulo1{text-align:center; font-weight: bold;}\
        </style>\
        <div><img src='logo.png' style='width:150px; height:75px;'></div>\
        <div class='titulo1'>HISTORIAL GASTOS DE "+us.label+" "+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY') +"</div><br>"

        let caja=0
        let gastos=0
      if(this.gastos.length>0){
      cadena+="<div>DETALLE DE GASTO</div>\
      <table><tr><th>MONTO</th><th>GLOSA</th><th>OBSERVACION</th></tr>"
      this.gastos.forEach(r=>{
        cadena+="<tr><td>"+r.precio+" Bs</td><td>"+r.glosa+"</td><td>"+r.observacion+"</td></tr>"
        if(r.glosa=='CAJA CHICA')
        caja+=parseFloat(r.precio!=null?r.precio:0)
        else
        gastos+=parseFloat(r.precio!=null?r.precio:0)

      })
        cadena+="</table><div><b>TOTAL GASTOS: </b>"+gastos+" Bs</div><br>"}
        if(this.chica.length>0){
        cadena+="<div>DETALLE DE GASTO CAJA CHICA</div>\
        <table><tr><th>MONTO</th><th>GLOSA</th><th>MOTIVO</th></tr>"

        this.chica.forEach(r=>{
        cadena+="<tr><td>"+r.monto+" Bs</td><td>"+r.glosa+"</td><td>"+r.motivo+"</td></tr>"
        caja+=parseFloat(r.monto!=null?r.monto:0)
      })
      cadena+="</table><div><b>TOTAL GASTO CAJA CHICA: </b>"+ caja+" Bs</div><br>"}

      cadena+="<table><tr><td>"

      cadena+="</td><td>"
        if(gastos>0) cadena+="<div style='font-size:16px'><b>TOTAL GASTOS : </b>"+ gastos+" Bs</div>"
      cadena+="</td><td>"
        if(caja>0) cadena+="<div style='font-size:16px'><b>TOTAL CAJA CHICA : </b>"+ caja+" Bs</div>"

      cadena+="</td></tr></table>"


        let myWindow = window.open("_blank");
        myWindow.document.write(cadena);
        myWindow.document.close();
    },
    misgastos(){
      if(this.rango=='DIA'){
        this.fecha2=this.fecha1
      }
      this.$q.loading.show()
      this.gastos=[]
      this.chica=[]
      if(!this.$store.state.login.gastoreporteuser) this.user={label:this.$store.state.login.user.name,id:this.$store.state.login.user.id}
      //console.log(this.user)
      $('#example').DataTable().destroy()
      $('#example2').DataTable().destroy()

      this.$axios.post(process.env.API+'/misgastos',{fecha1:this.fecha1,fecha2:this.fecha2,user_id:this.user.id}).then(res=>{
        //console.log(res.data)
        // this.gastos=res.data
         $('#example').DataTable().destroy()

        console.log(res.data)
        res.data.forEach(r=>{
          //console.log(r.glosa.nombre)
          this.gastos.push({
            id:r.id,
            observacion:r.observacion,
            glosa:r.glosa.nombre,
            precio:r.precio,
            fecha:moment(r.fecha).format('DD-MM-YYYY'),
            fecha2:moment(r.fecha).format('YYYY-MM-DD'),
            hora:r.hora,
            user:r.user.name,
            gl:r.glosa,

          })
        })
          if(!this.$store.state.login.gastoreporteuser) this.user={label:this.$store.state.login.user.name,id:this.$store.state.login.user.id}
            this.$axios.post(process.env.API+'/listcaja',{fecha1:this.fecha1,fecha2:this.fecha2,user_id:this.user.id}).then(res=>{
        console.log(res.data)
        //return false
        $('#example2').DataTable().destroy()
              this.totalCajaChicaIngreso=0
                 res.data.forEach(r => {
            if(r.tipo=='GASTO'){
                this.chica.push({
                  id:r.id,
                  motivo:r.motivo,
                  glosa:r.glosa==null?'GASTO':r.glosa.nombre,
                  monto:r.monto,
                  fecha:moment(r.fecha).format('DD-MM-YYYY'),
                  hora:r.hora,
                  user:r.user.name,
                  gl:r.glosa==null?'GASTO':r.glosa,
                  })
                        }
            if(r.tipo=='AGREGA'){
              this.totalCajaChicaIngreso+=parseFloat(r.monto)
              
            }
         })
         this.$nextTick(()=>{
          $('#example2').DataTable( {
                       "language":{
              "processing": "Procesando...",
              "lengthMenu": "Mostrar _MENU_ registros",
              "zeroRecords": "No se encontraron resultados",
              "emptyTable": "Ningún dato disponible en esta tabla",
              "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
              "infoFiltered": "(filtrado de un total de _MAX_ registros)",
              "search": "Buscar:",
              "infoThousands": ",",
              "loadingRecords": "Cargando...",
              "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
              },
              "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
              },
              "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad",
                "collection": "Colección",
                "colvisRestore": "Restaurar visibilidad",
                "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
                "copySuccess": {
                  "1": "Copiada 1 fila al portapapeles",
                  "_": "Copiadas %ds fila al portapapeles"
                },
                "copyTitle": "Copiar al portapapeles",
                "csv": "CSV",
                "excel": "Excel",
                "pageLength": {
                  "-1": "Mostrar todas las filas",
                  "_": "Mostrar %d filas"
                },
                "pdf": "PDF",
                "print": "Imprimir",
                "renameState": "Cambiar nombre",
                "updateState": "Actualizar",
                "createState": "Crear Estado",
                "removeAllStates": "Remover Estados",
                "removeState": "Remover",
                "savedStates": "Estados Guardados",
                "stateRestore": "Estado %d"
              },
              "autoFill": {
                "cancel": "Cancelar",
                "fill": "Rellene todas las celdas con <i>%d<\/i>",
                "fillHorizontal": "Rellenar celdas horizontalmente",
                "fillVertical": "Rellenar celdas verticalmentemente"
              },
              "decimal": ",",
              "searchBuilder": {
                "add": "Añadir condición",
                "button": {
                  "0": "Constructor de búsqueda",
                  "_": "Constructor de búsqueda (%d)"
                },
                "clearAll": "Borrar todo",
                "condition": "Condición",
                "conditions": {
                  "date": {
                    "after": "Despues",
                    "before": "Antes",
                    "between": "Entre",
                    "empty": "Vacío",
                    "equals": "Igual a",
                    "notBetween": "No entre",
                    "notEmpty": "No Vacio",
                    "not": "Diferente de"
                  },
                  "number": {
                    "between": "Entre",
                    "empty": "Vacio",
                    "equals": "Igual a",
                    "gt": "Mayor a",
                    "gte": "Mayor o igual a",
                    "lt": "Menor que",
                    "lte": "Menor o igual que",
                    "notBetween": "No entre",
                    "notEmpty": "No vacío",
                    "not": "Diferente de"
                  },
                  "string": {
                    "contains": "Contiene",
                    "empty": "Vacío",
                    "endsWith": "Termina en",
                    "equals": "Igual a",
                    "notEmpty": "No Vacio",
                    "startsWith": "Empieza con",
                    "not": "Diferente de",
                    "notContains": "No Contiene",
                    "notStarts": "No empieza con",
                    "notEnds": "No termina con"
                  },
                  "array": {
                    "not": "Diferente de",
                    "equals": "Igual",
                    "empty": "Vacío",
                    "contains": "Contiene",
                    "notEmpty": "No Vacío",
                    "without": "Sin"
                  }
                },
                "data": "Data",
                "deleteTitle": "Eliminar regla de filtrado",
                "leftTitle": "Criterios anulados",
                "logicAnd": "Y",
                "logicOr": "O",
                "rightTitle": "Criterios de sangría",
                "title": {
                  "0": "Constructor de búsqueda",
                  "_": "Constructor de búsqueda (%d)"
                },
                "value": "Valor"
              },
              "searchPanes": {
                "clearMessage": "Borrar todo",
                "collapse": {
                  "0": "Paneles de búsqueda",
                  "_": "Paneles de búsqueda (%d)"
                },
                "count": "{total}",
                "countFiltered": "{shown} ({total})",
                "emptyPanes": "Sin paneles de búsqueda",
                "loadMessage": "Cargando paneles de búsqueda",
                "title": "Filtros Activos - %d",
                "showMessage": "Mostrar Todo",
                "collapseMessage": "Colapsar Todo"
              },
              "select": {
                "cells": {
                  "1": "1 celda seleccionada",
                  "_": "%d celdas seleccionadas"
                },
                "columns": {
                  "1": "1 columna seleccionada",
                  "_": "%d columnas seleccionadas"
                },
                "rows": {
                  "1": "1 fila seleccionada",
                  "_": "%d filas seleccionadas"
                }
              },
              "thousands": ".",
              "datetime": {
                "previous": "Anterior",
                "next": "Proximo",
                "hours": "Horas",
                "minutes": "Minutos",
                "seconds": "Segundos",
                "unknown": "-",
                "amPm": [
                  "AM",
                  "PM"
                ],
                "months": {
                  "0": "Enero",
                  "1": "Febrero",
                  "10": "Noviembre",
                  "11": "Diciembre",
                  "2": "Marzo",
                  "3": "Abril",
                  "4": "Mayo",
                  "5": "Junio",
                  "6": "Julio",
                  "7": "Agosto",
                  "8": "Septiembre",
                  "9": "Octubre"
                },
                "weekdays": [
                  "Dom",
                  "Lun",
                  "Mar",
                  "Mie",
                  "Jue",
                  "Vie",
                  "Sab"
                ]
              },
              "editor": {
                "close": "Cerrar",
                "create": {
                  "button": "Nuevo",
                  "title": "Crear Nuevo Registro",
                  "submit": "Crear"
                },
                "edit": {
                  "button": "Editar",
                  "title": "Editar Registro",
                  "submit": "Actualizar"
                },
                "remove": {
                  "button": "Eliminar",
                  "title": "Eliminar Registro",
                  "submit": "Eliminar",
                  "confirm": {
                    "_": "¿Está seguro que desea eliminar %d filas?",
                    "1": "¿Está seguro que desea eliminar 1 fila?"
                  }
                },
                "error": {
                  "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
                },
                "multi": {
                  "title": "Múltiples Valores",
                  "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
                  "restore": "Deshacer Cambios",
                  "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
                }
              },
              "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
              "stateRestore": {
                "creationModal": {
                  "button": "Crear",
                  "name": "Nombre:",
                  "order": "Clasificación",
                  "paging": "Paginación",
                  "search": "Busqueda",
                  "select": "Seleccionar",
                  "columns": {
                    "search": "Búsqueda de Columna",
                    "visible": "Visibilidad de Columna"
                  },
                  "title": "Crear Nuevo Estado",
                  "toggleLabel": "Incluir:"
                },
                "emptyError": "El nombre no puede estar vacio",
                "removeConfirm": "¿Seguro que quiere eliminar este %s?",
                "removeError": "Error al eliminar el registro",
                "removeJoiner": "y",
                "removeSubmit": "Eliminar",
                "renameButton": "Cambiar Nombre",
                "renameLabel": "Nuevo nombre para %s",
                "duplicateError": "Ya existe un Estado con este nombre.",
                "emptyStates": "No hay Estados guardados",
                "removeTitle": "Remover Estado",
                "renameTitle": "Cambiar Nombre Estado"
              }
            },
                      dom: 'Blfrtip',
            // pageLength: 5,
             lengthMenu: [[-1,20, 50, 100], ["All",20, 50, 100]],
            buttons: [
               'excel', 'pdf'
             ],
            "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;

              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  typeof i === 'number' ?
                    i : 0;
              };

              // Total over all pages
              this.tot = api
                .column( 1 )
                .data()
                .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );
              // console.log(this.tot)
              // Total over this page
              this.pageTotal = api
                .column( 1, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );
              $( api.column( 4 ).footer() ).html(
                '$'+this.pageTotal +' ( $'+ this.tot +' total)'
              );
            }
          } );
        })

        this.$nextTick(()=>{
          $('#example').DataTable( {
                       "language":{
              "processing": "Procesando...",
              "lengthMenu": "Mostrar _MENU_ registros",
              "zeroRecords": "No se encontraron resultados",
              "emptyTable": "Ningún dato disponible en esta tabla",
              "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
              "infoFiltered": "(filtrado de un total de _MAX_ registros)",
              "search": "Buscar:",
              "infoThousands": ",",
              "loadingRecords": "Cargando...",
              "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
              },
              "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
              },
              "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad",
                "collection": "Colección",
                "colvisRestore": "Restaurar visibilidad",
                "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
                "copySuccess": {
                  "1": "Copiada 1 fila al portapapeles",
                  "_": "Copiadas %ds fila al portapapeles"
                },
                "copyTitle": "Copiar al portapapeles",
                "csv": "CSV",
                "excel": "Excel",
                "pageLength": {
                  "-1": "Mostrar todas las filas",
                  "_": "Mostrar %d filas"
                },
                "pdf": "PDF",
                "print": "Imprimir",
                "renameState": "Cambiar nombre",
                "updateState": "Actualizar",
                "createState": "Crear Estado",
                "removeAllStates": "Remover Estados",
                "removeState": "Remover",
                "savedStates": "Estados Guardados",
                "stateRestore": "Estado %d"
              },
              "autoFill": {
                "cancel": "Cancelar",
                "fill": "Rellene todas las celdas con <i>%d<\/i>",
                "fillHorizontal": "Rellenar celdas horizontalmente",
                "fillVertical": "Rellenar celdas verticalmentemente"
              },
              "decimal": ",",
              "searchBuilder": {
                "add": "Añadir condición",
                "button": {
                  "0": "Constructor de búsqueda",
                  "_": "Constructor de búsqueda (%d)"
                },
                "clearAll": "Borrar todo",
                "condition": "Condición",
                "conditions": {
                  "date": {
                    "after": "Despues",
                    "before": "Antes",
                    "between": "Entre",
                    "empty": "Vacío",
                    "equals": "Igual a",
                    "notBetween": "No entre",
                    "notEmpty": "No Vacio",
                    "not": "Diferente de"
                  },
                  "number": {
                    "between": "Entre",
                    "empty": "Vacio",
                    "equals": "Igual a",
                    "gt": "Mayor a",
                    "gte": "Mayor o igual a",
                    "lt": "Menor que",
                    "lte": "Menor o igual que",
                    "notBetween": "No entre",
                    "notEmpty": "No vacío",
                    "not": "Diferente de"
                  },
                  "string": {
                    "contains": "Contiene",
                    "empty": "Vacío",
                    "endsWith": "Termina en",
                    "equals": "Igual a",
                    "notEmpty": "No Vacio",
                    "startsWith": "Empieza con",
                    "not": "Diferente de",
                    "notContains": "No Contiene",
                    "notStarts": "No empieza con",
                    "notEnds": "No termina con"
                  },
                  "array": {
                    "not": "Diferente de",
                    "equals": "Igual",
                    "empty": "Vacío",
                    "contains": "Contiene",
                    "notEmpty": "No Vacío",
                    "without": "Sin"
                  }
                },
                "data": "Data",
                "deleteTitle": "Eliminar regla de filtrado",
                "leftTitle": "Criterios anulados",
                "logicAnd": "Y",
                "logicOr": "O",
                "rightTitle": "Criterios de sangría",
                "title": {
                  "0": "Constructor de búsqueda",
                  "_": "Constructor de búsqueda (%d)"
                },
                "value": "Valor"
              },
              "searchPanes": {
                "clearMessage": "Borrar todo",
                "collapse": {
                  "0": "Paneles de búsqueda",
                  "_": "Paneles de búsqueda (%d)"
                },
                "count": "{total}",
                "countFiltered": "{shown} ({total})",
                "emptyPanes": "Sin paneles de búsqueda",
                "loadMessage": "Cargando paneles de búsqueda",
                "title": "Filtros Activos - %d",
                "showMessage": "Mostrar Todo",
                "collapseMessage": "Colapsar Todo"
              },
              "select": {
                "cells": {
                  "1": "1 celda seleccionada",
                  "_": "%d celdas seleccionadas"
                },
                "columns": {
                  "1": "1 columna seleccionada",
                  "_": "%d columnas seleccionadas"
                },
                "rows": {
                  "1": "1 fila seleccionada",
                  "_": "%d filas seleccionadas"
                }
              },
              "thousands": ".",
              "datetime": {
                "previous": "Anterior",
                "next": "Proximo",
                "hours": "Horas",
                "minutes": "Minutos",
                "seconds": "Segundos",
                "unknown": "-",
                "amPm": [
                  "AM",
                  "PM"
                ],
                "months": {
                  "0": "Enero",
                  "1": "Febrero",
                  "10": "Noviembre",
                  "11": "Diciembre",
                  "2": "Marzo",
                  "3": "Abril",
                  "4": "Mayo",
                  "5": "Junio",
                  "6": "Julio",
                  "7": "Agosto",
                  "8": "Septiembre",
                  "9": "Octubre"
                },
                "weekdays": [
                  "Dom",
                  "Lun",
                  "Mar",
                  "Mie",
                  "Jue",
                  "Vie",
                  "Sab"
                ]
              },
              "editor": {
                "close": "Cerrar",
                "create": {
                  "button": "Nuevo",
                  "title": "Crear Nuevo Registro",
                  "submit": "Crear"
                },
                "edit": {
                  "button": "Editar",
                  "title": "Editar Registro",
                  "submit": "Actualizar"
                },
                "remove": {
                  "button": "Eliminar",
                  "title": "Eliminar Registro",
                  "submit": "Eliminar",
                  "confirm": {
                    "_": "¿Está seguro que desea eliminar %d filas?",
                    "1": "¿Está seguro que desea eliminar 1 fila?"
                  }
                },
                "error": {
                  "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
                },
                "multi": {
                  "title": "Múltiples Valores",
                  "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
                  "restore": "Deshacer Cambios",
                  "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
                }
              },
              "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
              "stateRestore": {
                "creationModal": {
                  "button": "Crear",
                  "name": "Nombre:",
                  "order": "Clasificación",
                  "paging": "Paginación",
                  "search": "Busqueda",
                  "select": "Seleccionar",
                  "columns": {
                    "search": "Búsqueda de Columna",
                    "visible": "Visibilidad de Columna"
                  },
                  "title": "Crear Nuevo Estado",
                  "toggleLabel": "Incluir:"
                },
                "emptyError": "El nombre no puede estar vacio",
                "removeConfirm": "¿Seguro que quiere eliminar este %s?",
                "removeError": "Error al eliminar el registro",
                "removeJoiner": "y",
                "removeSubmit": "Eliminar",
                "renameButton": "Cambiar Nombre",
                "renameLabel": "Nuevo nombre para %s",
                "duplicateError": "Ya existe un Estado con este nombre.",
                "emptyStates": "No hay Estados guardados",
                "removeTitle": "Remover Estado",
                "renameTitle": "Cambiar Nombre Estado"
              }
            },
                      dom: 'Blfrtip',
            // pageLength: 5,
             lengthMenu: [[-1,20, 50, 100], [ "All",20, 50, 100]],
            buttons: [
              'excel', 'pdf'
             ],
            "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;

              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  typeof i === 'number' ?
                    i : 0;
              };

              // Total over all pages
              this.tot = api
                .column( 1 )
                .data()
                .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );
              // console.log(this.tot)
              // Total over this page
              this.pageTotal = api
                .column( 1, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );
              // this.ttotal= api
              //   .column( 3, { page: 'current'} )
              //   .data()
              //   .reduce( function (a, b) {
              //     return intVal(a) + intVal(b);
              //   }, 0 );
              // this.tcuenta= api
              //   .column(4 , { page: 'current'} )
              //   .data()
              //   .reduce( function (a, b) {
              //     return intVal(a) + intVal(b);
              //   }, 0 );
              // this.tsaldo= api
              //   .column(5 , { page: 'current'} )
              //   .data()
              //   .reduce( function (a, b) {
              //     return intVal(a) + intVal(b);
              //   }, 0 );

              // Update footer
              $( api.column( 4 ).footer() ).html(
                '$'+this.pageTotal +' ( $'+ this.tot +' total)'
                // 'total:'+this.ttotal +' a cuenta:'+this.tcuenta +' saldo:'+this.tsaldo +' '
              );
            }
          } );
        })

        })

        this.$axios.post(process.env.API+'/replanilla',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
          console.log(res.data)
            this.resumenplanilla=res.data[0].total==null?0:res.data[0].total
        })

        this.$axios.post(process.env.API+'/totalReporte',{ini:this.fecha1,fin:this.fecha2}).then(res=>{
          console.log(res.data)
            this.totalPagoAlmacen=res.data[0].total==null?0:res.data[0].total
        })
/*
        this.$axios.post(process.env.API+'/misventas',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
          this.$q.loading.hide()

           console.log(res.data)
           console.log(this.user.id)
          this.$q.loading.hide()
          this.ventas=[]
          res.data.forEach(r=>{
             if (r.estado!='ANULADO'){
              if(this.$store.state.login.gastoreporteuser){
              if(this.user.id==0){this.ventas.push({
                id:r.id,
                fecha:r.fecha,
                total:r.total,
                // tipo:r.tipo,
                acuenta:r.acuenta,
                saldo:r.saldo,
                //producto:r.detalle.nombreproducto,//
                estado:r.estado,
                local:r.cliente.local,
                titular:r.cliente.titular,
                user:r.user.name,
                //detalle:r.detalle.nombreproducto,//
                //cantidad:r.detalle.cantidad,//
                fechaentrega:r.fechaentrega,
                tipo:r.tipo,
                hora:r.hora
              })}
              else{
              if(this.user.id==r.user_id){this.ventas.push({
                id:r.id,
                fecha:r.fecha,
                total:r.total,
                tipo:r.tipo,
                acuenta:r.acuenta,
                saldo:r.saldo,
                //producto:r.detalle.nombreproducto,
                estado:r.estado,
                local:r.cliente.local,
                titular:r.cliente.titular,
                user:r.user.name,
               // detalle:r.detalle.nombreproducto,
               // cantidad:r.detalle.cantidad,
                fechaentrega:r.fechaentrega,
                // tipo:r.tipo,
                hora:r.hora
              })}
              }
              }
              else{
                if(this.$store.state.login.user.id==r.user_id)
              this.ventas.push({
                id:r.id,
                fecha:r.fecha,
                total:r.total,
                tipo:r.tipo,
                acuenta:r.acuenta,
                saldo:r.saldo,
                //producto:r.detalle.nombreproducto,
                estado:r.estado,
                local:r.cliente.local,
                titular:r.cliente.titular,
                user:r.user.name,
                //detalle:r.detalle.nombreproducto,
                //cantidad:r.detalle.cantidad,
                fechaentrega:r.fechaentrega,
                // tipo:r.tipo,
                hora:r.hora
              })
              }
              }
          })*/
          if(!this.$store.state.login.gastoreporteuser){
            this.user=this.users.find(r=>r.id==this.$store.state.login.user.id)
          }
          this.$axios.post(process.env.API+'/reportSale',{fecha1:this.fecha1,fecha2:this.fecha2,user_id:this.user.id}).then(res=>{
          this.ventas=[]
            res.data.forEach(r => {
              this.ventas.push({
                id:r.id,
                fecha:r.fecha,
                total:r.total,
                acuenta:r.acuenta,
                saldo:r.saldo,
                estado:r.estado,
                local:r.cliente.local==null?'':r.cliente.local,
                titular:r.cliente.titular,
                user:r.user.name,
                fechaentrega:r.fechaentrega,
                tipo:r.tipo,
                hora:r.hora,
                detalles:r.detalles
              })
            })

          console.log(this.ventas)
          if(!this.$store.state.login.gastoreporteuser) this.user={label:this.$store.state.login.user.name,id:this.$store.state.login.user.id}
          this.$axios.post(process.env.API+'/misanulados',{fecha1:this.fecha1,fecha2:this.fecha2,id:this.user.id}).then(res=>{
            console.log(res.data)
          this.anulados=res.data;

          if(!this.$store.state.login.gastoreporteuser) this.user={label:this.$store.state.login.user.name,id:this.$store.state.login.user.id}

            this.$axios.post(process.env.API+'/reportepago',{fecha1:this.fecha1,fecha2:this.fecha2,id:this.user.id}).then(res=>{
              console.log(res.data)
              this.rpagos=[]
              this.rpagos=res.data
          if(!this.$store.state.login.gastoreporteuser)
          this.user={label:this.$store.state.login.user.name,id:this.$store.state.login.user.id}

            this.$axios.post(process.env.API+'/reporteventa',{fecha1:this.fecha1,fecha2:this.fecha2,id:this.user.id}).then(res=>{
              //console.log(res.data)
            this.prestamoventa=[]
            this.prestamoventa=res.data
            this.$q.loading.hide()
            })

            })
          })

        })

      }).catch(err=>{
        this.$q.loading.hide()
        this.$q.notify({
          color:'red',
          icon:'error',
          message:err.response.data.message
        })
      })
    },
    agregarpago(){
      if(this.empleadopago.tipo=='FIJO'){
            if((parseFloat(this.empleadopago.salario) - this.totaldescuento) < parseFloat( this.pago.monto) && (this.pago.tipo=='ADELANTO' || this.pago.tipo=='DESCUENTO'))
          {
              this.$q.notify({
                message:'El monto excede al salario ',
                icon:'info',
                color:'red'
              })
            return false;
          }
      }

      // console.log('a');
      //this.pago.fecha=date.formatDate( Date.now(),'YYYY-MM-DD');
      this.pago.empleado_id=this.empleadopago.id
      this.pago.empleado_nombre=this.empleadopago.label;
      this.pago.checkbox=this.checkgasto
      if(this.checkgasto=='GASTO' && (this.pago.tipo=='ADELANTO' || this.pago.tipo=='EXTRA') && this.pago.monto > this.montogeneral){
                      this.$q.notify({
                message:'El monto excede en General ',
                icon:'info',
                color:'red'
              })
        return false
      }

     if(this.checkgasto=='CAJA' && (this.pago.tipo=='ADELANTO' || this.pago.tipo=='EXTRA') && this.pago.monto > this.montocajachica){
                      this.$q.notify({
                message:'El monto excede en Caja Chica ',
                icon:'info',
                color:'red'
              })
        return false
      }
       //console.log(this.pago)
      // return false
      this.$q.loading.show()

      this.$axios.post(process.env.API+'/sueldo',this.pago).then(res=>{
        //console.log(res.data)
        //return false
        //this.empleadohistorial=res.data
        //this.misempleados()

          let myWindow = window.open("", "Imprimir", "width=1000,height=1000")
        myWindow.document.write(res.data)
        myWindow.document.close()
      myWindow.print()
        myWindow.close()
        this.pagosval()
        this.misgastos()
          this.totalcaja()
          this.totalgeneral()
        this.$q.loading.hide()

        this.pagos=false
        this.checkgasto='CAJA'
        this.pago.monto=0
        this.pago.tipo=''
        this.pago.observacion=''
        this.pago.fecha=date.formatDate( Date.now(),'YYYY-MM-DD');
      })
    },
    misempleados(){
      this.$q.loading.show()
      this.$axios.get(process.env.API+'/listEmpleado').then(res=>{
        // console.log(res.data)
        this.$q.loading.hide()
        this.empleados=[]
        res.data.forEach(element => {
          element.label=element.nombre
          this.empleados.push(element)
        });
        console.log (this.empleados)
        this.empleadopago=this.empleados[0]

      })
    },
    agregar(){
     // console.log(this.glosa)
      if(this.glosa.id==undefined){
        return false
      }

      this.totalgeneral()
      console.log(this.montogeneral)
      //return false
      if(parseFloat(this.montogeneral) < parseFloat(this.empleado.precio)){
          this.$q.notify({
            message:'No se Tiene Fondos',
            icon:'info',
            color:'red'
          })
        return false
      }
      this.$q.loading.show()
      this.empleado.observacion=this.empleado.observacion.toUpperCase()
      this.empleado.glosa=this.glosa.nombre
      this.empleado.glosa_id=this.glosa.id
      if (this.boolcrear){
        this.$axios.post(process.env.API+'/gasto',this.empleado).then(res=>{
          // console.log(res.data)
                  let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.focus();
        // setTimeout(function(){
          myWindow.print();
          myWindow.close();
          this.$q.loading.hide()
          // this.empleados=res.data
          this.misgastos()
          this.listadog()
          this.totalgeneral()

          this.empleado.precio=0
          this.empleado.observacion=''
          this.empleado.glosa=''
          this.glosa={label:''}
          this.$q.notify({
            message:'Creado correctamente',
            icon:'info',
            color:'positive'
          })
        })
      }else{
        this.$axios.put(process.env.API+'/gasto/'+this.empleado.id,this.empleado).then(res=>{
          // console.log(res.data)
          this.$q.loading.hide()
          // this.empleados=res.data
          this.misgastos()
          this.listadog()
          this.boolcrear=true
          // this.empleado={fechanac: '2000-01-01'}
          this.empleado={fecha:date.formatDate( Date.now(),'YYYY-MM-DD')}
          this.$q.notify({
            message:'Creado correctamente',
            icon:'info',
            color:'positive'
          })
        })
      }


    },
    guardar(){
      // console.log(this.detalles)
      // return  false
      if (this.detalles.length==0){
        this.$q.notify({
         message:'Tienes que tener un detalle de ventas',
         color:'red',
         icon:'error'
        })
        return false;
      }
      // return false
      this.$q.loading.show()
      this.$axios.post(process.env.API+'/venta',{
        total:this.total,
        acuenta:this.acuenta,
        saldo:this.saldo,
        estado:this.estado,
        cliente_id:this.cliente.id,
        detalles:this.detalles
      }).then(res=>{
        // console.log(res.data)
        this.$q.notify({
          message:'Venta exitosa',
          color:'green',
          icon:'info'
        })
        this.misventas()
      })
    },
    printgasto(gasto){
      let cadena="<style>   .textcnt{   text-align:center;        }        table{width:100%;}        td{vertical-align:top;}        </style>"
        cadena+="<div class='textcnt'> DETALLE GASTO</div>"
        cadena+="<div class='textcnt'>Nro "+gasto.id+"</div>        <hr>"
        cadena+="<div>Nombre: "+gasto.user+"</div>"
        cadena+="<div>Fecha: "+date.formatDate( gasto.fecha,'DD/MM/YYYY') +"</div>        <hr>"
        cadena+="<table>        <tr><td>Glosa: </td><td><b>"+gasto.glosa+"</b></td></tr>"
        cadena+="<tr><td>Costo: </td><td><b>"+gasto.precio+"</b></td></tr>"
        cadena+="<tr><td>Observacion: </td><td><b>"+gasto.observacion+"</b></td></tr>        </table>"
        cadena+="    <br>"

      let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(cadena);
        myWindow.document.close();
        myWindow.focus();
        // setTimeout(function(){
          myWindow.print();
          myWindow.close();
    },
    deleteval(index){
      // this.detalles.splice(index, 1);
      // console.log(index)
      this.$q.dialog({
        title:'Seguro de eliminar?',
        cancel:true,
      }).onOk(()=>{
        this.$q.loading.show()
        this.$axios.delete(process.env.API+'/gasto/'+index.id).then(res=>{
          this.$q.loading.hide()
          this.misgastos()
          this.$q.notify({
            message:'Borrado correctamente',
            icon:'info',
            color:'positive'
          })
        }).catch(err=>{
          // console.log(err.response)
          this.$q.loading.hide()
          this.$q.notify({
            message:err.response.data.message,
            icon:'error',
            color:'red'
          })
        })
      })

    },
    updateval(index){
      console.log(index)
      //index.glosa.label=index.glosa.nombre
      this.glosa=index.gl
      this.glosa.label=this.glosa.nombre
      this.empleado=index
      this.empleado.fecha=index.fecha2
      this.boolcrear=false
      this.dialogGasto=true
      // this.detalles.splice(index, 1);
      // console.log(index)
      // this.$q.loading.show()
      // this.$axios.delete(process.env.API+'/empleado/'+index.id).then(res=>{
      //   this.$q.loading.hide()
      //   this.misempleados()
      // }).catch(err=>{
      //   // console.log(err.response)
      //   this.$q.loading.hide()
      //   this.$q.notify({
      //     message:err.response.data.message,
      //     icon:'error',
      //     color:'red'
      //   })
      // })
    },
    pagosval(){
      // console.log('a')
      //this.pagos=true
      this.$q.loading.show()
      this.$axios.get(process.env.API+'/empleado/'+this.empleadopago.id).then(res=>{
        // console.log(res.data);
        this.listpagos=res.data
        this.$q.loading.hide()
      })
    }
  },
  computed:{
    totalruta(){
        let total=0;
      this.ventas.forEach(r => {
            if(r.fechaentrega!=null && r.fechaentrega!=''){
              total++
            }})
            return total;
    },
          totaldescuento(){
        let total=0;
      this.listpagos.forEach(element => {
        if(date.formatDate( element.fecha,'YYYY-MM') == date.formatDate( this.pago.fecha,'YYYY-MM'))
        if(element.tipo=='ADELANTO' || element.tipo=='DESCUENTO')
          total+=parseFloat(element.monto)
      });
      return total;
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
    totalanulado(){
      let total=0;
      this.anulados.forEach(r=>{
        total+=parseFloat(r.efectivo)
      })
      return total;
    },

        totalpagos(){
      let total=0;
      this.rpagos.forEach(r=>{
        total+=parseFloat(r.monto)
      })
      return total;
    },
            totalpresventa(){
      let total=0;
      this.prestamoventa.forEach(r=>{
        total+=parseFloat(r.efectivo)
      })
      return total;
    }
  }
}
</script>

<style scoped>

</style>
