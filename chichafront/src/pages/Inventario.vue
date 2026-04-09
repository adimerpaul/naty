<template>
  <div class="row">
    <div class="col-12">
      <div class="text-h6">
        <div style="text-align:center">REGISTRO DE INVENTARIO DE MATERIALES</div>
        <q-btn label="Registrar" icon="add" color="positive" @click="crear = true"/>
      </div>
      <!--            <div class="text-subtitle2">by John Doe</div>-->
      <q-dialog v-model="crear">
        <q-card style="width: 700px; max-width: 80vw;">
          <q-card-section>
            <div class="text-h6">Registro nuevo Material</div>
          </q-card-section>
          <q-card-section>

            <q-form
              @submit="registrar"
              @reset="onReset"
              class="q-gutter-md"
            >
              <div class="row">
                <div class="col-12 col-sm-6 q-pa-xs">
                  <q-input
                    outlined
                    dense
                    type="text"
                    v-model="inventario.codigo"
                    label="Codigo"
                    style="text-transform: uppercase"
                    lazy-rules
                    :rules="[
                          val => !!val || '* Required',
                          val => val.length > 0 || 'Por favor ingrese dato'
                          ]"
                  />
                </div>
                <div class="col-12 col-sm-6 q-pa-xs">
                  <q-input
                    outlined
                    dense
                    type="text"
                    v-model="inventario.nombre"
                    style="text-transform: uppercase"
                    label="Nombre"
                    lazy-rules
                    :rules="[
                          val => !!val || '* Required',
                          val => val.length > 0 || 'Por favor ingrese dato']"
                  />
                </div>
                <div class="col-12 col-sm-6 q-pa-xs">
                  <q-input
                    outlined
                    dense
                    type="number"
                    v-model="inventario.cantidad"
                    label="CANTIDAD"
                    :rules="[
                          val => val >= 0 || 'Por favor ingrese dato']"
                  />

                </div>
                <div class="col-12 col-sm-6 q-pa-xs">
                  <q-input
                    outlined
                    dense
                    type="number"
                    step="0.1"
                    v-model="inventario.precio"
                    label="PRECIO"
                    :rules="[
                          val => val >= 0 || 'Por favor ingrese dato']"
                  />

                </div>
                <div class="col-12 col-sm-6 q-pa-xs">
                  <q-input
                    outlined
                    dense
                    type="number"
                    v-model="inventario.orden"
                    label="ORDEN"
                    :rules="[
                          val => val >= 0 || 'Por favor ingrese dato']"
                  />

                </div>
                <div class="col-12 col-sm-6 q-pa-xs">

                  <q-input
                    outlined
                    dense
                    v-model="inventario.detalle"
                    type="text"
                    label="OBSERVACIONES"
                    style="text-transform: uppercase"
                  />
                  <!--                  <q-select v-model="inventario.producto_id" :options="productos" label="Producto" />-->


                </div>
                <div class="col-12 col-sm-6 q-pa-xs ">
                  <q-btn label="Registrar" type="submit" color="primary" icon="send"/>
                </div>
                <!--                    <div class="col-sm-2">-->

                <!--                    </div>-->

              </div>


            </q-form>
          </q-card-section>

        </q-card>
      </q-dialog>
      <!--
              <div class="q-pa-md">
                <q-table
                  title="INVENTARIO DE MATERIALES"
                  :rows="rows"
                  :filter="filter"
                  :columns="columns"
                  row-key="name"
                :rows-per-page-options="[50,100,200,0]">
                  <template v-slot:top-right>
                    <q-input outlined dense debounce="300" v-model="filter" placeholder="Buscar">
                      <template v-slot:append>
                        <q-icon name="search" />
                      </template>
                    </q-input>
                  </template>
                  <template v-slot:body-cell-estado="props" >
                      <q-td key="estado" :props="props" @click="activar(props.row)">
                        <q-badge color="green" v-if="props.row.estado=='ACTIVO'">
                          {{ props.row.estado }}
                        </q-badge>
                        <q-badge color="red" v-else>
                          {{ props.row.estado }}
                        </q-badge>
                      </q-td>
                  </template>
                  <template v-slot:body-cell-opcion="props" >
                      <q-td key="opcion" :props="props" >
                      <q-btn  dense round flat color="green" @click="addRow(props)" icon="add"></q-btn>
                      <q-btn  dense round flat color="red" @click="substractRow(props)" icon="remove"></q-btn>

                      <q-btn dense round flat color="accent" @click="logRow(props)" icon="list"></q-btn>
                      <q-btn dense round flat color="yellow" @click="editRow(props)" icon="edit"></q-btn>
                      <q-btn dense round flat color="red" @click="delRow(props)" icon="delete"></q-btn>
                      </q-td>
                  </template>
                </q-table>

              </div>
              -->
      <div class="col-12">

        <div class="q-pa-md">
          <div class=" responsive">
            <table id="example" class="display" style="width:100%">
              <thead>
              <tr>
                <th>CODIGO</th>
                <th>ITEM</th>
                <th>CANTIDAD GLOBAL</th>
                <th>PRESTAMO</th>
                <th>VENTAS</th>
                <th>SALDO</th>
                <th>PRECIO</th>
                <th>OBSERVACIONES</th>
                <th>ORDEN</th>
                <th>ESTADO</th>
                <th>OPCIONES</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="v in rows" :key="v.id">
                <td>{{ v.codigo }}</td>
                <td>{{ v.nombre }}</td>
                <td>{{ v.global }}</td>
                <td>{{ v.prestamo }}</td>
                <td>{{ v.ventas }}</td>
                <td style="font-size:16px; font-weight: bold;">{{ v.cantidad }}</td>
                <td>{{ v.precio }}</td>
                <td>{{ v.detalle }}</td>
                <td>{{ v.orden }}</td>
                <td>
                  <q-btn @click="activar(v)" dense :color="v.estado=='ACTIVO'?'green':'red'">
                    {{ v.estado }}
                  </q-btn>
                </td>
                <td>

                  <q-btn dense round flat color="green" @click="addRow(v)" icon="add"
                         v-if="$store.state.login.editinventario">
                    <q-tooltip>Agregar Prod</q-tooltip>
                  </q-btn>
                  <q-btn dense round flat color="red" @click="substractRow(v)" icon="remove"
                         v-if="$store.state.login.editinventario">
                    <q-tooltip>Retirar Prod</q-tooltip>
                  </q-btn>

                  <q-btn dense round flat color="accent" @click="logRow(v)" icon="list">
                    <q-tooltip>Listado</q-tooltip>
                  </q-btn>
                  <q-btn dense round flat color="yellow" @click="editRow(v)" icon="edit"
                         v-if="$store.state.login.editinventario">
                    <q-tooltip>Editar</q-tooltip>
                  </q-btn>
                  <q-btn dense round flat color="red" @click="delRow(v)" icon="delete"
                         v-if="$store.state.login.editinventario">
                    <q-tooltip>Eliminar</q-tooltip>
                  </q-btn>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <q-dialog v-model="dialog_mod">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">MODIFICAR INVENTARIO</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form
              @submit="onMod"
              class="q-gutter-md"
            >
              <q-input
                filled
                type="text"
                v-model="dato.codigo"
                label="Codigo"
                style="text-transform: uppercase"
                lazy-rules
                :rules="[ val => val.length > 0 || 'Por favor ingrese dato']"
              />
              <q-input
                filled
                type="text"
                v-model="dato.nombre"
                label="Item"
                style="text-transform: uppercase"
                lazy-rules
                :rules="[ val => val.length > 0 || 'Por favor ingrese dato']"
              />
              <div class="col-12 col-sm-2 q-pa-xs">
                <q-input
                  filled
                  dense
                  type="number"
                  v-model="dato.orden"
                  label="ORDEN"
                  :rules="[
                          val => val >= 0 || 'Por favor ingrese dato']"
                />

                <q-input
                  filled
                  dense
                  type="number"
                  step="0.1"
                  v-model="dato.precio"
                  label="PRECIO"
                  :rules="[
                        val => val >= 0 || 'Por favor ingrese dato']"
                />
              </div>
              <q-input
                filled
                type="text"
                v-model="dato.detalle"
                style="text-transform: uppercase"
                label="Observaciones"
              />
              <!--                  <q-select v-model="dato.producto_id" :options="productos" label="Producto" />-->


              <div>
                <q-btn label="Modificar" type="submit" color="positive" icon="add_circle"/>
                <q-btn label="Cancelar" icon="delete" color="negative" v-close-popup/>
              </div>
            </q-form>
          </q-card-section>


        </q-card>
      </q-dialog>

      <q-dialog v-model="dialog_add">
        <q-card>
          <q-card-section class="bg-amber-14 text-white">
            <div class="text-h7">AGREGAR CANTIDAD DE MATERIAL</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form
              @submit="onAdd"
              class="q-gutter-md"
            >
              <q-input
                filled
                v-model="dato.nombre"
                type="text"
                label="Nombre"
                readonly
              />
              <q-input
                filled
                v-model="dato.motivo"
                type="text"
                label="Motivo Descripción"
                style="text-transform: uppercase;"
                lazy-rules
                :rules="[ val => val && val.length > 0 || 'Por favor Valor']"
              />

              <q-input
                filled
                v-model="agregar"
                label="Cantidad a agregar"
                type="number"
                min=1
                lazy-rules
                :rules="[ val => val>0 && val < 1000 || 'Por favor Valor']"
              />

              <div>
                <q-btn label="Agregar" type="submit" color="positive" icon="add_circle"/>
                <q-btn flat label="Cancelar" v-close-popup color="teal" icon="cancel"/>
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>


      <q-dialog v-model="dialog_sub">
        <q-card>
          <q-card-section class="bg-amber-14 text-white">
            <div class="text-h7">DAR DE BAJA - MATERIALES</div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form
              @submit="onSub"
              class="q-gutter-md"
            >
              <q-input
                filled
                v-model="dato.nombre"
                type="text"
                label="Nombre"
                readonly
              />
              <q-input
                filled
                v-model="dato.motivo"
                type="text"
                label="Motivo Description"
                style="text-transform: uppercase;"
                lazy-rules
                :rules="[ val => val && val.length > 0 || 'Por favor Valor']"
              />
              <q-input
                filled
                v-model="disminuir"
                label="Cantidad a retirar"
                type="number"
                min=1
                lazy-rules
                :rules="[ val => val>0 && val < 1000 || 'Por favor Valor']"
              />

              <div>
                <q-btn label="Retirar" type="submit" color="red" icon="remove_circle"/>
                <q-btn flat label="Cancelar" v-close-popup color="teal" icon="cancel"/>
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

      <q-dialog v-model="dialog_log">
        <q-card style="min-width: 650px">
          <q-card-section>
            <div class="text-h7">DETALLE DE REGISTRO DE MATERIALES</div>
          </q-card-section>

          <q-card-section class="q-pt-none">
            <q-table
              dense
              flat bordered
              :columns="logcol"
              :rows="logdata"
              row-key="name"
              :rows-per-page-options="[0]"
            >

              <template v-slot:body-cell-agregar="props">
                <q-td key="estado" :props="props" @click="activar(props)">
                  <q-badge color="green" v-if="props.row.agregar==1">
                    Agregar
                  </q-badge>
                  <q-badge color="red" v-else>
                    Disminuir
                  </q-badge>
                </q-td>
              </template>
            </q-table>

          </q-card-section>
          <q-card-actions align="right" class="text-primary">
            <q-btn flat label="Cancel" v-close-popup/>
          </q-card-actions>
        </q-card>
      </q-dialog>


    </div>
  </div>

