<template>
  <div date-rangepicker class="flex items-center" ref="datepicker">
    <div class="relative">
      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"
             xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd"
                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                clip-rule="evenodd"></path>
        </svg>
      </div>
      <input name="start" type="text"
             ref="start"
             v-model="start"
             @change="updateValue"
             class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
             placeholder="Select date start">
    </div>
    <span class="mx-2 text-gray-500">to</span>
    <div class="relative">
      <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"
             xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd"
                d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                clip-rule="evenodd"></path>
        </svg>
      </div>
      <input name="end" type="text"
             v-model="end"
             ref="end"
             class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
             @change="updateValue"
             placeholder="Select date end">
    </div>
  </div>
</template>

<script>
import DateRangePicker from 'flowbite-datepicker/DateRangePicker';
import {format} from 'date-fns'

export default {
  name: "DateRangePicker",
  props: ['value'],
  data() {
    return {
      start: format(new Date(),'dd/MM/yyyy'),
      end: format(new Date(),'dd/MM/yyyy')
    }
  },
  mounted() {
    new DateRangePicker(this.$refs.datepicker, {
      format: 'dd/mm/yyyy',
    });

    this.$refs.start.addEventListener('changeDate', (e) => {
      this.start = e.target.value
    })

    this.$refs.end.addEventListener('changeDate', (e) => {
      this.end = e.target.value
    })

  },
  watch: {
    start() {
      this.updateValue()
    },
    end() {
      this.updateValue()
    }
  },
  methods: {
    updateValue() {
      this.$emit('selected',{
        start:this.start,
        end:this.end
      })
    }
  }
}
</script>

<style scoped>

</style>