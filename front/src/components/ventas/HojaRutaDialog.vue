<template>
  <q-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)">
    <q-card style="width: 800px; max-width: 96vw;">
      <q-card-section class="row items-center q-pb-none">
        <div>
          <div class="text-subtitle1 text-weight-bold">Hoja de ruta #{{ venta?.id || row?.id }}</div>
          <div class="text-caption text-grey-7">{{ venta?.cliente_nombre || row?.cliente_nombre || '-' }}</div>
        </div>
        <q-space />
        <q-btn icon="close" flat round dense v-close-popup />
      </q-card-section>
      <q-card-section>
        <q-form @submit.prevent="guardarEImprimir">
          <div class="row q-col-gutter-sm">
            <div class="col-12 col-md-4">
              <q-input v-model="form.hoja_fecha_entrega" type="date" dense outlined label="Fecha entrega" />
            </div>
            <div class="col-12 col-md-4">
              <q-select
                v-model="form.hoja_turno"
                dense
                outlined
                emit-value
                map-options
                :options="turnoOptions"
                label="Turno"
              />
            </div>
            <div class="col-12 col-md-4">
              <q-input v-model="form.hoja_hora" type="text" dense outlined label="Hora" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.hoja_telefono_1" dense outlined label="Telefono 1" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model="form.hoja_telefono_2" dense outlined label="Telefono 2" />
            </div>
            <div class="col-12">
              <q-input v-model="form.hoja_direccion" dense outlined label="Direccion" />
            </div>
            <div class="col-12">
              <q-input v-model="form.hoja_envases" dense outlined type="textarea" autogrow label="Envases" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model.number="form.hoja_cuenta" type="number" min="0" step="0.01" dense outlined label="A cuenta" suffix="Bs" />
            </div>
            <div class="col-12 col-md-6">
              <q-input v-model.number="form.hoja_saldo" type="number" min="0" step="0.01" dense outlined label="Saldo" suffix="Bs" />
            </div>
            <div class="col-12">
              <q-input v-model="form.hoja_observaciones" dense outlined type="textarea" autogrow label="Observaciones" />
            </div>

            <div class="col-12" v-if="venta">
              <q-separator class="q-mb-sm" />
              <div class="row items-center q-gutter-sm q-mb-sm">
                <q-icon name="place" color="red-6" />
                <span class="text-caption text-weight-medium">Ubicacion de entrega</span>
                <q-space />
                <q-btn
                  dense
                  no-caps
                  flat
                  color="grey-8"
                  icon="my_location"
                  label="Usar mi ubicacion"
                  size="sm"
                  @click="usarUbicacionActual"
                />
                <q-btn
                  dense
                  no-caps
                  flat
                  color="primary"
                  icon="content_copy"
                  label="Copiar coordenadas"
                  size="sm"
                  @click="copiarCoordenadas"
                />
                <q-btn
                  dense
                  no-caps
                  flat
                  color="green-7"
                  icon="open_in_new"
                  label="Ver en Google Maps"
                  size="sm"
                  @click="abrirGoogleMaps"
                />
                <q-btn
                  dense
                  no-caps
                  unelevated
                  color="positive"
                  icon="chat"
                  label="Compartir por WhatsApp"
                  size="sm"
                  @click="compartirWhatsapp"
                />
              </div>

              <div class="row q-col-gutter-sm q-mb-sm">
                <div class="col-6">
                  <q-input v-model.number="form.hoja_lat" type="number" step="0.0000001" dense outlined label="Latitud" />
                </div>
                <div class="col-6">
                  <q-input v-model.number="form.hoja_lng" type="number" step="0.0000001" dense outlined label="Longitud" />
                </div>
              </div>

              <div class="row items-center q-mb-xs">
                <div class="text-caption text-grey-7">
                  <q-icon name="touch_app" size="14px" /> Arrastra el marcador o haz clic en el mapa para fijar la ubicacion de entrega
                  <span v-if="!tieneUbicacion"> (sin coordenadas guardadas — mostrando el centro de Oruro por defecto)</span>
                </div>
                <q-space />
                <q-btn-toggle
                  v-model="mapaCapa"
                  dense
                  no-caps
                  unelevated
                  size="sm"
                  toggle-color="primary"
                  color="grey-3"
                  text-color="black"
                  :options="[
                    { label: 'Satelite', value: 's', icon: 'satellite_alt' },
                    { label: 'Calle', value: 'm', icon: 'map' }
                  ]"
                />
              </div>
              <l-map
                key="hoja-ruta-map"
                style="height: 260px; width: 100%; border-radius: 8px;"
                :zoom="tieneUbicacion ? 16 : 13"
                :center="mapaCenter"
                :use-global-leaflet="false"
                @click="onMapClick"
              >
                <l-tile-layer
                  :key="mapaCapa"
                  :url="`https://mt{s}.google.com/vt/lyrs=${mapaCapa}&x={x}&y={y}&z={z}`"
                  :subdomains="['0','1','2','3']"
                  :max-zoom="20"
                />
                <l-marker
                  :lat-lng="mapaCenter"
                  :draggable="true"
                  @dragend="onMarkerDragEnd"
                >
                  <l-popup>
                    <div style="min-width: 160px; text-align: center;">
                      <b>{{ venta.cliente_nombre }}</b><br>
                      <span style="font-size:11px;">{{ mapaCenter[0].toFixed(6) }}, {{ mapaCenter[1].toFixed(6) }}</span>
                    </div>
                  </l-popup>
                </l-marker>
              </l-map>
            </div>
          </div>
          <div class="row justify-end q-gutter-sm q-mt-md">
            <q-btn flat no-caps label="Cancelar" v-close-popup />
            <q-btn color="primary" no-caps icon="print" label="Guardar e imprimir" type="submit" :loading="loading" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import 'leaflet/dist/leaflet.css'
