<template>
  <q-page class="q-pa-md">
    <div class="row q-col-gutter-md">
      <div class="col-12 col-lg-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <div>
              <div class="text-h6">Impuestos</div>
              <div class="text-caption text-grey-7">Generacion de CUIS y CUFD para SIAT piloto</div>
            </div>
            <q-space />
            <q-btn flat dense icon="refresh" label="Actualizar" no-caps @click="cargarEstado" :loading="loading" />
          </q-card-section>
          <q-separator />
          <q-card-section class="q-gutter-sm">
            <q-banner rounded :class="estado.configurado ? 'bg-positive text-white' : 'bg-negative text-white'">
              {{ estado.configurado ? 'Variables SIAT configuradas' : 'Faltan variables SIAT en el backend' }}
            </q-banner>
            <q-list dense bordered separator>
              <q-item>
                <q-item-section>
                  <q-item-label>Ambiente</q-item-label>
                  <q-item-label caption>{{ estado.ambiente ?? '-' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Modalidad</q-item-label>
                  <q-item-label caption>{{ estado.modalidad ?? '-' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Sucursal / Punto de venta</q-item-label>
                  <q-item-label caption>{{ estado.codigo_sucursal ?? 0 }} / {{ estado.codigo_punto_venta ?? 0 }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>URL SIAT</q-item-label>
                  <q-item-label caption class="ellipsis">{{ estado.url || '-' }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-lg-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <div class="text-subtitle1 text-weight-bold">CUIS</div>
            <q-space />
            <q-btn color="primary" no-caps icon="vpn_key" label="Generar CUIS" @click="generarCuis" :loading="loadingCuis" />
          </q-card-section>
          <q-separator />
          <q-card-section>
            <q-list dense bordered separator>
              <q-item>
                <q-item-section>
                  <q-item-label>Codigo</q-item-label>
                  <q-item-label caption class="text-weight-bold">{{ estado.cuis?.codigo || 'Sin CUIS vigente' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Creacion</q-item-label>
                  <q-item-label caption>{{ formatDateTime(estado.cuis?.fechaCreacion) }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Vigencia</q-item-label>
                  <q-item-label caption>{{ formatDateTime(estado.cuis?.fechaVigencia) }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>

      <div class="col-12 col-lg-4">
        <q-card flat bordered>
          <q-card-section class="row items-center">
            <div class="text-subtitle1 text-weight-bold">CUFD</div>
            <q-space />
            <q-btn color="primary" no-caps icon="approval" label="Generar CUFD" @click="generarCufd" :loading="loadingCufd" />
          </q-card-section>
          <q-separator />
          <q-card-section>
            <q-list dense bordered separator>
              <q-item>
                <q-item-section>
                  <q-item-label>Codigo</q-item-label>
                  <q-item-label caption class="text-weight-bold">{{ estado.cufd?.codigo || 'Sin CUFD vigente' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Control</q-item-label>
                  <q-item-label caption>{{ estado.cufd?.codigoControl || '-' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Direccion</q-item-label>
                  <q-item-label caption>{{ estado.cufd?.direccion || '-' }}</q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>Vigencia</q-item-label>
                  <q-item-label caption>{{ formatDateTime(estado.cufd?.fechaVigencia) }}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
export default {
  name: 'ImpuestosPage',
  data () {
    return {
      loading: false,
      loadingCuis: false,
      loadingCufd: false,
      estado: {}
    }
  },
  mounted () {
    this.cargarEstado()
  },
  methods: {
    formatDateTime (value) {
      if (!value) return '-'
      return String(value).replace('T', ' ').slice(0, 19)
    },
    async cargarEstado () {
      this.loading = true
      try {
        const res = await this.$axios.get('impuestos/estado')
        this.estado = res.data || {}
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar el estado de impuestos')
      } finally {
        this.loading = false
      }
    },
    async generarCuis () {
      this.loadingCuis = true
      try {
        const res = await this.$axios.post('impuestos/cuis')
        this.$alert.success(res.data?.message || 'CUIS generado')
        await this.cargarEstado()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo generar CUIS')
      } finally {
        this.loadingCuis = false
      }
    },
    async generarCufd () {
      this.loadingCufd = true
      try {
        const res = await this.$axios.post('impuestos/cufd')
        this.$alert.success(res.data?.message || 'CUFD generado')
        await this.cargarEstado()
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo generar CUFD')
      } finally {
        this.loadingCufd = false
      }
    }
  }
}
</script>
