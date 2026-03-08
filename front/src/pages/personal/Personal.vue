<template>
  <q-page class="q-pa-md">
    <q-card flat bordered class="q-mb-md">
      <q-card-section class="row items-center">
        <div>
          <div class="text-h6">Personal</div>
          <div class="text-caption text-grey-7">Gestion de personal del sistema Naty</div>
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
              <div class="text-caption text-grey-7">Total personal</div>
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
      :rows="personales"
      :columns="columns"
      row-key="id"
      dense
      flat
      bordered
      wrap-cells
      :filter="filter"
      v-model:pagination="pagination"
      :rows-per-page-options="[25, 50, 100]"
      loading-label="Cargando..."
      no-data-label="Sin registros"
    >
      <template #top-right>
        <q-btn color="positive" label="Nuevo" icon="add_circle_outline" no-caps class="q-mr-sm" :loading="loading" @click="nuevo" />
        <q-btn color="primary" label="Actualizar" icon="refresh" no-caps :loading="loading" @click="getData" />
      </template>

      <template #body-cell-actions="props">
        <q-td :props="props" class="text-left">
          <q-btn-dropdown dense color="primary" label="Opciones" no-caps size="10px">
            <q-list dense>
              <q-item clickable v-close-popup @click="editar(props.row)">
                <q-item-section avatar><q-icon name="edit" /></q-item-section>
                <q-item-section><q-item-label>Editar</q-item-label></q-item-section>
              </q-item>
              <q-item clickable v-close-popup @click="eliminar(props.row.id)">
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
            <q-img v-if="props.row.fotografia" :src="imgPersonal(props.row.fotografia)" />
            <q-icon v-else name="person" />
          </q-avatar>
        </q-td>
      </template>

      <template #body-cell-salario="props">
        <q-td :props="props" class="text-right">{{ money(props.row.salario) }}</q-td>
      </template>

      <template #body-cell-estado="props">
        <q-td :props="props">
          <q-chip dense :color="props.row.estado === 'ACTIVO' ? 'positive' : 'grey-7'" text-color="white">
            {{ props.row.estado }}
          </q-chip>
        </q-td>
      </template>
    </q-table>

    <q-dialog v-model="dialog" persistent>
      <q-card style="width: 620px; max-width: 96vw;">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-subtitle1 text-weight-bold">{{ item.id ? 'Editar personal' : 'Nuevo personal' }}</div>
          <q-space />
          <q-btn icon="close" flat round dense @click="dialog = false" />
        </q-card-section>
        <q-card-section class="q-pt-sm">
          <q-form @submit.prevent="guardar">
            <div class="row q-col-gutter-sm">
              <div class="col-12 col-md-4"><q-input v-model="item.ci" label="CI" dense outlined :rules="[req]" /></div>
              <div class="col-12 col-md-8"><q-input v-model="item.nombre" label="Nombre" dense outlined :rules="[req]" /></div>
              <div class="col-12 col-md-4"><q-input v-model.number="item.salario" label="Salario" type="number" min="0" step="0.01" dense outlined /></div>
              <div class="col-12 col-md-4"><q-input v-model="item.fechanac" label="Fecha nac." type="date" dense outlined /></div>
              <div class="col-12 col-md-4"><q-input v-model.number="item.dias" label="Dias" type="number" min="0" dense outlined /></div>
              <div class="col-12 col-md-4"><q-input v-model="item.celular" label="Celular" dense outlined /></div>
              <div class="col-12 col-md-4"><q-input v-model="item.tipo" label="Tipo" dense outlined /></div>
              <div class="col-12 col-md-4"><q-input v-model="item.fechaingreso" label="Fecha ingreso" type="date" dense outlined /></div>
              <div class="col-12 col-md-4">
                <q-select v-model="item.estado" dense outlined emit-value map-options label="Estado" :options="estadoOptions" />
              </div>
            </div>

            <q-file v-model="fotoFile" dense outlined accept="image/*" label="Fotografia" class="q-mt-sm" />
            <div v-if="item.fotografia || fotoPreview" class="q-mt-sm q-mb-md">
              <q-img :src="fotoPreview || imgPersonal(item.fotografia)" style="max-width: 220px; border-radius: 8px;" />
            </div>

            <div class="row justify-end q-gutter-sm">
              <q-btn color="negative" flat no-caps label="Cancelar" @click="dialog = false" :disable="loading" />
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
  name: 'PersonalPage',
  data () {
    return {
      personales: [],
      item: {},
      dialog: false,
      loading: false,
      filter: '',
      fotoFile: null,
      fotoPreview: null,
      pagination: {
        page: 1,
        rowsPerPage: 50,
        sortBy: 'id',
        descending: true
      },
      estadoOptions: [
        { label: 'ACTIVO', value: 'ACTIVO' },
        { label: 'INACTIVO', value: 'INACTIVO' }
      ],
      columns: [
        { name: 'actions', label: '', align: 'left' },
        { name: 'fotografia', label: 'Foto', align: 'left', field: 'fotografia' },
        { name: 'ci', label: 'CI', align: 'left', field: 'ci' },
        { name: 'nombre', label: 'Nombre', align: 'left', field: 'nombre' },
        { name: 'salario', label: 'Salario', align: 'right', field: 'salario' },
        { name: 'celular', label: 'Celular', align: 'left', field: 'celular' },
        { name: 'tipo', label: 'Tipo', align: 'left', field: 'tipo' },
        { name: 'fechaingreso', label: 'Ingreso', align: 'left', field: 'fechaingreso' },
        { name: 'estado', label: 'Estado', align: 'left', field: 'estado' }
      ]
    }
  },
  computed: {
    kpi () {
      const total = this.personales.length
      const activos = this.personales.filter(p => p.estado === 'ACTIVO').length
      return { total, activos, inactivos: total - activos }
    }
  },
  watch: {
    fotoFile (file) {
      if (!file) {
        this.fotoPreview = null
        return
      }
      this.fotoPreview = URL.createObjectURL(file)
    }
  },
  mounted () {
    this.getData()
  },
  methods: {
    req (v) { return !!v || 'Campo requerido' },
    money (n) { return Number(n || 0).toFixed(2) },
    imgPersonal (foto) { return `${this.$url}../../images/personales/${foto}` },
    nuevo () {
      this.item = {
        ci: '',
        nombre: '',
        salario: 0,
        fechanac: null,
        dias: 0,
        celular: '',
        tipo: '',
        fechaingreso: null,
        estado: 'ACTIVO',
        fotografia: null
      }
      this.fotoFile = null
      this.fotoPreview = null
      this.dialog = true
    },
    editar (row) {
      this.item = { ...row, salario: Number(row.salario || 0), dias: Number(row.dias || 0) }
      this.fotoFile = null
      this.fotoPreview = null
      this.dialog = true
    },
    getData () {
      this.loading = true
      this.$axios.get('personales')
        .then(res => { this.personales = res.data || [] })
        .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo cargar personal'))
        .finally(() => { this.loading = false })
    },
    async guardar () {
      this.loading = true
      try {
        const fd = new FormData()
        fd.append('ci', this.item.ci || '')
        fd.append('nombre', this.item.nombre || '')
        fd.append('salario', this.item.salario ?? 0)
        fd.append('fechanac', this.item.fechanac || '')
        fd.append('dias', this.item.dias ?? 0)
        fd.append('celular', this.item.celular || '')
        fd.append('tipo', this.item.tipo || '')
        fd.append('fechaingreso', this.item.fechaingreso || '')
        fd.append('estado', this.item.estado || 'ACTIVO')
        if (this.fotoFile) fd.append('fotografia', this.fotoFile)

        if (this.item.id) {
          fd.append('_method', 'PUT')
          await this.$axios.post(`personales/${this.item.id}`, fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
          this.$alert.success('Personal actualizado')
        } else {
          await this.$axios.post('personales', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
          this.$alert.success('Personal creado')
        }
        this.dialog = false
        this.getData()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar personal')
      } finally {
        this.loading = false
      }
    },
    eliminar (id) {
      this.$alert.dialog('Desea eliminar este registro?')
        .onOk(() => {
          this.loading = true
          this.$axios.delete(`personales/${id}`)
            .then(() => {
              this.$alert.success('Personal eliminado')
              this.getData()
            })
            .catch(e => this.$alert.error(e.response?.data?.message || 'No se pudo eliminar'))
            .finally(() => { this.loading = false })
        })
    }
  }
}
</script>

