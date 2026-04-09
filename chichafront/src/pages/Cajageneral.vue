<template>
  <div class="q-pa-md">
    <q-btn
      label="Agregar o Retirar"
      color="positive"
      @click="alert = true"
      icon="add_circle"
      class="q-mb-xs"
    />
                <q-form @submit.prevent="misdatos">
            <div class="row">
            <div class="col-3  q-pa-xs"><q-input type="date" label="fecha" v-model="fecha1" outlined required/></div>
            <div class="col-3  q-pa-xs"><q-input type="date" label="fecha" v-model="fecha2" outlined required v-if="rango=='RANGO'"/></div>
            <div class="col-3" ><q-toggle v-model="rango" true-value="RANGO" false-value="DIA" :label="rango +' FECHA'" style="width:100%"/></div>
            <div class="col-3  q-pa-xs flex flex-center">
              <q-btn color="info"  label="Consultar" icon="search" type="submit" />

            </div>
            </div>
            </q-form>
     <q-badge class="full-width text-h5 flex flex-center" color="red" >TOTAL EN CAJA GENERAL: {{cajageneral.monto}} Bs.</q-badge>

    <q-dialog v-model="alert">
      <q-card style="max-width: 80%; width: 50%">
        <q-card-section class="bg-green-14 text-white">
          <div class="text-h6"><q-icon name="add_circle" /> Caja General</div>
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
                  :options="['AGREGAR','RETIRAR']"
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
      <th>id</th>
      <th>Fecha</th>
      <th>Monto</th>
      <th>Detalle</th>
      <th>Motivo</th>
      <th>Glosa</th>
      <th>Tipo</th>
      <th>Opcion</th>
      </tr>
    </thead>
    <tbody>
    <tr v-for="r in data " :key="r">
      <td>{{r.id}}</td>
      <td>{{r.fecha}}</td>
      <td>{{r.monto}}</td>
      <td>{{r.detalle}}</td>
      <td>{{r.motivo}}</td>
      <td>{{r.glosa==null?'':r.glosa.nombre}}</td>
      <td>
              <q-badge color="green" v-if="r.tipo=='INGRESO'">{{r.tipo}}</q-badge>
              <q-badge color="red" v-if="r.tipo=='EGRESO'">{{r.tipo}}</q-badge>
              <q-badge color="blue" v-if="r.tipo=='AGREGAR'">{{r.tipo}}</q-badge>
              <q-badge color="purple" v-if="r.tipo=='RETIRAR'">{{r.tipo}}</q-badge>
      </td>
                  <q-btn
              dense
              round
              flat
              color="red"
              @click="deleteRow(r)"
              icon="delete"
              v-if="$store.state.login.user.id==1 && (r.tipo=='AGREGAR' || r.tipo=='RETIRAR')"
            ><q-tooltip>Eliminar</q-tooltip></q-btn>
      <td>
      </td>
    </tr>
    </tbody>
    </table>
    <!--
    <q-table :filter="filter" title="Caja General"
      :rows="data"
      :columns="columns"
      row-key="name"
      :rows-per-page-options="[50,100]">
      <template v-slot:top-right>
        <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>
            <template v-slot:body-cell-tipo="props">
          <q-td key="opcion" :props="props">
              <q-badge color="green" v-if="props.row.tipo=='INGRESO'">{{props.row.tipo}}</q-badge>
              <q-badge color="red" v-if="props.row.tipo=='EGRESO'">{{props.row.tipo}}</q-badge>
              <q-badge color="blue" v-if="props.row.tipo=='AGREGAR'">{{props.row.tipo}}</q-badge>
              <q-badge color="purple" v-if="props.row.tipo=='RETIRAR'">{{props.row.tipo}}</q-badge>
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
              v-if="props.row.tipo=='AGREGAR' || props.row.tipo=='RETIRAR'"
            ></q-btn>
          </q-td>

      </template>
    </q-table>
-->


  </div>
</template>

<script>
import { date } from 'quasar'
import {jsPDF} from "jspdf";
import moment from 'moment'

