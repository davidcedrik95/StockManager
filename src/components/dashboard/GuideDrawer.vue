<template>
  <v-navigation-drawer
    v-model="internalValue"
    location="right"
    width="400"
    temporary
  >
    <v-card flat>
      <v-card-title class="d-flex justify-space-between align-center">
        <span>Produkt-Guide</span>
        <v-btn icon @click="close">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </v-card-title>
      <v-divider />
      <v-card-text>
        <v-stepper vertical>
          <v-stepper-item title="Allgemeine Informationen">
            Name, Marke und Kategorie sind Pflichtfelder
          </v-stepper-item>
          <v-stepper-item title="Preis & Artikelnummer">
            Preis und eindeutige Artikelnummer hinzufügen
          </v-stepper-item>
          <v-stepper-item title="Bilder">
            Hauptbild + Galerie hinzufügen
          </v-stepper-item>
          <v-stepper-item title="Technische Spezifikationen">
            Abhängig vom gewählten Artikeltyp
          </v-stepper-item>
          <v-stepper-item title="Lieferung">
            Versandkosten und Lieferzeit festlegen
          </v-stepper-item>
        </v-stepper>
      </v-card-text>
    </v-card>
  </v-navigation-drawer>
</template>

<script setup>
import { computed } from 'vue'   // WICHTIG: computed importieren

const props = defineProps({
  modelValue: Boolean
})
const emit = defineEmits(['update:modelValue'])

const internalValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const close = () => {
  internalValue.value = false
}
</script>