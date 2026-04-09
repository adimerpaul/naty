<template>
<q-page class="q-pa-xs">
  <div class="row">
    <div class="col-12">
      <div class="text-subtitle1 bg-blue-9 text-center text-white">Ventas</div>
    </div>
    <div class="col-12">
      <q-form @submit.prevent="guardar">
        <div class="row">
          <div class="col-6 col-sm-6 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha" outlined required/></div>
          <div class="col-6 col-sm-6 q-pa-xs"><q-input type="text" label="Responsable" v-model="responsable" outlined required/></div>
        </div>
        <div class="row">
          <div class="col-6 col-sm-2 q-pa-xs">
            <q-select
              outlined
              v-model="model"
              use-input
              input-debounce="0"
              label="Selecionar local"
              :options="options"
              @filter="filterFn"
              @click="generar"
            />
          </div>
          <div class="col-6 col-sm-2 q-pa-xs"><q-select v-model="producto" outlined :options="productos" option-label="nombre" option-value="id" label="Producto" required/></div>
          <div class="col-6 col-sm-1 q-pa-xs">
            <q-input
            type="number"
            step="0.1"
            label="precio"
            v-model="producto.precio"
            outlined
            required />
          </div>
                    <div class="col-6 col-sm-1 q-pa-xs">
<!--            <q-input type="number" label="Cantidad" v-model="cantidad" outlined/>-->
            <q-input label="Cantidad" type="number" step="0.5" v-model="cantidad" outlined/>
          </div>
        <div class="col-6 col-sm-2 q-pa-xs">
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
            <q-input type="text" label="A cuenta" v-model="acuenta" step="0.1" outlined required/>
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
            <q-btn color="accent" class="" size="lg"  label="Generar Venta" icon="send" type="submit" />
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
            <template v-if="$store.state.login.historialventalocal">
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

                    <q-td key="local" :props="props">
                      {{ props.row.local }}
                    </q-td>
                    <q-td key="titular" :props="props">
                      {{ props.row.titular }}
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

                                          <q-td key="observacion" :props="props">
                        <q-badge :color="props.row.observacion=='CANCELADO'?'positive':'negative'">{{ props.row.observacion }}</q-badge>
                      </q-td>
                      <q-td key="user" :props="props">
                        {{ props.row.user }}
                      </q-td>
                      <q-td key="opcion" :props="props">
                        <q-btn dense icon="cancel" color="red" v-if="props.row.estado!='ANULADO' && $store.state.login.anularventa" @click="anular(props.row)" />
                        <template v-if="$store.state.login.reimpresion" >
                          <q-btn dense icon="print" color="info" v-if="props.row.estado!='ANULADO'" @click="impboleta(props.row)" />
                        </template>
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
<!--        <div class="col-12 col-sm-6">-->
<!--          <div class="row">-->
<!--            <div class="col-12">-->
<!--              <div class="text-subtitle1 bg-accent text-center text-white">Historial de prestamos</div>-->
<!--            </div>-->
<!--            <div class="col-6 col-sm-4 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha4" outlined required/></div>-->
<!--            <div class="col-6 col-sm-4 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha5" outlined required/></div>-->
<!--            <div class="col-6 col-sm-4 q-pa-xs flex flex-center">-->
<!--              <q-btn color="info"  label="Consultar" icon="search" type="submit" @click="generar" />-->
<!--            </div>-->
<!--            <div class="row">-->
<!--              <q-form-->
<!--                  @submit="regprestamo"-->
<!--                  @reset="onReset"-->
<!--                  class="q-gutter-md"-->
<!--                >-->
<!--            <div class="row">-->

<!--                <div class="col-2">-->
<!--                <q-select v-model="inventario" outlined :options="inventarios" option-label="nombre" option-value="id" label="Material" required/>-->
<!--                  </div>-->
<!--                  <div class="col-2">-->
<!--                  <q-input-->
<!--                    outlined-->
<!--                    type="number"-->
<!--                    v-model="prestamo.cantidad"-->
<!--                    label="Cantidad"-->
<!--                    required-->
<!--                  />-->
<!--                  </div>-->

