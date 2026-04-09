<template>
    <div class="row">
      <div class="col-12">
            <div class="text-h6">
              <h5 style="text-align:center">Reporte de Ventas por Usuario</h5>
            </div>

                <q-form
                  @submit="generar"
                  >
                <div class="row">
                    <div class="col-3">
                        <q-select dense v-model="usuario" outlined :options="usuarios" option-label="name" option-value="id" label="Usuarios" required/>
                    </div>
                <div class="col-2">
                  <q-input
                  dense
                    outlined
                    type="date"
                    v-model="fecha"
                    label="Fecha ini"
                    required
                  />
                  </div>
                  <div class="col-2">
                  <q-input
                  dense
                    outlined
                    type="date"
                    v-model="fecha2"
                    label="Fecha fin"
                    required
                    :rules="[ val => val>=fecha || 'Fecha debe ser mayor o igual']"
                    v-if="rango=='RANGO'"
                  />
                  </div>
                  <div class="col-3" ><q-toggle v-model="rango" true-value="RANGO" false-value="DIA" :label="rango +' FECHA'" style="width:100%"/></div>

                  <div class="col-2 flex flex-center">
                    <q-btn label="Generar" type="submit" color="primary" icon="send" />
                  </div>
                  </div>

                </q-form>


        <div class="q-pa-md">
         <!-- <q-table
            title="Reporte Ventas"
            :rows="ventas"
            :columns="columns"
            row-key="local">

          </q-table>
-->
          <div class=" responsive">
                <table id="example" style="width:100%" class="cell-border">
                  <thead>
                  <tr>
                    <th>Local</th>
                    <th>Titular</th>
                    <th>Total</th>
                    <th>A cuenta</th>
                    <th>Saldo</th>
                    <th>Tipo cliente</th>
                    <th>Estado</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="v in ventas" :key="v.id">
                    <td>{{v.local}}</td>
                    <td>{{v.titular}}</td>
                    <td>{{v.total}}</td>
                    <td>{{v.acuenta}}</td>
                    <td>{{v.saldo}}</td>
                    <td>
                      <q-badge color="accent" v-if="v.tipocliente==1">LOCAL</q-badge>
                      <q-badge color="teal" v-else>CLIENTE</q-badge>
                    </td>
                    <td>
                      <q-badge :color="v.estado=='CANCELADO'?'positive':'negative'">{{ v.estado }}</q-badge>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>


                <q-dialog v-model="alert">
                <q-card>
                    <q-card-section>
                    <div class="text-h6">Pagos Realizados</div>
                    </q-card-section>

                    <q-card-section class="q-pt-none">
                    <q-table title="Pagos" :rows="pagos" :columns="colrep" row-key="name" />

                    </q-card-section>

                    <q-card-actions align="right">
                    <q-btn flat label="OK" color="primary" v-close-popup />
                    </q-card-actions>
                </q-card>
                </q-dialog>

                <q-dialog v-model="dialog_pago">
                <q-card>
                    <q-card-section>
                    <div class="text-h6">Registrar Pago</div>
                    </q-card-section>

                    <q-card-section class="q-pt-none">
                    <q-form @submit.prevent="onPago" class="q-gutter-md" >

                    <q-input filled v-model="regpago.monto"
                    label="Monto"
                    hint="Efectivo"
                    type="number"
                    lazy-rules
                    :rules="[ val => val && val > 0 && val <=regpago.saldo || 'Please type something']"
                    />
                    <q-input filled type="text"
                    v-model="regpago.observacion"
                    label="Observacion"
                    />
                    <div>
                    <q-btn label="Registrar" type="submit" color="green"/>
                    <q-btn flat label="Cancelar" color="primary" v-close-popup />
                    </div>
                    </q-form>

                    </q-card-section>

                </q-card>
                </q-dialog>

        </div>

            <div class="col-12">
              <q-form>
                <div class="row">
                  <div class="col-4 q-pa-md">
                    <q-input type="text" label="Venta total" label-color="positive"  v-model="ventat"  outlined/>
                  </div>
                  <div class="col-4 q-pa-md">
                    <q-input type="text" label="Saldo caja" label-color="info"  v-model="porc"  outlined/>
                  </div>
                  <div class="col-4 q-pa-md">
                    <q-input type="text" label="Por cobrar" label-color="negative"  v-model="saldoc"  outlined/>
                  </div>
                </div>
              </q-form>
            </div>

         <div class="q-pa-md" hidden>
          <q-table
            title="Reporte Deudores"
            :rows="deudas"
            :columns="columns2"
            row-key="local"
            :filter="filter"
          >
            <template v-slot:body-cell-opcion="props">
              <q-td :props="props">
                              <q-btn icon="segment" color="green"  @click="listpago(props.row)" />
                              <q-btn icon="monetization_on" color="amber" v-if="props.row.estado=='POR COBRAR'" @click="pago(props.row)"/>
              </q-td>
            </template>
            <template v-slot:top-right>
              <q-input borderless dense debounce="300" v-model="filter" placeholder="Buscar">
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
            </template>
          </q-table>
        </div>

      </div>
    </div>

</template>
<script>
import {date} from 'quasar'
import { jsPDF } from "jspdf";
import $ from "jquery";