</template>
<script>
import $ from "jquery";

export default {
  data() {
    return {
      filter: '',
      crear: false,
      dialog_mod: false,
      dialog_del: false,
      dialog_add: false,
      dialog_sub: false,
      dialog_log: false,
      agregar: '',
      disminuir: '',
      inventario: {orden: 0},
      productos: [],
      color: '',
      dato: {},
      columns: [
        {name: 'codigo', label: 'Codigo', align: 'center', field: 'codigo', sortable: true},
        {name: 'nombre', label: 'ITEM', align: 'center', field: 'nombre', sortable: true},
        {name: 'global', align: 'center', label: 'CANTIDAD GLOBAL', field: 'global', sortable: true},
        {name: 'prestamo', align: 'center', label: 'PRESTAMO', field: 'prestamo', sortable: true},
        {name: 'ventas', align: 'center', label: 'VENTAS', field: 'ventas', sortable: true},
        {name: 'cantidad', align: 'center', label: 'SALDO', field: 'cantidad', sortable: true},
        {name: 'detalle', align: 'center', label: 'OBSERVACIONES', field: 'detalle', sortable: true},
        {name: 'orden', align: 'center', label: 'ORDEN', field: 'orden', sortable: true},
        {name: 'estado', align: 'center', label: 'ESTADO', field: 'estado'},
        {name: 'opcion', label: 'OPCIONES', field: 'action'}
      ],
      rows: [],
      logdata: [],
      logcol: [
        {
          name: 'fecha',
          label: 'FECHA',
          align: 'left',
          field: 'fecha',
          sortable: true
        },
        {name: 'cantidad', align: 'right', label: 'CANTIDAD', field: 'cantidad', sortable: true},
        {name: 'agregar', align: 'left', label: 'AGREGAR', field: 'agregar', sortable: true},
        {name: 'motivo', align: 'left', label: 'MOTIVO', field: 'motivo', sortable: true},
      ],

    }
  },
  created() {
    $('#example').DataTable();
    this.listado();
    this.cargar();
  },
  methods: {
    listado() {
      this.$q.loading.show();
      $('#example').DataTable().destroy();

      this.$axios.get(process.env.API + '/inventario').then(res => {
        // console.log(res.data)
        this.rows = []
        res.data.forEach(r => {
          let d = r
          let enprestamo = 0
          let enventa = 0
          // console.log(r.prestamos.length)
          if (r.prestamos.length != 0) {
            // let enprestamos=

            r.prestamos.forEach(p => {
              // console.log(p.estado!=)

              if (p.estado == 'EN PRESTAMO') {
                enprestamo = enprestamo + p.prestado
                // console.log(p.cantidad)
              }
              if (p.estado == 'VENTA') {
                console.log(p)
                enventa = enventa + p.cantidad
                // console.log(p.cantidad)
              }
            })
          }
          d.global = d.cantidad + enprestamo
          d.prestamo = enprestamo
          d.ventas = enventa
          this.rows.push(d)
        })
        // this.rows=res.data;
        this.$q.loading.hide();
        $('#example').DataTable().destroy();
        this.$nextTick(() => {
          $('#example').DataTable({
            dom: 'Blfrtip',
            buttons: [
              'excel', 'pdf'
            ],
            "lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]]

          });
        })
      })
    },
    cargar() {
      this.productos = [];
      this.$axios.get(process.env.API + '/listaproducto').then(res => {
        res.data.forEach(element => {
          this.productos.push({label: element.nombre, value: element.id});

        });
      })
    },
    registrar() {
      // this.inventario.producto_id=this.inventario.producto_id.value;
      this.inventario.orden = this.inventario.orden == undefined ? 0 : this.inventario.orden
      this.$axios.post(process.env.API + '/inventario', this.inventario).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Creado correctamente'
        });
        this.crear = false;
        this.onReset();
        this.listado();
      }).catch(err => {

        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: 'Error al registrar '
        });
      })
    },


    activar(props) {
      this.dato = props;
      this.$axios.post(process.env.API + '/activarinv', this.dato).then(res => {
        this.listado();
      });

    },
    editRow(props) {
      this.dato = props;
      this.dialog_mod = true;
    },
    delRow(props) {
      this.dato = props;
      this.dialog_del = true;
    },
    addRow(props) {
      this.dato = props;
      this.dialog_add = true;
    },
    substractRow(props) {
      this.dato = props;
      this.dialog_sub = true;
    },
    logRow(props) {
      this.dato = props;
      this.$axios.post(process.env.API + '/listlog/', this.dato).then(res => {
        console.log(res.data)
        this.logdata = res.data;
      })
      this.dialog_log = true;
    },
    onMod() {
      this.$q.loading.show();
      this.$axios.put(process.env.API + '/inventario/' + this.dato.id, this.dato).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Modificado correctamente'
        });
        this.dialog_mod = false;
        this.listado();
      }).catch(err => {

        this.$q.notify({
          color: 'red-4',
          textColor: 'white',
          icon: 'error',
          message: 'Error al Modificar'
        });
      })
      this.$q.loading.hide();
    },
    onDel() {
      this.$q.loading.show();
      this.$axios.delete(process.env.API + '/inventario/' + this.dato.id).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Eliminado correctamente'
        });
        this.dialog_del = false;
        this.listado();
      })
      this.$q.loading.hide();
    },

    onAdd() {
      this.$q.loading.show();
      this.modprod = {id: this.dato.id, cantidad: this.agregar, motivo: this.dato.motivo}
      this.$axios.post(process.env.API + '/inventarioadd', this.modprod).then(res => {
        console.log(res.data)
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Agregado correctamente'
        })
        this.dialog_add = false;
        this.agregar = '';
        this.dato.motivo = '';
        this.listado();
      }).catch(err => {
        // console.log()
        this.$q.loading.hide();
        this.$q.notify({
          color: 'red',
          textColor: 'white',
          icon: 'error',
          message: err.response.data.message
        });
      });
    },


    onSub() {
      this.$q.loading.show();
      this.modprod = {id: this.dato.id, cantidad: this.disminuir, motivo: this.dato.motivo};
      this.$axios.post(process.env.API + '/inventariosub', this.modprod).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'modificado correctamente'
        });
        this.dialog_sub = false;
        this.disminuir = '';
        this.dato.motivo = '';
        this.listado();
      })
    },
    onReset() {
      this.inventario = {orden: 0};
    }

  },


}
</script>
