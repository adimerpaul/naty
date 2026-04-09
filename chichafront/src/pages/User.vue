<template>
  <div class="q-pa-md">
    <q-btn
      label="Nuevo usuario"
      color="positive"
      @click="alert = true"
      icon="add_circle"
      class="q-mb-xs"
    />

    <q-dialog v-model="alert">
      <q-card style="max-width: 80%; width: 50%">
        <q-card-section class="bg-green-14 text-white">
          <div class="text-h7">
            <q-icon name="add_circle"/>
            REGISTRO DE NUEVO USUARIO
          </div>
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form @submit="onSubmit" @reset="onReset" class="q-gutter-md">
            <div class="row">
              <div class="col-6">
                <q-input
                  filled
                  v-model="dato.name"
                  type="text"
                  label="Nombre "
                  hint="Ingresar Nombre"
                  lazy-rules
                  :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"
                />
                <!--                <q-input-->
                <!--                  filled-->
                <!--                  v-model="dato.codigo"-->
                <!--                  type="text"-->
                <!--                  label="Codigo "-->
                <!--                  hint="Ingresar codigo"-->
                <!--                  lazy-rules-->
                <!--                  :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"-->
                <!--                />-->

                <q-input
                  filled
                  v-model="dato.email"
                  type="email"
                  label="Email"
                  hint="Correo electronico"
                  lazy-rules
                  :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"
                />

                <!--                <q-input-->
                <!--                  filled-->
                <!--                  v-model="dato.carnet"-->
                <!--                  type="text"-->
                <!--                  label="Carnet identidad"-->
                <!--                  hint="Carnet identidad"-->
                <!--                  lazy-rules-->
                <!--                  :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"-->
                <!--                />-->
                <!--                <q-input-->
                <!--                  filled-->
                <!--                  v-model="dato.celular"-->
                <!--                  type="text"-->
                <!--                  label="Celular"-->
                <!--                  hint="Celular persona"-->
                <!--                  lazy-rules-->
                <!--                  :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"-->
                <!--                />-->
                <q-input
                  filled
                  v-model="dato.password"
                  type="password"
                  label="Contraseña"
                  hint="Contraseña"
                  lazy-rules
                  :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"
                />
                <!--                <q-select-->
                <!--                  filled-->
                <!--                  label="Unidad"-->
                <!--                  v-model="dato.unid_id"-->
                <!--                  :options="unidades"-->
                <!--                  option-label="nombre"-->
                <!--                  option-value="id"-->
                <!--                />-->
                <q-input
                  filled
                  label="Fecha limite"
                  type="date"
                  hint="Fecha limite de acceso"
                  v-model="dato.fechalimite"
                />
              </div>
              <div class="col-6">
                <div class="text-5">Permisos</div>
                <q-checkbox style="width: 100%" v-for="(permiso,index) in permisos" :key="index" :label="permiso.nombre"
                            v-model="permiso.estado"/>
              </div>
            </div>

            <div>
              <q-btn label="Crear" type="submit" color="positive" icon="add_circle"/>
              <q-btn label="Cancelar" icon="delete" color="negative" v-close-popup/>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <q-table dense :filter="filter" title="REGISTRO DE USUARIOS" :rows="data" :columns="columns" row-key="name"
             :rows-per-page-options="[0,50,100]">
      <template v-slot:top-right>
        <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search"/>
          </template>
        </q-input>
      </template>
      <template v-slot:body-cell-estado="props">
        <q-td key="estado" :props="props">
          <q-badge :color="props.row.estado=='ACTIVO'?'green':'red'" :label="props.row.estado"
                   @click="cambioEstado(props.row)"/>

        </q-td>
        <!--          <q-td key="unid" :props="props">-->
        <!--            {{props.row.unid.nombre}}-->
        <!--          </q-td>-->
        <!--          <q-td key="codigo" :props="props">-->
        <!--            {{props.row.codigo}}-->
        <!--          </q-td>-->
        <!--          <q-td key="celular" :props="props">-->
        <!--            {{props.row.celular}}-->
        <!--          </q-td>-->
        <!--          <q-td key="carnet" :props="props">-->
        <!--            {{props.row.carnet}}-->
        <!--          </q-td>-->
      </template>
      <template v-slot:body-cell-permisos="props">

        <q-td key="permisos" :props="props">
          <!--            {{props.row.permisos}}-->
          <ul>
            <li v-for="(p,i) in props.row.permisos" :key="i">{{ p.nombre }}</li>
          </ul>
        </q-td>
      </template>
      <template v-slot:body-cell-opcion="props">

        <q-td key="opcion" :props="props">
          <q-btn dense round flat color="yellow" @click="editRow(props)" icon="edit">
            <q-tooltip>Editar</q-tooltip>
          </q-btn>
          <q-btn dense round flat color="positive" @click="cambiopass(props)" icon="vpn_key">
            <q-tooltip>Cambiar contraseña</q-tooltip>
          </q-btn>
          <q-btn dense round flat color="green-10" @click="mispermisos(props)" icon="post_add">
            <q-tooltip>Permisos</q-tooltip>
          </q-btn>
          <q-btn dense round flat color="red" @click="deleteRow(props)" icon="o_delete">
            <q-tooltip>Eliminar</q-tooltip>
          </q-btn>
        </q-td>

        <!--          <q-td key="opcion" :props="props">-->
        <!--            <q-btn-->
        <!--              dense-->
        <!--              round-->
        <!--              flat-->
        <!--              color="green"-->
        <!--              @click="addRow(props)"-->
        <!--              icon="playlist_add"-->
        <!--            ></q-btn>-->
        <!--            <q-btn-->
        <!--              dense-->
        <!--              round-->
        <!--              flat-->
        <!--              color="green"-->
        <!--              @click="verRow(props)"-->
        <!--              icon="list"-->
        <!--            ></q-btn>-->
        <!--            <q-btn-->
        <!--              dense-->
        <!--              round-->
        <!--              flat-->
        <!--              color="yellow"-->
        <!--              @click="editRow(props)"-->
        <!--              icon="edit"-->
        <!--            ></q-btn>-->
        <!--            <q-btn-->
        <!--              dense-->
        <!--              round-->
        <!--              flat-->
        <!--              color="red"-->
        <!--              @click="deleteRow(props)"-->
        <!--              icon="delete"-->
        <!--            ></q-btn>-->
        <!--          </q-td>-->
      </template>
      <!--      <template v-slot:body-cell-opcion="props">-->
      <!--        <q-td :props="props">-->
      <!--        <q-btn-->
      <!--            dense-->
      <!--            round-->
      <!--            flat-->
      <!--            color="yellow"-->
      <!--            @click="editRow(props)"-->
      <!--            icon="edit"-->
      <!--          ></q-btn>-->
      <!--          <q-btn-->
      <!--            dense-->
      <!--            round-->
      <!--            flat-->
      <!--            color="red"-->
      <!--            @click="deleteRow(props)"-->
      <!--            icon="delete"-->
      <!--          ></q-btn>-->
      <!--        </q-td>-->
      <!--      </template>-->
    </q-table>
    <br>
    <div class="row">
      <div class="col-4 q-pa-xs ">
        <q-input dense outlined v-model="ini" type="date" label="Fecha Ini"/>
      </div>
      <div class="col-4 q-pa-xs ">
        <q-input dense outlined v-model="fin" type="date" label="Fecha Fin"/>
      </div>
      <div class="col-4 q-pa-xs">
        <q-btn dense outline color="info" icon="search" @click="getHistorial()"/>
      </div>
      <div class="col-12">
        <q-table title="LISTADO ACCESO" :rows="logs" :columns="colhist" row-key="name" dense/>

      </div>
    </div>
    <!--    {{permisos2}}-->
    <q-dialog v-model="dialog_mod">
      <q-card style="max-width: 80%; width: 50%">
        <q-card-section class="bg-warning text-white">
          <div class="text-h7">
            <q-icon name="edit"/>
            MODIFICAR DATOS DE USUARIO
          </div>
        </q-card-section>
        <q-card-section class="q-pt-xs">
          <q-form @submit="onMod" class="q-gutter-md">
            <q-input
              filled
              v-model="dato2.name"
              type="text"
              label="Nombre "
              hint="Ingresar Nombre"
              lazy-rules
              :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"
            />
            <!--            <q-input-->
            <!--              filled-->
            <!--              v-model="dato2.codigo"-->
            <!--              type="text"-->
            <!--              label="Codigo "-->
            <!--              hint="Ingresar codigo"-->
            <!--              lazy-rules-->
            <!--              :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"-->
            <!--            />-->
            <q-input
              filled
              v-model="dato2.email"
              type="email"
              label="Email"
              hint="Correo electronico"
              lazy-rules
              :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"
            />
            <!--            <q-input-->
            <!--              filled-->
            <!--              v-model="dato2.carnet"-->
            <!--              type="text"-->
            <!--              label="Carnet"-->
            <!--              hint="Carnet de identidad"-->
            <!--              lazy-rules-->
            <!--              :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"-->
            <!--            />-->
            <!--            <q-input-->
            <!--              filled-->
            <!--              v-model="dato2.celular"-->
            <!--              type="text"-->
            <!--              label="Celular"-->
            <!--              hint="Celular"-->
            <!--              lazy-rules-->
            <!--              :rules="[(val) => (val && val.length > 0) || 'Por favor ingresa datos']"-->
            <!--            />-->
            <!--            <pre>{{dato2}}</pre>-->
            <!--            <q-select-->
            <!--              filled-->
            <!--              label="Unidad"-->
            <!--              v-model="dato2.unid_id"-->
            <!--              :options="unidades"-->
            <!--              option-label="nombre"-->
            <!--              option-value="id"-->
            <!--            />-->

            <q-input
              filled
              label="Fecha limite"
              type="date"
              v-model="dato2.fechalimite"
            />
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
    <q-dialog v-model="modelpermiso">
      <q-card style="width: 850px;max-width: 80vw">
        <q-card-section class="bg-info q-py-none">
          <div class="text-h7 text-white">
            <q-icon name="folder"/>
            PERMISOS DE ACCESO
          </div>
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="updatepermisos">
            <div class="row" v-for="(permiso,index) in permisos2" :key="index">
              <!--              <div class="col-1">-->
              <!--                <q-btn-group>-->
              <!--                  <q-btn icon="expand_less" size="10px" outline dense round color="green" @click="orden(permiso,'Aumentar')"/>-->
              <!--                  <q-btn icon="expand_more" size="10px" outline dense round color="red" @click="orden(permiso,'Reducir')"/>-->
              <!--                </q-btn-group>-->
              <!--              </div>-->
              <div class="col-4">
                <q-checkbox dense :label="`${permiso.id} ${permiso.nombre}`" v-model="permiso.estado"/>
              </div>
              <div class="col-6 text-bold">
                {{ permiso.grupo }}
              </div>
            </div>
            <q-btn dense class="full-width" no-caps type="submit" color="info" icon="send" label="Actualizar"></q-btn>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </div>
