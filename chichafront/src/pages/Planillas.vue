<template>
<q-page class="q-pa-xs">
<!--  <div class="col-12">-->
<!--          <iframe id="docpdf" src="" frameborder="0" style="width: 100%;height: 100vh"></iframe>-->
<!--  </div>-->
<q-table dense flat bordered :rows="empleados" :rows-per-page-options="[0]" :columns="columsempleado">
  <template v-slot:body-cell-opcion="props">
    <q-td :props="props" auto-width>
      <q-btn-dropdown color="primary" label="Opciones">
        <q-list>
          <q-item clickable v-close-popup @click="clickmod(props.row)" >
              <q-item-section avatar>
                <q-avatar icon="edit" color="warning" text-color="white" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Modificar empleado</q-item-label>
                <q-item-label caption>Modificar empleado</q-item-label>
              </q-item-section>
          </q-item>
          <q-item clickable v-close-popup @click="deleteval(props.row)">
              <q-item-section avatar>
                <q-avatar icon="delete" color="negative" text-color="white" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Eliminar empleado</q-item-label>
                <q-item-label caption>Eliminar empleado</q-item-label>
              </q-item-section>
          </q-item>
          <q-item clickable v-close-popup @click="clickplanilla(props.row)">
            <q-item-section avatar>
              <q-avatar icon="add_task" color="primary" text-color="white" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Generar planilla</q-item-label>
              <q-item-label caption>Generar planilla</q-item-label>
            </q-item-section>
<!--            <q-item-section side>-->
<!--              <q-icon name="info" color="amber" />-->
<!--            </q-item-section>-->
          </q-item>
        </q-list>
      </q-btn-dropdown>
    </q-td>
  </template>
  <template v-slot:body-cell-tipo="props">
    <q-td :props="props" auto-width>
      <q-badge :color="props.row.tipo=='CONTRATO'?'green':'EVENTUAL'">{{props.row.tipo}}</q-badge>
    </q-td>
  </template>
  <template v-slot:body-cell-nombre="props">
    <q-td :props="props"  >
      {{props.row.nombre}}
    </q-td>
  </template>
  <template v-slot:body-cell-planillas="props">
    <q-td :props="props" auto-width >
<!--      <pre>{{}}</pre>-->
      <q-list dense bordered padding class="rounded-borders">
        <q-item v-for="p in props.row.planillas" :key="p.id" clickable v-ripple>
          <q-item-section>
            {{ p.fechainicio }}
            {{ p.fechafin }}
            {{ p.monto }}
          </q-item-section>
        </q-item>
      </q-list>
    </q-td>
  </template>
  <template v-slot:body-cell-celular="props">
    <q-td :props="props" auto-width >
      {{props.row.celular}}
    </q-td>
  </template>
  <template v-slot:top-right>
    <q-btn @click="clickcreateempleado" color="positive" icon="add_circle" label="Crear Empleado"/>
    <q-input dense outlined placeholder="Buscar"/>
  </template>
</q-table>

<q-dialog v-model="dialogcreateempleado" >
<q-card>
  <q-card-section class="text-center text-subtitle2">Crear Empleado</q-card-section>
  <q-separator/>
  <q-card-section>
    <q-form @submit="createempleado">
      <div class="row">
        <div class="col-12">
          <q-input dense outlined label="ci" v-model="empleado.ci"/>
        </div>
        <div class="col-12">
          <q-input dense outlined label="nombre" v-model="empleado.nombre"/>
        </div>
        <div class="col-12">
          <q-input dense type="date" outlined label="fechanac" v-model="empleado.fechanac"/>
        </div>
        <div class="col-12">
          <q-input dense outlined label="celular" v-model="empleado.celular"/>
        </div>
        <div class="col-12">
          <q-select :options="['CONTRATO','EVENTUAL']" dense outlined label="tipo" v-model="empleado.tipo"/>
        </div>
        <div class="col-12">
          <q-input dense outlined label="salario" type="number" v-model="empleado.salario"/>
        </div>
        <div class="col-12">
          <q-btn label="Crear empleado" class="full-width" type="submit" icon="add_circle" color="positive"  />
        </div>
      </div>
    </q-form>
  </q-card-section>
</q-card>
</q-dialog>

