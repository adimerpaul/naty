<template>
<q-page class="q-pa-xs">
  <div class="row">
    <div class="col-12">
      <div class="text-subtitle1 bg-blue-9 text-center text-white">Ventas</div>
    </div>
    <div class="col-12">
      <q-form @submit.prevent="agregar">
        <div class="row">
          <div class="col-6 col-sm-6 q-pa-xs"><q-input type="date" label="fecha" v-model="fecha" outlined required/></div>
          <div class="col-6 col-sm-6 q-pa-xs"><q-input type="text" label="Responsable" v-model="responsable" outlined required/></div>
        </div>
        <div class="row">
          <div class="col-6 col-sm-2 q-pa-xs"><q-select v-model="cliente" outlined :options="clientes" option-label="local" option-value="id" label="Local" required/></div>
          <div class="col-6 col-sm-2 q-pa-xs"><q-select v-model="producto" outlined :options="productos" option-label="nombre" option-value="id" label="Producto" required/></div>
          <div class="col-6 col-sm-2 q-pa-xs">
            <q-input
            type="number"
            label="precio"
            v-model="producto.precio"
            outlined
            required />
          </div>
          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="number" label="Cantidad" step="0.1" v-model="cantidad" outlined/></div>
          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" disable label="Subtotal" v-model="subtotal" outlined/></div>
          <div class="col-6 col-sm-2 q-pa-xs flex flex-center">
            <q-btn color="primary"  label="Agregar" icon="send" type="submit" />
          </div>
        </div>
      </q-form>
    </div>
    <div class="col-12">
      <q-table
      dense
      :columns="columns"
      :rows="detalles"
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
          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" label="Total" v-model="total" disable outlined/></div>
          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" label="Acuenta" v-model="acuenta" outlined/></div>
          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" label="Saldo" v-model="saldo" disable outlined/></div>
          <div class="col-6 col-sm-2 q-pa-xs"><q-input type="text" label="Estado" v-model="estado" outlined/></div>
          <div class="col-6 col-sm-2 q-pa-xs flex flex-center">
            <q-btn color="positive"  label="Venta" icon="shop" type="submit" />
          </div>
        </div>
      </q-form>
    </div>
    <div class="col-12">
      <div class="text-subtitle1 bg-info text-center text-white">Historial de ventas</div>
    </div>
    <div class="col-6 col-sm-4 q-pa-xs"><q-input type="date" label="fecha inicio" v-model="fecha2" outlined required/></div>
    <div class="col-6 col-sm-4 q-pa-xs"><q-input type="date" label="fecha fin" v-model="fecha3" outlined required/></div>
    <div class="col-6 col-sm-4 q-pa-xs flex flex-center">
      <q-btn color="info"  label="Consultar" icon="search" type="submit" @click="misventas" />
    </div>
    <div class="col-12">
      <q-table dense
        :columns="columns2"
        :rows="ventas"
        title="Historial de ventas"
        :filter="filter"
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
  name: "Venta",
  data(){
    return{
      fecha:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha2:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha3:date.formatDate(new Date(),'YYYY-MM-DD'),
      responsable:'',
      clientes:[],
      cliente:'',
      productos:[],
      producto:'',
      cantidad:1,
      acuenta:0,
      detalles:[],
      columns:[
        {name:'nombreproducto',label:'Nombre producto',field:'nombreproducto'},
        {name:'precio',label:'Precio',field:'precio'},
        {name:'cantidad',label:'Cantidad',field:'cantidad'},
        {name:'subtotal',label:'Subtotal',field:'subtotal'},
        { name: 'action', label: 'Borrar', field: 'action' }
      ],
      columns2:[
        {name:'total',label:'total',field:'total'},
        {name:'acuenta',label:'acuenta',field:'acuenta'},
        {name:'saldo',label:'saldo',field:'saldo'},
        {name:'estado',label:'estado',field:'estado'},
        {name:'cliente',label:'cliente',field:'cliente'},
        {name:'user',label:'user',field:'user'},
      ],
      ventas:[],
      filter:''
    }
  },
  mounted() {
    this.misventas()
    // console.log(this.$store.state.login)
    this.$axios.get(process.env.API+'/cliente').then(res=>{
      this.clientes=res.data
      this.cliente=res.data[0]
    })
    this.$axios.get(process.env.API+'/producto').then(res=>{
      this.productos=res.data
      this.producto=res.data[0]
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
  },
  methods:{
    misventas(){
      this.$q.loading.show()
      this.$axios.post(process.env.API+'/misventas',{fecha1:this.fecha2,fecha2:this.fecha3,}).then(res=>{
        // this.ventas=res.data
        // console.log(res.data)
        this.$q.loading.hide()
        this.ventas=[]
        res.data.forEach(r=>{
          this.ventas.push({
            total:r.total,
            acuenta:r.acuenta,
            saldo:r.saldo,
            estado:r.estado,
            cliente:r.cliente.local,
            user:r.user.name,
          })
        })
      })
    },
    agregar(){
      // console.log('a')
      this.detalles.push({
        nombreproducto:this.producto.nombre,
        precio:this.producto.precio,
        producto_id:this.producto.id,
        cantidad:this.cantidad,
        subtotal:this.subtotal,
      })
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
    deleteval(index){
      this.detalles.splice(index, 1);

    }
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
      if (this.acuenta<this.total)
        return 'POR COBRAR'
      else
        return 'CANCELADO'
    },
    saldo(){
      return this.total-this.acuenta
    }
  }
}
</script>

<style scoped>

</style>
