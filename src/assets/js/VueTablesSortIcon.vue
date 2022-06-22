<template>
     <span v-if="props.sortable" :class="props.class">
       <img :src="icon" alt="" width="15">
     </span>
</template>

<script>
export default {
  name: "VueTablesSortIcon",
  props: ['props'],
  data() {
    return {
      svgPath: window.svgPath
    }
  },
  computed: {
    icon() {
      // sortStatus = { sorted: Boolean, asc: Boolean }

      let icon;
      // if not sorted return base icon
      if (!this.props.sortStatus.sorted) {
        icon = 'sort';
      } else {
        icon = this.props.sortStatus.asc ? 'sort-up' : 'sort-down';
      }

      return this.svgPath + '/' + icon + '.svg'

    }
  }
}
</script>

<style scoped>
 span {
   float:right;
 }
</style>