export default {
  data(){
    return{
      fecha:date.formatDate(new Date(),'YYYY-MM-DD'),
      fecha2:date.formatDate(new Date(),'YYYY-MM-DD'),
      usuarios:[],
      filter:'',
      usuario:'',
      ventas:[],
      rango:'RANGO',
      deudas:[],
      regpago:{},
      pagos:{},
      dato:{},
      alert:false,
      dialog_pago:false,
            colrep:[
        {name:'fecha',label:'Fecha',field:'fecha'},
        {name:'monto',label:'Monto',field:'monto'},
        {name:'Observacion',label:'Observacion',field:'observacion'},
      ],
      columns : [
  {
    name: 'local',
    label: 'local',
    align: 'center',
    field: 'local',
    sortable: true
  },
  { name: 'titular', align: 'center', label: 'titular', field: 'titular', sortable: true },
  { name: 'total', align: 'center', label: 'total', field: 'total'},
  { name: 'cuenta', align: 'center', label: 'cuenta', field: 'cuenta' },
  { name: 'saldo', align: 'center', label: 'saldo', field: 'saldo' },
  { name: 'estado', align: 'center', label: 'Estado', field: 'estado' },
  { name: 'tipo', align: 'center', label: 'Tipo', field: 'tipo' },
  { name: 'usuario', align: 'center', label: 'usuario', field: 'name' }
],
      columns2 : [
  {
    name: 'local',
    label: 'Local',
    align: 'center',
    field: 'local',
    sortable: true
  },
  { name: 'titular', align: 'center', label: 'Titular', field: 'titular', sortable: true },
  { name: 'total', align: 'center', label: 'Total', field: 'total'},
  { name: 'cuenta', align: 'center', label: 'Cuenta', field: 'cuenta' },
  { name: 'saldo', align: 'center', label: 'Saldo', field: 'saldo' },
  { name: 'fecha', align: 'center', label: 'fecha', field: 'fecha' },
  { name: 'estado', align: 'center', label: 'Estado', field: 'estado' },
  { name: 'tipo', align: 'center', label: 'tipo', field: 'tipocliente' },
  { name: 'usuario', align: 'center', label: 'usuario', field: 'name' },
  { name: 'opcion', align: 'center', label: 'opcion' ,field:'opcion'}
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
      "lengthMenu": [[-1,10, 25, 50], ["All",10, 25, 50]]
    } );
  },
  created() {
      this.listado();
      this.deudores();
  },
  methods: {
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
      this.$axios.post(process.env.API+'/listuser').then(res=>{
        console.log(res.data)
        this.usuarios.push({id:'0',name:'Todos'})
        res.data.forEach(r=>{
          this.usuarios.push(r)
        })
        if (this.usuarios.length>0)
        this.usuario=res.data[0];
      })
    },

    deudores(){
      this.deudas=[];
      this.$axios.post(process.env.API+'/listadodeudores').then(res=>{
        console.log(res.data)
        res.data.forEach(elem => {
          let tip=''
          if(elem.cliente.tipocliente==1) tip='LOCAL'; else tip='DETALLE';
          this.deudas.push({
            // id:elem.id,
            id:elem.id,
            local:elem.cliente.local,
            titular:elem.cliente.titular,
            total:elem.total,
            cuenta:elem.acuenta,
            saldo:elem.saldo,
            tipocliente:tip,
            fecha:elem.fecha,
            estado:elem.estado,
            name:elem.user.name,
            pagos:elem.pagos
          });

        });
      })
    },

    generar(){
            this.ventas=[];
            this.$q.loading.show()
            // console.log(this.usuario)
            // $('#example').DataTable().destroy();
            if(this.rango=='DIA'){
        this.fecha2=this.fecha
      }
            this.$axios.post(process.env.API+'/listadoventa',{ini:this.fecha,fin:this.fecha2,id:this.usuario.id}).then(res=>{
                console.log(res.data);
              this.$q.loading.hide()
                res.data.forEach(el => {
                    this.ventas.push({
                        local:el.cliente.local,
                        tipo:el.tipo,
                        titular:el.cliente.titular,
                        cuenta:el.acuenta,
                        saldo:el.saldo,
                        total:el.total,
                        estado:el.estado,
                        name:el.user.name
                    })
                });
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
              },dom: 'Blfrtip',
              buttons: [
                 'excel', 'pdf'
              ],
      "lengthMenu": [[-1,10, 25, 50], ["All",10, 25, 50]]
              
            } );
        })

            })
    },
          pago(props){
          this.regpago.venta_id=props.id;
          this.regpago.saldo=props.saldo;
        this.dialog_pago=true;
      },
    listpago(dato){
        this.pagos=dato.pagos;
        this.alert=true;
    },

  },
computed:{
    ventat(){
      let total=0;
      this.ventas.forEach(r=>{
        total+= parseFloat(r.total)
      })
      return total
    },
    porc(){
      let total=0;
      this.ventas.forEach(r=>{
        total+= parseFloat(r.cuenta)
      })
      return total
    },
    saldoc(){
      let total=0;
      this.ventas.forEach(r=>{
        total+= parseFloat(r.saldo)
      })
      return total
    },
}

}
</script>
