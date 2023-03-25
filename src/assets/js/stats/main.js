import Vue from 'vue'
import DateRangePicker from '../DateRangePicker'
import ActivityLogsStatsWidget from './ActivityLogsStatsWidget'
import {format} from 'date-fns'

window.vm = new Vue({
    el: "#dashboard-grid",
    data() {
        let start = new Date();
        start = start.setDate(start.getDate() - 10)

        return {
            filters: {
                dateRange: {
                    start: format(start, 'dd/MM/yyyy'),
                    end: format(new Date(), 'dd/MM/yyyy'),
                },
                isCp: '',
                isAjax:'',
                siteId:null,
                userId:null
            }
        }
    },
    components: {
        ActivityLogsStatsWidget
    }
})

new Vue({
    el: "#stats-filters",
    components: {
        DateRangePicker
    },
    mounted() {
        let start = new Date();
        start = start.setDate(start.getDate() - 10)

        this.setDateRange({
            start: format(start, 'dd/MM/yyyy'),
            end: format(new Date(), 'dd/MM/yyyy')
        });
    },
    methods: {
        toggleFilters() {
          $(this.$refs.filters).toggle()
        },
        updateFilter(key, value) {
            vm.filters[key] = value
        },
        setDateRange(data) {
            vm.filters.dateRange = data
        }
    }
})

const ActivityLogsWidgets = Craft.Dashboard.extend({
    init: function (widgetTypes) {
        $("#newwidgetmenubtn").on('click', function () {
            $("#widget-menu").toggle()
        })
        this.widgetTypes = widgetTypes;
        this.widgets = {};

        this.$widgetManagerBtn = $('#statsWidgetManagerBtn');
        this.$newWidgetBtn = $('#statsNewwidgetmenubtn');

        this.addListener(this.$widgetManagerBtn, 'click', 'showWidgetManager');

        Garnish.$doc.ready(() => {
            this.$grid = $('#dashboard-grid');
            this.grid = this.$grid.data('grid');

        });
    },
    findWidgetById: function (id) {
        return this.$grid.find('#widget' + id).parent()
    },
    newWidgetOptionSelect: function (e) {
        // $("#new-widget-menu").hide()
        const $option = $(e.target);
        this.createStatsWidget($option.data('type'));
    },
    createStatsWidget: function (type) {
        Craft.sendActionRequest('POST', 'activity-logs/statistics/create-widget', {
            type
        })
            .then((response) => {
                window.reload()
            });
    },

    saveSettings: function (e) {
        e.preventDefault();
    },
    showWidgetManager: function () {

        if (!this.widgetManager) {
            var $widgets = this.$grid.find('> .item > .widget')

            const $form = $(
                '<form method="post" accept-charset="UTF-8">' +
                '<input type="hidden" name="action" value="activity-log/stats/save-widget"/>' +
                '</form>'
            ).appendTo(Garnish.$bod)

            const $noWidgets = $(
                '<p id="nowidgets" class="zilch small' +
                ($widgets.length ? ' hidden' : '') +
                '">' +
                Craft.t('app', 'You don’t have any widgets yet.') +
                '</p>'
            ).appendTo($form)

            const $table = $(
                '<table class="data' +
                (!$widgets.length ? ' hidden' : '') +
                '" role="presentation"/>'
            ).appendTo($form)

            const $tbody = $('<tbody/>').appendTo($table);

            for (var i = 0; i < $widgets.length; i++) {
                var $widget = $widgets.eq(i),
                    widget = $widget.data('widget');

                // Make sure it's actually saved
                if (!widget || !widget.id) {
                    continue;
                }

                widget.getManagerRow().appendTo($tbody);
            }

            this.widgetManager = new Garnish.HUD(this.$widgetManagerBtn, $form, {
                hudClass: 'hud widgetmanagerhud',
                onShow: () => {
                    this.$widgetManagerBtn
                        .addClass('active')
                        .attr('aria-expanded', 'true');
                },
                onHide: () => {
                    this.$widgetManagerBtn
                        .removeClass('active')
                        .attr('aria-expanded', 'false');
                },
            });

            this.widgetAdminTable = new Craft.AdminTable({
                tableSelector: $table,
                noObjectsSelector: $noWidgets,
                sortable: true,
                reorderAction: 'activity-logs/statistics/reorder-widgets',
                deleteAction: 'activity-logs/statistics/delete-widget',
                confirmDeleteMessage: null,
                deleteSuccessMessage: null,
                noItemsSelector: '#nowidgets',
                onReorderItems: (ids) => {
                    var lastWidget = null;

                    for (var i = 0; i < ids.length; i++) {
                        var widget = this.findWidgetById(ids[i])
                        if (!lastWidget) {
                            widget.prependTo(this.$grid);
                        } else {
                            widget.insertAfter(lastWidget);
                        }

                        lastWidget = widget;
                    }

                    this.grid.resetItemOrder();
                },
                onDeleteItem: (id) => {
                    const widget = this.findWidgetById(id);
                    widget.remove()
                    window.location.reload()
                },
            });
        } else {
            this.widgetManager.show();
        }
    },
})

