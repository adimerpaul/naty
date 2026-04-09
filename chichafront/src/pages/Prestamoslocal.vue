<template>
  <q-page class="q-pa-xs">
    <div class="col-12">
      <div class="text-subtitle1 bg-blue-9 text-center text-white">PRESTAMOS LOCAL</div>
    </div>
    <div class="row">
      <div class="col-12 col-sm-3 q-pa-xs">
        <q-select input-debounce="0" dense use-input outlined label="Seleccionar local" v-model="cliente"
                  :options="prestamos" @filter="filterFn"/>
      </div>
      <div class="col-md-4 col-sm-3 q-pa-xs">
        <q-input dense outlined label="Fecha" v-model="fecha" type="date"/>
      </div>
      <div class="col-md-4 col-sm-3 q-pa-xs">
        <q-btn color="green" dense label="REGISTRAR" @click="validarReg()"/>

      </div>
    </div>
    <q-dialog v-model="dialogReg" persistent>
      <q-card style="width: 700px; max-width: 80vw;">
        <q-card-section>
          <div class="text-h6">REGISTRAR PRESTAMO</div>
        </q-card-section>
        <q-card-section>
          <q-form @submit.prevent="agregar">
            <div class="row">
                            <div class="col-12 col-sm-12 q-pa-xs flex flex-center">
                <input type="radio" value="EN PRESTAMO" v-model="tipo"
                       style="margin-right: 0.5em;height:35px; width:35px; "/><b
                :style="tipo=='EN PRESTAMO'?'color:red':''"> EN PRESTAMO </b>
                <input type="radio" value="VENTA" v-model="tipo"
                       style="margin-left: 1em;margin-right: 0.5em;height:35px; width:35px; "/><b
                :style="tipo=='VENTA'?'color:red':''"> VENTA </b>
              </div>
              <div class="col-12 col-sm-4 q-pa-xs">
                <q-select dense outlined label="Seleccionar Inventario" v-model="inventario" :options="inventarios"
                          option-label="nombre" @update:model-value="calcular"/>
              </div>
              <div class="col-12 col-sm-2 q-pa-xs">
                <!--<q-select dense outlined label="Seleccionar Cantidad" v-model="cantidad" :options="cantidades" @update:model-value="calcular"/>-->
                <q-input dense outlined label="Cantidad" v-model="cantidad" type="number" @update:model-value="calcular"
                         :rules="[val => val>0 || 'Ingrese valor']"/>
              </div>

              <div class="col-12 col-sm-2 q-pa-xs">
                <q-input dense outlined label="Efectivo" v-model="efectivo" type="number" step="0.01"/>
              </div>

              <div class="col-12 col-sm-4 q-pa-xs">
                <q-input dense outlined label="Fisico" v-model="fisico" style="text-transform: uppercase"/>
              </div>
              <div class="col-12 col-sm-6 q-pa-xs">
                <q-input dense outlined label="Observacion" v-model="observacion" style="text-transform: uppercase"/>
              </div>

              <div class="col-12 col-sm-6 q-pa-xs " style=" text-align: right;">
                <q-btn dense label="agregar" icon="send" color="positive" type="submit" v-if="!boolmod"/>
              </div>
              <div class="col-12">
                <q-table dense :rows="listPrestamo" :columns="colregistro" row-key="name">
                  <template v-slot:body-cell-op="props">
                    <q-td key="op" :props="props">
                      <q-btn color="red" icon="delete" dense @click="delListPres(props)"/>
                    </q-td>
                  </template>
                </q-table>
              </div>
              <div class="col-12  q-pa-xs " style=" text-align: right;">
                <q-btn dense label="CANCELAR" color="red" v-close-popup v-if="listPrestamo.length==0"/>
                <q-btn dense label="FINALIZAR" color="info" @click="impresionList()" v-if="listPrestamo.length>=1"/>
                <q-btn dense label="Modficar" icon="edit" color="yellow" v-if="boolmod" @click="modificar"/>
              </div>

            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>

    <div class="text-h5" v-if="tab==2">TOTAL EN CAJA: {{ totalefectivo }} Bs.</div>
    <!--  {{cliente}}-->
    <div class="col-12 flex flex-center">
      <q-pagination v-model="pagination.page" :max="pagination.last_page" :min="1"
                    :total-pages="Math.ceil(pagination.rowsNumber / pagination.rowsPerPage)"
                    @click="listadoprestamo()"
                    :max-pages="6"
                    boundary-numbers
      />
    </div>
    <q-table title="Lista de prestamos local"
             :rows="listadop"
             flat bordered
             :columns="colprestamo"
             v-model:pagination="pagination"
             :filter="filter"
             :rows-per-page-options="[0]"
             row-key="name"
             hide-bottom>

      <template v-slot:top-right>
        <q-select v-model="estado" :options="estados" label="ESTADO" outlined dense
                  @update:modelValue="listadoprestamo"/>
        <q-input borderless dense outlined debounce="300" v-model="filter" placeholder="Buscar"
                 @update:modelValue="listadoprestamo">
          <template v-slot:append>
            <q-icon name="search"/>
          </template>
        </q-input>
      </template>
      <template v-slot:body-cell-prestado="props">
        <q-td key="prestado" :props="props"
              :style="props.row.prestado>0?'color:red;font-size:20px; font-weight: bold;':'color:green;font-size:20px; font-weight: bold;'">
          {{ props.row.prestado }}
        </q-td>
      </template>
      <template v-slot:body-cell-observacion="props">
        <q-td key="observacion" :props="props">
          <q-btn color="accent" icon="notes" @click="verObs(props.row)"
                 v-if="props.row.observacion!='' && props.row.observacion!=null" dense/>

        </q-td>
      </template>
      <template v-slot:body-cell-estado="props">
        <q-td key="estado" :props="props">
          <q-badge color="red" v-if="props.row.estado=='ANULADO'">
            BAJA
          </q-badge>
          <q-badge color="amber" v-if="props.row.estado=='DEVUELTO'">
            {{ props.row.estado }}
          </q-badge>
          <q-badge color="positive" v-if="props.row.estado=='EN PRESTAMO'">
            {{ props.row.estado }}
          </q-badge>
          <q-badge color="blue" v-if="props.row.estado=='VENTA'">
            {{ props.row.estado }}
          </q-badge>
        </q-td>
      </template>
      <template v-slot:body-cell-logprestamos="props">
        <q-td key="logprestamos" :props="props">
          <ul v-for="l in props.row.logprestamos" :key="l.id"
              style="margin: 0px;padding:0px;border: 0px;list-style: none">
            <li style="margin: 0px;padding:0px;border: 0px;"><b>Cant:</b> {{ l.cantidad }}
              <b>Fecha: </b>{{ cambiofecha(l.fecha) }}
            </li>
          </ul>
        </q-td>
      </template>
      <template v-slot:body-cell-telefono="props">
        <q-td key="telfono" :props="props">
          <q-btn icon="call" color="accent" @click="verTelef(props.row)" dense/>
        </q-td>
      </template>
      <template v-slot:body-cell-fisico="props">
        <q-td key="fisico" :props="props">
          <q-btn icon="list" color="accent" @click="verFisico(props.row)" dense v-if="props.row.fisico!=''"/>
        </q-td>
      </template>
      <template v-slot:body-cell-opcion="props">
        <q-td key="opcion" :props="props">
          <q-btn-dropdown color="primary" label="Opcion" no-caps dense>
            <q-list>
              <q-item clickable v-close-popup @click="onDev(props.row)" v-if="props.row.estado=='EN PRESTAMO'">
                <q-item-section avatar>
                  <q-avatar icon="refresh" color="primary" text-color="white"/>
                </q-item-section>
                <q-item-section>
                  <q-item-label>Devolver</q-item-label>
                </q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="onList(props.row)">
                <q-item-section avatar>
                  <q-avatar icon="list" color="green" text-color="white"/>
                </q-item-section>
                <q-item-section>
                  <q-item-label>Listar</q-item-label>
                </q-item-section>
              </q-item>
