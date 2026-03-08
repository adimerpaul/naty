<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <div>
          <div class="text-h6 text-title">{{ tituloPagina }}</div>
          <div class="text-caption text-grey-7">
            Gestion de productos de compras y ventas
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
            <q-avatar color="primary" text-color="white" icon="inventory_2" />
            <div class="q-ml-md">
              <div class="text-caption text-grey-7">Total productos</div>
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
              <div class="text-caption text-grey-7">Activos</div>
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
              <div class="text-caption text-grey-7">Inactivos</div>
              <div class="text-h6 text-weight-bold">{{ kpi.inactivos }}</div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <q-table
      :rows="productos"
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
          @click="productoNuevo"
        />
        <q-btn
          color="primary"
          label="Actualizar"
          icon="refresh"
          no-caps
          :loading="loading"
          @click="productosGet"
        />
      </template>

      <template #body-cell-actions="props">
        <q-td :props="props" class="text-left">
          <q-btn-dropdown dense color="primary" label="Opciones" no-caps size="10px">
            <q-list dense>
              <q-item clickable v-close-popup @click="productoEditar(props.row)">
                <q-item-section avatar><q-icon name="edit" /></q-item-section>
                <q-item-section><q-item-label>Editar</q-item-label></q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="productoEliminar(props.row.id)">
                <q-item-section avatar><q-icon name="delete" color="negative" /></q-item-section>
                <q-item-section><q-item-label>Eliminar</q-item-label></q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
        </q-td>
      </template>

      <template #body-cell-fotografia="props">
        <q-td :props="props">
          <q-avatar rounded size="38px">
            <q-img v-if="props.row.fotografia" :src="imgProducto(props.row.fotografia)" />
            <q-icon v-else name="image_not_supported" />
          </q-avatar>
        </q-td>
      </template>

      <template #body-cell-color="props">
        <q-td :props="props">
          <div class="row items-center q-gutter-xs">
            <div class="color-box" :style="{ backgroundColor: props.row.color || '#ffffff' }" />
            <span>{{ props.row.color }}</span>
          </div>
        </q-td>
      </template>

      <template #body-cell-estado="props">
        <q-td :props="props">
          <q-chip dense :color="props.row.estado ? 'positive' : 'grey-7'" text-color="white">
            {{ props.row.estado ? 'Activo' : 'Inactivo' }}
          </q-chip>
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialogProducto" persistent>
      <q-card style="width: 560px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">
            {{ producto.id ? 'Editar producto' : 'Nuevo producto' }}
          </div>
          <q-space />
          <q-btn icon="close" flat round dense @click="dialogProducto = false" />
        </q-card-section>

        <q-card-section class="q-pt-sm">
          <q-form @submit.prevent="productoGuardar">
            <q-input v-model="producto.nombre" label="Nombre" dense outlined :rules="[req]" class="q-mb-sm" />
            <q-input v-model.number="producto.precio" label="Precio" type="number" step="0.01" dense outlined :rules="[req]" class="q-mb-sm" />
            <q-input v-model="producto.observacion" label="Observacion" dense outlined class="q-mb-sm" />

            <div class="row q-col-gutter-sm">
              <div class="col-6">
                <q-select
                  v-model="producto.grupo"
                  :options="grupoOptions"
                  option-label="label"
                  option-value="value"
                  emit-value
                  map-options
                  label="Grupo"
                  dense
                  outlined
                  :rules="[req]"
                  class="q-mb-sm"
                />
              </div>
              <div class="col-6">
                <q-input v-model.number="producto.orden" label="Orden" type="number" dense outlined :rules="[req]" class="q-mb-sm" />
              </div>
            </div>

            <div class="row q-col-gutter-sm">
              <div class="col-6">
                <q-input v-model="producto.color" label="Color" type="color" dense outlined class="q-mb-sm" />
              </div>
              <div class="col-6">
                <q-toggle v-model="producto.estado" label="Activo" color="positive" class="q-mt-sm" />
              </div>
            </div>

            <q-file
              v-model="fotoFile"
              dense
              outlined
              accept="image/*"
              label="Fotografia"
              class="q-mb-sm"
            />

            <div v-if="producto.fotografia || fotoPreview" class="q-mb-md">
              <q-img :src="fotoPreview || imgProducto(producto.fotografia)" style="max-width: 220px; border-radius: 8px;" />
            </div>

            <div class="row justify-end q-gutter-sm">
              <q-btn color="negative" flat no-caps label="Cancelar" @click="dialogProducto = false" :disable="loading" />
              <q-btn color="primary" no-caps label="Guardar" type="submit" :loading="loading" />
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
export default {
  name: 'ProductosPage',
  data () {
    return {
      productos: [],
      producto: {},
      dialogProducto: false,
      loading: false,
      filter: '',
      fotoFile: null,
      fotoPreview: null,
      pagination: {
        page: 1,
        rowsPerPage: 25,
        sortBy: 'orden',
        descending: false
      },
      grupoOptions: [
        { label: 'Chicha', value: 'chicha' },
        { label: 'Garapina', value: 'garapina' }
      ],
      columns: [
        { name: 'actions', label: 'Acciones', align: 'left' },
        { name: 'fotografia', label: 'Foto', align: 'left', field: 'fotografia' },
        { name: 'nombre', label: 'Nombre', align: 'left', field: 'nombre' },
        { name: 'grupo', label: 'Grupo', align: 'left', field: 'grupo' },
        { name: 'precio', label: 'Precio', align: 'left', field: 'precio' },
        { name: 'orden', label: 'Orden', align: 'left', field: 'orden' },
        { name: 'color', label: 'Color', align: 'left', field: 'color' },
        { name: 'estado', label: 'Estado', align: 'left', field: 'estado' }
      ]
    }
  },
  computed: {
    tipoProducto () {
      return this.$route.params.tipo === 'local' ? 'local' : 'detalle'
    },
    tituloPagina () {
      return this.tipoProducto === 'local' ? 'Producto local' : 'Producto detalle'
    },
    kpi () {
      const total = this.productos.length
      const activos = this.productos.filter(p => !!p.estado).length
      return { total, activos, inactivos: total - activos }
    }
  },
  watch: {
    '$route.params.tipo' () {
      this.productosGet()
    },
    fotoFile (file) {
      if (!file) {
        this.fotoPreview = null
        return
      }
      this.fotoPreview = URL.createObjectURL(file)
    }
  },
  mounted () {
    this.productosGet()
  },
  methods: {
    req (v) {
      return !!v || 'Campo requerido'
    },
    imgProducto (foto) {
      return `${this.$url}../../images/productos/${foto}`
    },
    productoNuevo () {
      this.producto = {
        nombre: '',
        precio: 0,
        observacion: '',
        grupo: 'chicha',
        orden: 1,
        color: '#ffffff',
        estado: true,
        fotografia: null
      }
      this.fotoFile = null
      this.fotoPreview = null
      this.dialogProducto = true
    },
    productoEditar (row) {
      this.producto = { ...row, precio: Number(row.precio), orden: Number(row.orden || 1) }
      this.fotoFile = null
      this.fotoPreview = null
      this.dialogProducto = true
    },
    productosGet () {
      this.loading = true
      this.$axios.get('productos', { params: { tipo_producto: this.tipoProducto } })
        .then(res => { this.productos = res.data })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar productos'))
        .finally(() => { this.loading = false })
    },
    async productoGuardar () {
      this.loading = true
      try {
        const fd = new FormData()
        fd.append('nombre', this.producto.nombre || '')
        fd.append('precio', this.producto.precio ?? 0)
        fd.append('observacion', this.producto.observacion || '')
        fd.append('grupo', this.producto.grupo || 'chicha')
        fd.append('tipo_producto', this.tipoProducto)
        fd.append('orden', this.producto.orden ?? 1)
        fd.append('color', this.producto.color || '#ffffff')
        fd.append('estado', this.producto.estado ? '1' : '0')
        if (this.fotoFile) fd.append('fotografia', this.fotoFile)

        if (this.producto.id) {
          fd.append('_method', 'PUT')
          await this.$axios.post(`productos/${this.producto.id}`, fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
          this.$alert.success('Producto actualizado')
        } else {
          await this.$axios.post('productos', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
          this.$alert.success('Producto creado')
        }

        this.dialogProducto = false
        this.productosGet()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar')
      } finally {
        this.loading = false
      }
    },
    productoEliminar (id) {
      this.$alert.dialog('Desea eliminar el producto?')
        .onOk(() => {
          this.loading = true
          this.$axios.delete(`productos/${id}`)
            .then(() => {
              this.$alert.success('Producto eliminado')
              this.productosGet()
            })
            .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo eliminar'))
            .finally(() => { this.loading = false })
        })
    }
  }
}
</script>

<style scoped>
.color-box {
  width: 18px;
  height: 18px;
  border-radius: 4px;
  border: 1px solid rgba(0, 0, 0, .2);
}
</style>

