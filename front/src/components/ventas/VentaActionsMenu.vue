<template>
  <q-btn-dropdown dense color="primary" label="Opciones" no-caps>
    <q-list dense>
      <q-item clickable v-close-popup @click="$emit('view', row)">
        <q-item-section avatar><q-icon name="visibility" color="primary" /></q-item-section>
        <q-item-section>Ver</q-item-section>
      </q-item>
      <q-item clickable v-close-popup @click="$emit('route-sheet', row)">
        <q-item-section avatar><q-icon name="route" color="teal" /></q-item-section>
        <q-item-section>Hoja de ruta</q-item-section>
      </q-item>
      <q-item clickable v-close-popup @click="$emit('print-route-sheet', row)">
        <q-item-section avatar><q-icon name="print" color="teal" /></q-item-section>
        <q-item-section>Imprimir hoja de ruta</q-item-section>
      </q-item>
      <q-item clickable v-close-popup @click="$emit('print-route-map', row)">
        <q-item-section avatar><q-icon name="map" color="teal" /></q-item-section>
        <q-item-section>Imprimir mapa</q-item-section>
      </q-item>
      <q-item clickable v-close-popup @click="$emit('print', row)">
        <q-item-section avatar><q-icon name="print" color="deep-orange" /></q-item-section>
        <q-item-section>Imprimir</q-item-section>
      </q-item>
      <q-item clickable v-close-popup :disable="!row?.cuf" @click="$emit('print-tax', row)">
        <q-item-section avatar><q-icon name="verified" color="primary" /></q-item-section>
        <q-item-section>Imprimir impuestos</q-item-section>
      </q-item>
      <q-item clickable v-close-popup :disable="row?.estado === 'ANULADA'" @click="$emit('cancel', row)">
        <q-item-section avatar><q-icon name="block" color="negative" /></q-item-section>
        <q-item-section>Anular</q-item-section>
      </q-item>
    </q-list>
  </q-btn-dropdown>
</template>

<script>
export default {
  name: 'VentaActionsMenu',
  props: {
    row: {
      type: Object,
      required: true
    }
  },
  emits: ['view', 'route-sheet', 'print-route-sheet', 'print-route-map', 'print', 'print-tax', 'cancel']
}
</script>
