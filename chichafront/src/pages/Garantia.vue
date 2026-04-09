<template>
<q-page class="q-pa-xs">
  <div class="row">
    <div class="col-12">
      <div class="text-subtitle1 bg-blue-9 text-center text-white">Registro de Garantias de Prestamo</div>
    </div>
    <div class="col-12">
      <q-form @submit.prevent="agregar">
        <div class="row">
          <div class="col-6 col-sm-6 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha" outlined required/></div>
          <div class="col-6 col-sm-6 q-pa-xs"><q-input type="text" label="Responsable" v-model="responsable" outlined required readonly/></div>
        </div>
        <div class="row">
          <div class="col-6 col-sm-3 q-pa-xs"><q-select v-model="cliente" outlined :options="clientes" option-label="local" option-value="id" label="Local" required/></div>
          <div class="col-6 col-sm-3 q-pa-xs"><q-input type="number" label="Efectivo" v-model="efectivo" outlined/></div>
          <div class="col-6 col-sm-3 q-pa-xs"><q-input type="text" label="Fisico"  v-model="fisico" outlined /></div>
          <div class="col-6 col-sm-3 q-pa-xs"><q-input type="text" label="Detalle" v-model="detalle" outlined /></div>
        </div>
        <div class="row">
          <div class="col-6 col-sm-2 q-pa-xs"><q-select v-model="inventario" outlined :options="inventarios" option-label="nombre" option-value="id" label="Material" required/></div>
          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="number" label="Cantidad" v-model="cantidad" outlined/></div>
          <div class="col-6 col-sm-2 q-pa-xs flex flex-center">
            <q-btn color="primary"  label="Agregar" icon="send" type="submit" />
          </div>
        </div>
      </q-form>
    </div>
    <div class="col-12">
      <q-table
      :columns="columns"
      :rows="detalles"
      :rows-per-page-options="[0,50,100]"
      >
        <template v-slot:body-cell-action="props">
          <q-td :props="props">
            <q-btn
              color="negative"
              icon-right="delete"
              no-caps
              flat
              dense
              @click="deleteval(detalles.indexOf(props.row))"
            />
          </q-td>
        </template>
      </q-table>
    </div>

    <div class="col-12">
      <q-form @submit.prevent="guardar">
        <div class="row">
          <div class="col-6 col-sm-2 q-pa-xs flex flex-center">
            <q-btn color="positive"  label="Registrar Garantia" icon="shop" type="submit" />
          </div>
        </div>
      </q-form>
    </div>
    <div class="col-12">
      <div class="text-subtitle1 bg-info text-center text-white">Historial de Garantia</div>
    </div>
    <div class="col-6 col-sm-6 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha2" outlined required/></div>
    <div class="col-6 col-sm-6 q-pa-xs flex flex-center">
      <q-btn color="info"  label="Consultar" icon="search" type="submit" @click="listado" />
    </div>
    <div class="col-12">
      <q-table
        :columns="columns2"
        :rows="garantias"
        title="Historial de Garantia"
        :filter="filter"
      :rows-per-page-options="[0,50,100]"
        
      >
        <template v-slot:top-right>
          <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
            <template v-slot:append>
              <q-icon name="search" />
            </template>
          </q-input>
        </template>
      </q-table>
    </div>
  </div>
</q-page>
</template>

<script>
import {date} from 'quasar'
export default {
  name: "Garantias",
  data(){
    return{
      fecha:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha2:date.formatDate(new Date(),'YYYY-MM-DD'),
      responsable:'',
      fisico:'',
      detalle:'',
      clientes:[],
      cliente:'',
      inventarios:[],
      inventario:'',
      efectivo:0,
      cantidad:1,
      acuenta:0,
      detalles:[],
      columns:[
        {name:'nombreinv',label:'Nombre material',field:'nombreinv'},
        {name:'cantidad',label:'Cantidad',field:'cantidad'},
        {name: 'action', label: 'Borrar', field: 'action' }
      ],
      columns2:[
        {name:'efectivo',label:'efectivo',field:'efectivo'},
        {name:'fisico',label:'fisico',field:'fisico'},
        {name:'estado',label:'estado',field:'estado'},
        {name:'cliente',label:'cliente',field:'cliente'},
        {name:'user',label:'user',field:'user'},
      ],
      garantias:[],
      filter:''
    }
  },
  mounted() {
    this.listado()
    // console.log(this.$store.state.login)
    this.$axios.get(process.env.API+'/cliente').then(res=>{
      this.clientes=res.data
      this.cliente=res.data[0]
    })
    this.$axios.get(process.env.API+'/inventario').then(res=>{
      this.inventarios=res.data
      this.inventario=res.data[0]
    })
    this.$axios.post(process.env.API+'/listado',{fecha:this.fecha2}).then(res=>{
      this.venta=res.data
      // console.log(this.venta)
      // this.producto=res.data[0]
    })
    this.$axios.post(process.env.API+'/me').then(res=>{
      // console.log(res.data)
      this.responsable=res.data.name
    })
    // this.responsable=this.$store.getters["login/user"].name
  },
  methods:{
    listado(){
      this.$q.loading.show()
      this.$axios.post(process.env.API+'/listado',{fecha:this.fecha2}).then(res=>{
        this.$q.loading.hide()
        this.garantias=[];
        console.log(res.data);
        res.data.forEach(r=>{
          this.garantias.push({
            efectivo:r.efectivo,
            fisico:r.fisico,
            estado:r.estado,
            cliente:r.cliente['local'],
            user:r.user['name']
          })
        })
      })
    },
    agregar(){
      // console.log('a')
      this.detalles.push({
        nombreinv:this.inventario.nombre,
        inventario_id:this.inventario.id,
        cantidad:this.cantidad
      })
    },
    guardar(){
      // console.log(this.detalles)
      // return  false
      if (this.detalles.length==0){
        this.$q.notify({
         message:'Tienes que tener un detalle',
         color:'red',
         icon:'error'
        })
        return false;
      }
      // return false
      this.$q.loading.show()
      this.$axios.post(process.env.API+'/garantia',{
          fecha:this.fecha,
         efectivo:this.efectivo,
         fisico:this.fisico, 
         detalle:this.detalle,
         estado:'PRESTAMO',
        cliente_id:this.cliente.id,
        detalles:this.detalles
      }).then(res=>{
        // console.log(res.data)
        this.$q.notify({
          message:'registro exitosa',
          color:'green',
          icon:'info'
        })
        this.listado()
      })
    },
    deleteval(index){
      this.detalles.splice(index, 1);
    }
  },

}
</script>