<q-dialog v-model="dialogmodempleado" >
<q-card>
  <q-card-section class="text-center text-subtitle2">Modificar Empleado</q-card-section>
  <q-separator/>
  <q-card-section>
    <q-form @submit="modempleado">
      <div class="row">
        <div class="col-12">
          <q-input dense outlined label="ci" v-model="empleado2.ci"/>
        </div>
        <div class="col-12">
          <q-input dense outlined label="nombre" v-model="empleado2.nombre"/>
        </div>
        <div class="col-12">
          <q-input dense type="date" outlined label="fechanac" v-model="empleado2.fechanac"/>
        </div>
        <div class="col-12">
          <q-input dense outlined label="celular" v-model="empleado2.celular"/>
        </div>
        <div class="col-12">
          <q-select :options="['CONTRATO','EVENTUAL']" dense outlined label="tipo" v-model="empleado2.tipo"/>
        </div>
        <div class="col-12">
          <q-input dense outlined label="salario" type="number" v-model="empleado2.salario"/>
        </div>
        <div class="col-12">
          <q-btn label="Modificar empleado" class="full-width" type="submit" icon="add_circle" color="positive"  />
        </div>
      </div>
    </q-form>
  </q-card-section>
</q-card>
</q-dialog>

  <q-dialog v-model="dialogcreateplanilla" full-width full-height>
    <q-card>
      <q-card-section class="text-center text-subtitle2">Crear Planilla {{empleado.nombre}} {{empleado.tipo}}</q-card-section>
      <q-separator/>
      <q-card-section>
        <div class="row">
          <div class="col-12">
            <q-form @submit="createplanilla">
              <div class="row">
                <div class="col-3">
                  <q-input dense type="date" outlined label="fechainicio" v-model="fechainicio"/>
                </div>
                <div class="col-3">
                  <q-input dense type="date" outlined label="fechafin" v-model="fechafin"/>
                </div>
                <div class="col-3">
                  <q-input dense outlined label="monto" v-model="monto"/>
                </div>
                <div class="col-3 flex flex-center">
                  <q-btn label="Crear planilla" class="full-width" type="submit" icon="add_circle" color="positive"  />
                </div>
              </div>
            </q-form>
          </div>
          <div class="col-12">
            <q-table :columns="columsmisplanillaempleado" title="Historial de planillas" :rows-per-page-options="[0]" :rows="misplanillaempleado">
              <template v-slot:body-cell-estado="props">
                <q-td :props="props">
                  <q-badge :color="props.row.estado=='CREADO'?'positive':'negative'" :label="props.row.estado" />
                </q-td>
              </template>
              <template v-slot:body-cell-opcion="props">
                <q-td :props="props" auto-width>
                  <q-btn-dropdown :color="props.row.estado=='CREADO'?'positive':'negative'" label="Opciones">
                    <q-list>
                      <q-item v-if="props.row.estado=='CREADO'" clickable v-close-popup @click="clickupdateplanilla(props.row)">
                        <q-item-section avatar>
                          <q-avatar icon="edit_note" color="accent" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Modificar planilla</q-item-label>
                          <q-item-label caption>Modificar planilla</q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-item v-if="props.row.estado=='CREADO'" clickable v-close-popup @click="eliminarplanilla(props.row)">
                        <q-item-section avatar>
                          <q-avatar icon="delete" color="negative" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Eliminar planilla</q-item-label>
                          <q-item-label caption>Eliminar planilla</q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="imprimirplanilla(props.row)">
                        <q-item-section avatar>
                          <q-avatar icon="print" color="primary" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Imprimir planilla</q-item-label>
                          <q-item-label caption>Imprimir planilla</q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-item v-if="props.row.estado=='CREADO'" clickable v-close-popup @click="adelantodescuento(props.row)">
                        <q-item-section avatar>
                          <q-avatar icon="paid" color="positive" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Adelanto y decuento</q-item-label>
                          <q-item-label caption>Adelanto y decuento</q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-item v-if="props.row.estado=='CREADO'" clickable v-close-popup @click="darcancelado(props.row)">
                        <q-item-section avatar>
                          <q-avatar icon="logout" color="accent" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Dar por cancelado</q-item-label>
                          <q-item-label caption>Dar por cancelado</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-btn-dropdown>
                </q-td>
              </template>
            </q-table>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-dialog v-model="dialogplanilla">
    <q-card>
      <q-card-section class="text-center text-bold">Planilla {{empleado.nombre}} {{empleado.tipo}}</q-card-section>
      <q-separator/>
      <q-card-section>
        <q-form @submit.prevent="updateplanilla">
          <div class="row">
            <div class="col-12">
              <q-input label="fechainicio" type="date" dense outlined v-model="planilla.fechainicio" />
            </div>
            <div class="col-12">
              <q-input label="fechafin" type="date" dense outlined v-model="planilla.fechafin" />
            </div>
            <div class="col-12">
              <q-input label="monto" dense outlined v-model="planilla.monto" />
            </div>
            <div class="col-12">
              <q-input label="adelanto" dense outlined v-model="planilla.adelanto" />
            </div>
            <div class="col-12">
              <q-input label="bono" dense outlined v-model="planilla.bono" />
            </div>