<!--                  <div class="col-2">-->
<!--                  <q-input-->
<!--                    outlined-->
<!--                    type="text"-->
<!--                    v-model="prestamo.efectivo"-->
<!--                    label="Efectivo"-->
<!--                  />-->
<!--                  </div>-->
<!--                  <div class="col-2">-->
<!--                  <q-input-->
<!--                    outlined-->
<!--                    type="text"-->
<!--                    v-model="prestamo.fisico"-->
<!--                    label="Fisico"-->
<!--                  />-->
<!--                  </div>-->
<!--                  <div class="col-2">-->
<!--                  <q-input-->
<!--                    outlined-->
<!--                    type="text"-->
<!--                    v-model="prestamo.observacion"-->
<!--                    label="Observacion"-->
<!--                  />-->
<!--                  </div>-->
<!--                  <div class="col-2 flex flex-center">-->
<!--                    <q-btn  type="submit" color="primary" icon="send" />-->
<!--                  </div>-->
<!--            </div>-->
<!--                </q-form>-->
<!--            </div>-->
<!--            <div class="col-12">-->
<!--              <q-table-->
<!--                :columns="columns3"-->
<!--                :rows="prestamos"-->
<!--                title="Historial de Prestamo"-->
<!--              >-->
<!--                &lt;!&ndash;        <template v-slot:top-right>&ndash;&gt;-->
<!--                &lt;!&ndash;          <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">&ndash;&gt;-->
<!--                &lt;!&ndash;            <template v-slot:append>&ndash;&gt;-->
<!--                &lt;!&ndash;              <q-icon name="search" />&ndash;&gt;-->
<!--                &lt;!&ndash;            </template>&ndash;&gt;-->
<!--                &lt;!&ndash;          </q-input>&ndash;&gt;-->
<!--                &lt;!&ndash;        </template>&ndash;&gt;-->
<!--              <template v-slot:body-cell-opcion="props" >-->
<!--                <q-td key="opcion" :props="props" >-->
<!--                <q-btn dense round flat color="red" @click="devolver1(props)" icon="undo" v-if="props.row.estado=='PRESTAMO'"></q-btn>-->
<!--                </q-td>-->
<!--            </template>-->
<!--              </q-table>-->
<!--            </div>-->

<!--            <div class="col-12">-->
<!--              <q-btn label="Imprimir prestamos" icon="print" color="accent" class="full-width" @click="impprestamos"/>-->
<!--            </div>-->
<!--                <q-dialog v-model="dialog_garantia" >-->
<!--              <q-card style="min-width: 350px">-->
<!--                <q-card-section>-->
<!--                  <div class="text-h6">Devolver Material Prestado</div>-->
<!--                </q-card-section>-->

<!--                <q-card-section class="q-pt-none">-->
<!--                  <q-input dense v-model="garantia.observacion" autofocus />-->
<!--                </q-card-section>-->

