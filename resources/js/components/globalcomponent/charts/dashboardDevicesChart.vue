<template>
    <!-- Loader -->
    <div v-if="isLoading" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-indicator">Loading...</div>
    </div>

    <!-- Chart Container -->
    <div v-if="!isLoading" class="card-body d-flex flex-column justify-content-between bottom_20border onewhitebg">
        <div class="chartWrapper">
            <canvas ref="devicesChart" width="250" height="250"></canvas>
            <div class="center-text1">
            <div class="white_inner">
                <h2>Devices</h2>
                <small>{{ formattedDigits(totalDevices) }}</small>
            </div>
            </div>
        </div>
        <div class="custom-legend">
            <div class="legend-item def_16_size">
                <span class="legend-color themetextcolor" style="background-color: #fbbf24;"></span><span>Covered ({{ formattedDigits(totalCoveredDevices) }})</span> 
            </div>
            <div class="legend-item def_16_size">
                <span class="legend-color themetextcolor" style="background-color: #f27667;"></span><span> Uncovered ({{ formattedDigits(totalUncoveredDevices) }})</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['totaldevice', 'covereddevice', 'uncovereddevice'],
    data() {
        return {
            totalDevices: this.totaldevice,
            totalCoveredDevices: this.covereddevice,
            totalUncoveredDevices: this.uncovereddevice,
            isLoading: true,
        };
    },
    mounted() {
        /* Show loader, then after short delay, show chart */
        setTimeout(() => {
            this.isLoading = false;
            /* Wait for DOM to update before accessing refs */
            this.$nextTick(() => {
                this.devicesChart();
            });
        }, 800);
    },
    methods: {
        devicesChart() {
            const devicesChartCanvas = this.$refs.devicesChart;
            if (!devicesChartCanvas) {
                this.isLoading = true;
                return;
            }
            const ctxq = devicesChartCanvas.getContext('2d');
            const coveredDevicesChart = this.totalCoveredDevices;
            const uncoveredDevicesChart = this.totalUncoveredDevices;
            /* Chart.js can't render a doughnut with all-zero data, so we add tiny values */
            let chartData = [coveredDevicesChart, uncoveredDevicesChart];
            let backgroundColor = ['#fbbf24', '#f27667'];
            let isZeroData = coveredDevicesChart == 0 && uncoveredDevicesChart == 0;

            if (isZeroData) {
                chartData = [0.0001, 0.0001]; /* invisible slices for both */
                backgroundColor = ['#e0e0e0', '#e0e0e0'];
            }

            new Chart(ctxq, {
                type: 'doughnut',
                data: {
                    labels: ['Covered', 'Uncovered'],
                    datasets: [{
                    data: chartData,
                    backgroundColor: backgroundColor,
                    borderWidth: 2,
                    cutout: '89%'
                    }]
                },
                options: {
                    responsive: true,
                    animation: { animateRotate: true, duration: 2000 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            titleSpacing: 8,
                            footerSpacing: 8,
                            callbacks: {
                                label: (tooltipItem) => {
                                    if (isZeroData) {
                                        return `0`;
                                    } else {
                                        return `${tooltipItem.formattedValue}`;
                                    }
                                }
                            }
                        },
                    }
                }
            });
        },
        formattedDigits(number)
        {
            return formattedNumber(number);
        },
    }
};
</script>
