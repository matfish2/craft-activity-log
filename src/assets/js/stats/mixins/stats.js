import Chart from 'chart.js/auto'

export default {
    props: {
        data: {
            type: Object,
            required: true
        },
        name: {
            type: String,
            required: true
        }
    },
    methods: {
        generateChart(type, datasetOptions) {
            new Chart(
                this.$refs.chart,
                {
                    type,
                    data: {
                        labels: this.data.labels,
                        datasets: [
                            {
                                data: this.data.values,
                                ...datasetOptions
                            }
                        ]
                    }
                }
            );
        },
        generatePieChart(options) {
            this.generateChart('pie', {
                ...options,
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.label;
                            let value = context.formattedValue;
                            let rawValue = context.raw

                            if (!label)
                                label = 'Unknown'

                            let sum = 0;
                            let dataArr = context.chart.data.datasets[0].data;
                            dataArr.map(data => {
                                sum += Number(data);
                            });

                            let percentage = (rawValue * 100 / sum).toFixed(2) + '%';
                            return label + ": " + percentage + " (" + value + ")";
                        }
                    }
                }
            })
        }
    }
}