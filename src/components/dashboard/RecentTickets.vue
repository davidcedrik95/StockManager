<template>
  <v-container fluid class="pa-4 pa-md-6">
    <v-row>
      <!-- Colonne gauche : Tickets (inchangée) -->
      <v-col cols="12" lg="8">
        <v-card elevation="2" rounded="lg" >
          <v-card-title class="text-h5 py-3 bg-grey-lighten-3">
            Aktuelle Tickets
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-0">
            <v-table density="comfortable" class="table-earnings">
              <thead>
                <tr>
                  <th>Datum</th>
                  <th>Ref.-Nr.</th>
                  <th>Betreff</th>
                  <th>Kategorie</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in earningsItems" :key="item.refNr">
                  <td>{{ item.date }}</td>
                  <td>{{ item.refNr }}</td>
                  <td>{{ item.name }}</td>
                  <td>{{ item.kategorie }}</td>
                  <td>
                    <v-chip :color="getStatusColor(item.status)" size="small" label>
                      {{ item.status }}
                    </v-chip>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Colonne droite : Graphique des ventes mensuelles (taille caractères agrandie) -->
      <v-col cols="12" lg="4">
        <v-card elevation="4" rounded="lg" class="sales-chart-card">
          <v-card-title class="text-subtitle-1 py-2 bg-grey-lighten-3">
            <v-icon start size="small">mdi-chart-line</v-icon>
            Monatliche Verkäufe
          </v-card-title>
          <v-divider></v-divider>
          <v-card-text class="pa-2">
            <canvas ref="salesChartCanvas" style="width: 100%; height: 220px;"></canvas>
            <div class="text-center text-body-2 text-grey mt-2 font-weight-medium">
              {{ totalSales }} € Gesamtumsatz (letzte 6 Monate)
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

// ----- Tickets -----
const getStatusColor = (status) => {
  const colors = {
    'In Bearbeitung': 'warning',
    'In Prüfung': 'info',
    'Zurückgestellt': 'orange-darken-2',
    'Abgeschlossen': 'success',
    'Storniert': 'error',
    'Abgelehnt': 'red-darken-2',
    'In Planung': 'primary'
  }
  return colors[status] || 'grey'
}

const earningsItems = [
  { date: '2025-01-15 14:32', refNr: '100398', name: 'Sicherheitstechnische Kontrolle (STK)', kategorie: 'Dienstleistungen', status: 'In Bearbeitung' },
  { date: '2025-01-14 10:18', refNr: '100397', name: 'DGÜV-Prüfung', kategorie: 'Dienstleistungen', status: 'In Prüfung' },
  { date: '2025-01-13 16:45', refNr: '100396', name: 'Serviceanforderung', kategorie: 'Services Formular', status: 'Zurückgestellt' },
  { date: '2025-01-10 11:55', refNr: '100392', name: 'Installationsanforderung', kategorie: 'Services Formular', status: 'Abgelehnt' },
  { date: '2025-01-09 15:42', refNr: '100391', name: 'Angebot', kategorie: 'Online-Shop', status: 'In Planung' }
]

// Données de ventes (6 derniers mois)
const monthlySales = ref([
  { month: 'Aug', sales: 12450 },
  { month: 'Sep', sales: 18920 },
  { month: 'Okt', sales: 15780 },
  { month: 'Nov', sales: 22340 },
  { month: 'Dez', sales: 28900 },
  { month: 'Jan', sales: 20560 }
])

const totalSales = computed(() => {
  return monthlySales.value.reduce((sum, item) => sum + item.sales, 0).toLocaleString('de-DE')
})

// ----- Graphique linéaire avec polices agrandies -----
const salesChartCanvas = ref(null)
let chartInstance = null

const renderSalesChart = () => {
  if (!salesChartCanvas.value) return
  if (chartInstance) chartInstance.destroy()

  const labels = monthlySales.value.map(item => item.month)
  const data = monthlySales.value.map(item => item.sales)

  const ctx = salesChartCanvas.value.getContext('2d')
  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Umsatz (€)',
        data: data,
        borderColor: '#2e7d32',
        backgroundColor: 'rgba(46, 125, 50, 0.1)',
        borderWidth: 2.5,
        pointBackgroundColor: '#2e7d32',
        pointBorderColor: '#fff',
        pointRadius: 4,
        pointHoverRadius: 6,
        tension: 0.3,
        fill: true
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.raw.toLocaleString('de-DE')} €`
          },
          titleFont: { size: 13, weight: 'bold' },
          bodyFont: { size: 12 }
        },
        legend: {
          position: 'top',
          labels: {
            font: { size: 12, weight: 'bold' },
            boxWidth: 12,
            padding: 10
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: (value) => value.toLocaleString('de-DE') + ' €',
            font: { size: 11, weight: '500' }
          },
          title: {
            display: true,
            text: 'Umsatz (€)',
            font: { size: 12, weight: 'bold' }
          }
        },
        x: {
          ticks: {
            font: { size: 11, weight: '500' }
          },
          title: {
            display: true,
            text: 'Monat',
            font: { size: 12, weight: 'bold' }
          }
        }
      }
    }
  })
}

onMounted(() => {
  renderSalesChart()
})
</script>

<style scoped>
.sales-chart-card {
  background: white;
  border-left: 4px solid #2e7d32;
}
canvas {
  max-height: 220px;
  width: 100%;
}

</style>