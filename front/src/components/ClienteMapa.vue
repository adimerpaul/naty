<template>
  <div>
    <div class="row items-center q-gutter-sm q-mb-sm">
      <q-btn
        dense
        color="primary"
        icon="my_location"
        label="Donde estas"
        no-caps
        @click="usarMiUbicacion"
      />
      <div class="text-caption text-grey-7">
        Haz clic en el mapa para fijar la ubicacion del cliente.
      </div>
    </div>

    <l-map
      ref="mapRef"
      style="height: 260px; width: 100%; border-radius: 10px;"
      :zoom="zoom"
      :center="center"
      :use-global-leaflet="false"
      @click="mapClick"
    >
      <l-tile-layer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        layer-type="base"
        name="OpenStreetMap"
      />
      <l-marker v-if="hasMarker" :lat-lng="marker">
        <l-popup>
          Ubicacion seleccionada<br>
          Lat: {{ marker[0].toFixed(6) }}<br>
          Lng: {{ marker[1].toFixed(6) }}
        </l-popup>
      </l-marker>
    </l-map>

    <div class="text-caption q-mt-sm">
      Lat: {{ latText }} | Lng: {{ lngText }}
    </div>
  </div>
</template>

<script>
import 'leaflet/dist/leaflet.css'
import { LMap, LMarker, LPopup, LTileLayer } from '@vue-leaflet/vue-leaflet'
import L from 'leaflet'
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png'
import markerIcon from 'leaflet/dist/images/marker-icon.png'
import markerShadow from 'leaflet/dist/images/marker-shadow.png'

delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2x,
  iconUrl: markerIcon,
  shadowUrl: markerShadow
})

const DEFAULT_CENTER = [-17.9647, -67.1060] // Oruro

export default {
  name: 'ClienteMapa',
  components: {
    LMap,
    LTileLayer,
    LMarker,
    LPopup
  },
  props: {
    lat: {
      type: [Number, String, null],
      default: null
    },
    lng: {
      type: [Number, String, null],
      default: null
    }
  },
  emits: ['update:lat', 'update:lng'],
  data () {
    return {
      zoom: 14,
      center: DEFAULT_CENTER
    }
  },
  computed: {
    hasMarker () {
      return this.lat !== null && this.lng !== null && this.lat !== '' && this.lng !== ''
    },
    marker () {
      return [Number(this.lat), Number(this.lng)]
    },
    latText () {
      return this.hasMarker ? Number(this.lat).toFixed(6) : '-'
    },
    lngText () {
      return this.hasMarker ? Number(this.lng).toFixed(6) : '-'
    }
  },
  mounted () {
    if (this.hasMarker) {
      this.center = [Number(this.lat), Number(this.lng)]
    }
  },
  watch: {
    lat () {
      if (this.hasMarker) {
        this.center = [Number(this.lat), Number(this.lng)]
      }
    },
    lng () {
      if (this.hasMarker) {
        this.center = [Number(this.lat), Number(this.lng)]
      }
    }
  },
  methods: {
    emitirUbicacion (lat, lng) {
      this.$emit('update:lat', Number(lat.toFixed(7)))
      this.$emit('update:lng', Number(lng.toFixed(7)))
      this.center = [lat, lng]
    },
    mapClick (e) {
      this.emitirUbicacion(e.latlng.lat, e.latlng.lng)
    },
    usarMiUbicacion () {
      if (!navigator.geolocation) {
        this.$emit('update:lat', null)
        this.$emit('update:lng', null)
        return
      }
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          this.emitirUbicacion(pos.coords.latitude, pos.coords.longitude)
        },
        () => {
          // No-op: el usuario puede seleccionar manualmente en el mapa.
        },
        { enableHighAccuracy: true, timeout: 10000 }
      )
    }
  }
}
</script>
