<template>
  <div class="row">
    <div class="col-12">
      <q-badge label="REGISTRO DE NUEVOS CLIENTES " color="teal" class="full-width text-h6"/>
    </div>
<!--    <div class="col-12">-->
<!--      <q-card>-->
<!--        <q-form @submit="registrar(2)" class="q-gutter-md">-->
<!--          <div class="row">-->
<!--            <div class="col-12 col-sm-2">-->
<!--              <q-input-->
<!--                outlined-->
<!--                type="text"-->
<!--                v-model="cliente.ci"-->
<!--                label="Cedula Identidad"-->
<!--                style="text-transform: uppercase"-->
<!--              />-->
<!--            </div>-->
<!--            <div class="col-12 col-sm-2">-->
<!--              <q-input-->
<!--                outlined-->
<!--                v-model="cliente.titular"-->
<!--                type="text"-->
<!--                label="Nombre Completo"-->
<!--                style="text-transform: uppercase"-->
<!--                lazy-rules-->
<!--                :rules="[ val => val && val.length > 0 || 'Por favor ingresar dato']"-->
<!--              />-->
<!--            </div>-->

<!--            <div class="col-12 col-sm-2">-->
<!--              <q-input-->
<!--                outlined-->
<!--                type="text"-->
<!--                v-model="cliente.telefono"-->
<!--                label="Telefono o Celular"-->
<!--                style="text-transform: uppercase"-->
<!--              />-->
<!--            </div>-->

<!--            <div class="col-12 col-sm-2">-->
<!--              <q-input-->
<!--                type="text"-->
<!--                outlined-->
<!--                v-model="cliente.direccion"-->
<!--                label="Direccion*"-->
<!--                style="text-transform: uppercase"-->
<!--              />-->
<!--            </div>-->
<!--            <div class="col-12 col-sm-2">-->
<!--              <q-input-->
<!--                outlined-->
<!--                v-model="cliente.observacion"-->
<!--                label="Observacion"-->
<!--                type="text"-->
<!--                style="text-transform: uppercase"-->
<!--              />-->
<!--            </div>-->
<!--            <div class="col-12 flex flex-center">-->
<!--              <q-btn label="Registrar" icon="send" type="submit" color="primary" class="full-width"/>-->
<!--            </div>-->
<!--          </div>-->
<!--          <div>-->
<!--          </div>-->

<!--        </q-form>-->

<!--      </q-card>-->
<!--    </div>-->
    <div class="col-12">

      <div class="q-pa-md">
        <div class=" responsive">
          <!-- <q-markup-table dense bordered wrap-cells>
             <thead>
             <tr>
               <th>CI</th>
               <th>Titular</th>
               <th>Telefono</th>
               <th>Direccion</th>
               <th>Observacion</th>
               <th>Tipo Cliente</th>
               <th>Estado</th>
               <th>Opcion</th>
             </tr>
             </thead>
             <tbody>
             <tr v-for="v in rows" :key="v.id">
               <td>{{v.ci}}</td>
               <td>{{v.titular}}</td>
               <td>{{v.telefono}}</td>
               <td>{{v.direccion}}</td>
               <td>{{v.observacion}}</td>
               <td>
                 <q-badge color="accent" v-if="v.tipocliente==1">LOCAL</q-badge>
                 <q-badge color="teal" v-else>CLIENTE</q-badge>
               </td>
               <td>
                 <q-badge  :color="v.estado=='ACTIVO'?'positive':'negative'"  @click="activar(v)">{{ v.estado=='ACTIVO'?'HABILITADO':'INACTIVO' }}</q-badge>
               </td>
               <td>
                 <q-btn dense round flat color="yellow" @click="editRow(v)" icon="edit"><q-tooltip>Modificar</q-tooltip></q-btn>
                 <q-btn dense round flat color="red" @click="delRow(v)" icon="delete"><q-tooltip>Eliminar</q-tooltip></q-btn>
               </td>
             </tr>
             </tbody>
           </q-markup-table>-->
        </div>
        <div class="row">
          <div class="col-2 q-pa-xs">
            <q-input type="date" outlined dense v-model="ini" label="Fecha Ini"/>
          </div>
          <div class="col-2 q-pa-xs ">
            <q-input type="date" outlined dense v-model="fin" label="Fecha fin"/>
          </div>
          <div class="col-4 q-pa-xs">
            <q-btn color="green" label="Generar ultmas ventas excel" @click="generarExcel" no-caps icon="shopping_cart"/>
          </div>
          <div class="col-4 q-pa-xs text-right">
            <q-btn color="green" label="Crear Cliente" @click="clickCreateCliente" no-caps icon="add_circle"/>
          </div>
        </div>
        <q-table
          title="CLIENTES"
          dense
          flat
          bordered
          :rows="rows"
          :columns="columns"
          row-key="name"
          :filter="filter"
          :rows-per-page-options="[20,50,100,0]"
        >

          <template v-slot:body-cell-estado="props">