import { LMap, LMarker, LPopup, LTileLayer } from '@vue-leaflet/vue-leaflet'
import L from 'leaflet'
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png'
import markerIcon from 'leaflet/dist/images/marker-icon.png'
import markerShadow from 'leaflet/dist/images/marker-shadow.png'
import { Imprimir } from 'src/addons/Imprimir'

delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({ iconRetinaUrl: markerIcon2x, iconUrl: markerIcon, shadowUrl: markerShadow })

const ORURO_CENTER = [-17.9645186, -67.124877]

export default {
  name: 'HojaRutaDialog',
  components: { LMap, LTileLayer, LMarker, LPopup },
  props: {
    modelValue: {
      type: Boolean,
      default: false
    },
    row: {
      type: Object,
      default: null
    }
  },
  emits: ['update:modelValue', 'saved'],
  data () {
    return {
      loading: false,
      venta: null,
      mapaCapa: 's',
      turnoOptions: [
        { label: 'Mañana', value: 'mañana' },
        { label: 'Tarde', value: 'tarde' },
        { label: 'Noche', value: 'noche' }
      ],
      form: this.emptyForm()
    }
  },
  computed: {
    tieneUbicacion () {
      return this.form.hoja_lat != null && this.form.hoja_lng != null &&
        this.form.hoja_lat !== '' && this.form.hoja_lng !== '' &&
        !Number.isNaN(Number(this.form.hoja_lat)) && !Number.isNaN(Number(this.form.hoja_lng))
    },
    mapaCenter () {
      return this.tieneUbicacion ? [Number(this.form.hoja_lat), Number(this.form.hoja_lng)] : ORURO_CENTER
    }
  },
  watch: {
    modelValue (value) {
      if (value) this.cargar()
    }
  },
  methods: {
    emptyForm () {
      return {
        hoja_fecha_entrega: '',
        hoja_turno: 'mañana',
        hoja_hora: '',
        hoja_telefono_1: '',
        hoja_telefono_2: '',
        hoja_direccion: '',
        hoja_lat: null,
        hoja_lng: null,
        hoja_envases: '',
        hoja_cuenta: 0,
        hoja_saldo: 0,
        hoja_observaciones: ''
      }
    },
    async cargar () {
      if (!this.row?.id) return
      this.loading = true
      try {
        const res = await this.$axios.get(`ventas/${this.row.id}`)
        this.venta = res.data
        this.form = this.buildForm(this.venta)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo cargar la hoja de ruta')
        this.$emit('update:modelValue', false)
      } finally {
        this.loading = false
      }
    },
    buildForm (venta) {
      const fecha = venta.hoja_fecha_entrega || venta.fecha || new Date().toISOString().slice(0, 10)
      const hora = venta.hoja_hora || (venta.hora ? String(venta.hora).slice(0, 5) : '')
      return {
        hoja_fecha_entrega: String(fecha).slice(0, 10),
        hoja_turno: venta.hoja_turno || 'mañana',
        hoja_hora: hora,
        hoja_telefono_1: venta.hoja_telefono_1 || venta.cliente_telefono || '',
        hoja_telefono_2: venta.hoja_telefono_2 || '',
        hoja_direccion: venta.hoja_direccion || venta.cliente_direccion || '',
        hoja_lat: venta.hoja_lat ?? venta.cliente_lat ?? null,
        hoja_lng: venta.hoja_lng ?? venta.cliente_lng ?? null,
        hoja_envases: venta.hoja_envases || this.envasesDefault(venta),
        hoja_cuenta: Number(venta.hoja_cuenta ?? venta.total_pagado ?? 0),
        hoja_saldo: Number(venta.hoja_saldo ?? venta.saldo_pendiente ?? venta.total ?? 0),
        hoja_observaciones: venta.hoja_observaciones || venta.observacion || ''
      }
    },
    envasesDefault (venta) {
      return (venta.prestamos || [])
        .map(p => `${p.cantidad || 0} ${p.inventario?.nombre || 'material'}`)
        .join(', ')
    },
    copiarCoordenadas () {
      const [lat, lng] = this.mapaCenter
      const texto = `${lat.toFixed(6)}, ${lng.toFixed(6)}`
      navigator.clipboard.writeText(texto).then(() => {
        this.$alert.success('Coordenadas copiadas: ' + texto)
      }).catch(() => {
        this.$alert.error('No se pudo copiar al portapapeles')
      })
    },
    ubicacionUrl () {
      const [lat, lng] = this.mapaCenter
      return `https://www.google.com/maps?q=${lat.toFixed(7)},${lng.toFixed(7)}`
    },
    abrirGoogleMaps () {
      window.open(this.ubicacionUrl(), '_blank', 'noopener')
    },
    setUbicacion (lat, lng) {
      this.form.hoja_lat = Number(lat.toFixed(7))
      this.form.hoja_lng = Number(lng.toFixed(7))
    },
    onMapClick (e) {
      this.setUbicacion(e.latlng.lat, e.latlng.lng)
    },
    onMarkerDragEnd (e) {
      const pos = e.target.getLatLng()
      this.setUbicacion(pos.lat, pos.lng)
    },
    usarUbicacionActual () {
      if (!navigator.geolocation) {
        this.$alert.error('El navegador no soporta geolocalizacion')
        return
      }
      navigator.geolocation.getCurrentPosition(
        pos => {
          this.setUbicacion(pos.coords.latitude, pos.coords.longitude)
          this.$alert.success('Ubicacion actual capturada')
        },
        () => {
          this.$alert.error('No se pudo obtener la ubicacion actual')
        },
        { enableHighAccuracy: true, timeout: 10000 }
      )
    },
    compartirWhatsapp () {
      const cliente = this.venta?.cliente_nombre || 'cliente'
      const mensaje = `Hoja de ruta #${this.venta?.id} - ${cliente}\nUbicacion de entrega: ${this.ubicacionUrl()}`
      const telefono = String(this.form.hoja_telefono_1 || '').replace(/\D/g, '')
      const destino = telefono ? (telefono.startsWith('591') ? telefono : `591${telefono}`) : ''
      const url = `https://wa.me/${destino}?text=${encodeURIComponent(mensaje)}`
      window.open(url, '_blank', 'noopener')
    },
    async guardarEImprimir () {
      if (!this.venta?.id) return
      this.loading = true
      try {
        const res = await this.$axios.put(`ventas/${this.venta.id}/hoja-ruta`, this.form)
        this.venta = res.data
        this.form = this.buildForm(this.venta)
        this.$emit('saved', this.venta)
        this.$emit('update:modelValue', false)
        Imprimir.hojaRuta(this.venta)
      } catch (e) {
        this.$alert.error(e.response?.data?.message || 'No se pudo guardar la hoja de ruta')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>
