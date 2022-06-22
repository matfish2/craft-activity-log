<template>
        <multiselect v-model="selectedValue"
                     @input="$emit('selected', selectedValue.id)"
                     id="ajax"
                     label="text" track-by="id"
                     placeholder="Action"
                     open-direction="bottom"
                     :options="options"
                     :multiple="false"
                     :searchable="true"
                     :internal-search="false"
                     :taggable="true"
                     :clear-on-select="false"
                     :close-on-select="true" :options-limit="10"
                     :max-height="600"
                     :show-no-results="true"
                     :hide-selected="true"
                     @search-change="asyncFind">
          <span slot="noResult">Oops! No elements found.</span>
        </multiselect>
</template>

<script>
import Multiselect from 'vue-multiselect'
require('vue-multiselect/dist/vue-multiselect.min.css')
export default {
  name: "SearchField",
  components: {Multiselect},
  props: {
    options:{},
    name: {},
    value: {},
  },
  data() {
    return {
      selectedValue: '',
    }
  },
  methods: {
    async asyncFind(query) {
      // this.isLoading = true
      // const {data} = await axios.get(`/?action=activity-log/activity-log/users&q=${query}`)
      // this.users = data
      // this.isLoading = false
    },
    reset() {
      this.selectedValue = null
    }
  },
  computed: {}
}
</script>

<style scoped>

</style>