<template>
    <div class="container-fluid mt_12">
        <div class="card rounded-lg shadow-sm border-0 onewhitebg">
            <div class="card-body five_tables   reports_drive">
                <!-- Header Section -->
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3 border-bottom pb-3 mb-4">
                    <div>
                        <h5 class="h5 font-weight-semibold mb-0 themetextcolor">Reports</h5>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-pills mb-4" id="reportsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                       
                        <a class="nav-link text-center  py-3 h-100 active " data-bs-toggle="pill" href="#devices">Devices</a>
                    </li>
                    <li class="nav-item" role="presentation">
                       
                        <a class="nav-link text-center  py-3 h-100  " data-bs-toggle="pill" href="#repairs">Repairs</a>
                    </li>
                    <li class="nav-item" role="presentation">
                       
                        <a class="nav-link text-center  py-3 h-100  " data-bs-toggle="pill" href="#claims">Claims</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        
                         <a class="nav-link text-center  py-3 h-100  " data-bs-toggle="pill" href="#users">Users</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content p-3 onewhitebg border rounded-bottom reporttab" id="reportsTabContent">
                    <div class="d-flex align-items-center gap-11 mb-3">
                        <div class="year-selector">
                            <select v-model="selectedYear" class="form-control ">
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                                <option value="2023">2023</option>
                                <option value="2022">2022</option>
                            </select>
                        </div>
                    </div>

                    <!-- Devices Tab -->
                    <div class="tab-pane onewhitebg fade show active" id="devices" role="tabpanel" aria-labelledby="devices-tab">
                        <div class="row">
                            <div class="col-12 col-lg-8">
                                <admin-device-month-chart-reports :year="selectedYear"></admin-device-month-chart-reports>
                            </div>
                            <div class="col-12 col-lg-4">
                                <admin-device-pie-chart-reports :year="selectedYear"></admin-device-pie-chart-reports>
                            </div>
                        </div>
                    </div>

                    <!-- Repairs Tab -->
                    <div class="tab-pane fade" id="repairs" role="tabpanel" aria-labelledby="repairs-tab">
                        <div class="row">
                            <div class="col-12 col-lg-8">
                                <admin-device-repair-month-chart-reports :year="selectedYear"></admin-device-repair-month-chart-reports>
                            </div>
                            <div class="col-12 col-lg-4">
                                <admin-device-repair-pie-chart-reports :year="selectedYear"></admin-device-repair-pie-chart-reports>
                            </div>
                        </div>
                    </div>

                    <!-- Claims Tab -->
                    <div class="tab-pane fade" id="claims" role="tabpanel" aria-labelledby="claims-tab">
                        <div class="row">
                            <div class="col-12 col-lg-8">
                                <admin-device-claim-month-chart-reports :year="selectedYear"></admin-device-claim-month-chart-reports>
                            </div>
                            <div class="col-12 col-lg-4">
                                <admin-device-claim-pie-chart-reports :year="selectedYear"></admin-device-claim-pie-chart-reports>
                            </div>
                        </div>
                    </div>

                    <!-- Users Tab -->
                    <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                        <div class="row">
                            <div class="col-12 col-lg-8">
                                <admin-users-month-chart-reports :year="selectedYear"></admin-users-month-chart-reports>
                            </div>
                            <div class="col-12 col-lg-4">
                                <admin-users-pie-chart-reports :year="selectedYear"></admin-users-pie-chart-reports>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedYear: '2025'
        };
    },

};
</script>
