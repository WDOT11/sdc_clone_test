<template>
    <!-- Loader -->
    <div v-if="isLoading == true" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-indicator">Loading...</div>
    </div>
    <!-- Chart Container -->
    <div v-if="!isLoading"
        class="card-body d-flex flex-column justify-content-end px-3 border_radious375 report_tab_inner h-100 ">
        <div class="chart-containerm">
            <canvas ref="deviceChart"></canvas>
        </div>
    </div>
</template>
<script>
export default {
    props: ["year"],
    data() {
        return {
            totalDevices: 0,
            deviceData: [],
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
        /** Devices data */
        async getDevicesData(year) {
            this.isLoading = true;
            try {
                let url = `${this.$userAppUrl}smarttiusadmin/reports/device-month?year=${year}`;
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.deviceData) {
                        this.deviceData = response.data.deviceData;
                        this.isLoading = false;
                        /* Delay a bit to make sure chart canvas is in DOM */
                        this.$nextTick(() => {
                        const canvas = this.$refs.deviceChart;
                        if (canvas) {
                            this.devicesChart();
                        } else {
                            /* Loader */
                            this.isLoading = true;
                        }
                    });
                    }else {
                        /* Loader */
                        this.isLoading = true;
                    }
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
            /** Devices Month chart */
            const canvas = this.$refs.deviceChart;
            /** Check condition for null and empty data */
            if (!canvas || !canvas.parentElement) {
                this.isLoading = true;
                return;
            }
            canvas.width = canvas.parentElement.offsetWidth;
            const ctxbar = canvas.getContext("2d");
            /* Month labels*/
            const monthLabels = this.deviceData.map((item) => item.month); /* Extract month names from deviceClaims data */
            /* Initialize empty arrays for the chart data */
            const totalDevicesData = this.deviceData.map((item) => item.devices || 0); /* Map device data from data */
            /* Calculate the dynamic max value for the y-axis */
            const maxValue = Math.max(...totalDevicesData);
            // const adjustedMax = Math.ceil(maxValue / 10) * 10;
            const adjustedMax = Math.max(Math.ceil(maxValue / 10) * 10, 10);
            this.messageChart = new Chart(ctxbar, {
                type: "line",
                data: {
                    labels: monthLabels,
                    datasets: [{
                        data: totalDevicesData,
                        borderColor: '#2CA058',
                        backgroundColor: 'rgba(44, 160, 88, 0.2)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: adjustedMax
                        }
                    }
                },
            });
        },
    },
    beforeUnmount() {
        /* Destroy chart when component unmounts */
        if (this.messageChart) {
            this.messageChart.destroy();
        }
    },
};
</script>