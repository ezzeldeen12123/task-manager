<script setup>
import { tasksApi } from "@/plugins/apis/tasksRequest";
import AnalyticsTotalRevenue from '@/views/dashboard/AnalyticsTotalRevenue.vue';

const tasksRequest = tasksApi()
const statistics = ref()

tasksRequest.fetchStatistics().then(response => {
  statistics.value = response.data
}).catch(e => {
  console.error(e)
})

</script>

<template>
  <VRow v-if="statistics">
    <!-- 👉 Total Revenue -->
    <VCol
      cols="12"
      md="4"
      order="2"
      order-md="1"
    >
      <AnalyticsTotalRevenue title="Finished" color="#00AB41" :value="statistics['finished']"/>
    </VCol>
    <VCol
      cols="12"
      md="4"
      order="2"
      order-md="1"
    >
      <AnalyticsTotalRevenue title="In Progress" color="#3abeff" :value="statistics['in-progress']"/>
    </VCol>
  </VRow>
</template>