<!--            <div class="col-12">-->
<!--              <q-input label="restante" dense outlined v-model="planilla.restante" />-->
<!--            </div>-->
            <div class="col-12">
              <q-input label="total" dense outlined v-model="planilla.total" />
            </div>
            <div class="col-12 flex flex-center">
              <q-btn type="submit" class="full-width" color="positive" icon="edith" label="Modificar planilla" />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-dialog v-model="dialoglogplanillaupdate">
    <q-card>
      <q-card-section class="text-center text-bold">
        <div >Planilla {{empleado.nombre}} {{empleado.tipo}}</div> <div>Del: {{planilla.fechainicio}} Al {{planilla.fechafin}}</div>
      </q-card-section>
      <q-separator/>
      <q-card-section>
        <q-form @submit.prevent="updatelogplanilla">
          <div class="row">
            <div class="col-12">
              <q-input label="tipo" dense outlined v-model="logplanillaupdate.tipo" />
            </div>
            <div class="col-12">
              <q-input label="monto" type="number" dense outlined v-model="logplanillaupdate.monto" />
            </div>
            <div class="col-12">
              <q-input label="motivo" dense outlined v-model="logplanillaupdate.motivo" />
            </div>
            <div class="col-12 flex flex-center">
              <q-btn type="submit" class="full-width" color="positive" icon="edith" label="Modificar planilla" />
            </div>
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
  <q-dialog v-model="dialoglogplanilla">
    <q-card>
      <q-card-section class="text-center text-bold">
        <div >Historial de adelantos y descuentos {{empleado.nombre}} {{empleado.tipo}}</div> <div>Del: {{planilla.fechainicio}} Al {{planilla.fechafin}}</div></q-card-section>
      <q-separator/>
      <q-card-section>
        <div class="row">
          <div class="col-12">
            <q-form @submit.prevent="createlogplanilla">
              <div class="row">
                <div class="col-3">
                  <q-select :options="['BONO','ADELANTO','DESCUENTO']" label="Tipo"  dense outlined v-model="logplanilla.tipo" />
                </div>
                <div class="col-3">
                  <q-input label="Monto" type="number" dense outlined v-model="logplanilla.monto" />
                </div>
                <div class="col-3">
                  <q-input label="Motivo" type="text" dense outlined v-model="logplanilla.motivo" />
                </div>
                <div class="col-3 flex flex-center">
                  <q-btn type="submit" class="full-width" color="positive" icon="add_circle" label="Crear" />
                </div>
              </div>
            </q-form>
          </div>
          <div class="col-12">
            <q-table :rows="logplanillas" title="Historial de planillas" :columns="columslogplanillas" :rows-per-page-options="[0]">
              <template v-slot:body-cell-tipo="props">
                <q-td :props="props">
                  <q-badge :color="props.row.tipo=='ADELANTO'?'negative':props.row.tipo=='DESCUENTO'?'negative':props.row.tipo=='BONO'?'positive':''">{{props.row.tipo}}</q-badge>
                </q-td>
              </template>
              <template v-slot:body-cell-opcion="props">
                <q-td :props="props" auto-width>
                  <q-btn-dropdown color="primary" label="Opciones">
                    <q-list>
                      <q-item clickable v-close-popup @click="clickupdatelogplanilla(props.row)">
                        <q-item-section avatar>
                          <q-avatar icon="edit_note" color="accent" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Modificar planilla</q-item-label>
                          <q-item-label caption>Modificar planilla</q-item-label>
                        </q-item-section>
                      </q-item>
                      <q-item clickable v-close-popup @click="eliminarlogplanilla(props.row)">
                        <q-item-section avatar>
                          <q-avatar icon="delete" color="negative" text-color="white" />
                        </q-item-section>
                        <q-item-section>
                          <q-item-label>Eliminar planilla</q-item-label>
                          <q-item-label caption>Eliminar planilla</q-item-label>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </q-btn-dropdown>
                </q-td>
              </template>
            </q-table>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</q-page>