<!--            <q-tr :props="props">-->
              <q-td key="estado" :props="props" @click="activar(props.row)">
                <q-badge color="green" v-if="props.row.estado=='ACTIVO'">
                  {{ props.row.estado }}
                </q-badge>
                <q-badge color="red" v-else>
                  NO
                </q-badge>
              </q-td>
<!--            </q-tr>-->
          </template>

          <template v-slot:body-cell-tipocliente="props">
            <q-td key="tipocliente" :props="props">
              <q-badge color="accent" v-if="props.row.tipocliente=='1'">
                LOCAL
              </q-badge>
              <q-badge color="info" v-else>
                CLIENTE
              </q-badge>
            </q-td>
          </template>

          <template v-slot:body-cell-opcion="props">
            <q-td key="opcion" :props="props">
              <q-btn dense round flat color="yellow" @click="editRow(props.row)" icon="edit"></q-btn>
              <q-btn dense round flat color="red" @click="delRow(props.row)" icon="delete" v-if="$store.state.login.editcliente"></q-btn>
            </q-td>
          </template>
          <template v-slot:top-right>
            <q-input borderless dense debounce="300" v-model="filter" placeholder="Buscar">
              <template v-slot:append>
                <q-icon name="search"/>
              </template>
            </q-input>
          </template>
        </q-table>
      </div>

      <q-dialog v-model="dialog_mod">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">
              {{dato.id ? 'Modificar' : 'Registrar'}} CLIENTE
            </div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form
              @submit="onMod"
              class="q-gutter-md">

              <q-input
                outlined
                type="text"
                v-model="dato.ci"
                label="Cedula Identidad"
                style="text-transform: uppercase"
              />
              <q-input
                outlined
                v-model="dato.titular"
                type="text"
                label="Nombre Completo"
                style="text-transform: uppercase"
                lazy-rules
                :rules="[ val => val && val.length > 0 || 'Por favor ingresar dato']"
              />
              <q-input
                outlined
                type="text"
                v-model="dato.telefono"
                label="Telefono o Celular"
                style="text-transform: uppercase"
              />
              <q-input
                type="text"
                outlined
                v-model="dato.direccion"
                label="Direccion*"
                style="text-transform: uppercase"
              />
              <q-input
                outlined
                v-model="dato.observacion"
                label="Observacion"
                type="text"
                style="text-transform: uppercase"
              />
              <div v-if="dato.tipocliente=='1'">
                <q-input
                  outlined
                  v-model="dato.nit"
                  label="NIT"
                  type="text"
                  style="text-transform: uppercase"
                />
              </div>
              <div>
                <q-btn label="Modificar" type="submit" color="positive" icon="add_circle"/>
                <q-btn label="Cancelar" icon="delete" color="negative" v-close-popup/>
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>

      <q-dialog v-model="dialog_del">
        <q-card>
          <q-card-section class="row items-center">
            <q-avatar icon="clear" color="red" text-color="white"/>
            <span class="q-ml-sm">Seguro de eliminar Registro.</span>
          </q-card-section>

          <q-card-actions align="right">
            <q-btn flat label="Eliminar" color="deep-orange" @click="onDel"/>
            <q-btn flat label="Cancelar" color="primary" v-close-popup/>
          </q-card-actions>
        </q-card>
      </q-dialog>


    </div>

  </div>
</template>
<script>

import {date} from 'quasar'
import $ from "jquery";
import xlsx from "json-as-xlsx"

