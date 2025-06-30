<template>
    <!-- Loader -->
    <div v-if="isLoading == true" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-indicator">Loading...</div>
    </div>
    <!-- Chart Container -->
    <div v-if="!isLoading" class="card-body d-flex flex-column justify-content-end px-2 bottom_20border onewhitebg">
        <div class="chart-containerm px-2 mb-3">
            <canvas ref="messageChart"></canvas>
        </div>
        <div class="custom-legend">
            <div class="legend-item def_16_size">
                <span class="legend-color" style="background-color: #0e4181;"></span> Claims
            </div>
            <div class="legend-item def_16_size">
                <span class="legend-color" style="background-color: #00b6ff;"></span> Repairs
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['isadmin'],
    data() {
        return {
            isAdmin: this.isadmin,
            totalInsuredClaims: 0,
            totalUninsuredClaims: 0,
            deviceClaims: [],
            isLoading: true, // Loading state for the chart
        }
    },
    created() {
        /* Show loader, then after short delay, show chart */
        setTimeout(() => {
            this.isLoading = false;
            /* Wait for DOM to update before accessing refs */
            this.$nextTick(() => {
                this.getClaimRepairData();
            });
        }, 800);

    },
    methods: {
        /** Claims and Repair data */
        async getClaimRepairData() {
            let url = '';
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/dash-chart/insured-claims`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/dash-chart/claims-repair`;
            }
            try {
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.claimsData) {
                        this.deviceClaims = response.data.claimsData;
                        this.claimRepairChart();

                    } else {
                        /* Loader */
                        this.isLoading = true;
                    }
                }
            } catch (error) {
                /* Loader */
                this.isLoading = true;
            }
        },
        /** Claims and Repair Month chart */
        claimRepairChart() {
            /** Claims and Repair Month chart */
            const canvas = this.$refs.messageChart;
            /** Check condition for null and empty data */
            if (!canvas && !canvas.parentElement) {
                this.isLoading = true;
                return;
            }
            canvas.width = canvas.parentElement.offsetWidth;
            const ctxbar = canvas.getContext('2d');
            /* Month labels*/
            const monthLabels = this.deviceClaims.map(item => item.month);  /* Extract month names from deviceClaims data */

            /* Initialize empty arrays for the chart data */
            // const insuredData = this.deviceClaims.map(item => 0 || 0);  /* Map insured claims from data */
            // const uninsuredData = this.deviceClaims.map(item => 0 || 0);  /* Map uninsured claims from data */
            const insuredData = this.deviceClaims.map(item => item.totalInsuredClaims || 0);  /* Map insured claims from data */
            const uninsuredData = this.deviceClaims.map(item => item.totalUninsuredClaims || 0);  /* Map uninsured claims from data */
            /* Calculate the dynamic max value for the y-axis */
            const maxValue = Math.max(...insuredData, ...uninsuredData);
            // const adjustedMax = Math.ceil(maxValue / 10) * 10;
            const adjustedMax = Math.max(Math.ceil(maxValue / 10) * 10, 10); // Ensure at least 10
            const messageChart = new Chart(ctxbar, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [
                        {
                            label: " Claims",
                            data: insuredData,
                            backgroundColor: "#0E4181",
                            borderColor: "#ffffff",
                            borderWidth: 1,
                            barThickness: 10
                        },
                        {
                            label: " Repairs",
                            data: uninsuredData,
                            backgroundColor: "#00B6FF",
                            borderColor: "#ffffff",
                            borderWidth: 1,
                            barThickness: 10
                        }
                    ]
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
                        x: {
                            grid: {
                                display: true,
                                lineWidth: 1,
                                color: "rgba(0, 0, 0, 0.1)"
                            },
                        },
                        y: {
                            beginAtZero: true,
                            max: adjustedMax,
                            grid: {
                                display: true,
                                lineWidth: 1,
                                color: "rgba(0, 0, 0, 0.1)"
                            }
                        }
                    }
                }
            });
        }
    }
};
</script>