Craft.Widget = Craft.Widget.extend({
    getManagerRow: function () {
        var $row = $(
            '<tr data-id="' +
            this.id +
            '" data-name="' +
            (this.title
                ? Craft.escapeHtml(this.title)
                : this.getTypeInfo('name')) +
            '">' +
            '<td class="widgetmanagerhud-icon thin">' +
            this.getTypeInfo('iconSvg') +
            '</td>' +
            '<td id="' +
            this.getWidgetLabelId() +
            '">' +
            this.getManagerRowLabel() +
            '</td>' +
            '<td class="widgetmanagerhud-col-colspan-picker thin"></td>' +
            '<td class="widgetmanagerhud-col-move thin"><a class="move icon" title="' +
            Craft.t('app', 'Reorder') +
            '" role="button"></a></td>' +
            '<td class="thin"><a class="delete icon" tabindex="0" type="button" title="' +
            Craft.t('app', 'Delete') +
            '" role="button" aria-label="' +
            Craft.t('app', 'Delete') +
            '" aria-describedby="' +
            this.getWidgetLabelId() +
            '"></a></td>' +
            '</tr>'
        );

        // Initialize the colspan picker
        this.colspanPicker = new Craft.SlidePicker(this.getColspan(), {
            min: 1,
            max: () => {
                return window.dashboard.grid.totalCols;
            },
            step: 1,
            label: Craft.t('app', 'Number of columns'),
            describedBy: this.getWidgetLabelId(),
            valueLabel: (colspan) => {
                return Craft.t(
                    'app',
                    '{num, number} {num, plural, =1{column} other{columns}}',
                    {
                        num: colspan,
                    }
                );
            },
            onChange: (colspan) => {
                // Update the widget and grid
                this.setColspan(colspan);
                window.dashboard.grid.refreshCols(true);

                // Save the change
                let data = {
                    id: this.id,
                    colspan: colspan,
                };

                Craft.sendActionRequest('POST', 'activity-logs/statistics/change-widget-colspan', {
                    data,
                })
                    .then((response) => {
                        Craft.cp.displaySuccess(Craft.t('app', 'Widget saved.'));
                    })
                    .catch(({response}) => {
                        Craft.cp.displayError(Craft.t('app', 'Couldn’t save widget.'));
                    });
            },
        });

        this.colspanPicker.$container.appendTo(
            $row.find('> td.widgetmanagerhud-col-colspan-picker')
        );
        window.dashboard.grid.on('refreshCols', () => {
            this.colspanPicker.refresh();
        });

        return $row;
    }
})
window.widgetsManager = new ActivityLogsWidgets(widgetTypes)