<!--                <q-card-actions align="right" class="text-primary">-->
<!--                  <q-btn flat label="Cancel" v-close-popup />-->
<!--                  <q-btn flat label="Devolver" @click="devolver" />-->
<!--                </q-card-actions>-->
<!--              </q-card>-->
<!--            </q-dialog>-->
<!--          </div>-->
<!--        </div>-->
      </div>


    </div>

  </div>
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
      fecha:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha2:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha3:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha4:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha5:date.formatDate(new Date(),'YYYY-MM-DD'),
      responsable:'',
      clientes:[],
      clientes2:[],
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
      columns:[
        {name:'nombreproducto',label:'Nombre producto',field:'nombreproducto'},
        {name:'precio',label:'Precio',field:'precio'},
        {name:'cantidad',label:'Cantidad',field:'cantidad'},
        {name:'subtotal',label:'Subtotal',field:'subtotal'},
        { name: 'action', label: 'Borrar', field: 'action' }
      ],
      columns2:[
        {name:'fecha',label:'Fecha',field:'fecha'},
        {name:'local',label:'Local',field:'local'},
        {name:'titular',label:'Titular',field:'titular'},
        {name:'total',label:'Total',field:'total'},
        {name:'acuenta',label:'A cuenta',field:'acuenta'},
        {name:'saldo',label:'Saldo',field:'saldo'},
        {name:'estado',label:'Estado',field:'estado'},
        {name:'observacion',label:'Observacion',field:'observacion'},
        {name:'user',label:'Usuario',field:'user'},
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
    // console.log(this.$store.state.login)
    this.$axios.get(process.env.API+'/listacliente').then(res=>{
      // this.clientes=res.data
      // this.clientes2=res.data
      // this.cliente=res.data[0]
      // console.log(res.data)
      res.data.forEach(r=>{
        if (r.tipocliente==1)
          // console.log(r)
        this.clientes.push({
          label:r.local+' '+r.titular,
          id:r.id,
          // detalles:r.detalles,
          // nombrecompleto:r.cliente.paterno+' '+r.cliente.materno+' '+r.cliente.nombre,
          // padron:r.padron,
          // ci:r.cliente.ci,
          // total:r.total,
        })
      })
    })
    this.$axios.get(process.env.API+'/listaproducto').then(res=>{
      res.data.forEach(r=>{
        if(r.tipo=='LOCAL')
          this.productos.push(r);
      })
      if (this.producto.length>0)
        this.producto=this.productos[0]
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
    // this.responsable=this.$store.getters["login/user"].nameimpboleta
  },
  methods:{
    impruta(venta){
      this.$axios.post(process.env.API+'/ruta/'+venta.id)
        .then(res=>{
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
            message:'Venta Anulado ',
            color:'green',
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
          html:true,
          icon:'error',
          position:'center'
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
        doc.text(3, 1, 'Historial de prestmos '+ mc.model.label)
        doc.text(5, 1.5,  'DE '+moment(mc.fecha4).format('DD/MM/YYYY') +' AL '+moment(mc.fecha5).format('DD/MM/YYYY'))
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
        doc.text(1, y+3, r.fecha.toString())
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
        doc.text(9.5, 3, 'Local')
        doc.text(13.5, 3, 'Titular')
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
      doc.setFontSize(8);

      this.ventas.forEach(r=>{
        // xx+=0.5
        y+=0.5
        doc.text(1, y+3, r.total.toString())
        doc.text(3, y+3, r.acuenta.toString())
        doc.text(5, y+3, r.saldo.toString())
        doc.text(7, y+3, r.estado.toString())
        doc.text(9.5, y+3, r.local.toString())
        doc.text(13.5, y+3, r.titular.toString())
        doc.text(18.5, y+3, r.user.toString())
        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
      })
      doc.setFontSize(9);

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
          if (r.tipo=='LOCAL')
          this.ventas.push({
            id:r.id,
            fecha:r.fecha,
            total:r.total,
            acuenta:r.acuenta,
            saldo:r.saldo,
            estado:r.estado,
            local:r.cliente.local,
            titular:r.cliente.titular,
            observacion:r.observacion,
            user:r.user.name,

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
    guardar(){
      if (this.model==0){
        this.$q.notify({
          message:'<h5>Tienes que seleccionar cliente</h5>',
          color:'red',
          html:true,
          icon:'error',
          position:'center'
        })
        return false;
      }
            if (this.fecha==undefined){
        this.$q.notify({
          message:'<h5>Tienes que seleccionar fecha</h5>',
          color:'red',
          html:true,
          position:'center',
          icon:'error',
        })
        return false;
      }
      if (this.acuenta>this.subtotal || this.acuenta<0){
        this.$q.notify({
          message:'<h5>Ingrese monto acuenta correcto</h5>',
          color:'red',
          icon:'error',
          html:true,
          position:'center',
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
        tipo:'LOCAL',
        observacion:this.observacion,
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
        this.$q.notify({
          message:'Venta exitosa',
          color:'green',
          icon:'info'
        })
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
        this.misventas()

          this.subtotal=''
          this.acuenta=0
          this.saldo=''
          this.estado=''
          this.model=''
          this.producto=''
          this.observacion='NINGUNA'
          this.cantidad=1
          this.subtotal=0
      }).catch(err=>{
        this.$q.loading.hide()
        console.error(err)
        this.$q.notify({
          message:err.response.data.message,
          color:'red',
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
          icon:'error',
          html:true,
          position:'center',
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
          icon:'info'
        })
        this.generar()
      }).catch(err=>{
        this.$q.loading.hide()
        console.error(err)
        this.$q.notify({
          message:err.response.data.message,
          color:'red',
          icon:'error'
        })
      })
      }else{
        this.$q.notify({
          message:'<h5>Debes seleccionar cliente</h5>',
          color:'red',
          html:true,
          icon:'error',
          position:'center',
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
        return parseFloat(this.producto.precio)* parseFloat(this.cantidad)
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