export default {
  data() {
    return {
      ini: date.formatDate(Date.now(), 'YYYY-MM-DD'),
      fin: date.formatDate(Date.now(), 'YYYY-MM-DD'),
      tab: 'cliente',
      // days:[{id :'2019/02/01'}, {id:'2019/02/10'} ],
      days: [], filter: '',
      crear: false,
      dialog_mod: false,
      dialog_del: false,
      tipocliente: 1,
      cliente: {
        fechanac: '2000-01-01'
      },
      clientes: {},
      color: '',
      dato: {},
      columns: [

        {name: 'ci', align: 'center', label: 'CI', field: 'ci', sortable: true},
        {name: 'titular', align: 'center', label: 'TITULAR', field: 'titular', sortable: true},
        {name: 'telefono', label: 'TELÉFONO', field: 'telefono'},
        {name: 'direccion', label: 'DIRECCIÓN', field: 'direccion'},
        {name: 'observacion', label: 'OBSERVACIÓN', field: 'observacion'},
        {name: 'tipocliente', label: 'TIPO CLIENTE', field: 'tipocliente'},
        {name: 'estado', align: 'center', label: 'ESTADO', field: 'estado'},
        {name: 'opcion', label: 'OPCIONES', field: 'action'}
      ],
      rows: []

    }
  },
  created() {
    //this.filtrarlista();
  },
  mounted() {
    //this.filtrarlista();
    // $('#example').DataTable(  );
    this.listado(2);
    console.log(this.days)


  },
  methods: {
    clickCreateCliente() {
      this.dialog_mod = true
      this.dato = {
        fechanac: ''
      };
    },
    generarExcel() {
      this.$axios.post(process.env.API + '/repClienteVenta2', {ini: this.ini, fin: this.fin}).then(res => {
        console.log(res.data)
        let datacaja = [
          {
            sheet: "Cliente Total Venta",
            columns: [
              {label: "titular", value: "titular"}, // Top level data
              {label: "total", value: "total"}, // Top level data
              {label: "cantidad", value: "cantidad"}, // Top level data
            ],
            content: res.data
          },

        ]

        let settings = {
          fileName: "VentaCliente", // Name of the resulting spreadsheet
          extraLength: 7, // A bigger number means that columns will be wider
          writeOptions: {}, // Style options from https://github.com/SheetJS/sheetjs#writing-options
        }

        xlsx(datacaja, settings) // Will download the excel file
      })

    },
    listado(valor) {
      this.$q.loading.show();
      this.rows = [];
      this.days = [];
      // $('#example').DataTable().destroy();
      this.$axios.get(process.env.API + '/cumple').then(res => {
        console.log(res.data)
        res.data.forEach(el => {
          //console.log(valor);
          console.log(el.tipocliente);
          if (valor == el.tipocliente) {
            const fecha = date.extractDate(el.fechanac, 'YYYY-MM-DD')
            if (el.estado == 'ACTIVO')
              this.days.push(date.formatDate(Date.now(), 'YYYY') + '/' + date.formatDate(fecha, 'MM') + '/' + date.formatDate(fecha, 'DD'))
            this.clientes = {};
            this.clientes.id = el.id;
            this.clientes.local = el.local;
            this.clientes.ci = el.ci;
            this.clientes.titular = el.titular;
            this.clientes.tipo = el.tipo;
            this.clientes.telefono = el.telefono;
            this.clientes.fechanac = el.fechanac;
            this.clientes.direccion = el.direccion;
            this.clientes.legalidad = el.legalidad;
            this.clientes.categoria = el.categoria;
            this.clientes.razon = el.razon;
            this.clientes.nit = el.nit;
            this.clientes.observacion = el.observacion;
            this.clientes.tipocliente = el.tipocliente;
            this.clientes.estado = el.estado;
            if (el.estado != 'ACTIVO')
              this.clientes.estado = 'NO';
            this.rows.push(this.clientes);
          }
        });
        this.$axios.get(process.env.API + '/cumple2').then(res => {
          console.log(res.data)
          res.data.forEach(el => {
            // this.days.push( el.fechanac.replaceAll('-','/'))


            if (valor == el.tipocliente) {
              const fecha = date.extractDate(el.fechanac, 'YYYY-MM-DD')
              if (el.estado == 'ACTIVO')
                this.days.push(date.formatDate(Date.now(), 'YYYY') + '/' + date.formatDate(fecha, 'MM') + '/' + date.formatDate(fecha, 'DD'))
              this.clientes = {};
              this.clientes.id = el.id;
              this.clientes.local = el.local;
              this.clientes.ci = el.ci;
              this.clientes.titular = el.titular;
              this.clientes.tipo = el.tipo;
              this.clientes.telefono = el.telefono;
              this.clientes.fechanac = el.fechanac;
              this.clientes.direccion = el.direccion;
              this.clientes.legalidad = el.legalidad;
              this.clientes.categoria = el.categoria;
              this.clientes.razon = el.razon;
              this.clientes.nit = el.nit;
              this.clientes.observacion = el.observacion;
              this.clientes.tipocliente = el.tipocliente;
              this.clientes.estado = el.estado;
              if (el.estado != 'ACTIVO')
                this.clientes.estado = 'NO';

              this.rows.push(this.clientes);
            }
          });
          this.$q.loading.hide();
          //               $('#example').DataTable().destroy();
          // this.$nextTick(()=>{
          //   $('#example').DataTable( {
          //     dom: 'Blfrtip',
          //     buttons: [
          //        'excel', 'pdf'
          //     ],
          //      "lengthMenu": [[-1,10, 25, 50], [ "All",10, 25, 50]]
          //   } );
          // })
        })

      });

    },
    // filtrarlista(){
    //   if(this.tab=='local') this.listado(1)
    //   else this.listado(2)
    // },
    registrar(tipo) {
      this.cliente.tipocliente = tipo;
      if (tipo == '2')
        this.cliente.fechanac = null;
      this.$axios.post(process.env.API + '/cliente', this.cliente).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Creado correctamente'
        });
        this.crear = false;
        this.onReset();
        //this.filtrarlista();
        this.listado(2)
      }).catch(err => {

        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: err.response.data.message
        });
      })
    },


    activar(props) {
      this.dato = props;
      this.$axios.post(process.env.API + '/activar', this.dato).then(res => {
        //this.filtrarlista();\
        this.listado(2)
      });

    },
    editRow(props) {
      this.dato = props;
      // console.log(this.dato);
      this.dialog_mod = true;
    },
    delRow(props) {
      this.dato = props;
      this.dialog_del = true;
    },
    onMod() {
      console.log(this.dato.id);
      if (this.dato.id !== undefined) {
        this.$q.loading.show();
        this.$axios.put(process.env.API + '/cliente/' + this.dato.id, this.dato).then(res => {
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Modificado correctamente'
          });
          this.dialog_mod = false;
          //this.filtrarlista();
          this.listado(2)
        }).catch(err => {

          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: 'Error al Modificar'
          });
        }).finally(() => {
          this.$q.loading.hide();
          this.dialog_mod = false;
        })
        // this.$q.loading.hide();
      }else {
        this.$q.loading.show();
        this.dato.tipocliente = 2;
        this.$axios.post(process.env.API + '/cliente', this.dato).then(res => {
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Creado correctamente'
          });
          this.crear = false;
          this.onReset();
          //this.filtrarlista();
          this.listado(2)
        }).catch(err => {

          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: err.response.data.message
          });
        }).finally(() => {
          this.$q.loading.hide();
          this.dialog_mod = false;
        })
      }

      // this.$q.loading.show();
      // this.$axios.put(process.env.API + '/cliente/' + this.dato.id, this.dato).then(res => {
      //   this.$q.notify({
      //     color: 'green-4',
      //     textColor: 'white',
      //     icon: 'cloud_done',
      //     message: 'Modificado correctamente'
      //   });
      //   this.dialog_mod = false;
      //   //this.filtrarlista();
      //   this.listado(2)
      // }).catch(err => {
      //
      //   this.$q.notify({
      //     color: 'red-4',
      //     textColor: 'white',
      //     icon: 'error',
      //     message: 'Error al Modificar'
      //   });
      // })
      // this.$q.loading.hide();
    },
    onDel() {
      this.$q.loading.show();
      this.$axios.delete(process.env.API + '/cliente/' + this.dato.id).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Eliminado correctamente'
        });
        this.dialog_del = false;
        //this.filtrarlista();
        this.listado(2)
      }).catch(err => {
        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: 'Error al eliminar'
        });
      }).finally(() => {
        this.dialog_del = false;
      })
      this.$q.loading.hide();
    },
    onReset() {
      this.cliente = {
        fechanac: '2000-01-01'
      };
      console.log(this.tipocliente);
    }

  },


}
</script>
