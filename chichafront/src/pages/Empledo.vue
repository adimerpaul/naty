
<template>
  <q-page class="q-pa-xs">
  <div class="row">
    <div class="col-12">
      <div class="text-subtitle1 bg-primary text-center text-white">CONTROL EMPLEADOS</div>
    </div>
    <div class="col-12">
      <q-form @submit.prevent="agregar">
        <div class="row">
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined dense label="CI" v-model="empleado.ci" required/></div>
          <div class="col-12 q-pa-xs col-sm-4"><q-input outlined dense label="Nombre" v-model="empleado.nombre" required/></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-select outlined dense v-model="empleado.tipo" :options="['DESTAJO','FIJO']" label="Tipo"  /></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined dense label="Fecha Nacimiento" type="date" v-model="empleado.fechanac" required/></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined dense label="Fecha Ingreso" type="date" v-model="empleado.fechaingreso" required/></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined dense label="Celular" v-model="empleado.celular"/></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined dense label="Salario" v-model="empleado.salario" type="number"/></div>
          <div class="col-12 col-sm-12 flex flex-center">
            <q-btn class="full-width" icon="send" label="CREAR" type="submit" color="positive"/>
          </div>
        </div>
      </q-form>
    </div>
    <div class="col-12">
      <q-table
      :columns="columns"
      :rows="empleados"
      :rows-per-page-options="[0,50,100]"
      :filter="filter"
      wrap-cells
      >
      <template v-slot:top-right>
        <q-input borderless dense outlined   debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>
        <template v-slot:body-cell-tipo="props">
          <q-td :props="props" auto-width>
            <q-badge :color="props.row.tipo=='FIJO'?'green':'teal'">{{props.row.tipo}}</q-badge>
          </q-td>
        </template>
        <template v-slot:body-cell-estado="props">
          <q-td :props="props" auto-width>
            <q-badge :color="props.row.estado=='ACTIVO'?'green':'red'" @click="cambioEstado(props.row)">{{props.row.estado}}</q-badge>
          </q-td>
        </template>
        <template v-slot:body-cell-action="props">
          <q-td :props="props">
                      <q-btn
              color="accent"
              icon-right="payment"
              no-caps
              label="Planilla"
              flat
              dense
              @click="verplanilla(props.row)"
            ><q-tooltip>Listado planilla</q-tooltip></q-btn>
            <q-btn
              color="warning"
              icon-right="edit"
              no-caps
              label="Modificar"
              flat
              dense
              @click="updateval(props.row)"
            />
            <q-btn
              color="negative"
              icon-right="delete"
              no-caps
              label="Eliminar"
              flat
              dense
              @click="deleteval(props.row)"
            ><q-tooltip>Modificar</q-tooltip></q-btn>
            <q-btn
              color="info"
              icon-right="list"
              no-caps
              label="Sueldos"
              flat
              dense
              @click="pagosval(props.row)"
            ><q-tooltip>Lista Adelantos</q-tooltip></q-btn>

            <!--              @click="deleteval(detalles.indexOf(props.row))"-->
          </q-td>
        </template>
      </q-table>
      <div class="row">
        <div class="col-12">
          <div class="text-subtitle1 bg-accent text-center text-white">HISTORIAL DE PAGOS</div>
        </div>
        <div class="col-6 col-sm-4 q-pa-xs"><q-input dense type="date" label="Desde" v-model="fecha1" outlined required/></div>
        <div class="col-6 col-sm-4 q-pa-xs"><q-input dense type="date" label="Hasta" v-model="fecha2" outlined required/></div>
        <div class="col-6 col-sm-4 q-pa-xs flex flex-center">
          <q-btn color="info" class="full-width"  label="Consultar" icon="search" type="submit" @click="missalarios" />
        </div>
        <div class="col-12">
            <div class=" responsive">
                <table id="example" style="width:100%" class="cell-border">
                <thead>
                <tr>
                <th>EMPLEADO</th>
                <th>ADELANTO</th>
                <th>DESCUENTO</th>
                <th>EXTRA</th>
                <th>OPCION</th>
                </tr>
                </thead>
                  <tbody>
                  <tr v-for="v in salarios" :key="v.id">
                  <td>{{v.nombre}}</td>
                  <td>{{v.adelanto}}</td>
                  <td>{{v.descuento}}</td>
                  <td>{{v.extra}}</td>
                  <td>
                    <template v-if="$store.state.login.reimpresion" >
                      <!--<q-btn icon="print" dense color="yellow" @click="imprimirboleta(v)"/>
                      <q-btn icon="money" dense color="green" @click="cancelar(v)" v-if="v.cancelar==undefined ||v.cancelar==0"/>-->
                      <q-btn icon="list" dense color="accent" @click="genplanilla(v)"/>
                    </template>
                  </td>
                  </tr>
                  </tbody>
                </table>
            </div>
  <!--         <q-table
            :columns="columns3"
            :rows="salarios"
            title="Historial de pagos"

          >-->
<!--            :filter="filter"-->

            <!--        <template v-slot:top-right>-->
            <!--          <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">-->
            <!--            <template v-slot:append>-->
            <!--              <q-icon name="search" />-->
            <!--            </template>-->
            <!--          </q-input>-->
            <!--        </template>-->
