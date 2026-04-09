<template>
  <div class="q-pa-md">
    <q-btn
      label="Agregar o Retirar"
      color="positive"
      @click="alert = true"
      icon="add_circle"
      class="q-mb-xs"
      v-if="this.$store.state.login.editcajachica"
    />
                <q-form @submit.prevent="misdatos">
            <div class="row">
            <div class="col-3  q-pa-xs"><q-input type="date" label="fecha" v-model="fecha1" outlined required dense /></div>
            <div class="col-3  q-pa-xs"><q-input type="date" label="fecha" v-model="fecha2" outlined required dense v-if="rango=='RANGO'"/></div>
            <div class="col-2" ><q-toggle v-model="rango" true-value="RANGO" false-value="DIA" :label="rango +' FECHA'" style="width:100%"/></div>
            <div class="col-3  q-pa-xs flex flex-center">
              <q-btn color="info"  label="Consultar" icon="search" type="submit" dense/>
              <q-btn color="teal"  label="Gasto Cja Chica" icon="edit" @click="dialogcajachica=true" dense/>

            </div>
            </div>
            </q-form>
     <q-badge class="full-width text-h5 flex flex-center" color="red" >TOTAL EN CAJA CHICA: {{cajachica.monto}} Bs.</q-badge>
    <q-dialog v-model="alert">
      <q-card style="max-width: 80%; width: 50%">
        <q-card-section class="bg-green-14 text-white">
          <div class="text-h6"><q-icon name="add_circle" /> Caja Chica</div>
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
            <div class="row">
              <div class="col-12">
                <q-input
                  filled
                  v-model="dato.monto"
                  type="number"
                  step="0.1"
                  label="Monto Bs "
                  lazy-rules
                  :rules="[(val) => (val && val > 0) || 'Por favor ingresa datos']"
                />

                <q-input
                  filled
                  v-model="dato.motivo"
                  type="text"
                  label="Motivo"
                />

                <q-select
                  filled
                  v-model="dato.tipo"
                  label="TIPO"
                  :options="['AGREGA','RETIRA']"
                  lazy-rules
                  :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"
                />
              </div>
            </div>

            <div>
              <q-btn label="Crear" type="submit" color="positive" icon="add_circle" />
              <q-btn label="Cancelar" icon="delete" color="negative" v-close-popup />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
      <table id="example" class="display">
             <thead>
             <tr>
                 <th>FECHA</th>
                 <th>MONTO</th>
                 <th>MOTIVO</th>
                 <th>GLOSA</th>
                 <th>TIPO</th>
                 <th>OPCION</th>
              </tr>
             </thead>
             <tbody>
             <tr v-for="r in data" :key="r">
                 <td>{{ cambioFormato(r.fecha)}}</td>
                  <td>{{r.monto}}</td>
                  <td>{{r.motivo}}</td>
                  <td>{{r.glosa==null?'':r.glosa.nombre}}</td>
                  <td>{{r.tipo}}</td>
                  <td>
                    <q-btn
                      dense
                      round
                      flat
                      color="accent"
                      @click="viewRow(r)"
                      icon="list"
                    ></q-btn>
                    <q-btn
                      dense
                      round
                      flat
                      color="red"
                      @click="deleteRow(r)"
                      icon="delete"
                      v-if="this.$store.state.login.editcajachica"
                    ><q-tooltip>Eliminar</q-tooltip></q-btn>
                  </td>
              </tr>
             </tbody>
      </table>
    <q-table :filter="filter" title="Caja Chica"
      :rows="data"
      :columns="columns"
      row-key="name"
      :rows-per-page-options="[0,50,100]">
      <template v-slot:top-right>
        <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>
      <template v-slot:body-cell-motivo="props">
        <q-td key="motivo" :props="props">
          <q-btn
            dense
            round
            flat
            color="accent"
            @click="viewRow(props.row)"
            icon="list"
          ></q-btn>
        </q-td>

    </template>
      <template v-slot:body-cell-opcion="props">
          <q-td key="opcion" :props="props">
            <q-btn
              dense
              round
              flat
              color="red"
              @click="deleteRow(props.row)"
              icon="delete"
              v-if="this.$store.state.login.editcajachica"
            ><q-tooltip>Eliminar</q-tooltip></q-btn>
          </q-td>

      </template>

    </q-table>
    <q-dialog v-model="dialogcajachica">
      <q-card>
        <q-card-section>
          <div class="text-h6">Registro Gasto de Caja Chica {{cajachica.monto}} Bs</div>
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
                  val => val>0 && val<= cajachica  || 'No debe exceder el monto',
                  ]"
              lazy-rules/>
              </div>
               <div class="col-12">
                <q-input outlined label="Observacion" type="text" v-model="cchica.observacion" />
              </div>
              <div class="col-3 flex flex-center">
                <q-btn label="Registrar" type="submit" icon="send" color="info" :loading="loading" dense/>
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
</template>

