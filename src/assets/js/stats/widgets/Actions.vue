<template>
<div>
    <canvas ref="chart" :id="name"></canvas>
  </div>
</template>

<script>
import stats from '../mixins/stats'
export default {
  name: "Actions",
  mixins: [stats],
  mounted() {
    this.generatePieChart({
      backgroundColor: [
        '#5cb85c',
        '#0275d8',
        '#f0ad4e',
        '#d9534f',
        '#6cb25c',
        '#2270d8',
        '#e0ac4e',
        '#f9134f',
      ],
    })
  }
}
</script>

<style scoped>

</style>