<!--            <template v-slot:body="props">-->
<!--              <q-tr :props="props">-->
<!--                <q-td key="total" :props="props">-->
<!--                  {{ props.row.total }}-->
<!--                </q-td>-->
<!--                <q-td key="acuenta" :props="props">-->
<!--                  {{ props.row.acuenta }}-->
<!--                </q-td>-->
<!--                <q-td key="saldo" :props="props">-->
<!--                  {{ props.row.saldo }}-->
<!--                </q-td>-->
<!--                <q-td key="estado" :props="props">-->
<!--                  <q-badge :color="props.row.estado=='CANCELADO'?'positive':'negative'">{{ props.row.estado }}</q-badge>-->
<!--                </q-td>-->
<!--                <q-td key="local" :props="props">-->
<!--                  {{ props.row.local }}-->
<!--                </q-td>-->
<!--                <q-td key="titular" :props="props">-->
<!--                  {{ props.row.titular }}-->
<!--                </q-td>-->
<!--                <q-td key="user" :props="props">-->
<!--                  {{ props.row.user }}-->
<!--                </q-td>-->
<!--              </q-tr>-->
<!--            </template>-->
   <!--   <template v-slot:body-cell-opcion="props">
        <q-td :props="props">
          <div>
            <template v-if="$store.state.login.reimpresion" >
              <q-btn icon="print" color="yellow" @click="imprimirboleta(props.row)"/>
            </template>
          </div>
        </q-td>
      </template>
          </q-table>-->
        </div>
       <!-- <div class="col-12">
          <q-btn label="Imprimir" icon="print" color="info" class="full-width" @click="imprimir"/>
        </div>-->
      </div>

    <q-dialog v-model="modempleado" >
      <q-card style="min-width: 350px">
    <div class="col-12">
      <div class="text-subtitle1 bg-primary text-center text-white">Modificar empleados</div>
    </div>
    <div class="col-12">
      <q-form @submit.prevent="modificar">
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined label="CI" v-model="empleado2.ci" required/></div>
          <div class="col-12 q-pa-xs col-sm-3"><q-input outlined label="Nombre" v-model="empleado2.nombre" required/></div>
          <div class="col-12 q-pa-xs col-sm-3"><q-select v-model="empleado2.tipo" :options="['DESTAJO','FIJO']" label="Tipo" outlined /></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined label="Fecha Nacimiento" type="date" v-model="empleado2.fechanac" required/></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined label="Fecha Ingreso" type="date" v-model="empleado2.fechaingreso" /></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined label="Celular" v-model="empleado2.celular"/></div>
          <div class="col-12 q-pa-xs col-sm-2"><q-input outlined label="Salario" v-model="empleado2.salario" type="number"/></div>
          <div class="col-12 q-pa-xs col-sm-2 flex flex-center">
            <q-btn icon="edit" label="MODIFICAR" type="submit" color="warning"/>
          </div>
      </q-form>
    </div>
      </q-card>
    </q-dialog>

      <q-dialog v-model="pagos" full-width>
        <q-card>
          <q-card-section>
            <div class="text-h6">Historial de {{empleado2.nombre}}</div>
          </q-card-section>
          <q-card-actions align="right" class="bg-white text-teal">
            <q-btn flat label="OK" v-close-popup />
          </q-card-actions>
          <q-card-section class="q-pt-none">
            <q-form @submit.prevent="agregarpago">
              <div class="row">
                <div class="col-2">
                  <q-input outlined dense label="Monto" type="number" step="0.01" v-model="pago.monto" required/>
                </div>
                <div class="col-2">
                  <q-select outlined dense label="Tipo" v-model="pago.tipo" :options="['DESCUENTO','ADELANTO','EXTRA']"/>
                </div>
                <div class="col-2">
                  <q-input outlined dense label="Fecha" v-model="pago.fecha" type="date"/>
                </div>
                <div class="col-4">
                  <q-input outlined dense type="text" label="observacion" v-model="pago.observacion" />
                </div>
                <div class="col-3" v-if="$store.state.login.user.id==1">
                  <q-radio v-model="checkgasto" val="CAJA GENERAL" label="CAJA GENERAL" />
                  <q-radio v-model="checkgasto" val="CAJA" label="CAJA CHICA" />
                </div>
                <div class="col-2 flex flex-center">
                  <q-btn  class="full-with" label="Agregar" type="submit" icon="send" color="info"/>
                </div>
              </div>
            </q-form>
            <q-table
            title="Historial de pagos"
            :rows-per-page-options="[0,50,100]"
            :columns="columns2"
            :rows="empleadohistorial"
            v-if="$store.state.login.reportepago"
            >
        <template v-slot:body-cell-opcion="props">
          <q-td :props="props">
            <q-btn  color="negative" icon-right="delete" no-caps flat dense @click="delsueldo(props.row)" v-if="props.row.tipo!='ANULADO'"/>
                <q-btn dense round flat color="teal" @click="printRow(props)" icon="print"  v-if="props.row.tipo!='ANULADO'"></q-btn>

          </q-td>
        </template>
              <template v-slot:body-cell-tipo="props">
                <q-td :props="props">
                  <q-badge :color="props.row.tipo=='DESCUENTO'||props.row.tipo=='ANULADO'?'red':'green'" text-color="white">
                    {{ props.row.tipo }}
                  </q-badge>
                </q-td>
              </template>
              <template v-slot:body-cell-observacion="props">
                <q-td :props="props">
                  <q-input dense outlined v-model="props.row.observacion" type="textarea"/>
                </q-td>
              </template>
            </q-table>
          </q-card-section>

        </q-card>
      </q-dialog>
    </div>

  </div>
    <q-dialog v-model="dialoggenplanilla">
      <q-card>
        <q-card-section class="text-center text-bold q-pa-none q-ma-none">Datos {{emp.nombre}}</q-card-section>
        <q-card-section>

          <q-form @submit.prevent="creategenplanilla">
            <div class="row">
              <div class="col-12"><q-input dense outlined label="fechainicio" v-model="planilla.fechainicio" disable /></div>
              <div class="col-12"><q-input dense outlined label="fechafin" v-model="planilla.fechafin" disable /></div>
              <div class="col-12"><q-input dense outlined label="fechapago" v-model="planilla.fechapago" disable /></div>
              <div class="col-12"><q-input dense outlined label="Salario" v-model="planilla.monto" @update:model-value="actualizamonto"/></div>
              <div class="col-12"><q-input dense outlined label="adelanto" v-model="planilla.adelanto" disable /></div>
              <div class="col-12"><q-input dense outlined label="descuento" v-model="planilla.descuento" disable /></div>
              <div class="col-12"><q-input dense outlined label="extra" v-model="planilla.bono" disable /></div>
              <div class="col-12"><q-input dense outlined label="total" v-model="planilla.total" disable /></div>
              <div class="col-12"><q-input dense outlined label="observacion" v-model="planilla.observacion"  /></div>
              <div class="col-12">
                <q-btn class="full-width	" type="submit" color="primary" icon="add_circle" label="crear planilla" v-if="validarplan"/>
              </div>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

        <q-dialog v-model="dialogver" >
      <q-card style="width: 800px; max-width: 80vw;">
        <q-card-section class="text-center text-bold q-pa-none q-ma-none">Datos {{emp.nombre}}</q-card-section>
        <q-card-section>
            <div class="row">
            <q-table title="PLANILLAS" :rows="planillas" :columns="colplan" row-key="name"  :rows-per-page-options="[0,50,100]">
                    <template v-slot:body-cell-opcion="props">
          <q-td :props="props">
                <q-btn v-if="props.row.estado!='ANULADO'" dense round flat color="info"  icon="print" @click="imprimirplanilla(props.row)"></q-btn>
                <q-btn v-if="props.row.estado!='ANULADO'" color="negative" icon-right="delete" no-caps flat dense @click="delplanilla(props.row)"/>

          </q-td>
        </template>
            </q-table>


            </div>
        </q-card-section>
      </q-card>
    </q-dialog>

