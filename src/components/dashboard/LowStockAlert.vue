<template>
  <v-card rounded="lg" elevation="2" style="height: 100%; display: flex; flex-direction: column;">
    <!-- Modernisierte Kopfzeile -->
    <v-card-title class="d-flex justify-space-between align-center bg-grey-lighten-4 px-4 py-3">
      <span class="text-h6 font-weight-semibold">
        Niedriger Lagerbestand
      </span>

      <div class="d-flex align-center ga-1">
        <v-btn icon variant="text" size="small" color="grey-darken-1">
          <v-icon size="20">mdi-fullscreen</v-icon>
        </v-btn>
        <v-btn icon variant="text" size="small" color="grey-darken-1">
          <v-icon size="20">mdi-refresh</v-icon>
        </v-btn>
      </div>
    </v-card-title>

    <v-card-text class="pa-3" style="flex: 1;">
      <div v-if="outOfStockProducts.length === 0" class="text-center pa-6 text-grey">
        ✅ Keine Produkte ausverkauft
      </div>

      <div v-else>
        <!-- Kreisdiagramm: Verteilung der Ausverkäufe nach Kategorie -->
        <canvas ref="pieChartCanvas" style="max-height: 220px; width: 100%;"></canvas>

        <!-- Interaktive Legende -->
        <v-list density="compact" class="mt-2">
          <v-list-item
            v-for="(product, index) in outOfStockProducts"
            :key="product.id"
            @click="viewProduct(product)"
            style="cursor: pointer;"
          >
            <template v-slot:prepend>
              <div :style="{ width: '12px', height: '12px', borderRadius: '50%', backgroundColor: categoryColors[product.category] }"></div>
            </template>
            <v-list-item-title>
              {{ product.name }}
              <span class="text-caption text-grey">({{ product.brand }})</span>
            </v-list-item-title>
            <template v-slot:append>
              <v-chip color="error" size="x-small">Ausverkauft</v-chip>
            </template>
          </v-list-item>
        </v-list>
      </div>
    </v-card-text>

    <v-divider v-if="outOfStockProducts.length > 0"></v-divider>
    <v-card-actions v-if="outOfStockProducts.length > 0" class="pa-2">
      <v-btn color="warning" variant="tonal" block size="small" @click="viewAllOutOfStock">
        Alle ausverkauften Produkte anzeigen
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Chart, registerables } from 'chart.js'
import { useProductStore } from '../../stores/stock_manager_products'

Chart.register(...registerables)

const router = useRouter()
const productStore = useProductStore()

// Ausverkaufte Produkte (in_stock = false)
const outOfStockProducts = computed(() =>
  (productStore.products || []).filter(p => p.in_stock === false || p.in_stock === 0)
)

// Farben je Kategorie (konsistent mit PercentChart)
const categoryColors = {
  'cardio': '#00b5e9',
  'strength': '#fa4251',
  'rehabilitation': '#00ad5f',
  'accessories': '#ffc107',
  'Autre': '#9c27b0'
}

// Daten für das Kreisdiagramm: Gruppierung nach Kategorie
const categoryStats = computed(() => {
  const groups = {}
  outOfStockProducts.value.forEach(p => {
    const cat = p.category || 'Autre'
    groups[cat] = (groups[cat] || 0) + 1
  })
  return Object.entries(groups).map(([category, count]) => ({
    category,
    count,
    color: categoryColors[category] || '#607d8b'
  }))
})

const pieChartCanvas = ref(null)
let chartInstance = null

const renderPieChart = () => {
  if (!pieChartCanvas.value || categoryStats.value.length === 0) return
  if (chartInstance) chartInstance.destroy()

  const ctx = pieChartCanvas.value.getContext('2d')
  chartInstance = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: categoryStats.value.map(s => s.category),
      datasets: [{
        data: categoryStats.value.map(s => s.count),
        backgroundColor: categoryStats.value.map(s => s.color),
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        tooltip: {
          callbacks: {
            label: (context) => {
              const label = context.label || ''
              const value = context.raw
              const total = context.dataset.data.reduce((a, b) => a + b, 0)
              const percentage = ((value / total) * 100).toFixed(1)
              return `${label}: ${value} Produkt(e) (${percentage}%)`
            }
          }
        },
        legend: { display: false }
      }
    }
  })
}

const viewProduct = (product) => {
  // Leitet zur Produktdetailseite weiter (ggf. anpassen)
  router.push(`/products/${product.id}`)
}

const viewAllOutOfStock = () => {
  // Z. B. Weiterleitung zur Produktliste mit Filter "ausverkauft"
  router.push('/products?filter=out_of_stock')
}

watch(categoryStats, () => renderPieChart(), { deep: true })

onMounted(() => {
  renderPieChart()
})

onBeforeUnmount(() => {
  if (chartInstance) chartInstance.destroy()
})
</script>

<style scoped>
canvas {
  max-height: 220px;
  width: 100%;
  margin-bottom: 8px;
}
</style>