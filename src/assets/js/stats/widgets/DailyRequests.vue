<template>
 <div>
    <canvas ref="chart" id="daily-requests"></canvas>
  </div>
</template>

<script>

import stats from '../mixins/stats'
export default {
  name: "DailyRequests",
  mixins:[stats],
  mounted() {
    this.generateChart('bar',{
      label:'Requests per day',
      backgroundColor:'#00254d'
    })
  }
}
</script>

<style scoped>

</style>