</template>

<script>
import {date} from "quasar"
import $ from 'jquery'
import {jsPDF} from "jspdf"
import moment from 'moment'
const conversor = require('conversor-numero-a-letras-es-ar');

export default {
  name: `Planillas`,
  data(){
    return{
      dialoglogplanilla:false,
      logplanilla: {},
      dialoglogplanillaupdate:false,
      logplanillaupdate: {},
      logplanillas: [],
      dialogplanilla:false,
      dialogcreateempleado:false,
      dialogmodempleado:false,
      dialogcreateplanilla:false,
      fechainicio:date.formatDate(new Date(),'YYYY-MM-DD'),
      fechafin:date.formatDate(new Date(),'YYYY-MM-DD'),
      fechahoy:date.formatDate(new Date(),'YYYY-MM-DD'),
      monto:0,
      empleado:{tipo:'CONTRATO',fechanac:date.formatDate(new Date(),'YYYY-MM-DD')},
      empleado2:{},
      empleados:[],
      columsempleado:[
        {label:'opcion',name:'opcion',field:'opcion',align:'left'},
        {label:'ci',name:'ci',field:'ci',align:'left'},
        {label:'nombre',name:'nombre',field:'nombre',align:'left'},
        {label:'fechanac',name:'fechanac',field:'fechanac',align:'left'},
        {label:'tipo',name:'tipo',field:'tipo',align:'left'},
        {label:'celular',name:'celular',field:'celular',align:'left'},
        // {label:'planillas',name:'planillas',field:'planillas'},
        {label:'salario',name:'salario',field:'salario',align:'left'},
      ],
      columslogplanillas:[
        {label:'opcion',name:'opcion',field:'opcion',align:'left'},
        {label:'tipo',name:'tipo',field:'tipo',align:'left'},
        {label:'monto',name:'monto',field:'monto',align:'left'},
        {label:'motivo',name:'motivo',field:'motivo',align:'left'},
        {label:'fecha',name:'fecha',field:'fecha',align:'left'},
        {label:'hora',name:'hora',field:'hora',align:'left'},

        // {label:'planillas',name:'planillas',field:'planillas'},
        // {label:'salario',name:'salario',field:'salario',align:'left'},
      ],
      misplanillaempleado:[],
      columsmisplanillaempleado:[
        {label:'opcion',name:'opcion',field:'opcion',align:'left'},
        {label:'fechainicio',name:'fechainicio',field:'fechainicio',align:'left'},
        {label:'fechafin',name:'fechafin',field:'fechafin',align:'left'},
        {label:'monto',name:'monto',field:'monto',align:'left'},
        {label:'adelanto',name:'adelanto',field:'adelanto',align:'left'},
        {label:'descuento',name:'descuento',field:'descuento',align:'left'},
        {label:'bono',name:'bono',field:'bono',align:'left'},
        // {label:'restante',name:'restante',field:'restante',align:'left'},
        {label:'total',name:'total',field:'total',align:'left'},
        {label:'estado',name:'estado',field:'estado',align:'left'},
      ],
      planilla:{},
    }
  },
  created() {
    this.misempleados()

  },
  mounted() {

  },
  methods:{
    createlogplanilla(){
      this.$q.loading.show()
      this.logplanilla.fecha=date.formatDate(new Date(),'YYYY-MM-DD')
      this.logplanilla.hora=date.formatDate(new Date(),'HH:mm:ss')
      this.logplanilla.planilla_id=this.planilla.id
      // console.log(logp)
      this.$axios.post(process.env.API+'/logplanilla',this.logplanilla).then(res=>{
        // console.log(res.data)
        if (this.logplanilla.tipo=='BONO'){
          this.$axios.put(process.env.API+'/planilla/'+this.planilla.id, {
            adelanto:this.planilla.adelanto,
            descuento:this.planilla.descuento,
            bono:parseInt(this.planilla.bono)+parseInt(this.logplanilla.monto),
            // restante:this.planilla.restante,
            total:parseInt(this.planilla.total)+parseInt(this.logplanilla.monto),
            estado:'CREADO',
          }).then(res=>{
            this.mihistorialplanilla()
          })
        }
        if (this.logplanilla.tipo=='DESCUENTO'){
          this.$axios.put(process.env.API+'/planilla/'+this.planilla.id, {
            adelanto:this.planilla.adelanto,
            descuento:parseInt(this.planilla.descuento)+parseInt(this.logplanilla.monto),
            bono:this.planilla.bono,
            // restante:this.planilla.restante,
            total:parseInt(this.planilla.total)-parseInt(this.logplanilla.monto),
            estado:'CREADO',
          }).then(res=>{
            this.mihistorialplanilla()
          })
        }
        if (this.logplanilla.tipo=='ADELANTO'){
          this.$axios.put(process.env.API+'/planilla/'+this.planilla.id, {
            adelanto:parseInt(this.planilla.adelanto)+parseInt(this.logplanilla.monto),
            descuento:this.planilla.descuento,
            bono:this.planilla.bono,
            // restante:this.planilla.restante,
            total:parseInt(this.planilla.total)-parseInt(this.logplanilla.monto),
            estado:'CREADO',
          }).then(res=>{
            this.mihistorialplanilla()
          })
        }
        this.milogplanillas()

        this.logplanilla={}
        // this.$q.loading.hide()
        // this.misplanillaempleado=res.data
      })
    },
    clickmod(empleado){
      this.empleado2=empleado
      this.dialogmodempleado=true
    },
    clickplanilla(empleado){
      this.empleado=empleado
      // console.log(empleado)
      this.monto=this.empleado.salario
      this.dialogcreateplanilla=true
      this.mihistorialplanilla()
    },
    mihistorialplanilla(){
      this.$q.loading.show()
      this.$axios.get(process.env.API+'/planilla/'+this.empleado.id).then(res=>{
        // console.log(res.data)
        this.$q.loading.hide()
        this.misplanillaempleado=res.data
      })
    },
    clickcreateempleado(){
      this.dialogcreateempleado=true
      this.empleado={tipo:'CONTRATO',fechanac:date.formatDate(new Date(),'YYYY-MM-DD')}
    },
    misempleados(){
      this.$q.loading.show()
      this.$axios.get(process.env.API+'/empleado/1/edit').then(res=>{
        // console.log(res.data)
        this.$q.loading.hide()
        this.empleados=res.data
      })
    },
    createplanilla(){
      this.$q.loading.show()
      this.$axios.post(process.env.API+'/planilla',{
        fechainicio:this.fechainicio,
        fechafin:this.fechafin,
        fechapago:null,
        monto:this.monto,
        adelanto:0,
        descuento:0,
        bono:0,
        restante:this.monto,
        total:this.monto,
        estado:'CREADO',
        empleado_id:this.empleado.id,
      }).then(res=>{
        // this.$q.loading.hide()
        // console.log(res.data)
        // this.$q.loading.hide()
        // this.empleado={tipo:'CONTRATO',fechanac:date.formatDate(new Date(),'YYYY-MM-DD')}
        // this.dialogcreateplanilla=false
        // this.misempleados()
        // this.misplanillaempleado()
        this.mihistorialplanilla()
      })
    },
    updateplanilla(){
      this.$q.loading.show()
      this.$axios.put(process.env.API+'/planilla/'+this.planilla.id,this.planilla).then(res=>{
        // this.$q.loading.hide()
        // console.log(res.data)
        // this.$q.loading.hide()
        // this.empleado={tipo:'CONTRATO',fechanac:date.formatDate(new Date(),'YYYY-MM-DD')}
        // this.dialogcreateplanilla=false
        // this.misempleados()
        // this.misplanillaempleado()
        this.mihistorialplanilla()
        this.dialogplanilla=false
      })
    },
    updatelogplanilla(){
      this.$q.loading.show()
      this.logplanillaupdate.fecha=date.formatDate(new Date(),'YYYY-MM-DD')
      this.logplanillaupdate.hora=date.formatDate(new Date(),'HH:mm:ss')
      // this.logplanillaupdate.planilla_id=this.planilla.id
      // console.log(logp)
      this.$axios.put(process.env.API+'/logplanilla/'+this.logplanillaupdate.id,this.logplanillaupdate).then(res=>{
        // console.log(res.data)
        // this.logplanilla={}
        this.dialoglogplanillaupdate=false
        this.milogplanillas()
        // this.$q.loading.hide()
        // this.misplanillaempleado=res.data
      })
    },
    clickupdatelogplanilla(logplanilla){
      this.logplanillaupdate=logplanilla
      // console.log(planilla)
      this.dialoglogplanillaupdate=true
    },
    eliminarplanilla(planilla){
      this.$q.dialog({
        title:"Seguro eliminar planilla?",
        cancel:true
      }).onOk(()=>{
        this.$q.loading.show()
        this.$axios.delete(process.env.API+'/planilla/'+planilla.id).then(res=>{
          this.mihistorialplanilla()
        })
      })
    },
    eliminarlogplanilla(logplanilla){
      this.$q.dialog({
        title:"Seguro eliminar planilla?",
        cancel:true
      }).onOk(()=>{
        this.$q.loading.show()
        this.$axios.delete(process.env.API+'/logplanilla/'+logplanilla.id).then(res=>{
          this.milogplanillas()
        })
      })
    },
    darcancelado(planilla){
      this.$q.dialog({
        title:"Seguro dar por cacelado?",
        cancel:true
      }).onOk(()=>{
        this.$q.loading.show()
        this.$axios.put(process.env.API+'/planilla/'+planilla.id,{
          estado:'CANCELADO',
          fechapago:date.formatDate(new Date(),'YYYY-MM-DD')
        }).then(res=>{
          this.mihistorialplanilla()
        })
      })

    },
    adelantodescuento(planilla){
      this.planilla=planilla
      this.dialoglogplanilla=true
      this.milogplanillas()
    },
    milogplanillas(){
      this.$q.loading.show()
      this.$axios.get(process.env.API+'/logplanilla/'+this.planilla.id).then(res=>{
        // this.mihistorialplanilla()
        // console.log(res.data)
        this.logplanillas=res.data
        this.$q.loading.hide()
      })
    },
    imprimirplanilla(planilla){
      let cm=this;
      function header(){
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'png', 5, 2, 37, 17)
        doc.setFont(undefined,'bold')
        doc.setFontSize(8);
        doc.text(170, 5, 'fecha de proceso: '+cm.fechahoy,'DD/MM/YYYY')
        doc.setFontSize(11);
        doc.text('CHICHERIA DOÑA NATI',110, 7, 'center')
        doc.text('BOLETA DE PAGO',110, 12, 'center')
        doc.text('Correspondiente  '+moment(planilla.fechainicio).format('DD/MM/YYYY')+' Al '+moment(planilla.fechafin).format('DD/MM/YYYY')+' Fecha de pago: ' + (planilla.fechapago==null||planilla.fechapago==undefined||planilla.fechapago==''?'Sn':moment(planilla.fechapago).format('DD/MM/YYYY')),110, 17,'center' )
        // doc.text('____________________________________________________________________________________________________',1, 2.5,'center')
        doc.line(5,20,210,20)
        // doc.text(2, 3, 'FECHA DE PAGO')
        // // doc.text(4, 3, 'Nº TRAMITE')
        // doc.text(8, 3, 'N TRAMITES')
        // // doc.text(13.5, 3, 'CI/RUN/RUC')
        // doc.text(15, 3, 'MONTO PAGADO BS.')
        // doc.text(18, 3, 'OPERADOR')
        doc.setFont(undefined,'normal')
      }
      var doc = new jsPDF('p',undefined,'letter')
      doc.setFont("Times");
      header()
      doc.setFontSize(10);
      doc.setFont(undefined,'bold')
      doc.text(['CI:','Nombre:','Fecha nac:'],10,25)
      doc.setFont(undefined,'normal')
      doc.text([this.empleado.ci,this.empleado.nombre,this.empleado.fechanac],65,25,'center')
      doc.setFont(undefined,'bold')
      doc.text(['Tipo:','Celular','Edad:'],130,25)
      doc.setFont(undefined,'normal')
      var nacimiento=moment(this.empleado.fechanac);
      var hoy=moment();
      var anios=hoy.diff(nacimiento,"years");
      doc.text([this.empleado.tipo,this.empleado.celular.toString(),anios.toString()],170,25,'center')
      doc.line(5,35,210,35)
      doc.setFont(undefined,'bold')
      doc.text('INGRESOS',45,39,'center')
      doc.text('DESCUENTOS',150,39,'center')
      doc.line(5,40,210,40)
      doc.line(110,40,110,70)
      doc.setFont(undefined,'bold')
      doc.text(['Monto:','Bono:'],15,45)
      doc.setFont(undefined,'normal')
      doc.text([planilla.monto+' Bs',planilla.bono+' Bs'],105,45,'right')
      doc.setFont(undefined,'bold')
      doc.text(['Adelanto:','Descuento:'],115,45)
      doc.setFont(undefined,'normal')
      doc.text([planilla.adelanto+' Bs',planilla.descuento+' Bs'],205,45,'right')
      doc.line(5,65,210,65)
      doc.setFont(undefined,'bold')
      doc.text('Total ganado:',35,69,'center')
      doc.text('Total descuento:',140,69,'center')
      doc.setFont(undefined,'normal')
      doc.text((parseInt(planilla.monto)+parseInt(planilla.bono))+' Bs',105,69,'right')
      doc.text((parseInt(planilla.descuento)+parseInt(planilla.adelanto))+' Bs',205,69,'right')
      doc.line(5,70,210,70)
      doc.setFont(undefined,'bold')
      doc.text('Liquido pagable: '+planilla.total+' Bs',110,75,'center')
      let ClaseConversor = conversor.conversorNumerosALetras;
      let miConversor = new ClaseConversor();
      let a = miConversor.convertToText(planilla.total);
      // doc.text(1.5, y+4.4, 'SON: ')
      doc.setFont(undefined,'normal')
      doc.text('SON: '+a.toUpperCase()+' Bs',110, 80, 'center')
      doc.setFont(undefined,'bold')
      // // console.log(a)
      doc.text(5, 90, '____________________________                     _____________________________________           ______________________________')
      doc.text(10, 95, 'FIRMA SELLO CAJERO')
      doc.text(75, 95, 'FIRMA SELLO CONTROL INTERNO')
      doc.text(150, 95, 'FIRMA SELLO LIQUIDADOR')
      // // doc.setFont(undefined,'normal')
      // // doc.text(18, y+3.5, sumtotal+ ' Bs')
      //
      // $( '#docpdf' ).attr('src', doc.output('datauristring'));
      window.open(doc.output('bloburl'), '_blank');

    },
    clickupdateplanilla(planilla){
      this.planilla=planilla
      // console.log(planilla)
      this.dialogplanilla=true
    },
    createempleado(){
      console.log(this.empleado)
      this.$q.loading.show()
      // if (this.empleado.salario==undefined||this.empleados.salario==null||this.empleado.salario==''){
      //   this.empleado.salario=0
      // }
      this.$axios.post(process.env.API+'/empleado',this.empleado).then(res=>{
        // console.log(res.data)
        // this.$q.loading.hide()
        this.empleado={tipo:'CONTRATO',fechanac:date.formatDate(new Date(),'YYYY-MM-DD')}
        this.dialogcreateempleado=false
        this.misempleados()
      })
    },
    modempleado(){
    //  console.log(this.empleado)
      this.$q.loading.show()
      this.$axios.put(process.env.API+'/empleado/'+this.empleado2.id,this.empleado2).then(res=>{
        this.dialogmodempleado=false
        this.misempleados()
      })
    },
      deleteval(empleado){
      this.$q.dialog({
        title:'Seguro de eliminar?',
        cancel:true,
      }).onOk(()=>{
        this.$q.loading.show()
        this.$axios.delete(process.env.API+'/empleado/'+empleado.id).then(res=>{
          this.$q.loading.hide()
          this.misempleados()
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
  }

}
</script>

<style scoped>

</style>
