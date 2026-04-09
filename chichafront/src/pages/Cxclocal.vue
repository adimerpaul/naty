<template>
  <q-page class="q-pa-xs">
    <div class="col-12">
      <div class="text-subtitle1 bg-blue-9 text-center text-white">HISTORIAL CUENTAS POR COBRAR LOCAL</div>

      <div class="col-12 col-sm-12">
        <div class="col-12">
          <!--<div class="text-subtitle1 bg-info text-center text-white">Historial de ventas Local</div>-->
        </div>
        <q-form @submit.prevent="misventas">
          <div class="row q-pa-xs">
            <div class="col-2">
              <q-input type="date" label="fecha" v-model="fecha2" outlined required dense/>
            </div>
            <div class="col-2">
              <q-input type="date" label="fecha" v-model="fecha3" outlined required dense v-if="rango=='RANGO'"/>
            </div>
            <div class="col-2">
              <q-toggle v-model="rango" true-value="RANGO" false-value="DIA" :label="rango +' FECHA'" dense/>
            </div>
            <div class="col-2">
              <q-btn color="info" label="Consultar" icon="search" type="submit" dense no-caps/>
            </div>
          </div>
        </q-form>
        <div class="col-12">
          <q-btn color="red" icon="picture_as_pdf" label="Exportar PDF" @click="exportPdf()" class="q-mb-md" dense no-caps/>
            <q-btn color="green" icon="description" label="Exportar Excel" @click="exportExcel()" class="q-mb-md" dense no-caps/>
          <q-table
            :rows="ventas"
            :columns="columns2"
            :filter="filter"
            row-key="name"
        >
        <template v-slot:top-right>
        <q-select v-model="cliente" :options="clientes"  label="Cliente" outlined dense @update:model-value="filtrarList"/>
          <q-select v-model="filtEstado" :options="['TODOS','POR COBRAR','CANCELADO','ANULADO']" label="Estado" outlined dense @update:model-value="filtrarList"/>
        <q-input borderless dense debounce="300" v-model="filter" placeholder="Search">
          <template v-slot:append>
            <q-icon name="search" />
          </template>
        </q-input>
      </template>

      <template v-slot:body-cell-tipocliente="props">
          <q-td :props="props" auto-width>
            <q-badge color="accent" v-if="props.row.tipocliente=='1'">LOCAL</q-badge>
            <q-badge color="teal" v-else>CLIENTE</q-badge>
          </q-td>
        </template>
        <template v-slot:body-cell-saldo="props">
          <q-td :props="props" auto-width>
            <span style="font-weight:bold; font-size:20px">{{ props.row.saldo }}</span>
          </q-td>
        </template>
        <template v-slot:body-cell-estado="props">
          <q-td :props="props" auto-width>
            <q-badge :color="props.row.estado=='CANCELADO'?'positive':'negative'">{{ props.row.estado }}</q-badge>
          </q-td>
        </template>

        <template v-slot:body-cell-opcion="props">
          <q-td :props="props" auto-width>
            <q-btn icon="segment" color="green" @click="listpago(props.row)" dense>
                    <q-tooltip>Listado Pago</q-tooltip>
                  </q-btn>
                  <q-btn icon="monetization_on" color="amber" v-if="props.row.estado=='POR COBRAR'" @click="pago(props.row)" dense>
                    <q-tooltip>Realizar Pago</q-tooltip>
                  </q-btn>
          </q-td>
        </template>
        </q-table>
        <!--  <div class=" responsive">

            <q-markup-table id="example" style="width:100%" class="cell-border" dense flat bordered>
              <thead>
              <tr>
                <th class="hidden">FechaOrd</th>
                <th>Id</th>
                <th>Fecha</th>
                <th>Local</th>
                <th>Titular</th>
                <th>Total</th>
                <th>A cuenta</th>
                <th>Saldo</th>
                <th>Tipo cliente</th>
                <th>Estado</th>
                <th>Usuario</th>
                <th>Opcion</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="v in ventas" :key="v.i">
                <td class="hidden">{{ v.fechaord }}</td>
                <td>{{ v.id }}</td>
                <td>{{ v.fecha }}</td>
                <td>{{ v.local }}</td>
                <td>{{ v.titular }}</td>
                <td>{{ v.total }}</td>
                <td>{{ v.acuenta }}</td>
                <td style="font-weight:bold; font-size:20px">{{ v.saldo }}</td>
                <td>
                  <q-badge color="accent" v-if="v.tipocliente==1">LOCAL</q-badge>
                  <q-badge color="teal" v-else>CLIENTE</q-badge>
                </td>
                <td>
                  <q-badge :color="v.estado=='CANCELADO'?'positive':'negative'">{{ v.estado }}</q-badge>
                </td>
                <td>{{ v.user }}</td>
                <td>
                  <q-btn icon="segment" color="green" @click="listpago(v)" dense>
                    <q-tooltip>Listado Pago</q-tooltip>
                  </q-btn>
                  <q-btn icon="monetization_on" color="amber" v-if="v.estado=='POR COBRAR'" @click="pago(v)" dense>
                    <q-tooltip>Realizar Pago</q-tooltip>
                  </q-btn>
                </td>
              </tr>
              </tbody>

            </q-markup-table>
          </div>
        -->
          <q-dialog v-model="alert">
            <q-card>
              <q-card-section>
                <div class="text-h6">Pagos Realizados</div>
              </q-card-section>

              <q-card-section class="q-pt-none">
                <q-table title="Pagos" :rows="pagos" :columns="columns" row-key="name">
                  <template v-slot:body-cell-op="props" v-if="$store.state.login.delpago">
                    <q-td key="op" :props="props">
                      <q-btn color="red" icon="delete" dense @click="delPago(props.row)"/>
                    </q-td>
                  </template>
                </q-table>
              </q-card-section>

              <q-card-actions align="right">
                <q-btn flat label="OK" color="primary" v-close-popup/>
              </q-card-actions>
            </q-card>
          </q-dialog>

          <q-dialog v-model="dialog_pago">
            <q-card>
              <q-card-section>
                <div class="text-h6">Registrar Pago</div>
              </q-card-section>

              <q-card-section class="q-pt-none">
                <q-form @submit.prevent="onPago" class="q-gutter-md">
                  <q-input outlined v-model="regpago.fecha" type="date" label="Fecha" lazy-rules
                           :rules="[ val => val && val.length>0  || 'Ingrese la fecha']"/>
                  <q-input filled v-model="regpago.monto"
                           label="Monto"
                           hint="Efectivo"
                           type="number"
                           step="0.1"
                           lazy-rules
                           :rules="[ val => val && val > 0 && val <=regpago.saldo || 'no exceder el monto']"
                  />
                  <q-input filled type="text"
                           v-model="regpago.observacion"
                           label="Observacion"
                  />
                  <div>
                    <q-btn label="Registrar" type="submit" color="green" :loading="loading"/>
                    <q-btn flat label="Cancelar" color="primary" v-close-popup/>
                  </div>
                </q-form>

              </q-card-section>

            </q-card>
          </q-dialog>
          <!--

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
-->
        </div>

      </div>

    </div>
  </q-page>
