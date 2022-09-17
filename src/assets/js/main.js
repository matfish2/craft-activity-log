import Vue from 'vue';
import {Event, ServerTable} from 'vue-tables-2-premium'
import DateRangePicker from "./DateRangePicker";
import VueTablesSortIcon from "./VueTablesSortIcon";
import {format} from 'date-fns'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';

Vue.use(ServerTable, {}, false, 'tailwind', {
    sortControl: VueTablesSortIcon
})
Vue.component('date-range-picker', DateRangePicker)
Vue.component('v-select', vSelect)

window.axios = require('axios')
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

new Vue({
    el: '#activity-log-app',
    delimiters: ['{!!', '!!}'],
    async mounted() {
        const {data} = await axios.get('/' + window.cpTrigger + '?action=activity-logs/activity-log/initial-data')
        this.sites = data.sites.map(site => ({
            id: site.id,
            text: site.name
        }))


        this.options.listColumns.siteId = this.sites
        this.options.listColumns.actionSegments = [
            {id:'allActions',text:'All Actions'}
        ].concat(data.actions.map(action=>({
            id:action.action,
            text:action.label
        })))

        window.svgPath = data.svgPath
        this.svgPath = data.svgPath

        this.$nextTick(() => {
            this.loaded = true
        })
    },
    watch: {
        action() {

            if (!this.action) {
                Event.$emit('vue-tables.activity-log.filter::actionSegments', null)
                return;
            }

            let q;

            if (this.action.text) {
                q = {
                    type: 'like',
                    q: this.action.text
                }
            } else {
                q = {
                    type: 'equal',
                    q: this.action
                }
            }

            Event.$emit('vue-tables.activity-log.filter::actionSegments', q)
        }
    },
    data() {
        return {
            action: '',
            svgPath: '',
            loaded: false,
            loadingTable: true,
            sites: [],
            actions:[],
            userId: null,
            columns: ['url', 'userId', 'siteId', 'isCp', 'isAjax', 'ip', 'method', 'actionSegments', 'responseCode', 'execTime', 'createdAt'],
            options: {

                visibleColumns: ['url', 'userId', 'siteId', 'actionSegments', 'createdAt'],
                columnsDropdown: true,
                templates: {
                    url(h, row) {
                        let res = row.url + this.parseQuery(row.query)

                        if (res.length < 50) {
                            return res
                        }
                        return res.slice(0, 50) + '...'
                    }
                },
                alwaysShowPerPageSelect: true,
                customFilters: ['createdAt', 'actionSegments'],
                filterByColumn: true,
                filterable: [
                    'url',
                    'userId',
                    'siteId',
                    'isCp',
                    'isAjax',
                    'responseCode',
                    'ip',
                    'method'
                ],
                orderBy: {
                    column: 'createdAt',
                    ascending: false
                },
                headings: {
                    url: 'URL',
                    userId: 'User',
                    siteId: 'Site',
                    isCp: 'Is CP?',
                    isAjax: 'Is Ajax?',
                    actionSegments: 'Action',
                    responseCode: 'Response Code',
                    execTime: 'Execution Time',
                    ip: 'IP',
                    createdAt: 'Created At'
                },
                listColumns: {
                    siteId: [],
                    actionSegments: [],
                    method: [
                        {
                            id: 'GET',
                            text: 'GET'
                        },
                        {
                            id: 'POST',
                            text: 'POST'
                        },
                        {
                            id: 'PUT',
                            text: 'PUT'
                        },
                        {
                            id: 'DELETE',
                            text: 'DELETE'
                        },
                    ],
                    isCp: [
                        {
                            id: 1,
                            text: 'Yes'
                        },
                        {
                            id: 0,
                            text: 'No'
                        }
                    ],
                    isAjax: [
                        {
                            id: 1,
                            text: 'Yes'
                        },
                        {
                            id: 0,
                            text: 'No'
                        }
                    ],
                    responseCode: [
                        {
                            id: 200,
                            text: '2xx'
                        },
                        {
                            id: 300,
                            text: '3xx'
                        },
                        {
                            id: 400,
                            text: '4xx'
                        },
                        {
                            id: 500,
                            text: '5xx'
                        },
                    ]
                }
            },
        }
    },
    methods: {
        formatDate(date) {
            return format(new Date(date), 'dd/MM/yyyy HH:mm:ss')
        },
        getSite(siteId) {
            if (!siteId) {
                return '-'
            }

            return this.options.listColumns.siteId.find(site => Number(site.id) === Number(siteId)).text
        },
        getAction(actionSegments) {
            if (!actionSegments) {
                return '-'
            }

            let res = this.options.listColumns.actionSegments.find(action => action.id === actionSegments)

            return res ? res.text : actionSegments
        },
        beautifyJson(obj) {
            if (!obj) {
                return '-'
            }

            return JSON.stringify(JSON.parse(obj), null, 2)
        },
        updateDateRange(val) {
            Event.$emit('vue-tables.activity-log.filter::createdAt', val)
        },
        parseQuery(q) {
            if (!q) {
                return '';
            }

            return '?' + new URLSearchParams(JSON.parse(q)).toString()
        }
    }
})

