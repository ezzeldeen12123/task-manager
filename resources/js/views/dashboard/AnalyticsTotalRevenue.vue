<script setup>
import { hexToRgb } from '@layouts/utils';
import VueApexCharts from 'vue3-apexcharts';
import {
  useTheme
} from 'vuetify';

const vuetifyTheme = useTheme()
const props = defineProps({
  title: {
    type: String,
    required: true,
  }, 
  value: {
    type: Number,
    required: true,
  }, 
  color: {
    type: String,
    required: true,
  }
})

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  const primaryTextColor = `rgba(${ hexToRgb(String(currentTheme['on-surface'])) },${ variableTheme['high-emphasis-opacity'] })`

  return {
    radial: {
      chart: { sparkline: { enabled: true } },
      labels: [props.title],
      stroke: { dashArray: 5 },
      colors: [props.color],
      states: {
        hover: { filter: { type: 'none' } },
        active: { filter: { type: 'none' } },
      },
      fill: {
        type: 'gradient',
        gradient: {
          shade: 'dark',
          opacityTo: 0.6,
          opacityFrom: 1,
          shadeIntensity: 0.5,
          stops: [
            30,
            70,
            100,
          ],
          inverseColors: false,
          gradientToColors: [props.color],
        },
      },
      plotOptions: {
        radialBar: {
          endAngle: 150,
          startAngle: -140,
          hollow: { size: '55%' },
          track: { background: 'transparent' },
          dataLabels: {
            name: {
              offsetY: 25,
              fontWeight: 600,
              fontSize: '16px',
              color: currentTheme.secondary,
              fontFamily: 'Public Sans',
            },
            value: {
              offsetY: -15,
              fontWeight: 500,
              fontSize: '24px',
              color: primaryTextColor,
              fontFamily: 'Public Sans',
            },
          },
        },
      },
      responsive: [
        {
          breakpoint: 900,
          options: { chart: { height: 200 } },
        },
        {
          breakpoint: 735,
          options: { chart: { height: 200 } },
        },
        {
          breakpoint: 660,
          options: { chart: { height: 200 } },
        },
        {
          breakpoint: 600,
          options: { chart: { height: 280 } },
        },
      ],
    },
  }
})
</script>

<template>
  <VCard>
    <VRow no-gutters>
      <VCol
        cols="12"
        sm="12"
        xl="12"
      >
        <VCardText class="text-center">
          <!-- <VBtn
            size="small"
            variant="tonal"
            append-icon="bx-chevron-down"
            class="mt-4"
          >
            2023
            <VMenu activator="parent">
              <VList>
                <VListItem
                  v-for="(item, index) in ['2023', '2022', '2021']"
                  :key="index"
                  :value="item"
                >
                  <VListItemTitle>{{ item }}</VListItemTitle>
                </VListItem>
              </VList>
            </VMenu>
          </VBtn> -->

          <p class="font-weight-medium text-high-emphasis mb-3">
            Tasks
          </p>

          <!-- radial chart -->
          <VueApexCharts
            type="radialBar"
            :height="200"
            :options="chartOptions.radial"
            :series="[props.value]"
            class="mt-2"
          />
        </VCardText>
      </VCol>
    </VRow>
  </VCard>
</template>

<style lang="scss">
#bar-chart .apexcharts-series[rel="2"] {
  transform: translateY(-10px);
}
</style>
