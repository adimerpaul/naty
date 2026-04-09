<template>
  <div class="row">
    <div class="col-12">
      <!--            <div class="text-h6">-->
      <!--              <h5 style="text-align:center">INFORMACIÓN DE PRODUCTOS</h5>-->
      <!--            </div>-->
      <!--            <q-card style="width: 100%;">-->
      <!--              <q-card-section>-->
      <!--                <div class="text-h6">REGISTRO NUEVO PRODUCTO</div>-->
      <!--              </q-card-section>-->
      <!--              <q-card-section class="q-pt-none">-->
      <!--                <q-form-->
      <!--                  @submit="registrar"-->
      <!--                  @reset="onReset"-->
      <!--                  class="q-gutter-md"-->
      <!--                >-->
      <!--                <div class="row">-->
      <!--                <div class="col-2">-->
      <!--                  <q-input-->
      <!--                    outlined-->
      <!--                    type="text"-->
      <!--                    v-model="producto.nombre"-->
      <!--                    label="Nombre"-->
      <!--                    style="text-transform: uppercase"-->
      <!--                  />-->
      <!--                  </div>-->
      <!--                  <div class="col-2">-->
      <!--                  <q-input-->
      <!--                    outlined-->
      <!--                    type="number"-->
      <!--                    step="0.1"-->
      <!--                    v-model="producto.precio"-->
      <!--                    label="PRECIO"-->
      <!--                  />-->
      <!--                  </div>-->
      <!--                  <div class="col-2">-->
      <!--                    <q-select-->
      <!--                      outlined-->
      <!--                      v-model="producto.tipo"-->
      <!--                      :options="['LOCAL','DETALLE']"-->
      <!--                      label="TIPO"-->
      <!--                    />-->
      <!--                  </div>-->
      <!--                  <q-input-->
      <!--                  :style="'background-color: '+producto.color"-->
      <!--                  label="color"-->
      <!--                  outlined-->
      <!--                  v-model="producto.color"-->
      <!--                  class="my-input"-->
      <!--                  readonly-->

      <!--                >-->
      <!--                  <template v-slot:append>-->
      <!--                    <q-icon name="colorize" class="cursor-pointer">-->
      <!--                      <q-popup-proxy cover transition-show="scale" transition-hide="scale">-->
      <!--                        <q-color v-model="producto.color" />-->
      <!--                      </q-popup-proxy>-->
      <!--                    </q-icon>-->
      <!--                  </template>-->
      <!--                </q-input>-->
      <!--                  <div class="col-2">-->
      <!--                  <q-input-->
      <!--                    outlined-->
      <!--                    type="text"-->
      <!--                    v-model="producto.observacion"-->
      <!--                    label="Observacion"-->
      <!--                    style="text-transform: uppercase"-->
      <!--                  />-->
      <!--                  </div>-->
      <!--                  <div class="col-2 flex flex-center">-->
      <!--                    <q-btn label="Registrar" type="submit" color="primary" icon="send" />-->
      <!--                  </div>-->
      <!--                  </div>-->

      <!--                </q-form>-->
      <!--              </q-card-section>-->

      <!--            </q-card>-->

      <div class="q-pa-xs">
        <q-table
          dense
          flat
          bordered
          title="LISTA DE PRODUCTOS REGISTRADOS"
          :rows="rows"
          :columns="columns"
          :filter="filter"
          :rows-per-page-options="[0]"
          row-key="name">
          <template v-slot:top-right>
            <q-btn
              color="green"
              label="Agregar Producto"
              icon="add_circle"
              size="10px"
              no-caps
              @click="crearProducto"
              v-if="$store.state.login.registroProducto"
            />
            <q-input outlined dense debounce="300" v-model="filter" placeholder="Buscar">
              <template v-slot:append>
                <q-icon name="search"/>
              </template>
            </q-input>
          </template>
          <template v-slot:body-cell-estado="props">
            <!--              <q-tr :props="props" >-->
            <q-td key="estado" :props="props" @click="activar(props)">
              <q-badge color="green" v-if="props.row.estado=='ACTIVO'">
                {{ props.row.estado }}
              </q-badge>
              <q-badge color="red" v-else>
                {{ props.row.estado }}
              </q-badge>
            </q-td>
            <!--              </q-tr>-->
          </template>
          <template v-slot:body-cell-tipo="props">
            <!--              <q-tr :props="props" >-->
            <q-td key="estado" :props="props">
              <q-badge color="primary" v-if="props.row.tipo=='LOCAL'">
                {{ props.row.tipo }}
              </q-badge>
              <q-badge color="secondary" v-else>
                {{ props.row.tipo }}
              </q-badge>
            </q-td>
            <!--              </q-tr>-->
          </template>
          <template v-slot:body-cell-color="props">
            <q-td key="color" :props="props">
              <div :style="'width: 20px;height:20px; background-color: '+props.row.color"></div>
            </q-td>
          </template>
          <template v-slot:body-cell-opcion="props">
            <!--                <q-td key="opcion" :props="props" >-->
            <!--                <q-btn dense round flat color="yellow" @click="editRow(props)" icon="edit" v-if="$store.state.login.editproducto"><q-tooltip>Editar</q-tooltip></q-btn>-->
            <!--                <q-btn dense round flat color="red" @click="delRow(props)" icon="delete" v-if="$store.state.login.editproducto"><q-tooltip>Eliminar</q-tooltip></q-btn>-->
            <!--                </q-td>-->
            <!--              btn dro down copnes-->
            <q-td>
              <q-btn-dropdown label="Opciones" color="grey-3" text-color="black" no-caps size="10px" dense>
                <q-list>
                  <q-item clickable @click="editRow(props)" v-if="$store.state.login.editproducto">
                    <q-item-section avatar>
                      <q-icon name="edit"/>
                    </q-item-section>
                    <q-item-section>
                      Editar
                    </q-item-section>
                  </q-item>
                  <q-separator/>
                  <q-item clickable @click="delRow(props)" v-if="$store.state.login.editproducto">
                    <q-item-section avatar>
                      <q-icon name="delete"/>
                    </q-item-section>
                    <q-item-section>
                      Eliminar
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-btn-dropdown>
            </q-td>
          </template>
        </q-table>
      </div>

      <q-dialog v-model="dialog_mod">
        <q-card>
          <q-card-section class="bg-green-14 text-white">
            <div class="text-h7">
              {{ dato.id ? 'Modificar Registro Producto' : 'Nuevo Registro Producto' }}
            </div>
          </q-card-section>
          <q-card-section class="q-pt-xs">
            <q-form
              @submit="onMod"
              class="q-gutter-md"
            >
              <q-input
                outlined
                dense
                type="text"
                v-model="dato.nombre"
                label="Nombre"
              />

              <q-input
                outlined
                dense
                type="number"
                step="0.1"
                v-model="dato.precio"
                label="Precio"
              />

              <q-input
                outlined
                dense
                type="text"
                v-model="dato.observacion"
                label="Observacion"
              />
              <q-select
                outlined
                dense
                v-model="dato.grupo"
                :options="$gruposProductos"
                label="Grupo"
              />
              <q-select
                outlined
                dense
                v-model="dato.tipo"
                :options="['LOCAL','DETALLE']"
                label="TIPO"
              />
              <q-input
                outlined
                dense
                type="number"
                v-model="dato.orden"
                label="N° Orden"
              />
              <q-input
                :style="'background-color: '+dato.color"
                label="color"
                outlined
                dense
                v-model="dato.color"
                class="my-input"
                readonly

              >
                <template v-slot:append>
                  <q-icon name="colorize" class="cursor-pointer">
                    <q-popup-proxy cover transition-show="scale" transition-hide="scale">
                      <q-color v-model="dato.color"/>
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
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

      <q-dialog v-model="dialog_add">
        <q-card>
          <q-card-section class="bg-amber-14 text-white">
            <div class="text-h6">Agregar Cantidad Producto</div>
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
                label="Motivo Description"
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
                :rules="[ val => val>0 && val < 500 || 'Por favor Valor']"
              />

              <div>
                <q-btn label="Agregar" type="submit" color="positive" icon="add_circle"/>
              </div>
            </q-form>
          </q-card-section>
        </q-card>
      </q-dialog>


      <q-dialog v-model="dialog_sub">
        <q-card>
          <q-card-section class="bg-amber-14 text-white">
            <div class="text-h6">Retirar cantidad Producto</div>
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
                :rules="[ val => val>0 && val < 500 || 'Por favor Valor']"
              />

              <div>
                <q-btn label="Retirar" type="submit" color="red" icon="remove_circle"/>
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