</template>

<script>
import $ from "jquery";

import {date} from 'quasar'
import {jsPDF} from "jspdf";
import moment from 'moment'
import {Excel} from "src/addons/Excel";

export default {
  name: "Venta",
  data() {
    return {
      filtEstado:'TODOS',
      loading: false,
      filter:'',
      fecha: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fecha2: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fecha3: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fecha4: date.formatDate(new Date(), 'YYYY-MM-DD'),
      fecha5: date.formatDate(new Date(), 'YYYY-MM-DD'),
      alert: false,
      responsable: '',
      rango: 'RANGO',
      clientes: [{ label: 'TODOS', id: 0 }],
      cliente: {label: 'TODOS', id: 0},
      productos: [],
      inventarios: [],
      inventario: {},
      producto: '',
      cantidad: 1,
      cantidades: [],
      acuenta: '',
      detalles: [],
      garantia: {},
      dialog_garantia: false,
      dialog_pago: false,
      cantidadprestamo: 0,
      pagos: [],
      regpago: {fecha: date.formatDate(new Date(), 'YYYY-MM-DD')},
      prestamo: {},
      columns: [
        {name: 'op', label: 'OP', field: 'op'},
        {name: 'fecha', label: 'Fecha', field: row => moment(row.fecha).format('DD/MM/YYYY')+' ' + moment(row.created_at).format('HH:mm')},
        {name: 'monto', label: 'Monto', field: 'monto'},
        {name: 'Observacion', label: 'Observacion', field: 'observacion'},
      ],
      columns2: [
        {name: 'fecha', label: 'Fecha', field: 'fecha'},
        {name: 'local', label: 'Local', field: 'local'},
        {name: 'titular', label: 'Titular', field: 'titular'},
        {name: 'total', label: 'Total', field: 'total'},
        {name: 'acuenta', label: 'A cuenta', field: 'acuenta'},
        {name: 'saldo', label: 'Saldo', field: 'saldo'},
        {name: 'tipocliente', label: 'Tipo cliente', field: 'tipocliente'},
        {name: 'estado', label: 'Estado', field: 'estado'},
        {name: 'user', label: 'Usuario', field: 'user'},
        {name: 'opcion', label: 'Opcion', field: 'opcion'},
      ],
      columns3: [
        {name: 'fecha', label: 'fecha', field: 'fecha'},
        {name: 'nombre', label: 'Material', field: 'nombre'},
        {name: 'efectivo', label: 'efectivo', field: 'efectivo'},
        {name: 'fisico', label: 'fisico', field: 'fisico'},
        {name: 'observacion', label: 'observacion', field: 'observacion'},
        {name: 'estado', label: 'Estado', field: 'estado'},
        {name: 'user', label: 'Usuario', field: 'name'},
        {name: 'opcion', label: 'opcion', field: 'opcion'},
      ],
      ventas: [],
      prestamos: [],
      options: [],
      model: '',
      tot: '',
      pageTotal: '',
      ttotal: '',
      tacuenta: '',
      tsaldo: '',
    }
  },
  mounted() {

    // $('#example').DataTable({
    //   "language": {
    //     "processing": "Procesando...",
    //     "lengthMenu": "Mostrar _MENU_ registros",
    //     "zeroRecords": "No se encontraron resultados",
    //     "emptyTable": "Ningún dato disponible en esta tabla",
    //     "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    //     "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    //     "search": "Buscar:",
    //     "infoThousands": ",",
    //     "loadingRecords": "Cargando...",
    //     "paginate": {
    //       "first": "Primero",
    //       "last": "Último",
    //       "next": "Siguiente",
    //       "previous": "Anterior"
    //     },
    //     "aria": {
    //       "sortAscending": ": Activar para ordenar la columna de manera ascendente",
    //       "sortDescending": ": Activar para ordenar la columna de manera descendente"
    //     },
    //     "buttons": {
    //       "copy": "Copiar",
    //       "colvis": "Visibilidad",
    //       "collection": "Colección",
    //       "colvisRestore": "Restaurar visibilidad",
    //       "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
    //       "copySuccess": {
    //         "1": "Copiada 1 fila al portapapeles",
    //         "_": "Copiadas %ds fila al portapapeles"
    //       },
    //       "copyTitle": "Copiar al portapapeles",
    //       "csv": "CSV",
    //       "excel": "Excel",
    //       "pageLength": {
    //         "-1": "Mostrar todas las filas",
    //         "_": "Mostrar %d filas"
    //       },
    //       "pdf": "PDF",
    //       "print": "Imprimir",
    //       "renameState": "Cambiar nombre",
    //       "updateState": "Actualizar",
    //       "createState": "Crear Estado",
    //       "removeAllStates": "Remover Estados",
    //       "removeState": "Remover",
    //       "savedStates": "Estados Guardados",
    //       "stateRestore": "Estado %d"
    //     },
    //     "autoFill": {
    //       "cancel": "Cancelar",
    //       "fill": "Rellene todas las celdas con <i>%d<\/i>",
    //       "fillHorizontal": "Rellenar celdas horizontalmente",
    //       "fillVertical": "Rellenar celdas verticalmentemente"
    //     },
    //     "decimal": ",",
    //     "searchBuilder": {
    //       "add": "Añadir condición",
    //       "button": {
    //         "0": "Constructor de búsqueda",
    //         "_": "Constructor de búsqueda (%d)"
    //       },
    //       "clearAll": "Borrar todo",
    //       "condition": "Condición",
    //       "conditions": {
    //         "date": {
    //           "after": "Despues",
    //           "before": "Antes",
    //           "between": "Entre",
    //           "empty": "Vacío",
    //           "equals": "Igual a",
    //           "notBetween": "No entre",
    //           "notEmpty": "No Vacio",
    //           "not": "Diferente de"
    //         },
    //         "number": {
    //           "between": "Entre",
    //           "empty": "Vacio",
    //           "equals": "Igual a",
    //           "gt": "Mayor a",
    //           "gte": "Mayor o igual a",
    //           "lt": "Menor que",
    //           "lte": "Menor o igual que",
    //           "notBetween": "No entre",
    //           "notEmpty": "No vacío",
    //           "not": "Diferente de"
    //         },
    //         "string": {
    //           "contains": "Contiene",
    //           "empty": "Vacío",
    //           "endsWith": "Termina en",
    //           "equals": "Igual a",
    //           "notEmpty": "No Vacio",
    //           "startsWith": "Empieza con",
    //           "not": "Diferente de",
    //           "notContains": "No Contiene",
    //           "notStarts": "No empieza con",
    //           "notEnds": "No termina con"
    //         },
    //         "array": {
    //           "not": "Diferente de",
    //           "equals": "Igual",
    //           "empty": "Vacío",
    //           "contains": "Contiene",
    //           "notEmpty": "No Vacío",
    //           "without": "Sin"
    //         }
    //       },
    //       "data": "Data",
    //       "deleteTitle": "Eliminar regla de filtrado",
    //       "leftTitle": "Criterios anulados",
    //       "logicAnd": "Y",
    //       "logicOr": "O",
    //       "rightTitle": "Criterios de sangría",
    //       "title": {
    //         "0": "Constructor de búsqueda",
    //         "_": "Constructor de búsqueda (%d)"
    //       },
    //       "value": "Valor"
    //     },
    //     "searchPanes": {
    //       "clearMessage": "Borrar todo",
    //       "collapse": {
    //         "0": "Paneles de búsqueda",
    //         "_": "Paneles de búsqueda (%d)"
    //       },
    //       "count": "{total}",
    //       "countFiltered": "{shown} ({total})",
    //       "emptyPanes": "Sin paneles de búsqueda",
    //       "loadMessage": "Cargando paneles de búsqueda",
    //       "title": "Filtros Activos - %d",
    //       "showMessage": "Mostrar Todo",
    //       "collapseMessage": "Colapsar Todo"
    //     },
    //     "select": {
    //       "cells": {
    //         "1": "1 celda seleccionada",
    //         "_": "%d celdas seleccionadas"
    //       },
    //       "columns": {
    //         "1": "1 columna seleccionada",
    //         "_": "%d columnas seleccionadas"
    //       },
    //       "rows": {
    //         "1": "1 fila seleccionada",
    //         "_": "%d filas seleccionadas"
    //       }
    //     },
    //     "thousands": ".",
    //     "datetime": {
    //       "previous": "Anterior",
    //       "next": "Proximo",
    //       "hours": "Horas",
    //       "minutes": "Minutos",
    //       "seconds": "Segundos",
    //       "unknown": "-",
    //       "amPm": [
    //         "AM",
    //         "PM"
    //       ],
    //       "months": {
    //         "0": "Enero",
    //         "1": "Febrero",
    //         "10": "Noviembre",
    //         "11": "Diciembre",
    //         "2": "Marzo",
    //         "3": "Abril",
    //         "4": "Mayo",
    //         "5": "Junio",
    //         "6": "Julio",
    //         "7": "Agosto",
    //         "8": "Septiembre",
    //         "9": "Octubre"
    //       },
    //       "weekdays": [
    //         "Dom",
    //         "Lun",
    //         "Mar",
    //         "Mie",
    //         "Jue",
    //         "Vie",
    //         "Sab"
    //       ]
    //     },
    //     "editor": {
    //       "close": "Cerrar",
    //       "create": {
    //         "button": "Nuevo",
    //         "title": "Crear Nuevo Registro",
    //         "submit": "Crear"
    //       },
    //       "edit": {
    //         "button": "Editar",
    //         "title": "Editar Registro",
    //         "submit": "Actualizar"
    //       },
    //       "remove": {
    //         "button": "Eliminar",
    //         "title": "Eliminar Registro",
    //         "submit": "Eliminar",
    //         "confirm": {
    //           "_": "¿Está seguro que desea eliminar %d filas?",
    //           "1": "¿Está seguro que desea eliminar 1 fila?"
    //         }
    //       },
    //       "error": {
    //         "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
    //       },
    //       "multi": {
    //         "title": "Múltiples Valores",
    //         "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
    //         "restore": "Deshacer Cambios",
    //         "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
    //       }
    //     },
    //     "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
    //     "stateRestore": {
    //       "creationModal": {
    //         "button": "Crear",
    //         "name": "Nombre:",
    //         "order": "Clasificación",
    //         "paging": "Paginación",
    //         "search": "Busqueda",
    //         "select": "Seleccionar",
    //         "columns": {
    //           "search": "Búsqueda de Columna",
    //           "visible": "Visibilidad de Columna"
    //         },
    //         "title": "Crear Nuevo Estado",
    //         "toggleLabel": "Incluir:"
    //       },
    //       "emptyError": "El nombre no puede estar vacio",
    //       "removeConfirm": "¿Seguro que quiere eliminar este %s?",
    //       "removeError": "Error al eliminar el registro",
    //       "removeJoiner": "y",
    //       "removeSubmit": "Eliminar",
    //       "renameButton": "Cambiar Nombre",
    //       "renameLabel": "Nuevo nombre para %s",
    //       "duplicateError": "Ya existe un Estado con este nombre.",
    //       "emptyStates": "No hay Estados guardados",
    //       "removeTitle": "Remover Estado",
    //       "renameTitle": "Cambiar Nombre Estado"
    //     }
    //   },
    //   dom: 'Blfrtip',
    //   buttons: [
    //     'excel', 'pdf'
    //   ],
    //   "lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
    //   // buttons: [
    //   //   'copy', 'csv', 'excel', 'pdf', 'print'
    //   // ],
    //   "footerCallback": function (row, data, start, end, display) {
    //     var api = this.api(), data;
    //
    //     // Remove the formatting to get integer data for summation
    //     var intVal = function (i) {
    //       return typeof i === 'string' ?
    //         i.replace(/[\$,]/g, '') * 1 :
    //         typeof i === 'number' ?
    //           i : 0;
    //     };
    //
    //     // Total over all pages
    //     this.tot = api
    //       .column(4)
    //       .data()
    //       .reduce(function (a, b) {
    //         return intVal(a) + intVal(b);
    //       }, 0);
    //     // console.log(this.tot)
    //     // Total over this page
    //     this.pageTotal = api
    //       .column(4, {page: 'current'})
    //       .data()
    //       .reduce(function (a, b) {
    //         return intVal(a) + intVal(b);
    //       }, 0);
    //     this.ttotal = api
    //       .column(3, {page: 'current'})
    //       .data()
    //       .reduce(function (a, b) {
    //         return intVal(a) + intVal(b);
    //       }, 0);
    //     this.tcuenta = api
    //       .column(4, {page: 'current'})
    //       .data()
    //       .reduce(function (a, b) {
    //         return intVal(a) + intVal(b);
    //       }, 0);
    //     this.tsaldo = api
    //       .column(5, {page: 'current'})
    //       .data()
    //       .reduce(function (a, b) {
    //         return intVal(a) + intVal(b);
    //       }, 0);
    //
    //     // Update footer
    //     $(api.column(4).footer()).html(
    //       // '$'+this.pageTotal +' ( $'+ this.tot +' total)'
    //       'total:' + this.ttotal + ' a cuenta:' + this.tcuenta + ' saldo:' + this.tsaldo + ' '
    //     );
    //   }
    // });

    //this.misventas()
    // console.log(this.$store.state.login)


    // })
    this.$axios.post(process.env.API + '/me').then(res => {
      // console.log(res.data)
      this.responsable = res.data.name
    })
  },
  methods: {
    filtrarList(){
      /*if (this.filtEstado === 'TODOS') {
        this.ventas = this.filterVenta;
      } else {
        this.ventas = this.filterVenta.filter(
          item => item.estado === this.filtEstado
        );
      }*/
        this.ventas= this.filterVenta.filter(item => {
        const filtroEstado = this.filtEstado === 'TODOS' || item.estado === this.filtEstado;
        const filtroGrupo = this.cliente.id === 0 || item.cliente_id === this.cliente.id;
        return filtroEstado && filtroGrupo;
    });
    },
    exportPdf() {
      const url = process.env.API + '/cuentasCobrarLocal?fechaInicio=' + this.fecha2 + '&fechaFin=' + this.fecha3+'&estado='+this.filtEstado;
      window.open(url, '_blank');
    },
    exportExcel() {
      let data = [{
        sheet: "Cuentas por cobrar detalle",
        columns: [
          {label: "Fecha", value: "fecha"},
          {label: "Local", value: "local"},
          {label: "Titular", value: "titular"},
          {label: "Total", value: "total"},
          {label: "A cuenta", value: "acuenta"},
          {label: "Saldo", value: "saldo"},
          {label: "Tipo cliente", value: "tipocliente"},
          {label: "Estado", value: "estado"},
          {label: "Usuario", value: "user"},
        ],
        content: this.ventas.map(r => {
          return {
            fecha: r.fecha,
            local: r.local,
            titular: r.titular,
            total: r.total,
            acuenta: r.acuenta,
            saldo: r.saldo,
            tipocliente: r.tipocliente == 1 ? 'LOCAL' : 'CLIENTE',
            estado: r.estado == 'CANCELADO' ? 'CANCELADO' : 'POR COBRAR',
            user: r.user
          }
        })
      }]
      Excel.export(data, 'Cuentas por cobrar detalle')
    },
    delPago(pago) {
      this.$q.dialog({
        title: 'Eliminar',
        message: 'Esta seguro de Eliminar Pago'
      }).onOk(() => {
        this.$axios.delete(process.env.API + '/pago/' + pago.id).then(res => {
          this.alert = false;
          this.misventas()
          // console.log(res.data)
          this.$q.notify({
            message: 'Eliminado',
            color: 'red',
            icon: 'info'
          })
        })
      }).onCancel(() => {
        // console.log('Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    onPago() {
      this.loading = true
      this.$q.loading.show()
      this.$axios.post(process.env.API + '/pago', this.regpago).then(res => {
        this.regpago = {fecha: date.formatDate(new Date(), 'YYYY-MM-DD')};
        this.dialog_pago = false;

        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
        let myWindow2 = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow2.document.write(res.data);
        myWindow2.document.close();
        myWindow2.print();
        myWindow2.close();

        this.regpago = {fecha: date.formatDate(new Date(), 'YYYY-MM-DD')}
        this.misventas();
        this.$q.loading.hide()
        this.loading = false
        this.$q.notify({
          message: 'Registro correcto',
          color: 'green',
          icon: 'send'
        })
      })

    },
    pago(props) {
      this.regpago.venta_id = props.id;
      this.regpago.saldo = props.saldo;
      this.regpago.fecha = date.formatDate(new Date(), 'YYYY-MM-DD')
      this.dialog_pago = true;
    },
    listpago(dato) {
      this.pagos = dato.pagos;
      this.alert = true;
    },

    misventas() {
      this.$q.loading.show()
      this.ventas = [];
      if (this.rango == 'DIA') {
        this.fecha3 = this.fecha2
      }
      // $('#example').DataTable().destroy();
      this.$axios.post(process.env.API + '/listventlocal', {ini: this.fecha2, fin: this.fecha3}).then(res => {
        // this.ventas=res.data
        // console.log(res.data)
        this.$q.loading.hide()
        this.ventas = []
        // $('#example').DataTable().destroy();
        let unicos = [];  
        let ids = new Set();
        res.data.forEach(r => {
          this.ventas.push({
            id: r.id,
            fechaord: r.fecha,
            fecha: moment(r.fecha).format('DD/MM/YYYY'),
            total: r.total,
            acuenta: r.acuenta,
            saldo: r.saldo,
            tipocliente: r.cliente.tipocliente,
            estado: r.estado,
            local: r.cliente.local,
            titular: r.cliente.titular,
            cliente_id: r.cliente.id,
            user: r.user.name,
            pagos: r.pagos
          })
          const cliente = r.cliente;
          if (!ids.has(cliente.id)) {
            ids.add(cliente.id);
            unicos.push({ label: cliente.local + ' ' + cliente.titular, id: cliente.id });
          }
        })
        this.clientes= [{ label: 'TODOS', id: 0 }, ...unicos];
        this.filterVenta=this.ventas
        // console.log(this.ventas)
        // this.$nextTick(() => {
        //   // $('#example').DataTable( {
        //   //   dom: 'Blfrtip',
        //   //   // buttons: [
        //   //   //   'copy', 'csv', 'excel', 'pdf', 'print'
        //   //   // ],
        //   //
        //   //   "footerCallback": function ( row, data, start, end, display ) {
        //   //     var api = this.api(), data;
        //   //
        //   //     // Remove the formatting to get integer data for summation
        //   //     var intVal = function ( i ) {
        //   //       return typeof i === 'string' ?
        //   //         i.replace(/[\$,]/g, '')*1 :
        //   //         typeof i === 'number' ?
        //   //           i : 0;
        //   //     };
        //   //
        //   //     // Total over all pages
        //   //     this.tot = api
        //   //       .column( 4 )
        //   //       .data()
        //   //       .reduce( function (a, b) {
        //   //         return intVal(a) + intVal(b);
        //   //       }, 0 );
        //   //     // console.log(this.tot)
        //   //     // Total over this page
        //   //     this.pageTotal = api
        //   //       .column( 4, { page: 'current'} )
        //   //       .data()
        //   //       .reduce( function (a, b) {
        //   //         return intVal(a) + intVal(b);
        //   //       }, 0 );
        //   //
        //   //
        //   //     this.pageTotal = api
        //   //       .column( 4, { page: 'current'} )
        //   //       .data()
        //   //       .reduce( function (a, b) {
        //   //         return intVal(a) + intVal(b);
        //   //       }, 0 );
        //   //
        //   //     this.pageTotal = api
        //   //       .column( 4, { page: 'current'} )
        //   //       .data()
        //   //       .reduce( function (a, b) {
        //   //         return intVal(a) + intVal(b);
        //   //       }, 0 );
        //   //
        //   //     this.pageTotal = api
        //   //       .column( 4, { page: 'current'} )
        //   //       .data()
        //   //       .reduce( function (a, b) {
        //   //         return intVal(a) + intVal(b);
        //   //       }, 0 );
        //   //
        //   //     // Update footer
        //   //     $( api.column( 4 ).footer() ).html(
        //   //       '$'+this.pageTotal +' ( $'+ this.tot +' total)'
        //   //     );
        //   //   }
        //   // } );
        //
        //   $('#example').DataTable({
        //     "language": {
        //       "processing": "Procesando...",
        //       "lengthMenu": "Mostrar _MENU_ registros",
        //       "zeroRecords": "No se encontraron resultados",
        //       "emptyTable": "Ningún dato disponible en esta tabla",
        //       "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
        //       "infoFiltered": "(filtrado de un total de _MAX_ registros)",
        //       "search": "Buscar:",
        //       "infoThousands": ",",
        //       "loadingRecords": "Cargando...",
        //       "paginate": {
        //         "first": "Primero",
        //         "last": "Último",
        //         "next": "Siguiente",
        //         "previous": "Anterior"
        //       },
        //       "aria": {
        //         "sortAscending": ": Activar para ordenar la columna de manera ascendente",
        //         "sortDescending": ": Activar para ordenar la columna de manera descendente"
        //       },
        //       "buttons": {
        //         "copy": "Copiar",
        //         "colvis": "Visibilidad",
        //         "collection": "Colección",
        //         "colvisRestore": "Restaurar visibilidad",
        //         "copyKeys": "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br \/> <br \/> Para cancelar, haga clic en este mensaje o presione escape.",
        //         "copySuccess": {
        //           "1": "Copiada 1 fila al portapapeles",
        //           "_": "Copiadas %ds fila al portapapeles"
        //         },
        //         "copyTitle": "Copiar al portapapeles",
        //         "csv": "CSV",
        //         "excel": "Excel",
        //         "pageLength": {
        //           "-1": "Mostrar todas las filas",
        //           "_": "Mostrar %d filas"
        //         },
        //         "pdf": "PDF",
        //         "print": "Imprimir",
        //         "renameState": "Cambiar nombre",
        //         "updateState": "Actualizar",
        //         "createState": "Crear Estado",
        //         "removeAllStates": "Remover Estados",
        //         "removeState": "Remover",
        //         "savedStates": "Estados Guardados",
        //         "stateRestore": "Estado %d"
        //       },
        //       "autoFill": {
        //         "cancel": "Cancelar",
        //         "fill": "Rellene todas las celdas con <i>%d<\/i>",
        //         "fillHorizontal": "Rellenar celdas horizontalmente",
        //         "fillVertical": "Rellenar celdas verticalmentemente"
        //       },
        //       "decimal": ",",
        //       "searchBuilder": {
        //         "add": "Añadir condición",
        //         "button": {
        //           "0": "Constructor de búsqueda",
        //           "_": "Constructor de búsqueda (%d)"
        //         },
        //         "clearAll": "Borrar todo",
        //         "condition": "Condición",
        //         "conditions": {
        //           "date": {
        //             "after": "Despues",
        //             "before": "Antes",
        //             "between": "Entre",
        //             "empty": "Vacío",
        //             "equals": "Igual a",
        //             "notBetween": "No entre",
        //             "notEmpty": "No Vacio",
        //             "not": "Diferente de"
        //           },
        //           "number": {
        //             "between": "Entre",
        //             "empty": "Vacio",
        //             "equals": "Igual a",
        //             "gt": "Mayor a",
        //             "gte": "Mayor o igual a",
        //             "lt": "Menor que",
        //             "lte": "Menor o igual que",
        //             "notBetween": "No entre",
        //             "notEmpty": "No vacío",
        //             "not": "Diferente de"
        //           },
        //           "string": {
        //             "contains": "Contiene",
        //             "empty": "Vacío",
        //             "endsWith": "Termina en",
        //             "equals": "Igual a",
        //             "notEmpty": "No Vacio",
        //             "startsWith": "Empieza con",
        //             "not": "Diferente de",
        //             "notContains": "No Contiene",
        //             "notStarts": "No empieza con",
        //             "notEnds": "No termina con"
        //           },
        //           "array": {
        //             "not": "Diferente de",
        //             "equals": "Igual",
        //             "empty": "Vacío",
        //             "contains": "Contiene",
        //             "notEmpty": "No Vacío",
        //             "without": "Sin"
        //           }
        //         },
        //         "data": "Data",
        //         "deleteTitle": "Eliminar regla de filtrado",
        //         "leftTitle": "Criterios anulados",
        //         "logicAnd": "Y",
        //         "logicOr": "O",
        //         "rightTitle": "Criterios de sangría",
        //         "title": {
        //           "0": "Constructor de búsqueda",
        //           "_": "Constructor de búsqueda (%d)"
        //         },
        //         "value": "Valor"
        //       },
        //       "searchPanes": {
        //         "clearMessage": "Borrar todo",
        //         "collapse": {
        //           "0": "Paneles de búsqueda",
        //           "_": "Paneles de búsqueda (%d)"
        //         },
        //         "count": "{total}",
        //         "countFiltered": "{shown} ({total})",
        //         "emptyPanes": "Sin paneles de búsqueda",
        //         "loadMessage": "Cargando paneles de búsqueda",
        //         "title": "Filtros Activos - %d",
        //         "showMessage": "Mostrar Todo",
        //         "collapseMessage": "Colapsar Todo"
        //       },
        //       "select": {
        //         "cells": {
        //           "1": "1 celda seleccionada",
        //           "_": "%d celdas seleccionadas"
        //         },
        //         "columns": {
        //           "1": "1 columna seleccionada",
        //           "_": "%d columnas seleccionadas"
        //         },
        //         "rows": {
        //           "1": "1 fila seleccionada",
        //           "_": "%d filas seleccionadas"
        //         }
        //       },
        //       "thousands": ".",
        //       "datetime": {
        //         "previous": "Anterior",
        //         "next": "Proximo",
        //         "hours": "Horas",
        //         "minutes": "Minutos",
        //         "seconds": "Segundos",
        //         "unknown": "-",
        //         "amPm": [
        //           "AM",
        //           "PM"
        //         ],
        //         "months": {
        //           "0": "Enero",
        //           "1": "Febrero",
        //           "10": "Noviembre",
        //           "11": "Diciembre",
        //           "2": "Marzo",
        //           "3": "Abril",
        //           "4": "Mayo",
        //           "5": "Junio",
        //           "6": "Julio",
        //           "7": "Agosto",
        //           "8": "Septiembre",
        //           "9": "Octubre"
        //         },
        //         "weekdays": [
        //           "Dom",
        //           "Lun",
        //           "Mar",
        //           "Mie",
        //           "Jue",
        //           "Vie",
        //           "Sab"
        //         ]
        //       },
        //       "editor": {
        //         "close": "Cerrar",
        //         "create": {
        //           "button": "Nuevo",
        //           "title": "Crear Nuevo Registro",
        //           "submit": "Crear"
        //         },
        //         "edit": {
        //           "button": "Editar",
        //           "title": "Editar Registro",
        //           "submit": "Actualizar"
        //         },
        //         "remove": {
        //           "button": "Eliminar",
        //           "title": "Eliminar Registro",
        //           "submit": "Eliminar",
        //           "confirm": {
        //             "_": "¿Está seguro que desea eliminar %d filas?",
        //             "1": "¿Está seguro que desea eliminar 1 fila?"
        //           }
        //         },
        //         "error": {
        //           "system": "Ha ocurrido un error en el sistema (<a target=\"\\\" rel=\"\\ nofollow\" href=\"\\\">Más información&lt;\\\/a&gt;).<\/a>"
        //         },
        //         "multi": {
        //           "title": "Múltiples Valores",
        //           "info": "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, hacer click o tap aquí, de lo contrario conservarán sus valores individuales.",
        //           "restore": "Deshacer Cambios",
        //           "noMulti": "Este registro puede ser editado individualmente, pero no como parte de un grupo."
        //         }
        //       },
        //       "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
        //       "stateRestore": {
        //         "creationModal": {
        //           "button": "Crear",
        //           "name": "Nombre:",
        //           "order": "Clasificación",
        //           "paging": "Paginación",
        //           "search": "Busqueda",
        //           "select": "Seleccionar",
        //           "columns": {
        //             "search": "Búsqueda de Columna",
        //             "visible": "Visibilidad de Columna"
        //           },
        //           "title": "Crear Nuevo Estado",
        //           "toggleLabel": "Incluir:"
        //         },
        //         "emptyError": "El nombre no puede estar vacio",
        //         "removeConfirm": "¿Seguro que quiere eliminar este %s?",
        //         "removeError": "Error al eliminar el registro",
        //         "removeJoiner": "y",
        //         "removeSubmit": "Eliminar",
        //         "renameButton": "Cambiar Nombre",
        //         "renameLabel": "Nuevo nombre para %s",
        //         "duplicateError": "Ya existe un Estado con este nombre.",
        //         "emptyStates": "No hay Estados guardados",
        //         "removeTitle": "Remover Estado",
        //         "renameTitle": "Cambiar Nombre Estado"
        //       }
        //     },
        //     dom: 'Blfrtip',
        //     buttons: [
        //       'excel', 'pdf'
        //     ],
        //     "lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
        //     // buttons: [
        //     //   'copy', 'csv', 'excel', 'pdf', 'print'
        //     // ],
        //     "footerCallback": function (row, data, start, end, display) {
        //       var api = this.api(), data;
        //
        //       // Remove the formatting to get integer data for summation
        //       var intVal = function (i) {
        //         return typeof i === 'string' ?
        //           i.replace(/[\$,]/g, '') * 1 :
        //           typeof i === 'number' ?
        //             i : 0;
        //       };
        //
        //       // Total over all pages
        //       this.tot = api
        //         .column(4)
        //         .data()
        //         .reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //         }, 0);
        //       // console.log(this.tot)
        //       // Total over this page
        //       this.pageTotal = api
        //         .column(4, {page: 'current'})
        //         .data()
        //         .reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //         }, 0);
        //       this.ttotal = api
        //         .column(3, {page: 'current'})
        //         .data()
        //         .reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //         }, 0);
        //       this.tcuenta = api
        //         .column(4, {page: 'current'})
        //         .data()
        //         .reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //         }, 0);
        //       this.tsaldo = api
        //         .column(5, {page: 'current'})
        //         .data()
        //         .reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //         }, 0);
        //
        //       // Update footer
        //       $(api.column(4).footer()).html(
        //         // '$'+this.pageTotal +' ( $'+ this.tot +' total)'
        //         'total:' + this.ttotal + ' a cuenta:' + this.tcuenta + ' saldo:' + this.tsaldo + ' '
        //       );
        //     }
        //   });
        // })
      })
    },

    guardar() {
      if (this.model == 0) {
        this.$q.notify({
          message: 'Tienes que seleccionar cliente',
          color: 'red',
          icon: 'error'
        })
        return false;
      }
      if (this.fecha == undefined) {
        this.$q.notify({
          message: 'Tienes que seleccionar fecha',
          color: 'red',
          icon: 'error'
        })
        return false;
      }
      // console.log(this.detalles)
      // return  false
      // return false
      this.$q.loading.show()
      // console.log({
      //   nombreproducto:this.producto.nombre,
      //   precio:this.producto.precio,
      //   producto_id:this.producto.id,
      //   cantidad:this.cantidad,
      //   subtotal:this.subtotal,
      // })
      // return false

      this.$axios.post(process.env.API + '/venta', {
        fecha: this.fecha,
        total: this.subtotal,
        acuenta: this.acuenta,
        saldo: this.saldo,
        estado: this.estado,
        filtro: '',
        tipo: 'LOCAL',
        cliente_id: this.model.id,
        detalles: [{
          nombreproducto: this.producto.nombre,
          precio: this.producto.precio,
          producto_id: this.producto.id,
          cantidad: this.cantidad,
          subtotal: this.subtotal,
        }],

      }).then(res => {
        // console.log(res.data)
        this.$q.notify({
          message: 'Venta exitosa',
          color: 'green',
          icon: 'info'
        })
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        this.misventas()

        this.subtotal = ''
        this.acuenta = ''
        this.saldo = ''
        this.estado = ''
        this.model = ''
        this.producto = ''
        this.cantidad = 1
        this.subtotal = 0
        this.fecha = date.formatDate(new Date(), 'YYYY-MM-DD');
      }).catch(err => {
        this.$q.loading.hide()
        console.error(err)
        this.$q.notify({
          message: err.response.data.message,
          color: 'red',
          icon: 'error'
        })
      })
    },
    deleteval(index) {
      this.detalles.splice(index, 1);
    },
    generar() {
      if (this.model == 0) {
        this.$q.notify({
          message: 'Tienes que seleccionar cliente',
          color: 'red',
          icon: 'error'
        })
        return false;
      }

      console.log(this.model.id);
      if (this.model != '') {
        this.$axios.post(process.env.API + '/listaprestamo', {
          fecha1: this.fecha4, fecha2: this.fecha5, cliente_id: this.model.id
        }).then(res => {
          console.log(res.data)
          this.prestamos = res.data;

        })
      }
    },
    regprestamo() {

      if (this.model != '') {
        // console.log('a')
        this.prestamo.cliente_id = this.model.id;
        this.prestamo.inventario_id = this.inventario.id;
        this.$axios.post(process.env.API + '/garantia', this.prestamo).then(res => {
          console.log(res.data)
          this.$q.notify({
            message: 'Prestamo exitosa',
            color: 'green',
            icon: 'info'
          })
          this.generar()
        }).catch(err => {
          this.$q.loading.hide()
          console.error(err)
          this.$q.notify({
            message: err.response.data.message,
            color: 'red',
            icon: 'error'
          })
        })
      } else {
        this.$q.notify({
          message: 'Debes seleccionar cliente',
          color: 'red',
          icon: 'error'
        })
      }
    },
    devolver1(props) {
      this.garantia = props.row;
      console.log(this.garantia);
      this.dialog_garantia = true;
    },
    devolver() {
      console.log(this.garantia);
      this.$axios.post(process.env.API + '/devolver', this.garantia).then(res => {
        this.$q.notify({
          message: 'Devolucion exitosa',
          color: 'green',
          icon: 'info'
        })
      })
      this.dialog_garantia = false;

    },
    onReset() {
    },

  },
  computed: {
    subtotal() {
      if (this.producto.precio != undefined && this.producto.precio != NaN) {
        return parseFloat(this.producto.precio) * parseFloat(this.cantidad)
      } else {
        return 0
      }
    },
    total() {
      let total = 0;
      this.detalles.forEach(r => {
        total += parseFloat(r.subtotal)
      })
      return total
    },
    estado() {
      if (this.acuenta < this.subtotal)
        return 'POR COBRAR'
      else
        return 'CANCELADO'
    },
    saldo() {
      return this.subtotal - this.acuenta
    },
    ventat() {
      let total = 0;
      this.ventas.forEach(r => {
        total += parseFloat(r.total)
      })
      return total
    },
    porc() {
      let total = 0;
      this.ventas.forEach(r => {
        total += parseFloat(r.acuenta)
      })
      return total
    },
    saldoc() {
      let total = 0;
      this.ventas.forEach(r => {
        total += parseFloat(r.saldo)
      })
      return total
    },

  }
}
</script>

<style scoped>
th {
  white-space: nowrap;
}
</style>