<!--              <q-item clickable v-close-popup @click="onMod(props.row)" v-if="props.row.estado=='EN PRESTAMO'">-->
<!--                <q-item-section avatar>-->
<!--                  <q-avatar icon="edit" color="yellow" text-color="white"/>-->
<!--                </q-item-section>-->
<!--                <q-item-section>-->
<!--                  <q-item-label>Modificar</q-item-label>-->
<!--                </q-item-section>-->
<!--              </q-item>-->
              <q-item clickable v-close-popup @click="onEliminar(props.row)"
                      v-if="props.row.estado=='EN PRESTAMO' && $store.state.login.anularprestamo">
                <q-item-section avatar>
                  <!--                          <q-item-label  content-style="{background-color: coral;}"  >BAJA</q-item-label>-->
                  <q-avatar icon="stop" color="negative" text-color="white"/>
                </q-item-section>
                <q-item-section>
                  <q-item-label>Dar Baja</q-item-label>
                </q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="ondelete(props.row)"
                      v-if="(props.row.estado=='EN PRESTAMO' || props.row.estado=='VENTA') && $store.state.login.delprestamo">
                <q-item-section avatar>
                  <q-avatar icon="delete" color="negative" text-color="white"/>
                </q-item-section>
                <q-item-section>
                  <q-item-label>Eliminar</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
          <!--<q-btn size="xs" @click="onDev(props.row)" v-if="props.row.estado=='EN PRESTAMO'"  color="primary" icon="refresh"/>
          <q-btn size="xs" @click="onList(props.row)"  color="green" icon="list"/>
          <q-btn size="xs" @click="onMod(props.row)"  color="yellow" icon="edit"/>
          <q-btn size="xs" @click="onEliminar(props.row)" v-if="props.row.estado=='EN PRESTAMO' && $store.state.login.anularprestamo"  color="red-12" label="AN" />
          <q-btn size="xs" @click="ondelete(props.row)" v-if="(props.row.estado=='EN PRESTAMO' || props.row.estado=='VENTA')&& $store.state.login.delprestamo"  color="negative" icon="delete"/>-->
        </q-td>
      </template>

    </q-table>

    <hr>
    <table id="example" style="width:100%" v-if="tab==1" class="cell-border">
      <thead>
      <tr>
        <th>Local</th>
        <th>Titular</th>
        <th>Material</th>
        <th>Total</th>
        <th>Monto</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="r of reportepres" :key="r">
        <td>{{ r.local }}</td>
        <td>{{ r.titular }}</td>
        <td>{{ r.nombre }}</td>
        <td>{{ r.total }}</td>
        <td>{{ r.monto }}</td>
      </tr>
      </tbody>
    </table>
    <q-dialog v-model="dialog_dev">
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Devolucion de prestamo</div>
        </q-card-section>

        <q-form
          @submit="devolver"
          class="q-gutter-md"
        >

          <q-card-section class="q-pt-none">
            <q-input dense v-model="dev.cantidad" autofocus type="number" label="Cantidad" required min=1
                     :rules="[ val => val<=datoprestamo.prestado || 'Ingrese la cantidad correcta' ]"
            />

            <q-input dense v-model="dev.motivo" label="Observacion"

            />
          </q-card-section>
          <div>
            <q-btn label="Devolver" type="submit" color="primary"/>
            <q-btn label="Cancel" color="red" v-close-popup/>
          </div>
        </q-form>


      </q-card>
    </q-dialog>

    <q-dialog v-model="dialog_list">
      <q-card style="min-width: 350px">
        <q-card-section>
          <div class="text-h6">Listado Devolucion</div>
        </q-card-section>

        <q-card-section class="q-pt-none">

          <q-table
            title="Datos de Devolucion"
            :rows="listado"
            :columns="colum"
            row-key="name"
            :rows-per-page-options="[0]"
          >
            <template v-slot:body-cell-op="props" v-if="$store.state.login.delpago">
              <q-td key="op" :props="props">
                <q-btn color="red" icon="delete" dense @click="delDevuelto(props.row)"/>
              </q-td>
            </template>
          </q-table>
        </q-card-section>


      </q-card>
    </q-dialog>

  </q-page>
