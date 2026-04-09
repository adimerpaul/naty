<template>
    <div class="row">
      <div class="col-12">
        <div class="text-center text-bold">RESUMEN DE INGRESOS Y EGRESOS</div>
      </div>
      <div class="col-12">
                <q-form @submit="listado" >
                <div class="row">
                <div class="col-4">
                  <q-input
                    outlined
                    dense
                    type="date"
                    v-model="fecha1"
                    label="Fecha ini"
                    required
                    hint="Porfavor ingresar fecha inicio"

                    counter
                  />
                  </div>
                  <div class="col-4">
                  <q-input
                    outlined
                    dense
                    type="date"
                    v-model="fecha2"
                    label="Fecha fin"
                    required
                    :rules="[ val => val>=fecha1 || 'Fecha debe ser mayor o igual']"
                    hint="Porfavor ingresar fecha fin"
                    counter
                  />
                  </div>
                  <div class="col-4 flex ">
                    <q-btn  label="Generar" class="full-width "  type="submit" color="primary" icon="send" />
                  </div>
                  </div>

                </q-form>


        <div class="col-12">
          <div class=" responsive">
           <!-- <table id="example1" class="display">
              <thead>
                <tr>
                <th>DETALLE</th>
                <th>TOTAL</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="r in ingreso " :key="r">
                  <td>{{r.detalle}}</td>
                  <td>{{r.total}}</td>
                </tr>
              </tbody>
             </table>
             <table id="example2" class="display">
              <thead>
                <tr>
                <th>DETALLE</th>
                <th>TOTAL</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="r in ingreso " :key="r">
                  <td>{{r.detalle}}</td>
                  <td>{{r.total}}</td>
                </tr>
              </tbody>
             </table>
             <table id="example3" class="display">
              <thead>
                <tr>
                <th>DETALLE</th>
                <th>TOTAL</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="r in ingreso " :key="r">
                  <td>{{r.detalle}}</td>
                  <td>{{r.total}}</td>
                </tr>
              </tbody>
             </table>-->
              <q-table dense title="Ingresos" :rows="ingreso" :columns="columns" row-key="name"    :rows-per-page-options="[0,50,100]"/>
                <q-table dense title="Egresos" :rows="egreso" :columns="columns2" row-key="name"       :rows-per-page-options="[0,50,100]"/>
                <q-table dense title="Caja Chica" :rows="cchica" :columns="columns3" row-key="name"       :rows-per-page-options="[0,50,100]"/>
          </div>
        </div>

            <div class="col-12">
              <q-form>
                <div class="row">
                  <div class="col-3 q-pa-md">
                    <q-input type="text" label="Ingreso total" label-color="positive"  v-model="totalingreso"  outlined/>
                  </div>
                  <div class="col-3 q-pa-md">
                    <q-input type="text" label="Egreso Total" label-color="info"  v-model="totalegreso"  outlined/>
                  </div>
                  <div class="col-3 q-pa-md">
                    <q-input type="text" label="Caja Chica Total" label-color="info"  v-model="totalchica"  outlined/>
                  </div>
                  <div class="col-3 q-pa-md">
                    <q-input type="text" label="SALDO C CHICA" label-color="warning"  v-model="montocajachica"  outlined/>
                  </div>
                  <div class="col-3 q-pa-md">
                    <q-input type="text" label="Balance" label-color="teal"  outlined v-model="balance"/>
                  </div>
                </div>
              </q-form>
            </div>
      <div class="col-12">
        <q-btn label="Imprimir" icon="print" color="accent" class="full-width" @click="impresion2()"/>
      </div>
      </div>
    </div>

</template>
<script>
import {date} from 'quasar'
import { jsPDF } from "jspdf";
import $ from "jquery";
import moment from 'moment'