</template>

<script>
import {date} from 'quasar'

const {addToDate} = date
export default {
  data() {
    return {
      alert: false,
      dialog_mod: false,
      dialog_del: false,
      filter: '',
      dato: {
        fechalimite: date.formatDate(addToDate(new Date(), {days: 7}), 'YYYY-MM-DD')
      },
      ini: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fin: date.formatDate(new Date(), 'YYYY-MM-DD'),
      model: '',
      dato2: {},
      options: [],
      historial: [],
      props: [],
      unidades: [],
      permisos: [],
      permisos2: [],
      logs: [],
      modelpermiso: false,
      uni: {},
      colhist: [
        {name: "fecha", label: "FECHA ", field: "fecha", sortable: true,},
        {name: "hora", label: "HORA", field: "hora", sortable: true,},
        {name: "user", label: "USUARIO", field: row => row.user.name, sortable: true,},
      ],
      columns: [
        {name: "name", align: "left", label: "NOMBRE ", field: "name", sortable: true,},
        {name: "estado", align: "left", label: "ESTADO ", field: "estado", sortable: true,},
        // {name: "celular", align: "left", label: "Celular ", field: "celular", sortable: true,},
        // {name: "carnet", align: "left", label: "Carnet ", field: "carnet", sortable: true,},
        // {name: "unid", align: "left", label: "Unidad", field: "unid", sortable: true,},
        // {name: "codigo", align: "left", label: "Codigo", field: "codigo", sortable: true,},
        {name: "email", align: "left", label: "E-MAIL", field: "email", sortable: true,},
        {name: "permisos", align: "left", label: "PERMISOS", field: "permisos", sortable: true,},
        {
          name: "fechalimite",
          align: "left",
          label: "FECHA LÍMITE",
          field: row => date.formatDate(row.fechalimite, 'DD/MM/YYYY'),
          sortable: true,
        },
        {name: "opcion", label: "OPCIÓN", field: "action", sortable: false},
      ],
      data: [],
    };
  },
  created() {
    // if (!this.$store.state.boolusuario){
    //   // this.router.push('/')
    // }
    this.misdatos();
    // this.$axios.get(process.env.API+'/unid').then(res=>{
    //   // console.log(res.data)
    //   this.unidades=res.data
    // })
    this.permisosGet();
  },
  methods: {
    getHistorial() {
      if (this.ini == undefined || this.fin == undefined)
        return false
      this.$axios.post(process.env.API + '/loginHistorial', {ini: this.ini, fin: this.fin}).then(res => {
        console.log(res.data)
        this.logs = res.data
      })
    },
    permisosGet() {
      this.permisos = []
      this.permisos2 = []
      this.$axios.get(process.env.API + '/permiso').then(res => {
        // console.log(res.data)
        // this.permisos=res.data
        res.data.forEach(r => {
          this.permisos.push({id: r.id, nombre: r.nombre, estado: false, descripcion: r.descripcion,grupo: r.grupo})
          this.permisos2.push({id: r.id, nombre: r.nombre, estado: false, descripcion: r.descripcion,grupo: r.grupo})
        })
      })
    },
    orden(permiso, accion) {
      console.log(permiso)
      this.$axios.post(process.env.API + '/orden', {
        permiso_id: permiso.id,
        accion: accion
      }).then(res => {
        this.permisosGet()
      })
    },
    //   // updatepermiso(permiso){
    //   //   console.log(permiso)
    //   // },
    cambioEstado(us) {
      this.$axios.post(process.env.API + '/cambioestado', us).then(res => {
        this.misdatos()
      })

    },
    updatepermisos() {
      this.$axios.put(process.env.API + '/updatepermisos/' + this.dato2.id, {permisos: this.permisos2}).then(res => {
        // console.log(res.data)
        this.modelpermiso = false
        this.misdatos()
      }).catch(err => {
        this.$q.notify({
          message: err.response.data.message,
          icon: 'close',
          color: 'red'
        })
      })
    },
    mispermisos(i) {
      // console.log(i.row)
      this.modelpermiso = true
      this.dato2 = i.row
      // console.log(this.dato2.permisos)
      // return false
      let p;
      // this.permisos2.forEach(r=>{
      //
      // })
      this.permisos2.forEach(pe => {
        // console.log(pe);
        p = this.dato2.permisos.find(r => r.pivot.permiso_id == pe.id)
        // console.log(p)
        if (p != undefined)
          pe.estado = true
        else
          pe.estado = false
        // console.log(p)
      })
    },
    misdatos() {
      this.$q.loading.show();
      this.$axios.get(process.env.API + "/user").then((res) => {
        console.log(res.data)
        this.data = res.data;
        this.$q.loading.hide();
      });
    },
    editRow(item) {
      this.dato2 = item.row
      this.dato2.unid_id = item.row.unid
      // console.log(this.dato2)
      this.dialog_mod = true;
    },
    deleteRow(item) {
      this.dato2 = item.row;
      this.dialog_del = true;
    },
    onSubmit() {
      this.$q.loading.show();
      // this.dato.unid_id=this.dato.unid_id.id;
      this.$axios.post(process.env.API + "/user", {
        name: this.dato.name,
        password: this.dato.password,
        email: this.dato.email,
        celular: this.dato.celular,
        carnet: this.dato.carnet,
        // unid_id:this.dato.unid_id.id,
        fechalimite: this.dato.fechalimite,
        // codigo:this.dato.codigo,
        permisos: this.permisos
      }).then((res) => {
        // console.log(res.data)
        this.$q.notify({
          color: "green-4",
          textColor: "white",
          icon: "cloud_done",
          message: "Creado correctamente",
        });
        this.dato = {fechalimite: date.formatDate(Date.now(), 'YYYY-MM-DD')}
        this.alert = false;
        this.misdatos();
      }).catch(err => {
        console.log(err.response.data);
        this.$q.notify({
          message: err.response.data.message,
          icon: 'close',
          color: 'red'
        })
        this.$q.loading.hide()
      })
    },
    onMod() {
      this.$q.loading.show();
      this.$axios.put(process.env.API + "/user/" + this.dato2.id, {
        name: this.dato2.name,
        // password:this.dato2.password,
        // name:this.dato2.name,
        email: this.dato2.email,
        carnet: this.dato2.carnet,
        celular: this.dato2.celular,
        fechalimite: this.dato2.fechalimite,
        // codigo:this.dato2.codigo,
      }).then((res) => {
        this.$q.notify({
          color: "green-4",
          textColor: "white",
          icon: "cloud_done",
          message: "Modificado correctamente",
        });
        this.dialog_mod = false;
        this.misdatos();
      });
    },
    onDel() {
      this.$q.loading.show();
      this.$axios.delete(process.env.API + "/user/" + this.dato2.id)
        .then((res) => {
          this.$q.notify({
            color: "green-4",
            textColor: "white",
            icon: "cloud_done",
            message: "Eliminado correctamente",
          });
          this.dialog_del = false;
          this.misdatos();
        }).catch(err => {
        this.$q.loading.hide()
        this.$q.notify({
          message: err.response.data.message,
          icon: 'error',
          color: 'red'
        })
      })
    },
    onReset() {
      this.dato.nombre = null;
      this.dato.inicio = 0;
      this.dato.fin = 0;
    },
    cambiopass(i) {
      // console.log(i.row);
      this.$q.dialog({
        title: 'CAMBIAR PASSWORD',
        message: 'Ingresar nueva contraseña',
        prompt: {
          model: '',
          type: 'password' // optional
        },
        cancel: true,
        persistent: true
      }).onOk(data => {
        // console.log('>>>> OK, received', data)
        this.$q.loading.show()
        this.$axios.put(process.env.API + '/pass/' + i.row.id, {password: data}).then(res => {
          // console.log(res.data)
          this.$q.loading.hide()
        })
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    }
  },
};
</script>
