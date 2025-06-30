<template>
    <!-- Loader -->
    <div v-if="isLoading == true" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-indicator">Loading...</div>
    </div>
    <!-- Chart Container -->
    <div v-if="!isLoading"
        class="card-body bottom_20border d-flex flex-column justify-content-between px-3  onewhitebg">
        <div class="chart-containerm">
            <canvas ref="usersChart"></canvas>
        </div>
    </div>
</template>
<script>
export default {
    props: ["isadmin"],
    data() {
        return {
            isAdmin: this.isadmin,
            totalUsers: 0,
            usersData: [],
            isLoading: true, // Loading state for the chart
        };
    },
    created() {
        /* Show loader, then after short delay, show chart */
        setTimeout(() => {
            this.isLoading = false;
            /* Wait for DOM to update before accessing refs */
            this.$nextTick(() => {
                this.getUsersData();
            });
        }, 800);

    },
    methods: {
        /** Users data */
        async getUsersData() {
            let url = "";
            if (this.isAdmin) {
                url = `${this.$userAppUrl}smarttiusadmin/dash-chart/users`;
            } else {
                url = `${this.$userAppUrl}sdcsmuser/dash-chart/users`;
            }
            try {
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.usersData) {
                        this.usersData = response.data.usersData;
                        this.usersChart();
                    } else {
                        /* Loader */
                        this.isLoading = true;
                    }
                }
            } catch (error) {
                // this.$alertMessage.success = false;
                // this.$alertMessage.message = "Something went wrong, please try again.";
                this.isLoading = true;
            }
        },
        /** Users Month chart */
        usersChart() {
            /** Users Month chart */
            const canvas = this.$refs.usersChart;
            /** Check condition for null and empty data */
            if (!canvas && !canvas.parentElement) {
                this.isLoading = true;
                return;
            }
            canvas.width = canvas.parentElement.offsetWidth;
            const ctxbar = canvas.getContext("2d");
            /* Month labels*/
            const monthLabels = this.usersData.map((item) => item.month); /* Extract month names from deviceClaims data */
            /* Initialize empty arrays for the chart data */
            const totalUsersData = this.usersData.map((item) => item.users || 0); /* Map insured claims from data */
            /* Calculate the dynamic max value for the y-axis */
            const maxValue = Math.max(...totalUsersData);
            // const adjustedMax = Math.ceil(maxValue / 10) * 10;
            const adjustedMax = Math.max(Math.ceil(maxValue / 10) * 10, 10);
            const messageChart = new Chart(ctxbar, {
                type: "line",
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: "Users",
                        data: totalUsersData,
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
};
</script>