export default {
  data() {
    return {
      crear: false,
      filter: '',
      dialog_mod: false,
      dialog_del: false,
      dialog_add: false,
      dialog_sub: false,
      dialog_log: false,
      agregar: 0,
      disminuir: 0,
      producto: {},
      color: '',
      dato: {},
      columns: [
        {
          name: 'nombre',
          label: 'NOMBRE',
          align: 'center',
          field: 'nombre',
          sortable: true
        },
        {name: 'precio', align: 'center', label: 'PRECIO', field: 'precio', sortable: true},
        // {name: 'observacion', align: 'center', label: 'OBSERVACIÓN', field: 'observacion', sortable: true},
        // grupo
        {name: 'grupo', align: 'center', label: 'GRUPO', field: 'grupo', sortable: true},
        {name: 'tipo', align: 'center', label: 'TIPO', field: 'tipo', sortable: true},
        {name: 'estado', align: 'center', label: 'ESTADO', field: 'estado'},
        {name: 'orden', align: 'center', label: 'ORDEN', field: 'orden'},
        {name: 'color', align: 'center', label: 'COLOR', field: 'color'},
        {name: 'opcion', label: 'OPCIONES', field: 'action'}
      ],
      rows: [],

    }
  },
  created() {
    this.listado();
  },
  methods: {
    crearProducto() {
      this.dialog_mod = true;
      this.dato = {};
    },
    listado() {
      this.$q.loading.show();
      this.$axios.get(process.env.API + '/producto').then(res => {
        console.log(res.data)
        this.rows = res.data;
        this.$q.loading.hide();
      })
    },
    registrar() {
      this.producto.nombre = this.producto.nombre.toUpperCase()

      // console.log(this.producto.observacion)
      if (this.producto.observacion != undefined) {
        this.producto.observacion = this.producto.observacion.toUpperCase()
      }

      this.$axios.post(process.env.API + '/producto', this.producto).then(res => {
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
          message: 'Error al registrar'
        });
      })
    },


    activar(props) {
      this.dato = props.row;
      this.$axios.post(process.env.API + '/activarprod', this.dato).then(res => {
        this.listado();
      });

    },
    editRow(props) {
      this.dato = { ...props.row }; // Spread operator to clone the row data
      this.dialog_mod = true;
    },
    logRow(props) {
      this.dato = props.row;
      this.dialog_log = true;
    },
    delRow(props) {
      this.dato = props.row;
      this.dialog_del = true;
    },
    addRow(props) {
      this.dato = props.row;
      this.dialog_add = true;
    },
    substractRow(props) {
      this.dato = props.row;
      this.dialog_sub = true;
    },
    onMod() {
      if (this.dato.id == undefined) {
        this.$q.loading.show();
        this.$axios.post(process.env.API + '/producto', this.dato).then(res => {
          this.$q.notify({
            color: 'green-4',
            textColor: 'white',
            icon: 'cloud_done',
            message: 'Creado correctamente'
          });
          this.dialog_mod = false;
          this.listado();
        }).catch(err => {
          this.$q.notify({
            color: 'red-4',
            textColor: 'white',
            icon: 'error',
            message: 'Error al registrar'
          });
        }).finally(() => {
          this.$q.loading.hide();
          this.dialog_mod = false;
        })
      } else {
        this.$q.loading.show();
        this.$axios.put(process.env.API + '/producto/' + this.dato.id, this.dato).then(res => {
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
        }).finally(() => {
          this.$q.loading.hide();
          this.dialog_mod = false;
        })
      }

      //  this.$q.loading.show();
      //  this.$axios.put(process.env.API+'/producto/'+this.dato.id,this.dato).then(res=>{
      //   this.$q.notify({
      //    color: 'green-4',
      //    textColor: 'white',
      //    icon: 'cloud_done',
      //    message: 'Modificado correctamente'
      //  });
      //  this.dialog_mod=false;
      //  this.listado();
      // }).catch(err=>{
      //
      //    this.$q.notify({
      //      color: 'red-4',
      //      textColor: 'white',
      //      icon: 'error',
      //      message: 'Error al Modificar'
      //    });
      //  })
      //  this.$q.loading.hide();
    },
    onDel() {
      this.$q.loading.show();
      this.$axios.delete(process.env.API + '/producto/' + this.dato.id).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Eliminado correctamente'
        });
        this.dialog_del = false;
        this.listado();
      }).catch((error) => {
        this.$q.notify({
          color: 'red-4',
          icon: 'info',
          message: error
        });
      })

      this.$q.loading.hide();
    },
    onAdd() {
      this.$q.loading.show();
      this.modprod = {id: this.dato.id, cantidad: this.agregar, motivo: this.dato.motivo}
      this.$axios.post(process.env.API + '/inventarioadd', this.modprod).then(res => {
        this.$q.notify({
          color: 'green-4',
          textColor: 'white',
          icon: 'cloud_done',
          message: 'Agregado correctamente'
        });
        this.dialog_add = false;
        this.agregar = 0;
        this.listado();
      })
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
        this.disminuir = 0;
        this.listado();
      })
    },
    onReset() {
      this.producto = {};
    }

  },


}
</script>