import $ from "jquery";
export default {
  data() {
    return {
      alert: false,
      filter:'',
        fecha1:date.formatDate(Date.now(),'YYYY-MM-DD'),
        fecha2:date.formatDate( Date.now(),'YYYY-MM-DD'),
      model:'',
      dato:{},
      options: [],
      props: [],
      pageTotal:'',
      rango:'RANGO',
      tot:'',
      cajageneral:0,
      columns: [
        {name: "fecha", align: "left", label: "FECHA ", field: row=>moment(row.fecha).format('DD/MM/YYYY'), sortable: true,},
        {name: "monto", align: "left", label: "MONTO", field: "monto", sortable: true,},
        {name: "detalle", align: "left", label: "DETALLE", field: "detalle", sortable: true,},
        {name: "motivo", align: "left", label: "MOTIVO", field: "motivo", sortable: true,},
        {name: "glosa", align: "left", label: "GLOSA", field: row=>row.glosa==null?'':row.glosa.nombre, sortable: true,},
        {name: "tipo", align: "left", label: "TIPO", field: "tipo", sortable: true,},
        {name: "opcion", align: "left", label: "OPCION", field: "opcion" }

      ],
      data: [],
    };
  },
  created() {
    $('#example').DataTable( )

    this.misdatos();
    this.totalcaja();

  },
  methods: {
      deleteRow(logcaja){
        console.log(logcaja)
      this.$q.dialog({
        title: 'Confirmar',
        message: 'Esta seguro de eliminar',
        cancel: true,
        persistent: false
      }).onOk(() => {
               this.$axios.delete(process.env.API + "/loggeneral/"+logcaja.id).then((res) => {
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
      this.$axios.post(process.env.API + "/totalgeneral").then((res) => {
          this.cajageneral=res.data;
      })
      },

    misdatos() {
      this.$q.loading.show();
      if(this.rango=='DIA'){
        this.fecha2=this.fecha1
      }
      $('#example').DataTable().destroy()
      this.$axios.post(process.env.API + "/listgeneral",{fecha1:this.fecha1,fecha2:this.fecha2}).then((res) => {
      $('#example').DataTable().destroy()
         console.log(res.data)
         res.data.forEach(r => {
            r.fechaord=r.fecha
            r.fecha=moment(r.fecha).format('DD/MM/YYYY')
         });
        this.data = res.data;
        this.totalcaja();
        this.$nextTick(()=>{
          $('#example').DataTable( {
            order:[[0, 'desc']],
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
            },
                      dom: 'Blfrtip',
            // pageLength: 5,
             lengthMenu: [[-1,20, 50, 100], [ "All",20, 50, 100]],
            // buttons: [
            //   'copy', 'csv', 'excel', 'pdf', 'print'
            // ],
            "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api(), data;

              // Remove the formatting to get integer data for summation
              var intVal = function ( i ) {
                return typeof i === 'string' ?
                  i.replace(/[\$,]/g, '')*1 :
                  typeof i === 'number' ?
                    i : 0;
              };

              // Total over all pages
              this.tot = api
                .column( 1 )
                .data()
                .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );
              // console.log(this.tot)
              // Total over this page
              this.pageTotal = api
                .column( 1, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );
              // this.ttotal= api
              //   .column( 3, { page: 'current'} )
              //   .data()
              //   .reduce( function (a, b) {
              //     return intVal(a) + intVal(b);
              //   }, 0 );
              // this.tcuenta= api
              //   .column(4 , { page: 'current'} )
              //   .data()
              //   .reduce( function (a, b) {
              //     return intVal(a) + intVal(b);
              //   }, 0 );
              // this.tsaldo= api
              //   .column(5 , { page: 'current'} )
              //   .data()
              //   .reduce( function (a, b) {
              //     return intVal(a) + intVal(b);
              //   }, 0 );

              // Update footer
              $( api.column( 4 ).footer() ).html(
                '$'+this.pageTotal +' ( $'+ this.tot +' total)'
                // 'total:'+this.ttotal +' a cuenta:'+this.tcuenta +' saldo:'+this.tsaldo +' '
              );
            }
          } );
        this.$q.loading.hide();
      });
      })}
    ,


    onSubmit() {
        if(this.dato.tipo=='RETIRAR' && parseFloat(this.dato.monto) > parseFloat(this.cajageneral.monto))
        {
                    this.$q.notify({
          message:'No debe ser Mayor a lo q esta en caja',
          icon:'close',
          color:'red'
        })
            return false
        }
      this.$q.loading.show();
      // this.dato.unid_id=this.dato.unid_id.id;
      this.$axios.post(process.env.API + "/loggeneral", {
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
      this.dato.tipo = 'AGREGAR';
    },

  },
};
</script>