<script>
import { date } from 'quasar'
import moment from 'moment'
import $ from "jquery";
export default {
  data() {
    return {
      glosa:{},
      loading:false,
      cchica:{},     
      dialogcajachica:false,
      alert: false,
      filter:'',
        fecha1:date.formatDate(Date.now(),'YYYY-MM-DD'),
        fecha2:date.formatDate( Date.now(),'YYYY-MM-DD'),
      model:'',
      rango:'RANGO',
      dato:{},
      options: [],
      props: [],
      cajachica:0,
      montogeneral:0,
      columns: [
        {name: "fecha", align: "left", label: "FECHA ", field: row=>moment(row.fecha).format('DD/MM/YYYY'), sortable: true,},
        {name: "monto", align: "left", label: "MONTO", field: "monto", sortable: true,},
        {name: "motivo", align: "left", label: "MOTIVO", field: "motivo", sortable: true,},
        {name: "glosa", align: "left", label: "GLOSA", field: row=>row.glosa==null?'':row.glosa.nombre, sortable: true,},
        {name: "tipo", align: "left", label: "TIPO", field: "tipo", sortable: true,},
        {name: "opcion", align: "left", label: "OPCION", field: "opcion" }

      ],
      data: [],
      glosas:[],
      filterglosa:[],

    };
  },
  created() {
    $('#example').DataTable(  );
    this.misdatos();
    this.totalcaja();
    this.totalgeneral();
    this.misglosa();

  },
  methods: {
    cambioFormato(fecha){
      return moment(fecha).format('DD/MM/YYYY')
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
        this.dialogcajachica=false;
        this.glosa={label:''}
        this.misdatos()
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

    totalgeneral(){
      this.$axios.post(process.env.API + "/totalgeneral").then((res) => {
        console.log(res.data)
          this.montogeneral=parseFloat( res.data.monto);
      })
      },
      deleteRow(logcaja){
      this.$q.dialog({
        title: 'Confirmar',
        message: 'Esta seguro de eliminar',
        cancel: true,
        persistent: false
      }).onOk(() => {
               this.$axios.delete(process.env.API + "/logcaja/"+logcaja.id).then((res) => {
                 console.log(res.data)
                                  this.misdatos()
        })
      }).onOk(() => {
        // console.log('>>>> second OK catcher')
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })

      },
      totalcaja(){
      this.$axios.post(process.env.API + "/totalcaja").then((res) => {
          this.cajachica=res.data;
      })
      },

    misdatos() {
      this.$q.loading.show();
      if(this.rango=='DIA'){
        this.fecha2=this.fecha1
      }
      this.$axios.post(process.env.API + "/listcaja",{fecha1:this.fecha1,fecha2:this.fecha2,user_id:0}).then((res) => {
         console.log(res.data)
        this.data = res.data;
        this.totalcaja();
          $('#example').DataTable().destroy();
           this.$nextTick(()=>{
             $('#example').DataTable( {
               dom: 'Blfrtip',
               buttons: [
                 'excel', 'pdf'
               ],
                "lengthMenu": [[-1,10, 25, 50], ["All",10, 25, 50 ]]
             } );
           })
        this.$q.loading.hide();
      });
    },
    viewRow(dato){
      this.$q.dialog({
        title: 'MOTIVO ',
        message: dato.motivo
      }).onOk(() => {
        // console.log('OK')
      }).onCancel(() => {
        // console.log('Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },

    onSubmit() {
        if(this.dato.tipo=='RETIRA' && parseFloat(this.dato.monto) > parseFloat(this.cajachica.monto))
        {
                    this.$q.notify({
          message:'No debe ser Mayor a lo q esta en caja',
          icon:'close',
          color:'red'
        })
            return false
        }
        if(this.dato.tipo=='AGREGA' && parseFloat(this.dato.monto) > parseFloat(this.montogeneral))
        {
                    this.$q.notify({
          message:'No debe ser Mayor a lo q esta en caja General',
          icon:'close',
          color:'red'
        })
            return false
        }
      this.$q.loading.show();
      // this.dato.unid_id=this.dato.unid_id.id;
      this.$axios.post(process.env.API + "/logcaja", {
        monto:this.dato.monto,
        motivo:this.dato.motivo,
        tipo:this.dato.tipo,
      }).then((res) => {
          this.onReset()
        this.$q.notify({
          color: "green-4",
          textColor: "white",
          icon: "cloud_done",
          message: "Creado correctamente",
        });
        this.alert = false;
        this.misdatos();
        this.totalgeneral();
      }).catch(err=>{
        console.log(err.response.data);
        this.$q.notify({
          message:err.response.data.message,
          icon:'close',
          color:'red'
        })
        this.$q.loading.hide()
      })
    },


    onReset() {
      this.dato.monto = 0.0;
      this.dato.motivo = '';
      this.dato.tipo = 'AGREGA';
    },

  },
};
</script>
