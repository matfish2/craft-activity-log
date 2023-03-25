<template>
<div>
    <canvas ref="chart" :id="name"></canvas>
  </div>
</template>

<script>
import stats from '../mixins/stats'
export default {
  name: "ExecTime",
  mixins: [stats],
  mounted() {
    this.generateChart('line', {
      label:'Exec. time in ms'
    })
  }
}
</script>

<style scoped>

</style>