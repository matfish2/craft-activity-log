<template>
  <div>
    <canvas ref="chart" id="response-code"></canvas>
  </div>
</template>

<script>
import stats from '../mixins/stats'

export default {
  name: "ResponseCode",
  mixins: [stats],
  mounted() {
    this.generatePieChart({
      backgroundColor: [
        '#5cb85c',
        '#0275d8',
        '#f0ad4e',
        '#d9534f'
      ],
    })
  }
}
</script>

<style scoped>

</style>