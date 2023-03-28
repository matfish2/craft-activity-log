<template>
  <div style="width:100%; margin:0 auto;">
    <h2 v-if="!data">Loading...</h2>
    <component v-if="data" :name="name" :is="component" :data="data"></component>
  </div>
</template>

<script>

import DailyRequests from './widgets/DailyRequests';
import ResponseCode from './widgets/ResponseCode';
import RequestVerbs from './widgets/RequestVerbs';
import ExecTime from './widgets/ExecTime'
import RequestsPerUser from './widgets/RequestsPerUser'
export default {
  name: "ActivityLogsStatsWidget",
  components: {
    DailyRequests,
    ResponseCode,
    RequestVerbs,
    ExecTime,
    RequestsPerUser
  },
  props: {
    name: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      data: null
    }
  },
  async mounted() {
    await this.getData()

    this.$parent.$watch('filters', (val) => {
      this.getData()
    }, {deep: true})
  },
  methods: {
    async getData() {
      let f = this.$parent.filters
      this.data = null
      let start = f.dateRange.start
      let end = f.dateRange.end
      let url = '/' + window.cpTrigger + `?action=activity-logs/statistics&name=${this.name}&start=${start}&end=${end}`

      if (f.isCp !== '') {
        url += '&isCp=' + (f.isCp === '1' ? '1' : '0')
      }

      if (f.isAjax !== '') {
        url += '&isAjax=' + (f.isAjax === '1' ? '1' : '0')
      }

      if (f.siteId !== null && f.siteId!=='') {
        url += '&siteId=' + f.siteId
      }

      if (f.userId !== null && f.userId!=='') {
        url += '&userId=' + f.userId
      }

      const {data} = await axios.get(url)
      this.data = data
    }
  },
  computed: {
    component() {
      return this.name
    }
  }
}
</script>

<style scoped>

</style>