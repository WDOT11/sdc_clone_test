<template>
    <!-- Loader -->
    <div v-if="isLoading == true" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-indicator">Loading...</div>
    </div>
    <!-- Chart Container -->
    <div v-if="!isLoading" class="card-body d-flex flex-column justify-content-between bottom_20border onewhitebg">
        <div class="chartWrapper">
            <canvas ref="claimsRepairsChart" width="250" height="250"></canvas>
            <div class="center-text">
            <div class="white_inner">
                <h2>Total</h2>
                <small>{{ formattedDigits(totalInsuredClaims + totalUninsuredClaims) }}</small>
            </div>
            </div>
        </div>

        <div class="custom-legend">
            <div class="legend-item def_16_size">
                <span class="legend-color themetextcolor" style="background-color: #0e4181;"></span> <span>Claims ({{ formattedDigits(totalInsuredClaims) }})</span>
            </div>
            <div class="legend-item def_16_size">
                <span class="legend-color themetextcolor" style="background-color: #00b6ff;"></span><span> Repairs ({{ formattedDigits(totalUninsuredClaims) }})</span>                
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['insuredclaims', 'uninsuredclaims'],
    data() {
        return {
            totalInsuredClaims: this.insuredclaims,
            totalUninsuredClaims: this.uninsuredclaims,
            isLoading: true,

        }
    },
    mounted() {
        /* Show loader, then after short delay, show chart */
        setTimeout(() => {
            this.isLoading = false;
            /* Wait for DOM to update before accessing refs */
            this.$nextTick(() => {
                this.claimRepairChart();
            });
        }, 800);

    },
    methods: {
        claimRepairChart() {
            /** Claims and Repair chart */
            const claimsRepairsChartCanvas = this.$refs.claimsRepairsChart;
            /* Check if claimsRepairsChart exists before proceeding */
            if (!claimsRepairsChartCanvas) {
                this.isLoading = true;
                return;
            }
            const ctxx = claimsRepairsChartCanvas.getContext('2d');
            /* Data values */
            const totalRecords = Math.max(1, this.totalInsuredClaims + this.totalUninsuredClaims);
            /* const totalRecords = this.totalInsuredClaims + this.totalUninsuredClaims; */
            const totalClaims = this.totalInsuredClaims;
            const totalRepairs = this.totalUninsuredClaims;
            /* Chart.js can't render a doughnut with all-zero data, so we add tiny values */
            let chartData = [totalClaims, totalRepairs];
            let backgroundColor = ['#0e4181', '#00b6ff'];

            let isZeroData = totalClaims == 0 && totalRepairs == 0;
            if (isZeroData) {
                chartData = [0.0001, 0.0001]; /* invisible slices for both */
                backgroundColor = ['#e0e0e0', '#e0e0e0'];
            }


            new Chart(ctxx, {
                type: 'doughnut',
                data: {
                    labels: ['Claims', 'Repairs'],
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
                            titleSpacing: 8, /* More spacing between title and body */
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