export default {
  data(){
    return{
      montocajachica:0,
      totalch:0,
      fecha1:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha2:date.formatDate(new Date(),'YYYY-MM-DD'),
      resumen:[],
      usuarios:[],
      filter:'',
      usuario:'',
      ventas:[],
      deudas:[],
      ingreso:[],
      egreso:[],
      cchica:[],
      regpago:{},
      pagos:{},
      final:0,
      dato:{},
      alert:false,
      dialog_pago:false,

      columns : [

  { name: 'detalle', align: 'center', label: 'DETALLE', field: 'detalle', sortable: true },
  { name: 'total', align: 'center', label: 'total', field: 'total'},
],


      columns2 : [

  { name: 'detalle', align: 'center', label: 'DETALLE', field: 'detalle', sortable: true },
  { name: 'total', align: 'center', label: 'total', field: 'total'},
],
      columns3 : [

  { name: 'detalle', align: 'center', label: 'DETALLE', field: 'detalle', sortable: true },
  { name: 'total', align: 'center', label: 'total', field: 'total'},
],

    }
  },
  mounted() {
  },
  created() {
    $('#example1').DataTable( )
    $('#example2').DataTable( )
    $('#example3').DataTable( )

      this.listado();
      
  },
  methods: {
    totalcaja(){
      this.$axios.post(process.env.API + "/totalcaja").then((res) => {
          this.montocajachica=res.data.monto;
      })
    },
      getSaldoCh(){
      this.$axios.post(process.env.API+'/totalCajaIng',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
        this.totalch=res.data
        console.log(res.data)
      })

      },
          onPago(){
      this.$axios.post(process.env.API+'/pago',this.regpago).then(res=>{
          this.regpago={};
          this.dialog_pago=false;
          this.deudores();
                  this.$q.notify({
          message:'Registro correcto',
          color:'green',
          icon:'send'
        })
      })

      },
    listado(){
      this.ingreso=[]
      this.egreso=[]
      this.cchica=[]
      this.totalcaja()
      this.$axios.post(process.env.API+'/repventa',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{

        res.data.forEach(r=>{
        if(r.total!=null && r.total!=undefined)
          this.ingreso.push({detalle:'Venta '+r.tipo,total:r.total,ingreso:r.total,egreso:0})
        })
        this.$axios.post(process.env.API+'/repventpago',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
      //  console.log(res.data)
        if(res.data[0].total>0){
          let resultado=res.data[0]
          this.ingreso.push({detalle:'CxC pagos',total:resultado.total,ingreso:resultado.total,egreso:0})
        }
        this.$axios.post(process.env.API+'/repingprestamo',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
        //console.log(res.data)
        res.data.forEach(r=>{
        if(r.total!=null && r.total!=undefined)
          this.ingreso.push({detalle:'Anulacion Prestamo/ Venta '+r.estado,total:r.total,ingreso:r.total,egreso:0})
        })

      })
      })

      })


    this.$axios.post(process.env.API+'/repgastos',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
    //    console.log(res.data)
        res.data.forEach(r=>{
        if(r.total!=null && r.total!=undefined)
          this.egreso.push({detalle:r.glosa,total:r.total,egreso:r.total,ingreso:0})
        })
        this.$axios.post(process.env.API+'/replanilla',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
       //   console.log(res.data)
          let t=res.data[0]
          if(t.total!=null && t.total!=undefined){
            this.egreso.push({detalle:'SALARIOS',total:t.total,egreso:t.total,ingreso:0})}
         // console.log(this.egreso)
         this.$axios.post(process.env.API+'/repcompra',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
          console.log(res.data)
          let t=res.data[0]
          if(t.total!=null && t.total!=undefined){
            this.egreso.push({detalle:'COMPRA ALMACEN',total:t.total,egreso:t.total,ingreso:0})}

        })
        })

      })


      this.$axios.post(process.env.API+'/repcaja',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
          console.log(res.data)
          //let t=res.data[0]
          res.data.forEach(r => {
            this.cchica.push({detalle:r.glosa,total:r.total,egreso:r.total,ingreso:0})})

      this.$axios.post(process.env.API+'/repcaja2',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
        console.log(res.data)
          
          this.cchica.push({detalle:'RETIRO SALDO TOTAL',total:(res.data[0].total*(-1)),egreso:(res.data[0].total*(-1)),ingreso:0})
      })


         // console.log(this.egreso)

        })

    },
    impresion2(){
      this.resumen=[]
      this.ingreso.forEach(r => {
        this.resumen.push({detalle:r.detalle,ingreso:r.total,egreso:0});
      })

      this.egreso.forEach(r => {
        this.resumen.push({detalle:r.detalle,ingreso:0,egreso:r.total});
      })
      let mc=this
      let totalingreso=0
      let totalegreso=0
      let totalcaja=0
        let cadena="<style>\
        *{font-size:14px}\
        table{width:100%;border-collapse: collapse;}\
        table, th, td {  border: 1px solid;}\
        .titulo1{text-align:center; font-weight: bold;}\
        </style>\
        <div><img src='logo.png' style='width:150px; height:75px;'></div>\
        <div class='titulo1'>RESUMEN DE BALANCE DE <br>"+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY') +"</div><br>\
        <div>INGRESOS</div><table><tr><th style='width:50%'>DETALLE</th> <th>INGRESO</th> </tr>"

        this.ingreso.forEach(r=>{
        totalingreso=totalingreso+r.ingreso;
        cadena+="<tr><td>"+r.detalle+"</td><td>"+r.ingreso+"</td></tr>"
        })
        cadena+="</table><br><div>EGRESOS</div><table><tr><th style='width:50%'>DETALLE</th>  <th>EGRESO</th></tr>"


              this.egreso.forEach(r=>{
        totalegreso=totalegreso+r.egreso;
        cadena+="<tr><td>"+r.detalle+"</td><td>"+r.egreso+"</td></tr>"
        })
        cadena+="</table><br><div>CAJA CHICA</div><table><tr><th style='width:50%'>DETALLE</th> <th>EGRESO</th></tr>"
              this.cchica.forEach(r=>{
                r.egreso+ parseFloat(r.egreso)
        totalcaja=totalcaja + r.egreso;
        cadena+="<tr><td>"+r.detalle+"</td><td>"+r.egreso+"</td></tr>"
        })
        var ttotal=totalingreso - totalegreso - totalcaja + parseFloat(this.montocajachica)
        cadena+="</table><div><b>TOTAL INGRESO: </b> "+ totalingreso+" Bs</div>\
        <div><b>TOTAL EGRESO: </b> "+ totalegreso+" Bs</div>\
        <div><b>T. CJA CHICA: </b> "+ totalcaja+" Bs</div>\
        <div><b>SALDO CJA CHICA: </b> "+ this.montocajachica+" Bs</div>\
        <div><b>BALANCE: </b> "+ ttotal+" Bs</div>"

        let myWindow = window.open("_blank");
        myWindow.document.write(cadena);
        myWindow.document.close();
    },
    impresion(){
      this.resumen=[]
      this.ingreso.forEach(r => {
        this.resumen.push({detalle:r.detalle,ingreso:r.total,egreso:0});
      });

      this.egreso.forEach(r => {
        this.resumen.push({detalle:r.detalle,ingreso:0,egreso:r.total});
      });

           let mc=this

      function header(){
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
        doc.setFont(undefined,'bold')
        doc.text(5, 1, 'RESUMEN DE BALANCE ')
        doc.text(5, 1.5,  'DE '+moment(mc.fecha1).format('DD/MM/YYYY')+' AL '+moment(mc.fecha2).format('DD/MM/YYYY'))
        doc.text(1, 3, 'DETALLE')
        doc.text(10, 3, 'INGRESO')
        doc.text(14, 3, 'EGRESO')
        doc.setFont(undefined,'normal')
      }
      var doc = new jsPDF('p','cm','letter')
      // console.log(dat);
      doc.setFont("courier");
      doc.setFontSize(12);
      // var x=0,y=
      header()
      // let xx=x
      // let yy=y
      let y=0
      let totalingreso=0
      let totalegreso=0
      let totalcaja=0
      this.ingreso.forEach(r=>{
        totalingreso=totalingreso+r.ingreso;
        // xx+=0.5
        y+=0.5
        doc.text(1, y+3, r.detalle)
        doc.text(10, y+3, r.ingreso+'')
        doc.text(14, y+3, r.egreso+'')

        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
        })
        y+=0.5

              this.egreso.forEach(r=>{
        totalegreso=totalegreso+r.egreso;
        // xx+=0.5
        y+=0.5
        doc.text(1, y+3, r.detalle)
        doc.text(10, y+3, r.ingreso+'')
        doc.text(14, y+3, r.egreso+'')

        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
        })
        y+=0.5
              this.cchica.forEach(r=>{
        totalcaja=totalcaja+r.egreso;
        // xx+=0.5
        y+=0.5
        doc.text(1, y+3, r.detalle)
        doc.text(10, y+3, r.ingreso+'')
        doc.text(14, y+3, r.egreso+'')

        if (y+3>25){
          doc.addPage();
          header()
          y=0
        }
        })

      doc.setFontSize(9);

        doc.setFont(undefined,'bold')
        doc.text(1, y+4, 'TOTAL INGRESO: ')
        doc.setFont(undefined,'normal')
        doc.text(4, y+4, totalingreso+' Bs')

        doc.setFont(undefined,'bold')
        doc.text(7, y+4, 'TOTAL EGRESO: ')
        doc.setFont(undefined,'normal')
        doc.text(10, y+4, totalegreso+' Bs')

        doc.setFont(undefined,'bold')
        doc.text(7, y+4.5, 'T. CJA CHICA: ')
        doc.setFont(undefined,'normal')
        doc.text(10, y+4.5, totalcaja+' Bs')

        var ttotal=totalingreso - totalegreso - totalcaja
        doc.setFont(undefined,'bold')
        doc.text(14, y+4, 'BALANCE: ')
        doc.setFont(undefined,'normal')
        doc.text(18, y+4, ttotal+'Bs')


      // doc.save("Pago"+date.formatDate(Date.now(),'DD-MM-YYYY')+".pdf");
      window.open(doc.output('bloburl'), '_blank');
    },
  },
computed:{

    totalingreso(){
      let total=0;
      this.ingreso.forEach(r=>{
        total+= parseFloat(r.ingreso)
      })
      return total
    },
    totalegreso(){
      let total=0;
      this.egreso.forEach(r=>{
        total+= parseFloat(r.egreso)
      })
      return total
    },
    totalchica(){
      let total=0;
      this.cchica.forEach(r=>{
        total+= parseFloat(r.total)
      })
      return total
    },

    balance(){
      var total=this.totalingreso - this.totalegreso - this.totalchica
      return total;
      //return this.totalingreso() - this.totalegreso()
    }

}

}
</script>