</template>

<script>
import {date} from 'quasar'
import {jsPDF} from "jspdf";
import $ from "jquery";
import moment from 'moment'

export default {
  name: "Venta",
  data() {
    return {
      colregistro: [
        {label: 'OP', name: 'op', field: 'op'},
        {label: 'CANT', name: 'cantidad', field: 'cantidad'},
        {label: 'TIPO', name: 'tipo', field: 'tipo'},
        {label: 'MAT', name: 'inventario', field: 'inventario'},
        {label: 'OBS', name: 'obs', field: 'observacion'},
      ],
      dialogReg: false,
      listPrestamo: [],
      tab: 1,
      estados: ['TODO', 'EN PRESTAMO', 'DEVUELTO', 'ANULADO', 'VENTA'],
      estado: 'TODO',
      prestamos: [],
      cliente: {label: ''},
      inventarios: [],
      inventario: '',
      cantidades: [],
      cantidad: 1,
      fisico: '',
      efectivo: 0,
      observacion: '',
      totalefectivo: 0,
      dialog_dev: false,
      dialog_list: false,
      datoprestamo: {},
      listado: [],
      listadop: [],
      dev: {},
      tipo: 'EN PRESTAMO',
      reportepres: [],
      options: [],
      fecha: date.formatDate(new Date(), 'YYYY-MM-DD'),

      boolmod: false,
      filter: '',

      pagination: {
        // No longer using sort. if needed, this can now be done using the backend!
        // sortBy: 'name',
        // descending: false,
        page: 1,
        rowsPerPage: 20,
        // When using server side pagination, QTable needs
        // to know the "rows number" (total number of rows).
        // Why?
        // Because Quasar has no way of knowing what the last page
        // will be without this information!
        // Therefore, we now need to supply it with a "rowsNumber" ourself.
        rowsNumber: 0,
        last_page: 0
      },
      colum: [
        {name: 'op', label: 'OP', field: 'op', sortable: true},
        {
          name: 'fecha',
          align: 'center',
          label: 'fecha',
          field: row => moment(row.fecha).format('DD/MM/YYYY'),
          sortable: true
        },
        {name: 'cantidad', label: 'cantidad', field: 'cantidad', sortable: true},
        {name: 'motivo', label: 'Observacion', field: 'motivo'},
      ],
      colprestamo: [
        {name: 'opcion', label: 'OPCION', field: 'opcion'},
        {name: 'id', label: 'ID', field: 'id', sortable: true},
        {name: 'local', label: 'LOCAL', field: row => row.cliente.local, sortable: true, align: 'left'},
        {name: 'titular', label: 'TITULAR', field: row => row.cliente.titular, sortable: true, align: 'left'},
        {name: 'telefono', label: 'TELEFONO', field: row => row.cliente.telefono, sortable: true},
        // { name: 'motivoAnulacion', label: 'MOTIVO ANULACION', field: 'motivoAnulacion' },

        {name: 'Inventario', label: 'INVENTARIO', field: row => row.inventario.nombre, sortable: true, align: 'left'},
        {
          name: 'fecha',
          label: 'FECHA',
          field: row => moment(row.fecha).format('DD/MM/YYYY') + ' ' + moment(row.created_at).format('LT'),
          sortable: true
        },
        {
          name: 'fechaAnulacion',
          label: 'FECHA ANULACION',
          field: row => moment(row.fechaAnulacion).format('DD/MM/YYYY'),
          sortable: true
        },
        {name: 'motivoanulacion', label: 'MOTIVO ANULACION', field: row => row.motivoAnulacion, sortable: true},
        {name: 'estado', label: 'ESTADO', field: 'estado', sortable: true},
        {name: 'cantidad', label: 'CANTIDAD', field: 'cantidad', sortable: true},
        {name: 'prestado', label: 'PENDIENTE', field: 'prestado', sortable: true},
        {name: 'efectivo', label: 'EFECTIVO', field: 'efectivo', sortable: true},
        {name: 'observacion', label: 'OBSERVACION', field: 'observacion'},
        {name: 'user', label: 'USUARIO', field: row => row.user.name},
        {name: 'logprestamos', label: 'HISTORIAL', field: 'logprestamos'},
      ],
    }
  },
  mounted() {
    $('#example').DataTable({
      "language": {
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
      }, dom: 'Blfrtip',
      buttons: [
        'excel', 'pdf'
      ],
      "lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]]
    });


  },
  created() {

    for (let i = 1; i <= 100; i++) {
      this.cantidades.push(i);
    }
    // this.misprestamos()
    this.$axios.get(process.env.API + '/inventario').then(res => {
      // console.log(res.data)
      this.inventarios = res.data;
      this.inventario = this.inventarios[0];
    })
    this.listclientes();
    this.cajaprestamo();
    this.listadoprestamo();
    this.reporte();
  },
  methods: {
    validarReg() {
      if (this.cliente == '' || this.cliente == null) {
        this.$q.notify({
          message: 'Debe Selecionar Cliente',
          color: 'red',
          icon: 'info'
        })
        return false
      } else {
        if (this.cliente.id != undefined)
          this.dialogReg = true
        else {
          this.$q.notify({
            message: 'Debe Selecionar Cliente',
            color: 'red',
            icon: 'info'
          })
        }
      }
    },
    delListPres(p) {
      console.log(p.rowIndex)
      if (p.rowIndex > -1) { // only splice array when item is found
        this.listPrestamo.splice(p.rowIndex, 1); // 2nd parameter means remove one item only
      }
    },
    impresionList() {
      this.$axios.post(process.env.API + '/regprestamo', {listado: this.listPrestamo}).then(res => {
        console.log(res.data)
        //return false
        let cliente = res.data[0].cliente
        let user = res.data[0].user
        let fecha = res.data[0].fecha
        let prt = false
        let contenido = ''
        res.data.forEach(r => {
          if (r.estado == 'EN PRESTAMO') {
            r.estado = 'PRESTAMO'
            prt = true
          }
          contenido += `<tr>
                  <td>` + r.cantidad + `</td>
                  <td>` + r.inventario.nombre + `</td>
                  <td>` + r.efectivo + `</td>
                  <td>` + r.estado + `</td>
                  </tr>
        `
        })
        let cadena = `
        <style>
        .textc{text-align:center}
        .leyenda{text-align:justify;
        size-font 6px;}
        </style>
        <div style="padding:5px">
         <div style='text-align:center; font-size:10px;'>Detalle de Prestamo o <br> Venta de Envases</div>
        <div style='text-align:right'>` + fecha + `</div>
        <div><b>Nombre:</b> ` + cliente.titular + `</div>
        <div><b>Usuario: </b>` + user.name + `</div>
        <table style="width: 100%;">
        <tr><th>CANT</th><th>MATERIAL</th><th>MONTO</th><th>TIPO</th></tr>` + contenido + `</table>
        <hr style=" border: 4px dashed;"/>`
        if (prt) {
          cadena += `<div style='text-align:right'>` + fecha + `</div>
        <div><b>NOMBRE</b> ` + cliente.titular + `</div>
        <table style="width: 100%; ">
        <tr><th>CANT</th><th>MATERIAL</th><th>MONTO</th><th>TIPO</th></tr>` + contenido + `</table>
        <br>
        <br>
        <div class="textc">Firma</div>
        <br>
        <div class="leyenda"><b>*  Accepto todas las condiciones y terminos de prestamo de envases </b></div></div>`
        }
        console.log(cadena)
        let myWindow = window.open("", "Imprimir", "width=1000,height=1000")
        myWindow.document.write(cadena)
        myWindow.document.close()
        myWindow.print()
        myWindow.close()
        this.dialogReg = false
        this.listPrestamo = []
        this.filtrarlista()
        this.cajaprestamo()
      })
    },
    delDevuelto(devol) {
      this.$q.dialog({
        title: 'Eliminar',
        message: 'Esta seguro de Eliminar Devolucion'
      }).onOk(() => {
        this.$axios.delete(process.env.API + '/logprestamo/' + devol.id).then(res => {
          this.dialog_list = false
          this.$axios.get(process.env.API + '/inventario').then(res => {
            // console.log(res.data)
            this.inventarios = res.data;
            this.inventario = this.inventarios[0];
          })
          this.listclientes();
          this.cajaprestamo();
          this.listadoprestamo();
          this.reporte();
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
    verTelef(dato) {
      this.$q.dialog({
        title: 'Telefono Cliente: ' + dato.cliente.titular,
        message: '<h6>' + dato.cliente.telefono + '</h6>',
        html: true
      }).onOk(() => {
        // console.log('OK')
      }).onCancel(() => {
        // console.log('Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    verFisico(dato) {
      this.$q.dialog({
        title: ' Cliente: ' + dato.cliente.titular,
        message: dato.fisico
      }).onOk(() => {
        // console.log('OK')
      }).onCancel(() => {
        // console.log('Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    calcular() {
      this.efectivo = parseFloat(this.cantidad) * parseFloat(this.inventario.precio)
    },
    verObs(prest) {
      this.$q.dialog({
        title: 'Observacion',
        message: prest.observacion
      }).onOk(() => {
        // console.log('OK')
      }).onCancel(() => {
        // console.log('Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },
    filterFn(val, update) {
      if (val === '') {
        update(() => {
          this.prestamos = this.options
          // with Quasar v1.7.4+
          // here you have access to "ref" which
          // is the Vue reference of the QSelect
        })
        return
      }
      update(() => {
        const needle = val.toLowerCase()
        this.prestamos = this.options.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
      })
    },
    reporte() {
      this.$axios.post(process.env.API + '/reportecliente').then(res => {
        // console.log(res.data);
        this.reportepres = [];
        $('#example').DataTable().destroy();

        res.data.forEach(element => {
          if (this.tab == 1 && element.tipocliente == 1)
            this.reportepres.push(element);
          if (this.tab == 2 && element.tipocliente == 2)
            this.reportepres.push(element);
        });
        this.$nextTick(() => {
          $('#example').DataTable({
            "language": {
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
            }, dom: 'Blfrtip',
            buttons: [
              'excel', 'pdf'
            ],
            "lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]]
          });
        })


      })

    },
    listadoprestamo() {
      this.$axios.post(process.env.API + '/listPag', {
        estado: this.estado, tipo: this.tab, page: this.pagination.page,
        filter: this.filter
      }).then(res => {
        console.log(res.data);
        // return false
        this.listadop = [];
        this.pagination.rowsNumber = res.data.total
        this.pagination.page = res.data.current_page
        this.pagination.last_page = res.data.last_page
        res.data.data.forEach(element => {

          this.listadop.push(element)
        });

      })
    },
    onDev(p) {
      // console.log(p);
      this.datoprestamo = p;
      this.dialog_dev = true;
    },
    onEliminar(p) {
      this.$q.dialog({
        title: 'Dar de Baja',
        message: 'Motivo de Baja',
        prompt: {
          model: '',
          type: 'textarea'
        }
      }).onOk((data) => {
        p.motivo = data;
        this.$axios.post(process.env.API + '/anularprestamo', p).then(res => {
          // this.totalefectivo=res.data[0].total;
          this.cajaprestamo()
          this.listadoprestamo()
          console.log(res.data)
        })
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })

    },
    ondelete(p) {
      if (confirm('seguro de ELIMINAR?')) {
        this.$axios.delete(process.env.API + '/prestamo/' + p.id).then(res => {
          // this.totalefectivo=res.data[0].total;
          this.cajaprestamo()
          this.listadoprestamo()
          console.log(res.data)
        })
      }
    },
    onList(p) {
      this.listado = p.logprestamos;
      this.dialog_list = true;
    },
    cajaprestamo() {
      this.$axios.post(process.env.API + '/tefectivo').then(res => {
        this.totalefectivo = res.data[0].total;
      })

    },
    filtrarlista() {
      this.listadoprestamo();
      this.listclientes();
      this.reporte();
    },
    modificar() {
      if (this.inventario.cantidad < this.cantidad) {
        this.$q.notify({
          message: 'No ay sufuciente en Inventario',
          color: 'red',
          icon: 'warning'
        })
        return false;
      }
      this.$q.loading.show();
      this.$axios.post(process.env.API + '/modprestamo', {
        id: this.prestamo_id,
        efectivo: this.efectivo,
        fisico: this.fisico,
        fecha: this.fecha,
        observacion: this.observacion,
        cantidad: this.cantidad,
        cliente_id: this.cliente.id,
        inventario_id: this.inventario.id,
      }).then(res => {
        // console.log(res.data)
        this.listadoprestamo();
        this.boolmod = false
        this.dialogReg = false
        this.$q.loading.hide();
        // this.cliente=this.prestamos[0]
        this.cantidad = 1;
        this.cliente = {label: ''};
        this.inventario = this.inventarios[0];
        this.efectivo = 0
        this.observacion = ''
      })
      this.filtrarlista();
      this.cajaprestamo();
    },
    listclientes() {
      this.$q.loading.show()
      this.$axios.get(process.env.API + '/listacliente').then(res => {
        console.log(res.data)
        this.prestamos = []
        res.data.forEach(r => {
          if (this.tab == 1 && r.tipocliente == 1) {
            r.label = r.ci + ' ' + r.local + ' ' + r.titular
            this.prestamos.push(r)
          }
          if (this.tab == 2 && r.tipocliente == 2) {
            r.label = r.ci + ' ' + r.titular
            this.prestamos.push(r)
          }
        })
        this.options = this.prestamos
        this.cliente = {label: ''}
        this.$q.loading.hide()
      })
    },
    misprestamos() {
      this.$q.loading.show()
      this.$axios.get(process.env.API + '/cliente').then(res => {
        // console.log(res.data)
        this.prestamos = [];
        res.data.forEach(r => {
          r.label = r.ci + ' ' + r.local + ' ' + r.titular
          this.prestamos.push(r)
        });
        this.options = this.prestamos
        this.$q.loading.hide();
        this.cliente = this.prestamos[0];
      })
    },
    agregar() {
      if (this.inventario.cantidad < this.cantidad) {
        this.$q.notify({
          message: 'No ay sufuciente en Inventario',
          color: 'red',
          icon: 'warning'
        })
        return false;
      }
      if (this.cliente.id == undefined) {
        this.$q.notify({
          message: 'Tienes que seleccionar cliente',
          color: 'red',
          icon: 'error'
        })
        return false;
      }
      this.listPrestamo.push({
        fecha: this.fecha,
        cliente_id: this.cliente.id,
        efectivo: this.efectivo,
        fisico: this.fisico,
        observacion: this.observacion,
        cantidad: this.cantidad,
        tipo: this.tipo,
        inventario_id: this.inventario.id,
        inventario: this.inventario.nombre
      })
      this.cantidad = 1
      this.efectivo = 0
      this.fisico = ''
      this.observacion = ''
      this.inventario = this.inventario[0]
      this.calcular
      /*this.$q.loading.show();
      this.$axios.post(process.env.API+'/prestamo',{
        efectivo:this.efectivo,
        fisico:this.fisico,
        observacion:this.observacion,
        cantidad:this.cantidad,
        cliente_id:this.cliente.id,
        tipo:this.tipo,
        fecha:this.fecha,
        inventario_id:this.inventario.id,
      }).then(res=>{
        this.listPrestamo.push(res.data)
        // console.log(res.data)
        /*let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.print();
        myWindow.close();
        // this.prestamos=res.data
        this.listadoprestamo();
        this.$q.loading.hide();
        // this.cliente=this.prestamos[0]
        this.cantidad=1;
      this.fecha=date.formatDate(new Date(),'YYYY-MM-DD')

        this.cliente=this.cliente[0];
        this.inventario=this.inventario[0];
        this.calcular
      })
      this.filtrarlista();
      this.cajaprestamo();
      })*/
    },
    onMod(prop) {
      console.log(prop)
      this.boolmod = true
      this.dialogReg = true
      this.prestamo_id = prop.id
      this.efectivo = prop.efectivo;
      this.fisico = prop.fisico;
      this.observacion = prop.observacion;
      this.cantidad = prop.cantidad;
      this.cliente = prop.cliente;
      this.cliente.label = this.cliente.ci + ' ' + this.cliente.local + ' ' + this.cliente.titular
      this.inventario = prop.inventario;
      this.fecha = prop.fecha;
    },

    devolver() {
      this.$q.loading.show()
      this.$axios.post(process.env.API + '/logprestamo', {
        fecha: date.formatDate(new Date(), 'YYYY-MM-DD'),
        cantidad: this.dev.cantidad,
        motivo: this.dev.motivo,
        inventario_id: this.datoprestamo.inventario_id,
        id: this.datoprestamo.id,
      }).then(res => {
        console.log(res.data);

        let myWindow = window.open("", "Imprimir", "width=1000,height=1000");
        myWindow.document.write(res.data);
        myWindow.document.close();
        myWindow.focus();
        // setTimeout(function(){
        myWindow.print();
        myWindow.close();
        // this.prestamos=res.data
        this.dialog_dev = false;
        this.listadoprestamo();
        this.cajaprestamo()
        this.dev = {};
        this.$q.loading.hide();
      })
    },
    cambiofecha(fec) {
      return moment(fec).format('DD/MM/YYYY')
    },
    imprimir() {

      let mc = this

      function header() {
        var img = new Image()
        img.src = 'logo.png'
        doc.addImage(img, 'jpg', 0.5, 0.5, 2, 2)
        doc.setFont(undefined, 'bold')
        doc.text(5, 1, 'Historial de prestamos Pendientes')

        doc.text(1, 3, 'Local')
        doc.text(5, 3, 'Titular')
        doc.text(11, 3, 'Material')
        doc.text(15, 3, 'Cantidad')
        doc.text(17.5, 3, 'Monto')
        // doc.text(18.5, 3, 'Usuario')
        doc.setFont(undefined, 'normal')
      }

      var doc = new jsPDF('p', 'cm', 'letter')
      // console.log(dat);
      doc.setFont("courier");
      doc.setFontSize(9);
      // var x=0,y=
      header()
      // let xx=x
      // let yy=y
      let y = 0
      let suma = 0
      this.reportepres.forEach(r => {
        // xx+=0.5
        y += 0.5
        doc.text(1, y + 3, '' + r.local)
        doc.text(5, y + 3, '' + r.titular)
        doc.text(11, y + 3, '' + r.nombre)
        doc.text(16.5, y + 3, '' + r.total, {align: 'right'})
        if (r.monto != null) {
          doc.text(18, y + 3, '' + r.monto, {align: 'right'})
          suma += r.monto;
        } else
          doc.text(17, y + 3, 'sin garantía')
        if (y + 3 > 25) {
          doc.addPage();
          header()
          y = 0
        }
      })
      doc.setFont(undefined, 'bold')
      doc.text(16.5, y + 3.5, 'TOTAL')
      doc.text(18, y + 3.5, '' + suma + ' Bs');
      doc.setFont(undefined, 'normal')
      window.open(doc.output('bloburl'), '_blank');
    },

  }
}
</script>

<style scoped>
/*.table , th , td  {*/
/*  border: 0.2px solid black;*/
/*  border-collapse: collapse;*/
/*}*/
</style>
