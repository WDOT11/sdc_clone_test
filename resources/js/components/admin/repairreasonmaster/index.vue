<template>
    <div class="container-fluid ">
        <div class="card onewhitebg border-0   mt_12">
            <div class="card-body">
            <h4 class=" mb-3 border-bottom pb-2 coman_main_heading">Repair Reasons</h4>

            <!-- Search filter -->
            <div class="d-flex justify-content-between flex-wrap align-items-start gap-3 ">
                <button data-bs-toggle="modal" data-bs-target="#NewRepairReason" class="btn blogal_pbtn_padding def_14_size bg_blue wmax text-white d-flex align-items-center gap-10   ">
                    <img :src="profileIc" width="22" height="22">
                    Add
                </button>
                <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                    <!-- Search by reason name -->
                    <input type="text" v-model="search_name" class="def_14_size form-control w-auto" placeholder="Filter by Name">

                    <!-- buttons -->
                    <div class="d-flex gap-2">

                        <button class="btn blogal_pbtn_padding def_14_size bg_blue d-flex align-items-center gap-10 text-white" @click="getRepairReasons"><img :src="searchIc" width="20" height="20"> Search</button>
                        <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch">Clear</button>
                    </div>
                </div>
            </div>

            <!-- Add Repair Reason Modal -->
            <div class="modal" id="NewRepairReason">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content onewhitebg">

                    <!-- Modal Header -->
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                            <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                            <span>Add New Repair Reason</span>
                        </h4>
                        <button type="button" class="btn-close" id="addRepairReasonModalClose" @click="closeAddModal" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body onewhitebg">
                        <!-- <div v-if="showAddModal"> -->
                            <label class="form-label" >Reason Name <span class="text-danger">*</span></label>
                            <input v-model="reason_name" type="text" class="w-full " placeholder="Enter Repair Reason" :class="['form-control',{ 'is-invalid': validationErrors.reason_name },]" />
                            <small v-if="validationErrors.reason_name"><ErrorMessage :msg="validationErrors.reason_name"></ErrorMessage></small>
                        <!-- </div> -->
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer onewhitebg">
                        <button @click="saveRepairReason" class="btn bg_blue text-white  ">
                            Save
                        </button>
                    </div>

                    </div>
                </div>
            </div>
            <!-- Add Repair Reason Modal End -->

            <!-- Edit Repair Reason Modal -->
            <div class="modal" id="EditRepairReason">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content onewhitebg">

                    <!-- Modal Header -->
                    <div class="modal-header align-items-center">
                        <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                            <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                            <span>Edit Repair Reason</span>
                        </h4>
                        <button type="button" class="btn-close" id="editRepairReasonModalClose" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body onewhitebg">
                       <!-- <div v-if="showEditModal"> -->
                         <!-- Repair Reason Name -->
                    <label class="form-label" >Reason Name <span class="text-danger">*</span></label>
                    <input v-model="updated_reason_name" type="text" class="w-full " placeholder="Enter Repair Reason" :class="['form-control',{ 'is-invalid': validationErrors.reason_name },]" />
                    <small v-if="validationErrors.reason_name"><ErrorMessage :msg="validationErrors.reason_name"></ErrorMessage></small>
                       <!-- </div> -->
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer onewhitebg">
                        <!-- <button data-bs-dismiss="modal" @click="closeEditModal" class="btn btnblack text-white  rounded mr-2">
                            Cancel
                        </button> -->
                        <button @click="updateRepairReason" class="btn bg_blue text-white  ">
                            Save
                        </button>

                    </div>

                    </div>
                </div>
            </div>
            <!-- Edit Repair Reason Modal End -->

            <!-- Table to show the Repair reasons -->
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-hover def_14_size">
                    <thead class="table-light">
                        <tr>
                            <th  width="60" >#</th>
                            <th>Repair Reason Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="repairReasons.length > 0" v-for="(repairreason, index) in repairReasons" :key="repairreason.id">
                            <td>{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                            <td>
                                {{ repairreason.repair_reason_name }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-20">
                                <button data-bs-toggle="modal" data-bs-target="#EditRepairReason" @click="editRepairReason(repairreason.id)" class="themetextcolor  rounded-pill globaleditbtn extrabtns d-flex align-items-center def_14_size gap-1   editBtn"><img :src="editIc" width="35" height="35"><span class="edittext_Gl">Edit</span></button>
                                <!-- <button @click="confirmDelete(repairreason.id)" class=" bg-transparent text-white rounded"><img :src="deleteIc" width="28" height="28"></button> -->
                                </div>
                            </td>
                        </tr>
                        <tr v-else>
                            <td colspan="4">
                                No Repair Reason Found!
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="3" :paginate="getRepairReasons"></pagination>
        </div>
    </div>
</div>
</template>
<script>
export default {
props: {
    repairreasonsdata: {
        type : Object
    },
    paginationdata: {
        type : Object
    }
},
data() {
    return {
        deleteIc: this.$deleteIcon,
        editIc: this.$editIcIcon,
        viewIc: this.$viewIcIcon,
        searchIc: this.$searchIcIcon,
        profileIc: this.$profileIcIcon,

        /** Used while creating a new repair reason */
        reason_name: '',
        repair_reason_id: '',

        /** Used while updating a repair reason name */
        updated_reason_name: '',

        /** Used to search the repair reason */
        search_name: '',

        /** Used to store the repair reason and pagination */
        repairReasons: this.repairreasonsdata.data,
        pagination: this.paginationdata,

        /** Used to store the error messages */
        validationErrors: {},
    };
},
methods: {

    /** To close the add repair reason popup and make fields empty */
    closeAddModal(){
        this.validationErrors = {};
        this.reason_name = '';
    },

    /** To get the records on page change */
    async getRepairReasons( page = 1, search_name = this.search_name){
        show_ajax_loader();
        try {
            const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/repair-reasons?page=${page}${search_name && search_name !== "" ? `&search=${search_name}` : ""}`);
            if (response.data.success == true) {
                if (response.data.repairReasonData && response.data.pagination )
                {
                    this.repairReasons = response.data.repairReasonData.data;
                    this.pagination = response.data.pagination;
                    hide_ajax_loader();
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        } catch (error) {
            hide_ajax_loader();
            this.$alertMessage.success = false;
            this.$alertMessage.message = error.response.data.error;
            setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
        }
    },

    /** Save Repair Reason */
    async saveRepairReason(){
        show_ajax_loader();
        let validationPassed = true;
        this.validationErrors = {};
        /* Validate Repair reason */
        if (!this.reason_name) {
            this.validationErrors.reason_name = `Repair Reason name can't be empty.`;
            validationPassed = false;
        }

        /* Stop submission if validation fails */
        if (!validationPassed) {
            hide_ajax_loader();
            return;
        }

        try{
            let reason_name = this.reason_name;
            const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/repair-reasons/create`, {reason_name: reason_name})
            if (response.data.success == true) {
                hide_ajax_loader();
                this.$alertMessage.success = true;
                this.$alertMessage.message = response.data.msg;
                /* Hide the modal */
                hide_admin_popup('#addRepairReasonModalClose');

                /** Make input field empty */
                this.reason_name = '';

                /* Fetch/update data */
                this.getRepairReasons();
            } else if (response.data.errors ) {
                if(response.data.errors.reason_name){
                    this.validationErrors.reason_name = response.data.errors.reason_name[0];
                    hide_ajax_loader();
                }
            } else {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                /* Hide the modal */
                hide_admin_popup('#addRepairReasonModalClose');

                /** Make input field empty */
                this.reason_name = '';

                /* Fetch/update data */
                this.getRepairReasons();
            }
        }
        catch(error) {
            if (error && error.response && error.response.data && error.response.data.error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
                setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
            } else {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
                /* Hide the modal */
                hide_admin_popup('#addRepairReasonModalClose');

                /** Make input field empty */
                this.reason_name = '';

                /* Fetch/update data */
                this.getRepairReasons();
            }
        }
    },

    /** On click of edit button */
    async editRepairReason(repairreason_id) {
        show_ajax_loader();
        this.validationErrors = {};
        try {
            this.repair_reason_id = repairreason_id;
            const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/repair-reasons/edit/${this.repair_reason_id}`);
            if(response.data.editData && response.data.success == true) {
                if(response.data.editData.repair_reason_name){
                    this.updated_reason_name = response.data.editData.repair_reason_name;
                }
                hide_ajax_loader();
            } else {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                /* Hide the modal */
                hide_admin_popup('#editRepairReasonModalClose');

                /* Fetch/update data */
                this.getRepairReasons();
            }
        } catch(error){
            if (error && error.response && error.response.data && error.response.data.error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
                setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
            } else {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
                /* Hide the modal */
                hide_admin_popup('#editRepairReasonModalClose');

                /** Make input field empty */
                this.reason_name = '';

                /* Fetch/update data */
                this.getRepairReasons();
            }
        }
    },

    /** Update repair reason */
    async updateRepairReason(e){
        e.preventDefault();
        show_ajax_loader();

        let validationPassed = true;
        this.validationErrors = {};

        /** Client side validations */
        if (!this.updated_reason_name) {
            this.validationErrors.reason_name = `Repair reason name can't be empty.`;
            validationPassed = false;
        }

        /* Stop submission if validation fails */
        if (!validationPassed) {
            hide_ajax_loader();
            return;
        }
        try{
            const repair_reason_id = this.repair_reason_id;
            const reasonName = this.updated_reason_name;
            const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/repair-reasons/update/${repair_reason_id}`,
                {reason_name: reasonName}
            )

            if (response.data.success == true) {
                hide_ajax_loader();
                this.$alertMessage.success = true;
                this.$alertMessage.message = response.data.msg;
                /* Hide the modal */
                hide_admin_popup('#editRepairReasonModalClose');

                /* Fetch/update data */
                this.getRepairReasons();
            } else if(response.data.errors && response.data.success == false) {
                if(response.data.errors.reason_name){
                    this.validationErrors.reason_name = response.data.errors.reason_name[0];
                }
                hide_ajax_loader();
            } else {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                /* Hide the modal */
                hide_admin_popup('#editRepairReasonModalClose');

                /* Fetch/update data */
                this.getRepairReasons();
            }
        } catch(error) {
            if (error && error.response && error.response.data && error.response.data.error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
                setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
            } else {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'Something went wrong, please try again.';
                /* Hide the modal */
                hide_admin_popup('#editRepairReasonModalClose');

                /* Fetch/update data */
                this.getRepairReasons();
            }
        }
    },

    /** Commented to skip the deletion
    async confirmDelete(id) {
        this.$deleteAlertMessage.data.isDelete = false
        this.$deleteAlertMessage.data.isChangeStatus = true
        this.$deleteAlertMessage.data.itemId = id
        this.$deleteAlertMessage.data.message = 'Are you sure you want to change the status?'
        this.$deleteAlertMessage.data.callback = this.changeStatus
    },
    */

    async confirmDelete() {
        this.$alertMessage.success = false;
        this.$alertMessage.message = 'This is under development.';
    },

    /**
    async changeStatus(id){
        // Client-side validation
        let validationPassed = true;

        // Validate Claim reason
        if (!id) {
            validationPassed = false;
        }

        // Stop submission if validation fails
        if (!validationPassed) {
            this.$alertMessage.success = false;
            this.$alertMessage.message = "Something went wrong, Please try again later.";
            return;
        }

        try {
            const response = await axios.delete(
                `${this.$userAppUrl}smarttiusadmin/repair-reasons/delete/${id}`
            )
            if(response && response.data){
                if (response.data.success == true) {
                    this.$alertMessage.success = true
                    this.$alertMessage.message = response.data.msg
                    setTimeout(
                        () => this.getRepairReasons(),
                        5000
                    );
                } else {
                    this.$alertMessage.success = false
                    this.$alertMessage.message = response.data.message
                    setTimeout(
                        () => this.getRepairReasons(),
                        5000
                    );
                }
            } else {
                this.$alertMessage.success = false
                this.$alertMessage.message = response.data.message
                setTimeout(
                    () => this.getRepairReasons(),
                    5000
                );
            }
        } catch (error) {
            if (error && error.response && error.response.data && error.response.data.error) {
                this.$alertMessage.success = false;
                this.$alertMessage.message = error.response.data.error;
                setTimeout(
                    () => (location.href = `${this.$userAppUrl}smarttiusadmin/`),
                    5000
                );
            } else {
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
                setTimeout(
                    () => this.getRepairReasons(),
                    5000
                );
            }
        }
    },
    */

    /** On click of cancel from search */
    async cancelSearch(){
        show_ajax_loader();
        this.search_name = '';
        this.search_status_filter = '';
        this.getRepairReasons();
        hide_ajax_loader();
    },
},
};
</script>