</q-page>
</template>

<script>
import $ from "jquery";
import {date} from 'quasar'
import {jsPDF} from "jspdf";
import moment from 'moment'
import planillas from "pages/Planillas";
import { Console } from "console";

const conversor = require('conversor-numero-a-letras-es-ar');
export default {
  name: "Venta",
  data(){
    return{
      dialoggenplanilla:false,
      dialogver:false,
      filter:'',
      pagos:false,
      checkgasto:"GASTO",
      pago:{fecha:date.formatDate( Date.now(),'YYYY-MM-DD')},
      empleados:[],
      emp:{},
      empleados2:[],
      empleadohistorial:{},
      boolcrear:true,
      salarios:[],
      planillas:[],
      planilla:{},
      validarplan:true,
      modempleado:false,
      empleado:{fechanac:'2000-01-01',fechaingreso:date.formatDate( Date.now(),'YYYY-MM-DD')},
      empleado2:{},
      dialog_plan:false,
      fecha1:date.formatDate( Date.now(),'YYYY-MM-DD'),
      fecha2:date.formatDate( Date.now(),'YYYY-MM-DD'),
      montocaja:0,
      montogeneral:0,
      columns:[
        {name:'ci',label:'CI',field:'ci'},
        {name:'nombre',label:'NOMBRE',field:'nombre',sortable:true, align:'left'},
        {name:'fechaingreso',label:'FECHA INGRESO',field:'fechaingreso',sortable:true, align:'left'},
        {name:'tipo',label:'TIPO',field:'tipo'},
        {name:'estado',label:'ESTADO',field:'estado'},
        {name:'action',label:'OPCION',field:'action'},
      ],
      columns2:[
        {name:'opcion',label:'OPCION',field:'opcion'},
        {name:'fecha',label:'FECHA',field:row=>moment(row.fecha).format('DD/MM/YYYY'),sortable: true},
        {name:'hora',label:'HORA',field:'hora'},
        {name:'tipo',label:'TIPO',field:'tipo',sortable: true},
        {name:'monto',label:'MONTO',field:'monto', sortable: true},
        {name:'user',label:'USUARIO',field:row=>row.user.name, sortable: true},
        {name:'observacion',label:'observacion',field:'observacion'},
      ],
      columns3:[
        {name:'nombre',label:'Empleado',field:'nombre'},
        {name:'salario',label:'Sueldo',field:'salario'},
        {name:'adelanto',label:'Adelanto',field:'adelanto'},
        // {name:'detalle',label:'detalle',field:'detalle'},
        {name:'descuento',label:'Descuento',field:'descuento'},
        {name:'extra',label:'Extra',field:'extra'},
        {name:'pago',label:'Pago',field:'pago'},
        {name:'cancelar',label:'Cancelar',field:'cancelar'},
        {name:'opcion',label:'opcion',field:'opcion'},
      ],
            colplan:[
        {name:'opcion',label:'opcion',field:'opcion'},
        {name:'Fecha',label:'Fecha In',field:row=>moment(row.fechainicio).format('DD/MM/YYYY')},
        {name:'Fecha',label:'Fecha Fin',field:row=>moment(row.fechafin).format('DD/MM/YYYY')},
        {name:'Fecha',label:'Fecha pago',field:row=>moment(row.fechapago).format('DD/MM/YYYY')},
        {name:'salario',label:'Sueldo',field:'monto'},
        {name:'adelanto',label:'Adelanto',field:'adelanto'},
        {name:'descuento',label:'Descuento',field:'descuento'},
        {name:'extra',label:'Extra',field:'bono'},
        {name:'pago',label:'Pago',field:'total'},
      ],
    }
  },
  mounted() {
        $('#example').DataTable( {
          "language":{
        "processing": "Procesando...",
        "lengthMenu": "Mostrar _MENU_ registros",
        "zeroRecords": "No se encontraron resultados",
        "emptyTable": "Ningún dato disponible en esta tabla",
        "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        "infoFiltered": "(filtrado de un total de _MAX_ registros)",
        "search": "Buscar:",
        "infoThousands": ",",
        "loadingRecords": "Cargando...",
        "paginate": {
          "first": "Primero",
          "last": "Último",
          "next": "Siguiente",
          "previous": "Anterior"
        },
        "aria": {
          "sortAscending": ": Activar para ordenar la columna de manera ascendente",
          "sortDescending": ": Activar para ordenar la columna de manera descendente"
        },
        "buttons": {
          "copy": "Copiar",
          "colvis": "Visibilidad",
          "collection": "Colección",
          "colvisRestore": "Restaurar visibilidad",
          "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
          "copySuccess": {
            "1": "Copiada 1 fila al portapapeles",
            "_": "Copiadas %ds fila al portapapeles"
          },
          "copyTitle": "Copiar al portapapeles",
          "csv": "CSV",
          "excel": "Excel",
          "pageLength": {
            "-1": "Mostrar todas las filas",
            "_": "Mostrar %d filas"
          },
          "pdf": "PDF",
          "print": "Imprimir",
          "renameState": "Cambiar nombre",
          "updateState": "Actualizar",
          "createState": "Crear Estado",
          "removeAllStates": "Remover Estados",
          "removeState": "Remover",
          "savedStates": "Estados Guardados",
          "stateRestore": "Estado %d"
        },
        "autoFill": {
          "cancel": "Cancelar",
          "fill": "Rellene todas las celdas con <i>%d<\/i>",
          "fillHorizontal": "Rellenar celdas horizontalmente",
          "fillVertical": "Rellenar celdas verticalmentemente"
        },
        "decimal": ",",
        "searchBuilder": {
          "add": "Añadir condición",
          "button": {
            "0": "Constructor de búsqueda",
            "_": "Constructor de búsqueda (%d)"
          },
          "clearAll": "Borrar todo",
          "condition": "Condición",
          "conditions": {
            "date": {
              "after": "Despues",
              "before": "Antes",
              "between": "Entre",
              "empty": "Vacío",
              "equals": "Igual a",
              "notBetween": "No entre",
              "notEmpty": "No Vacio",
              "not": "Diferente de"
            },
            "number": {
              "between": "Entre",
              "empty": "Vacio",
              "equals": "Igual a",
              "gt": "Mayor a",
              "gte": "Mayor o igual a",
              "lt": "Menor que",
              "lte": "Menor o igual que",
              "notBetween": "No entre",
              "notEmpty": "No vacío",
              "not": "Diferente de"
            },
            "string": {
              "contains": "Contiene",
              "empty": "Vacío",
              "endsWith": "Termina en",
              "equals": "Igual a",
              "notEmpty": "No Vacio",
              "startsWith": "Empieza con",
              "not": "Diferente de",
              "notContains": "No Contiene",
              "notStarts": "No empieza con",
              "notEnds": "No termina con"
            },
            "array": {
              "not": "Diferente de",
              "equals": "Igual",
              "empty": "Vacío",
              "contains": "Contiene",
              "notEmpty": "No Vacío",
              "without": "Sin"
            }
          },
          "data": "Data",
          "deleteTitle": "Eliminar regla de filtrado",
          "leftTitle": "Criterios anulados",
          "logicAnd": "Y",
          "logicOr": "O",
          "rightTitle": "Criterios de sangría",
          "title": {
            "0": "Constructor de búsqueda",
            "_": "Constructor de búsqueda (%d)"
          },
          "value": "Valor"
        },
        "searchPanes": {
          "clearMessage": "Borrar todo",
          "collapse": {
            "0": "Paneles de búsqueda",
            "_": "Paneles de búsqueda (%d)"
          },
          "count": "{total}",
          "countFiltered": "{shown} ({total})",
          "emptyPanes": "Sin paneles de búsqueda",
          "loadMessage": "Cargando paneles de búsqueda",
          "title": "Filtros Activos - %d",
          "showMessage": "Mostrar Todo",
          "collapseMessage": "Colapsar Todo"
        },
        "select": {
          "cells": {
            "1": "1 celda seleccionada",
            "_": "%d celdas seleccionadas"
          },
          "columns": {
            "1": "1 columna seleccionada",
            "_": "%d columnas seleccionadas"
          },
          "rows": {
            "1": "1 fila seleccionada",
            "_": "%d filas seleccionadas"
          }
        },
        "thousands": ".",
        "datetime": {
          "previous": "Anterior",
          "next": "Proximo",
          "hours": "Horas",
          "minutes": "Minutos",
          "seconds": "Segundos",
          "unknown": "-",
          "amPm": [
            "AM",
            "PM"
          ],
          "months": {
            "0": "Enero",
            "1": "Febrero",
            "10": "Noviembre",
            "11": "Diciembre",
            "2": "Marzo",
            "3": "Abril",
            "4": "Mayo",
            "5": "Junio",
            "6": "Julio",
            "7": "Agosto",
            "8": "Septiembre",
            "9": "Octubre"
          },
          "weekdays": [
            "Dom",
            "Lun",
            "Mar",
            "Mie",
            "Jue",
            "Vie",
            "Sab"
          ]
        },
        "editor": {
          "close": "Cerrar",
          "create": {
            "button": "Nuevo",
            "title": "Crear Nuevo Registro",
            "submit": "Crear"
          },
          "edit": {
            "button": "Editar",
            "title": "Editar Registro",
            "submit": "Actualizar"
          },
          "remove": {
            "button": "Eliminar",
            "title": "Eliminar Registro",
            "submit": "Eliminar",
            "confirm": {
              "_": "¿Está seguro que desea eliminar %d filas?",
              "1": "¿Está seguro que desea eliminar 1 fila?"
            }
          },
          "error": {
            "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
          },
          "multi": {
            "title": "Múltiples Valores",
            "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
            "restore": "Deshacer Cambios",
            "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
          }
        },
        "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
        "stateRestore": {
          "creationModal": {
            "button": "Crear",
            "name": "Nombre:",
            "order": "Clasificación",
            "paging": "Paginación",
            "search": "Busqueda",
            "select": "Seleccionar",
            "columns": {
              "search": "Búsqueda de Columna",
              "visible": "Visibilidad de Columna"
            },
            "title": "Crear Nuevo Estado",
            "toggleLabel": "Incluir:"
          },
          "emptyError": "El nombre no puede estar vacio",
          "removeConfirm": "¿Seguro que quiere eliminar este %s?",
          "removeError": "Error al eliminar el registro",
          "removeJoiner": "y",
          "removeSubmit": "Eliminar",
          "renameButton": "Cambiar Nombre",
          "renameLabel": "Nuevo nombre para %s",
          "duplicateError": "Ya existe un Estado con este nombre.",
          "emptyStates": "No hay Estados guardados",
          "removeTitle": "Remover Estado",
          "renameTitle": "Cambiar Nombre Estado"
        }
      },dom: 'Blfrtip',
      buttons: [
         'excel', 'pdf'
      ],
               "lengthMenu": [[-1,50,100], ["All",50,100]]
    } );
    this.missalarios()
    this.misempleados()
    this.totalgeneral()
    // console.log(this.$store.state.login)
    // this.$axios.get(process.env.API+'/cliente').then(res=>{
    //   this.clientes=res.data
    //   this.cliente=res.data[0]
    // })
    // this.$axios.get(process.env.API+'/producto').then(res=>{
    //   this.productos=res.data
    //   this.producto=res.data[0]
    // })
    // this.$axios.post(process.env.API+'/misventas',{fecha:this.fecha2}).then(res=>{
    //   this.venta=res.data
    //   // console.log(this.venta)
    //   // this.producto=res.data[0]
    // })
    // this.$axios.post(process.env.API+'/me').then(res=>{
    //   // console.log(res.data)
    //   this.responsable=res.data.name
    // })
    // this.responsable=this.$store.getters["login/user"].name
  },
  methods:{
    cambioEstado(empledo){
      this.$axios.post(process.env.API + "/estadoEmpleado",empledo).then((res) => {
        this.missalarios()
        this.misempleados()
      })

    },
        printRow(props){
      this.$axios.get(process.env.API + "/impade/"+props.row.id).then((res) => {
                let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
      })

    },
    delplanilla(planilla){
      console.log(planilla)
      this.$q.dialog({
        title:'Seguro de eliminar?',
        cancel:true,
      }).onOk(()=>{
        this.$q.loading.show()
      this.$axios.delete(process.env.API+'/planilla/'+planilla.id).then(res=>{
          this.$q.notify({
            message:'Eliminado',
            icon:'info',
            color:'green'
          })
          this.dialogver=false
          this.misempleados()

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
    delsueldo(sueldo){
      this.$q.dialog({
        title: 'Confirmar',
        message: 'Ingrese el motivo por el cual esta eliminando?',
        prompt: {
          model: '',
          type: 'text' // optional
        },
        cancel: true,
        persistent: false
      }).onOk((data) => {
        this.$axios.delete(process.env.API+'/sueldo/'+sueldo.id,{
          params:{
            motivo:data
          }
        }).then(res=>{
          this.$q.notify({
            message:'Anulado',
            icon:'info',
            color:'green'
          })
          this.pagos=false
          this.misempleados()
      })
      }).onOk(() => {
        // console.log('>>>> second OK catcher')
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    genplanilla(plan){
        this.dialoggenplanilla=true
        this.emp=plan
        this.planilla.fechainicio=this.fecha1
        this.planilla.fechafin=this.fecha2
        this.planilla.fechapago=date.formatDate( Date.now(),'YYYY-MM-DD')
        this.planilla.monto=plan.salario==null?0:plan.salario
        this.planilla.adelanto=plan.adelanto==null?0:plan.adelanto
        this.planilla.descuento=plan.descuento==null?0:plan.descuento
        this.planilla.bono=plan.extra==null?0:plan.extra
        this.planilla.total=this.totalmonto
        this.planilla.empleado_id=plan.id
        this.planilla.restante=0
        this.$axios.post(process.env.API+'/valplanilla',this.planilla).then(res=>{
          if(res.data.length>0)
            this.validarplan=false
          else
            this.validarplan=true
        })

    },
    actualizamonto(){
        this.planilla.total=this.totalmonto

    },

    creategenplanilla(){
      this.checkgasto='GASTO'
            if(this.checkgasto=='GASTO'  && this.planilla.total > this.montogeneral){
                      this.$q.notify({
                message:'El monto excede en General ',
                icon:'info',
                color:'red'
              })
        return false
      }

     if(this.checkgasto=='CAJA' && this.planilla.total > this.montocajachica){
                      this.$q.notify({
                message:'El monto excede en Caja Chica ',
                icon:'info',
                color:'red'
              })
        return false
      }
          this.$q.loading.show()
          //console.log(this.planilla)
          //return false
          this.planilla.tpago=this.checkgasto
       this.$axios.post(process.env.API+'/planilla',this.planilla).then(res=>{
        // console.log(res.data)
        this.$q.loading.hide()
        this.$q.notify({
          message:'Planilla Creada',
          icon:'info',
          color:'green'
        })
        this.imprimirplanilla(this.planilla )
        this.dialoggenplanilla=false
        this.planilla={}
          this.misempleados()
          this.totalgeneral()
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
        doc.text(170, 5, 'fecha de proceso: '+date.formatDate( Date.now(),'DD-MM-YYYY'))
        doc.setFontSize(11);
        doc.text('CHICHERIA DOÑA NATI',110, 7, 'center')
        doc.text('BOLETA DE PAGO',110, 12, 'center')
        doc.text('Correspondiente  '+moment(planilla.fechainicio).format('DD/MM/YYYY')+' Al '+moment(planilla.fechafin).format('DD/MM/YYYY'),100, 17,'center')
        doc.text(' Fecha de pago: ' + (planilla.fechapago==null||planilla.fechapago==undefined||planilla.fechapago==''?'Sn':moment(planilla.fechapago).format('DD/MM/YYYY')),170, 17,'left')
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
      doc.text(['CI:','Nombre:'],10,25)
      doc.setFont(undefined,'normal')
      doc.text([this.emp.ci,this.emp.nombre],65,25,'center')
      doc.setFont(undefined,'bold')
      doc.text(['Tipo:'],130,25)
      doc.setFont(undefined,'normal')
      var nacimiento=moment(this.emp.fechanac);
      var hoy=moment();
      var anios=hoy.diff(nacimiento,"years");
      doc.text([this.emp.tipo],170,25,'center')
      doc.line(5,35,210,35)
      doc.setFont(undefined,'bold')
      doc.text('INGRESOS',45,39,'center')
      doc.text('DESCUENTOS',150,39,'center')
      doc.line(5,40,210,40)
      doc.line(110,40,110,70)
      doc.setFont(undefined,'bold')
      doc.text(['Salario:'],15,45)
      doc.setFont(undefined,'normal')
      doc.text([planilla.monto+' Bs'],105,45,'right')
      doc.setFont(undefined,'bold')
      doc.text(['Adelanto:','Descuento:'],115,45)
      doc.setFont(undefined,'normal')
      doc.text([planilla.adelanto+' Bs',planilla.descuento+' Bs'],205,45,'right')
      doc.line(5,65,210,65)
      doc.setFont(undefined,'bold')
      doc.text('Total ganado:',35,69,'center')
      doc.text('Total descuento:',140,69,'center')
      doc.setFont(undefined,'normal')
      doc.text((parseInt(planilla.monto))+' Bs',105,69,'right')
      doc.text((parseInt(planilla.descuento)+parseInt(planilla.adelanto))+' Bs',205,69,'right')
      doc.line(5,70,210,70)
      doc.setFont(undefined,'bold')
      doc.setFontSize(14);
      doc.text('Liquido pagable: '+planilla.total+' Bs',110,75,'center')
      doc.setFontSize(9);
      let ClaseConversor = conversor.conversorNumerosALetras;
      let miConversor = new ClaseConversor();
      let a = miConversor.convertToText(parseInt(planilla.total));
      //let a='aaa'
      // doc.text(1.5, y+4.4, 'SON: ')
      doc.setFont(undefined,'normal')
      doc.text('SON: '+a.toUpperCase()+' Bs',110, 80, 'center')
      doc.setFont(undefined,'bold')
      if(planilla.observacion!='' && planilla.observacion!=null){
      doc.setFont(undefined,'bold')
      doc.text('Obs: ',2, 85, 'left')
      doc.setFont(undefined,'normal')
      doc.text(planilla.observacion.substring(0,95),10, 85, 'left')
      doc.text(planilla.observacion.substring(95,95),10, 90, 'left')
      doc.text(planilla.observacion.substring(190,95),10, 90, 'left')
      doc.setFont(undefined,'bold')
      }
      // // console.log(a)
      doc.text(5, 100, '____________________________                                                                   ______________________________')
      doc.text(10, 105, 'FIRMA EMPLEADO')
      doc.text(150, 105, 'FIRMA RESPONSABLE')
      // // doc.setFont(undefined,'normal')
      // // doc.text(18, y+3.5, sumtotal+ ' Bs')
      //
      // $( '#docpdf' ).attr('src', doc.output('datauristring'));
      window.open(doc.output('bloburl'), '_blank');

    },
    cancelar(boleta){
           let total=0;
      if(boleta.adelanto==null) boleta.adelanto=0;
      if(boleta.descuento==null) boleta.descuento=0;
      if(boleta.extra==null) boleta.extra=0;
      if(boleta.pago==null) boleta.pago=0;
      if(boleta.obs==null) boleta.obs='';
      total = boleta.salario - boleta.adelanto - boleta.descuento + boleta.pago;
      this.$axios.post(process.env.API+'/cancelacion',{monto:total,fecha:this.fecha2,empleado_id:boleta.id,empleado_nombre:boleta.nombre}).then(res=>{
        this.missalarios();
        //console.log(res.data);
                  this.$q.notify({
            message:'Registro correctamente',
            icon:'info',
            color:'positive'
          })
      })

    },
    imprimirboleta(boleta){
      let total=0;
      if(boleta.adelanto==null) boleta.adelanto=0;
      if(boleta.descuento==null) boleta.descuento=0;
      if(boleta.extra==null) boleta.extra=0;
      if(boleta.pago==null) boleta.pago=0;
      if(boleta.obs==null) boleta.obs='';
      total = boleta.salario - boleta.adelanto - boleta.descuento + boleta.pago;
      let cadena="<div><img class='fondo' src='logo.png' style='height:20px'> Chicheria Doña Naty <span>"+moment().format('DD/MM/YYYY')+"</span></div><br>";
      cadena+="<div style='text-align:center'>BOLETA DE PAGO "+date.formatDate( this.fecha1,'YYYY-MM')+"</div><hr>";
      cadena+="<div>Nombre: "+boleta.nombre+"</div><div>Salario Basico: "+boleta.salario+"</div>";
      cadena+="<div>CI: "+boleta.ci+"</div><div>Celular: "+boleta.celular+"</div><hr>";

      cadena+="<table style='width:100%'><thead><tr><th>INGRESOS</th><th>DESCUENTOS</th></tr></thead>";
      cadena+="<tbody><tr><td>Salario Basico:"+boleta.salario+"</td><td>Descuentos: "+boleta.descuento+"</td></tr>";
      cadena+="<tr><td>Extra:"+boleta.extra+"</td><td>Adelanto: "+boleta.adelanto+"</td></tr>";
      cadena+="<tr><td>Pago:"+boleta.pago+"</td><td> </td></tr>";
      cadena+="</tbody></table><hr>";
      cadena+="<div style='text-align:right'>Liquido Pagable: "+total+"</div>";
      cadena+="<div> "+boleta.obs+"</div>";

      cadena+="<br><br><div style='text-align:center; with:100%'>FIRMA</div>";
            let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
            myWindow.document.write(cadena);
            myWindow.document.close();
            myWindow.focus();
            setTimeout(function(){
              myWindow.print();
              myWindow.close();
              // impDetalle(response);
              //    impAniv(response);
            },500);
    },
      imprimir(){

        let mc=this
        function header(){
          var img = new Image()
          img.src = 'logo.png'
          doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
          doc.setFont(undefined,'bold')
          doc.text(5, 1, 'Historial de prestmos Sueldos pagos Adelantos')
          doc.text(5, 1.5,  'DE '+mc.fecha1)
          doc.text(1, 3, 'Empleado')
          doc.text(7, 3, 'Sueldo')
          doc.text(9, 3, 'Extra')
          doc.text(11, 3, 'Adelanto')
          doc.text(13, 3, 'Descuento')
          doc.text(16, 3, 'Pago')
          doc.text(19, 3, 'Cancelar')
          // doc.text(13.5, 3, 'Estado')
          // doc.text(18.5, 3, 'Usuario')
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
        let sumatoria=0
        this.salarios.forEach(r=>{
          // xx+=0.5
          // console.log(r)
          let extra=r.extra==null?0:r.extra;
          let adelanto=r.adelanto==null?0:r.adelanto;
          let descuento=r.descuento==null?0:r.descuento;
          let pago=r.pago==null?0:r.pago;
          let total= r.salario - adelanto - descuento + pago;
          y+=0.5
          doc.text(1, y+3, ''+r.nombre)
          doc.text(7, y+3, ''+r.salario)
          doc.text(9, y+3, ''+extra)
          doc.text(11, y+3, ''+adelanto)
          doc.text(13, y+3, ''+descuento)
          doc.text(16, y+3, ''+pago)
          doc.text(19, y+3, ''+total)
          sumatoria+=total;
          // doc.text(13.5, y+3, r.estado.toString())
          // doc.text(18.5, y+3, r.name.toString())
          if (y+3>25){
            doc.addPage();
            header()
            y=0
          }
        })
        y+=0.5
          doc.text(11, y+3, 'TOTAL')
          doc.text(14, y+3, ''+sumatoria)

        // doc.text(2, y+4, 'Ventas totales: ')
        // doc.text(5, y+4, this.ventat+'Bs')
        // doc.text(7, y+4, 'Por cobrar totales: ')
        // doc.text(11, y+4, this.ventat+'Bs')
        // doc.text(14, y+4, 'Saldo totales: ')
        // doc.text(17, y+4, this.ventat+'Bs')
        // doc.save("Pago"+date.formatDate(Date.now(),'DD-MM-YYYY')+".pdf");
        window.open(doc.output('bloburl'), '_blank');
    },
    missalarios(){
      this.$q.loading.show()
      this.$axios.post(process.env.API+'/missueldos',{fecha1:this.fecha1,fecha2:this.fecha2}).then(res=>{
        // console.log(res.data)
        this.salarios=res.data
        $('#example').DataTable().destroy();
        this.$nextTick(()=>{
          $('#example').DataTable( {
            "language":{
              "processing": "Procesando...",
              "lengthMenu": "Mostrar _MENU_ registros",
              "zeroRecords": "No se encontraron resultados",
              "emptyTable": "Ningún dato disponible en esta tabla",
              "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
              "infoFiltered": "(filtrado de un total de _MAX_ registros)",
              "search": "Buscar:",
              "infoThousands": ",",
              "loadingRecords": "Cargando...",
              "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
              },
              "aria": {
                "sortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sortDescending": ": Activar para ordenar la columna de manera descendente"
              },
              "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad",
                "collection": "Colección",
                "colvisRestore": "Restaurar visibilidad",
                "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
                "copySuccess": {
                  "1": "Copiada 1 fila al portapapeles",
                  "_": "Copiadas %ds fila al portapapeles"
                },
                "copyTitle": "Copiar al portapapeles",
                "csv": "CSV",
                "excel": "Excel",
                "pageLength": {
                  "-1": "Mostrar todas las filas",
                  "_": "Mostrar %d filas"
                },
                "pdf": "PDF",
                "print": "Imprimir",
                "renameState": "Cambiar nombre",
                "updateState": "Actualizar",
                "createState": "Crear Estado",
                "removeAllStates": "Remover Estados",
                "removeState": "Remover",
                "savedStates": "Estados Guardados",
                "stateRestore": "Estado %d"
              },
              "autoFill": {
                "cancel": "Cancelar",
                "fill": "Rellene todas las celdas con <i>%d<\/i>",
                "fillHorizontal": "Rellenar celdas horizontalmente",
                "fillVertical": "Rellenar celdas verticalmentemente"
              },
              "decimal": ",",
              "searchBuilder": {
                "add": "Añadir condición",
                "button": {
                  "0": "Constructor de búsqueda",
                  "_": "Constructor de búsqueda (%d)"
                },
                "clearAll": "Borrar todo",
                "condition": "Condición",
                "conditions": {
                  "date": {
                    "after": "Despues",
                    "before": "Antes",
                    "between": "Entre",
                    "empty": "Vacío",
                    "equals": "Igual a",
                    "notBetween": "No entre",
                    "notEmpty": "No Vacio",
                    "not": "Diferente de"
                  },
                  "number": {
                    "between": "Entre",
                    "empty": "Vacio",
                    "equals": "Igual a",
                    "gt": "Mayor a",
                    "gte": "Mayor o igual a",
                    "lt": "Menor que",
                    "lte": "Menor o igual que",
                    "notBetween": "No entre",
                    "notEmpty": "No vacío",
                    "not": "Diferente de"
                  },
                  "string": {
                    "contains": "Contiene",
                    "empty": "Vacío",
                    "endsWith": "Termina en",
                    "equals": "Igual a",
                    "notEmpty": "No Vacio",
                    "startsWith": "Empieza con",
                    "not": "Diferente de",
                    "notContains": "No Contiene",
                    "notStarts": "No empieza con",
                    "notEnds": "No termina con"
                  },
                  "array": {
                    "not": "Diferente de",
                    "equals": "Igual",
                    "empty": "Vacío",
                    "contains": "Contiene",
                    "notEmpty": "No Vacío",
                    "without": "Sin"
                  }
                },
                "data": "Data",
                "deleteTitle": "Eliminar regla de filtrado",
                "leftTitle": "Criterios anulados",
                "logicAnd": "Y",
                "logicOr": "O",
                "rightTitle": "Criterios de sangría",
                "title": {
                  "0": "Constructor de búsqueda",
                  "_": "Constructor de búsqueda (%d)"
                },
                "value": "Valor"
              },
              "searchPanes": {
                "clearMessage": "Borrar todo",
                "collapse": {
                  "0": "Paneles de búsqueda",
                  "_": "Paneles de búsqueda (%d)"
                },
                "count": "{total}",
                "countFiltered": "{shown} ({total})",
                "emptyPanes": "Sin paneles de búsqueda",
                "loadMessage": "Cargando paneles de búsqueda",
                "title": "Filtros Activos - %d",
                "showMessage": "Mostrar Todo",
                "collapseMessage": "Colapsar Todo"
              },
              "select": {
                "cells": {
                  "1": "1 celda seleccionada",
                  "_": "%d celdas seleccionadas"
                },
                "columns": {
                  "1": "1 columna seleccionada",
                  "_": "%d columnas seleccionadas"
                },
                "rows": {
                  "1": "1 fila seleccionada",
                  "_": "%d filas seleccionadas"
                }
              },
              "thousands": ".",
              "datetime": {
                "previous": "Anterior",
                "next": "Proximo",
                "hours": "Horas",
                "minutes": "Minutos",
                "seconds": "Segundos",
                "unknown": "-",
                "amPm": [
                  "AM",
                  "PM"
                ],
                "months": {
                  "0": "Enero",
                  "1": "Febrero",
                  "10": "Noviembre",
                  "11": "Diciembre",
                  "2": "Marzo",
                  "3": "Abril",
                  "4": "Mayo",
                  "5": "Junio",
                  "6": "Julio",
                  "7": "Agosto",
                  "8": "Septiembre",
                  "9": "Octubre"
                },
                "weekdays": [
                  "Dom",
                  "Lun",
                  "Mar",
                  "Mie",
                  "Jue",
                  "Vie",
                  "Sab"
                ]
              },
              "editor": {
                "close": "Cerrar",
                "create": {
                  "button": "Nuevo",
                  "title": "Crear Nuevo Registro",
                  "submit": "Crear"
                },
                "edit": {
                  "button": "Editar",
                  "title": "Editar Registro",
                  "submit": "Actualizar"
                },
                "remove": {
                  "button": "Eliminar",
                  "title": "Eliminar Registro",
                  "submit": "Eliminar",
                  "confirm": {
                    "_": "¿Está seguro que desea eliminar %d filas?",
                    "1": "¿Está seguro que desea eliminar 1 fila?"
                  }
                },
                "error": {
                  "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
                },
                "multi": {
                  "title": "Múltiples Valores",
                  "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
                  "restore": "Deshacer Cambios",
                  "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
                }
              },
              "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
              "stateRestore": {
                "creationModal": {
                  "button": "Crear",
                  "name": "Nombre:",
                  "order": "Clasificación",
                  "paging": "Paginación",
                  "search": "Busqueda",
                  "select": "Seleccionar",
                  "columns": {
                    "search": "Búsqueda de Columna",
                    "visible": "Visibilidad de Columna"
                  },
                  "title": "Crear Nuevo Estado",
                  "toggleLabel": "Incluir:"
                },
                "emptyError": "El nombre no puede estar vacio",
                "removeConfirm": "¿Seguro que quiere eliminar este %s?",
                "removeError": "Error al eliminar el registro",
                "removeJoiner": "y",
                "removeSubmit": "Eliminar",
                "renameButton": "Cambiar Nombre",
                "renameLabel": "Nuevo nombre para %s",
                "duplicateError": "Ya existe un Estado con este nombre.",
                "emptyStates": "No hay Estados guardados",
                "removeTitle": "Remover Estado",
                "renameTitle": "Cambiar Nombre Estado"
              }
            },dom: 'Blfrtip',"ordering": false,
            buttons: [
               'excel', 'pdf'
            ],
            "lengthMenu": [[-1,50,100], ["All",50,100]]
          } );
        })
        this.$q.loading.hide()

      }).catch(err=>{
        this.$q.loading.hide()
        this.$q.notify({
          color:'red',
          icon:'error',
          message:err.response.data.message
        })
      })
    },
          totalcaja(){
      this.$axios.post(process.env.API + "/totalcaja").then((res) => {
          this.montocajachica=res.data.monto;
      })
      },
                        totalgeneral(){
      this.$axios.post(process.env.API + "/totalgeneral").then((res) => {
        console.log(res.data.monto)

          this.montogeneral=parseFloat( res.data.monto);
      })
      },
    agregarpago(){

      //console.log(this.empleadohistorial)
      //console.log(this.empleado2.salario)
      //console.log(this.totaldescuento)
      //return false
      if(this.empleado2.tipo=='FIJO'){
        if((parseFloat(this.empleado2.salario)- this.totaldescuento) < parseFloat( this.pago.monto) && (this.pago.tipo=='ADELANTO' || this.pago.tipo=='DESCUENTO'))
        {
            this.$q.notify({
              message:'El monto excede al salario ',
              icon:'info',
              color:'red'
            })
          return false;
        }
      }
      if(this.checkgasto=='GASTO' && (this.pago.tipo=='ADELANTO' || this.pago.tipo=='EXTRA') && this.pago.monto > this.montogeneral){
        //console.log(this.montogeneral)
                      this.$q.notify({
                message:'El monto excede en General ',
                icon:'info',
                color:'red'
              })
        return false
      }

     if(this.checkgasto=='CAJA' && (this.pago.tipo=='ADELANTO' || this.pago.tipo=='EXTRA') && this.pago.monto > this.montocajachica){
                      this.$q.notify({
                message:'El monto excede en Caja Chica ',
                icon:'info',
                color:'red'
              })
        return false
      }
      //console.log(this.empleadohistorial);
      this.pago.empleado_id=this.empleado2.id
      this.pago.empleado_nombre=this.empleado2.nombre
      this.pago.checkbox=this.checkgasto

      // console.log(this.pago)
      // return false

      this.$axios.post(process.env.API+'/sueldo',this.pago).then(res=>{
        //this.empleadohistorial=res.data

        this.pago={fecha:date.formatDate( Date.now(),'YYYY-MM-DD')}
        this.totalgeneral()
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
        this.pagos=false
      })
    },
    misempleados(){
      this.$q.loading.show()
      this.$axios.get(process.env.API+'/empleado').then(res=>{
        // this.ventas=res.data
        // console.log(res.data)
        this.$q.loading.hide()
        this.empleados=res.data
        // res.data.forEach(r=>{
        //   this.ventas.push({
        //     total:r.total,
        //     acuenta:r.acuenta,
        //     saldo:r.saldo,
        //     estado:r.estado,
        //     cliente:r.cliente.local,
        //     user:r.user.name,
        //   })
        // })
      })
    },
    valplanilla(){

    },
    modificar(){
              this.$axios.put(process.env.API+'/empleado/'+this.empleado2.id,this.empleado2).then(res=>{
          this.misempleados()
          this.boolcrear=true
          this.empleado2=[]
          this.modempleado=false;
          this.$q.notify({
            message:'Modificado correctamente',
            icon:'info',
            color:'positive'
          })
        })
    },
    agregar(){
      this.$q.loading.show()
        this.$axios.post(process.env.API+'/empleado',this.empleado).then(res=>{
          // this.ventas=res.data
          // console.log(res.data)
          this.$q.loading.hide()
          // this.empleados=res.data
          this.misempleados()
          this.empleado={fechanac: '2000-01-01'}
          this.$q.notify({
            message:'Creado correctamente',
            icon:'info',
            color:'positive'
          })
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
      // this.detalles.splice(index, 1);
      // console.log(index)
      this.$q.dialog({
        title:'Seguro de eliminar?',
        cancel:true,
      }).onOk(()=>{
        this.$q.loading.show()
        this.$axios.delete(process.env.API+'/empleado/'+index.id).then(res=>{
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
    verplanilla(empleado){
        this.emp=empleado
        this.planillas=this.emp.planillas
        this.dialogver=true
    },
    updateval(index){
      this.empleado2=index
      this.modempleado=true
      // this.detalles.splice(index, 1);
      // console.log(index)
      // this.$q.loading.show()
      // this.$axios.delete(process.env.API+'/empleado/'+index.id).then(res=>{
      //   this.$q.loading.hide()
      //   this.misempleados()
      // }).catch(err=>{
      //   // console.log(err.response)
      //   this.$q.loading.hide()
      //   this.$q.notify({
      //     message:err.response.data.message,
      //     icon:'error',
      //     color:'red'
      //   })
      // })
    },
    pagosval(index){
      // console.log('a')
      this.$q.loading.show()
      this.empleado2=index
      this.$axios.get(process.env.API+'/empleado/'+index.id).then(res=>{
         console.log(res.data);
        this.empleadohistorial=res.data
        this.$q.loading.hide()
        this.pagos=true
      })
    }
  },
  computed:{
     totalmonto(){
       let tot=0
       tot=parseFloat(this.planilla.monto) - parseFloat(this.planilla.adelanto==null?0:this.planilla.adelanto) - parseFloat(this.planilla.descuento==null?0:this.planilla.descuento)
       return tot
     },
      totaldescuento(){
        let total=0;
      this.empleadohistorial.forEach(element => {
        if (moment(element.fecha).format('YYYY-MM') === moment(this.pago.fecha).format('YYYY-MM'))
        if(element.tipo=='ADELANTO' || element.tipo=='DESCUENTO')
          //console.log(element)
          total+=parseFloat(element.monto)
      });
      return total;
      }
  //   subtotal(){
  //     if (this.producto.precio!=undefined && this.producto.precio!=NaN){
  //       return parseFloat(this.producto.precio)* parseFloat(this.cantidad)
  //     }
  //     else{
  //       return 0
  //     }
  //   },
  //   total(){
  //     let total=0;
  //     this.detalles.forEach(r=>{
  //       total+= parseFloat(r.subtotal)
  //     })
  //     return total
  //   },
  //   estado(){
  //     if (this.acuenta<this.total)
  //       return 'POR COBRAR'
  //     else
  //       return 'CANCELADO'
  //   },
  //   saldo(){
  //     return this.total-this.acuenta
  //   }
   }
}
</script>

<style scoped>

</style>
