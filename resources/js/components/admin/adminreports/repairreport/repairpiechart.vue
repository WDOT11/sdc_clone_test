<template>
    <!-- Loader -->
    <div v-if="isLoading" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-indicator">Loading...</div>
    </div>

    <!-- Chart Container -->
    <div v-if="!isLoading" class="card-body d-flex flex-column justify-content-between border_radious375 report_tab_inner">
        <div class="chartWrapper">
            <canvas ref="deviceRepairPieChart" width="250" height="250"></canvas>
            <div class="center-text">
            <div class="white_inner">
                <h2>Total</h2>
                <small>{{ formattedDigits(totaldeviceRepairRequests) }}</small>
            </div>
            </div>
        </div>
        <div class="custom-legend">
            <div class="legend-item def_16_size">
                <span class="legend-color" style="background-color: #00b6ff;"></span> Total Repair Requests
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["year"],
    data() {
        return {
            totaldeviceRepairRequests: 0,
            isLoading: true, /* Loading state for the chart */
            messageChart: null, /* Chart instance */
        };
    },
    watch: {
        /* Watch for year changes */
        year: {
            immediate: true,
            handler(newYear) {
                this.getDevicesData(newYear); /* Fetch data for the new year */
                this.isLoading = true; /* Set loading state to true when year changes */
            }
        }
    },
    methods: {
        formattedDigits(number)
        {
            return formattedNumber(number);
        },
        /** Devices data */
        async getDevicesData(year) {
            this.isLoading = true;
            try {
                let url = `${this.$userAppUrl}smarttiusadmin/reports/total-device/repair-request?year=${year}`;
                const response = await axios.get(url);
                if (response.data.success == true) {
                        this.totaldeviceRepairRequests = response.data.totaldeviceRepairRequests;
                        this.isLoading = false;
                        /* Delay a bit to make sure chart canvas is in DOM */
                        this.$nextTick(() => {
                            const canvas = this.$refs.deviceRepairPieChart;
                            if (canvas) {
                                this.devicesChart();
                            } else {
                                /* Loader */
                               this.isLoading = true;
                            }
                        });
                } else {
                        /* Loader */
                        this.isLoading = true;
                }
            } catch (error) {
                /* Loader */
                this.isLoading = true;
            }
        },
        /** Devices Month chart */
        devicesChart() {
            if (this.messageChart) {
                this.messageChart.destroy();
            }

            const canvas = this.$refs.deviceRepairPieChart;
            if (!canvas || !canvas.parentElement) {
                this.isLoading = true;
                return;
            }

            canvas.width = canvas.parentElement.offsetWidth;
            const ctxbar = canvas.getContext("2d");

            let total = this.totaldeviceRepairRequests;
            /* Chart.js can't render a doughnut with all-zero data, so we add tiny values */
            let chartData = [total];
            let backgroundColor = ['#00b6ff'];
            let isZeroData = total == 0;

            if (isZeroData) {
                chartData = [0.0001]; /* invisible slices for both */
                backgroundColor = ['#e0e0e0'];
            }

            this.messageChart = new Chart(ctxbar, {
                type: "doughnut",
                data: {
                    labels: ['Repair Requests'],
                    datasets: [{
                        data: chartData,
                        backgroundColor: backgroundColor,
                        borderWidth: 2,
                        cutout: '89%'
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (tooltipItem) => {
                                    const label = tooltipItem.label;
                                    if (isZeroData) {
                                        return `0`;
                                    } else {
                                        return `${tooltipItem.formattedValue}`;
                                    }
                                }
                            }
                        }
                    }
                },
            });
        }

    },
    beforeUnmount() {
        /* Destroy chart when component unmounts */
        if (this.messageChart) {
            this.messageChart.destroy();
        }
    },
};
</script>
