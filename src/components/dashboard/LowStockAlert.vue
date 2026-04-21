<template>
  <v-card rounded="lg" elevation="2" style="height: 100%; display: flex; flex-direction: column;">
    <v-card-title class="text-h6 pa-3 bg-grey-lighten-4">
      ⚠️ Produits en rupture de stock
    </v-card-title>

    <v-card-text class="pa-3" style="flex: 1;">
      <div v-if="outOfStockProducts.length === 0" class="text-center pa-6 text-grey">
        ✅ Aucun produit en rupture
      </div>

      <div v-else>
        <!-- Pie chart : répartition des ruptures par catégorie -->
        <canvas ref="pieChartCanvas" style="max-height: 220px; width: 100%;"></canvas>

        <!-- Légende interactive -->
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
              <v-chip color="error" size="x-small">Rupture</v-chip>
            </template>
          </v-list-item>
        </v-list>
      </div>
    </v-card-text>

    <v-divider v-if="outOfStockProducts.length > 0"></v-divider>
    <v-card-actions v-if="outOfStockProducts.length > 0" class="pa-2">
      <v-btn color="warning" variant="tonal" block size="small" @click="viewAllOutOfStock">
        Voir tous les produits en rupture
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

// Produits en rupture (in_stock = false)
const outOfStockProducts = computed(() =>
  (productStore.products || []).filter(p => p.in_stock === false || p.in_stock === 0)
)

// Couleurs par catégorie (cohérent avec PercentChart)
const categoryColors = {
  'cardio': '#00b5e9',
  'strength': '#fa4251',
  'rehabilitation': '#00ad5f',
  'accessories': '#ffc107',
  'Autre': '#9c27b0'
}

// Données pour le pie chart : regrouper par catégorie
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
              return `${label}: ${value} produit(s) (${percentage}%)`
            }
          }
        },
        legend: { display: false }
      }
    }
  })
}

const viewProduct = (product) => {
  // Redirige vers la page de détail du produit (à adapter si vous avez une route)
  router.push(`/products/${product.id}`)
}

const viewAllOutOfStock = () => {
  // Par exemple, redirige vers la liste des produits avec un filtre "rupture"
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