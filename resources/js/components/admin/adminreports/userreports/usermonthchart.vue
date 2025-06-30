<template>
    <!-- Loader -->
    <div v-if="isLoading == true" class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-indicator">Loading...</div>
    </div>
    <!-- Chart Container -->
    <div v-if="!isLoading"
        class="card-body h-100 d-flex flex-column justify-content-end px-3 border_radious375 report_tab_inner">
        <div class="chart-containerm">
            <canvas ref="userChart"></canvas>
        </div>
    </div>
</template>
<script>
export default {
    props: ["year"],
    data() {
        return {
            totalUsers: 0,
            userData: [],
            isLoading: true, /* Loading state for the chart */
            messageChart: null, /* Chart instance */
        };
    },
    watch: {
        // Watch for year changes
        year: {
            immediate: true,
            handler(newYear) {
                this.getUsersData(newYear); /* Fetch data for the new year */
                this.isLoading = true; /* Set loading state to true when year changes */
            }
        }
    },
    methods: {
        /** Users data */
        async getUsersData(year) {
            this.isLoading = true;
            try {
                let url = `${this.$userAppUrl}smarttiusadmin/reports/users-month?year=${year}`;
                const response = await axios.get(url);
                if (response.data.success == true) {
                    if (response.data.userData) {
                        this.userData = response.data.userData;
                        this.isLoading = false;
                        /* Delay a bit to make sure chart canvas is in DOM */
                        this.$nextTick(() => {
                        const canvas = this.$refs.userChart;
                        if (canvas) {
                            this.usersChart();
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
        /** Users Month chart */
        usersChart() {
            /** Destroy previous chart instance if it exists */
            if (this.messageChart) {
                this.messageChart.destroy();
            }
            /** Users Month chart */
            const canvas = this.$refs.userChart;
            /** Check condition for null and empty data */
            if (!canvas || !canvas.parentElement) {
                this.isLoading = true;
                return;
            }
            canvas.width = canvas.parentElement.offsetWidth;
            const ctxbar = canvas.getContext("2d");
            /* Month labels*/
            const monthLabels = this.userData.map((item) => item.month); /* Extract month names from userData */
            /* Initialize empty arrays for the chart data */
            const totalUsersData = this.userData.map((item) => item.users || 0); /* Map user data from userData */
            /* Calculate the dynamic max value for the y-axis */
            const maxValue = Math.max(...totalUsersData);
            // const adjustedMax = Math.ceil(maxValue / 10) * 10;
            const adjustedMax = Math.max(Math.ceil(maxValue / 10) * 10, 10);
            this.messageChart = new Chart(ctxbar, {
                type: "line",
                data: {
                    labels: monthLabels,
                    datasets: [{
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
    beforeUnmount() {
        /* Destroy chart when component unmounts */
        if (this.messageChart) {
            this.messageChart.destroy();
        }
    },
};
</script>