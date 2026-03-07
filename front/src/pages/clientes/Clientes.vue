<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <div>
          <div class="text-h6 text-title">{{ tituloPagina }}</div>
          <div class="text-caption text-grey-7">
            Gestion de clientes de compras y ventas
          </div>
        </div>
        <q-space />
        <q-input v-model="filter" label="Buscar" dense outlined debounce="250" style="width: 280px">
          <template #append><q-icon name="search" /></template>
        </q-input>
      </q-card-section>
    </q-card>

    <div class="row q-col-gutter-md q-mb-md">
      <div class="col-12 col-sm-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <q-avatar color="primary" text-color="white" icon="groups" />
            <div class="q-ml-md">
              <div class="text-caption text-grey-7">Total clientes</div>
              <div class="text-h6 text-weight-bold">{{ kpi.total }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <q-avatar color="positive" text-color="white" icon="check_circle" />
            <div class="q-ml-md">
              <div class="text-caption text-grey-7">Clientes activos</div>
              <div class="text-h6 text-weight-bold">{{ kpi.activos }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
      <div class="col-12 col-sm-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <q-avatar color="negative" text-color="white" icon="cancel" />
            <div class="q-ml-md">
              <div class="text-caption text-grey-7">Clientes inactivos</div>
              <div class="text-h6 text-weight-bold">{{ kpi.inactivos }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-table
      :rows="clientes"
      :columns="columns"
      row-key="id"
      dense
      flat
      bordered
      wrap-cells
      :filter="filter"
      v-model:pagination="pagination"
      :rows-per-page-options="[10, 25, 50, 100]"
      loading-label="Cargando..."
      no-data-label="Sin registros"
    >
      <template #top-right>
        <q-btn
          color="positive"
          label="Nuevo"
          icon="add_circle_outline"
          no-caps
          class="q-mr-sm"
          :loading="loading"
          @click="clienteNuevo"
        />
        <q-btn
          color="primary"
          label="Actualizar"
          icon="refresh"
          no-caps
          class="q-mr-sm"
          :loading="loading"
          @click="clientesGet"
        />
        <q-btn
          color="indigo"
          label="Excel"
          icon="table_view"
          no-caps
          class="q-mr-sm"
          :loading="loading"
          @click="exportExcel"
        />
        <q-btn
          color="deep-orange"
          label="PDF"
          icon="picture_as_pdf"
          no-caps
          :loading="loadingPdf"
          @click="exportPdf"
        />
      </template>

      <template #body-cell-estado="props">
        <q-td :props="props">
          <q-chip
            dense
            :color="props.row.estado ? 'positive' : 'grey-7'"
            text-color="white"
          >
            {{ props.row.estado ? 'Activo' : 'Inactivo' }}
          </q-chip>
        </q-td>
      </template>

      <template #body-cell-actions="props">
        <q-td :props="props" class="text-left">
          <q-btn-dropdown
            dense
            color="primary"
            label="Opciones"
            no-caps
            size="10px"
          >
            <q-list dense>
              <q-item clickable v-close-popup @click="clienteEditar(props.row)">
                <q-item-section avatar>
                  <q-icon name="edit" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Editar</q-item-label>
                </q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="clienteEliminar(props.row.id)">
                <q-item-section avatar>
                  <q-icon name="delete" color="negative" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>Eliminar</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialogCliente" persistent>
      <q-card style="width: 520px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            {{ cliente.id ? 'Editar cliente' : 'Nuevo cliente' }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense @click="dialogCliente = false" />
        </q-card-section>

        <q-card-section class="q-pt-sm">
          <q-form @submit.prevent="cliente.id ? clientePut() : clientePost()">
            <q-input v-model="cliente.nombre" label="Nombre" dense outlined :rules="[req]" class="q-mb-sm" />
            <q-input v-model="cliente.ci" label="CI" dense outlined class="q-mb-sm" />
            <q-input v-model="cliente.telefono" label="Telefono" dense outlined class="q-mb-sm" />
            <q-input v-model="cliente.direccion" label="Direccion" dense outlined class="q-mb-sm" />
            <q-input v-model="cliente.observacion" type="textarea" autogrow label="Observacion" dense outlined class="q-mb-sm" />
            <cliente-mapa
              class="q-mb-md"
              :lat="cliente.lat"
              :lng="cliente.lng"
              @update:lat="cliente.lat = $event"
              @update:lng="cliente.lng = $event"
            />

            <q-toggle v-model="cliente.estado" label="Activo" color="positive" class="q-mb-md" />

            <div class="row justify-end q-gutter-sm">
              <q-btn color="negative" flat no-caps label="Cancelar" @click="dialogCliente = false" :disable="loading" />
              <q-btn color="primary" no-caps label="Guardar" type="submit" :loading="loading" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import ClienteMapa from 'src/components/ClienteMapa.vue'

export default {
  name: 'ClientesPage',
  components: { ClienteMapa },

  data () {
    return {
      clientes: [],
      cliente: {},
      dialogCliente: false,
      loading: false,
      loadingPdf: false,
      filter: '',
      pagination: {
        page: 1,
        rowsPerPage: 25,
        sortBy: 'id',
        descending: true
      },
      columns: [
        { name: 'actions', label: 'Acciones', align: 'left' },
        { name: 'nombre', label: 'Nombre', align: 'left', field: 'nombre' },
        { name: 'ci', label: 'CI', align: 'left', field: 'ci' },
        { name: 'telefono', label: 'Telefono', align: 'left', field: 'telefono' },
        { name: 'direccion', label: 'Direccion', align: 'left', field: 'direccion' },
        { name: 'lat', label: 'Lat', align: 'left', field: 'lat' },
        { name: 'lng', label: 'Lng', align: 'left', field: 'lng' },
        { name: 'estado', label: 'Estado', align: 'left', field: 'estado' }
      ]
    }
  },

  computed: {
    tipoCliente () {
      return this.$route.params.tipo === 'local' ? 'local' : 'detalle'
    },
    tituloPagina () {
      return this.tipoCliente === 'local' ? 'Cliente local' : 'Cliente detalle'
    },
    kpi () {
      const total = this.clientes.length
      const activos = this.clientes.filter(c => !!c.estado).length
      return {
        total,
        activos,
        inactivos: total - activos
      }
    }
  },

  watch: {
    '$route.params.tipo' () {
      this.clientesGet()
    }
  },

  mounted () {
    this.clientesGet()
  },

  methods: {
    req (v) {
      return !!v || 'Campo requerido'
    },

    clienteNuevo () {
      this.cliente = {
        nombre: '',
        ci: '',
        telefono: '',
        direccion: '',
        observacion: '',
        lat: null,
        lng: null,
        estado: true
      }
      this.dialogCliente = true
    },

    clienteEditar (row) {
      this.cliente = {
        ...row,
        lat: row.lat !== null ? Number(row.lat) : null,
        lng: row.lng !== null ? Number(row.lng) : null
      }
      this.dialogCliente = true
    },

    clientesGet () {
      this.loading = true
      this.$axios.get('clientes', { params: { tipo_cliente: this.tipoCliente } })
        .then(res => { this.clientes = res.data })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar clientes'))
        .finally(() => { this.loading = false })
    },

    clientePost () {
      this.loading = true
      this.$axios.post('clientes', { ...this.cliente, tipo_cliente: this.tipoCliente })
        .then(() => {
          this.dialogCliente = false
          this.$alert.success('Cliente creado')
          this.clientesGet()
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo crear'))
        .finally(() => { this.loading = false })
    },

    clientePut () {
      this.loading = true
      this.$axios.put(`clientes/${this.cliente.id}`, { ...this.cliente, tipo_cliente: this.tipoCliente })
        .then(() => {
          this.dialogCliente = false
          this.$alert.success('Cliente actualizado')
          this.clientesGet()
        })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo actualizar'))
        .finally(() => { this.loading = false })
    },

    clienteEliminar (id) {
      this.$alert.dialog('¿Desea eliminar el cliente?')
        .onOk(() => {
          this.loading = true
          this.$axios.delete(`clientes/${id}`)
            .then(() => {
              this.$alert.success('Cliente eliminado')
              this.clientesGet()
            })
            .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo eliminar'))
            .finally(() => { this.loading = false })
        })
    },

    exportExcel () {
      const headers = ['ID', 'Nombre', 'Tipo', 'CI', 'Telefono', 'Direccion', 'Observacion', 'Lat', 'Lng', 'Estado']
      const trs = this.clientes.map(r => `
        <tr>
          <td>${r.id ?? ''}</td>
          <td>${r.nombre ?? ''}</td>
          <td>${r.tipo_cliente ?? ''}</td>
          <td>${r.ci ?? ''}</td>
          <td>${r.telefono ?? ''}</td>
          <td>${r.direccion ?? ''}</td>
          <td>${r.observacion ?? ''}</td>
          <td>${r.lat ?? ''}</td>
          <td>${r.lng ?? ''}</td>
          <td>${r.estado ? 'Activo' : 'Inactivo'}</td>
        </tr>
      `).join('')

      const html = `
        <table border="1">
          <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
          <tbody>${trs}</tbody>
        </table>
      `
      const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8;' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `clientes-${this.tipoCliente}.xls`
      document.body.appendChild(a)
      a.click()
      a.remove()
      URL.revokeObjectURL(url)
    },

    async exportPdf () {
      this.loadingPdf = true
      try {
        const res = await this.$axios.get('clientes/pdf', {
          params: { tipo_cliente: this.tipoCliente },
          responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([res.data], { type: 'application/pdf' }))
        const a = document.createElement('a')
        a.href = url
        a.download = `clientes-${this.tipoCliente}.pdf`
        document.body.appendChild(a)
        a.click()
        a.remove()
        window.URL.revokeObjectURL(url)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo generar PDF')
      } finally {
        this.loadingPdf = false
      }
    }
  }
}
</script>
