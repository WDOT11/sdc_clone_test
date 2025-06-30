<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0  ">
            <div class="card-body">
                <h4 class="mb-3 border-bottom pb-2  coman_main_heading">Supply Boxes</h4>

                <!-- Filter and show add model button -->
                <div class="d-flex justify-content-between flex-wrap align-items-center gap-3  ">
                    <button data-bs-toggle="modal" data-bs-target="#rollsModal" class="btn blogal_pbtn_padding bg_blue text-white  d-flex align-items-center gap-10  def_14_size  wmax">
                        <img :src="profileIc" width="22" height="22"> Add
                    </button>

                    <!--  Filter section -->
                    <div class="d-flex justify-content-between flex-wrap search_options align-items-start gap-3">
                        <!-- Search by box name -->
                        <input type="text" v-model="search_name" class="form-control def_14_size w-auto" placeholder="Filter by Name">
                        <!-- buttons -->
                        <div class="d-flex gap-2 ">
                            <button class="btn bg_blue def_14_size d-flex align-items-center gap-10 text-white blogal_pbtn_padding" @click="getSupplyBoxData"><img :src="searchIc" width="20" height="20"> Search</button>
                            <button class="btn customm_reset_btn def_14_size blogal_pbtn_padding" @click="cancelSearch">Clear</button>
                        </div>
                    </div>
                </div>

                <!-- Add Supply Box Modal -->
                <div class="modal fade" id="rollsModal">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <div class="d-flex align-items-center gap-10 ">
                                    <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                    <h4 class="def_22_size modal-title mb-0">Add New Supply Box</h4>
                                </div>
                                <button type="button" class="btn-close" @click="closeAddModal" id="addBoxClose" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">
                                <!-- Box name input -->
                                <label class="form-label ">Box Name <span class="text-danger">*</span></label>
                                <input v-model="boxName" type="text"  placeholder="Enter box name" :class="['form-control def_14_size',{ 'is-invalid': validationErrors.boxName },]" />
                                <small v-if="validationErrors.boxName" ><ErrorMessage :msg="validationErrors.boxName"></ErrorMessage></small>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg">
                                <button @click="saveBox" class="btn bg_blue text-white  "> Save </button>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Add Box Modal End -->

                <!-- Edit Box Modal -->
                <div  class="modal fade" id="editModal">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                            <!-- Modal Header -->
                            <div class="modal-header align-items-center">
                                <div class="d-flex align-items-center gap-10 ">
                                    <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                        <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                        <span>Edit Supply Box</span></h4>
                                </div>
                                <button type="button" class="btn-close" id="boxEditClose" data-bs-dismiss="modal"></button>
                            </div>

                            <!-- Modal body -->
                            <div class="modal-body onewhitebg">

                                <!-- Box Name -->
                                <div class="editBoxName">
                                    <label class="form-label ">Box Name <span class="text-danger">*</span></label>
                                    <input v-model="updated_box_name" type="text" class="" placeholder="Enter Box name" :class="['form-control def_14_size',{ 'is-invalid': validationErrors.boxName },]" />
                                    <small v-if="validationErrors.boxName" ><ErrorMessage :msg="validationErrors.boxName"></ErrorMessage></small>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div class="modal-footer onewhitebg justify-content-between">
                                <button @click="updateBox(updateID)" class="btn bg_blue text-white  ">
                                    Save
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Edit Box Modal End -->

                <!-- Table to show the box data -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size">
                        <thead class="table-light">
                            <tr>
                                <th width="60" >#</th>
                                <th >Name</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="supplyBoxes.length > 0" v-for="(box, index) in supplyBoxes" :key="box.id">
                                <td> {{ (pagination.current_page - 1) * pagination.per_page + index + 1 }} </td>
                                <td> {{ box.name }} </td>
                                <td>
                                    <div class="d-flex align-items-center gap-20">
                                        <button  data-bs-toggle="modal" data-bs-target="#editModal" @click="editBox(box.id)" class=" globaleditbtn extrabtns fw-semibold themetextcolor   rounded-pill text-decoration-none d-flex align-items-center def_14_size gap-1   editBtn">
                                            <img  @click="editBox(box.id)" data-bs-toggle="modal" data-bs-target="#editModal" :src="editIc" width="35" height="35">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="3"> No box data found! </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5" :paginate="getSupplyBoxData">
                </pagination>
            </div>
        </div>
    </div>
</template>
<script>
export default {

    props: {
        shippingsupplyboxdata:{
            type:Object
        },
        paginationdata:{
            type: Object
        },
    },

    data() {
        return {
            /** Icons */
            profileIc: this.$profileIcIcon,
            searchIc: this.$searchIcIcon,
            editIc: this.$editIcIcon,

            /** Supply box data */
            pagination: this.paginationdata,
            supplyBoxes: this.shippingsupplyboxdata.data,

            /** To manage the data while creating/updating a new box data */
            boxName: '',
            updated_box_name: '',
            updateID: '',

            /** Used to manage the searching */
            search_name : '',

            /* Store validation errors here */
            validationErrors: {}
        };
    },

    methods: {

        /** To get the supply boxes on page change */
        async getSupplyBoxData(page = 1, searched_name = this.search_name) {
            try {
                show_ajax_loader();
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/shipping-supply-boxes?page=${page}${searched_name && searched_name !== "" ? `&search=${searched_name}` : ""}`);
                if (response && response.data) {
                    if (response.data.success == true) {
                        if ( response.data.shippingSupplyBoxData && response.data.shippingSupplyBoxData.data && response.data.pagination ) {
                            this.supplyBoxes = response.data.shippingSupplyBoxData.data;
                            this.pagination = response.data.pagination;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = "Something went wrong, Please try again later.";
                        }
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                    setTimeout(
                        () => (location.href = `${this.$userAppUrl}smarttiusadmin`),
                        4000
                    );
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        cancelSearch() {
            this.search_name = '';
            this.getSupplyBoxData();
        },

        /** To save the box data */
        async saveBox(){
            this.validationErrors = {};
            if (this.boxName.trim() == '') {
                this.validationErrors.boxName = 'Box name is required.';
                return;
            }

            try {
                show_ajax_loader();
                const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/shipping-supply-boxes/create`, { name: this.boxName });
                if (response && response.data) {
                    if (response.data.success == true) {
                        this.getSupplyBoxData();
                        /* Hide the modal */
                        hide_admin_popup('#addBoxClose');
                        this.closeAddModal();
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = "Supply box added successfully.";
                    }
                    else if (response.data.errors ) {
                        if(response.data.errors.name){
                            this.validationErrors.boxName = response.data.errors.name[0];
                        }
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                hide_ajax_loader();
                if (error && error.response && error.response.data && error.response.data.error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                } else {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            }
        },

        /** TO close the add role model and make the form empty */
        closeAddModal(){
            this.boxName = '';
            this.validationErrors = {};
        },

        /** To edit the box data */
        async editBox(id) {
            show_ajax_loader();
            this.validationErrors = {};
            try {
                const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/shipping-supply-boxes/edit/${id}`);
                if (response && response.data) {
                    if (response.data.success == true) {
                        this.updateID = id;
                        this.updated_box_name = response.data.supplyBox.name;
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
            }
        },

        /** To update the box data */
        async updateBox(id) {
             let validationPassed = true;
            show_ajax_loader();

            this.validationErrors = {};

            /* Validate Device Brand */
            if (!this.updated_box_name) {
                this.validationErrors.boxName = 'Please enter box name.';
                validationPassed = false;
            }

            /* Stop submission if validation fails */
            if (!validationPassed) {
                hide_ajax_loader();
                return;
            }

            try {
                show_ajax_loader();
                this.validationErrors = {};
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/shipping-supply-boxes/update/${id}`, {
                    name: this.updated_box_name,
                });
                if (response && response.data) {

                    if (response.data.success == true) {
                        this.getSupplyBoxData();
                        hide_admin_popup('#boxEditClose');
                        this.closeAddModal();
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = "Supply box updated successfully.";
                    } else if (response.data.errors ) {
                        if(response.data.errors.name){
                            this.validationErrors.boxName = response.data.errors.name[0];
                        }
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "Something went wrong, Please try again later.";
                }
            } catch (error) {
                hide_ajax_loader();
                this.$alertMessage.success = false;
                this.$alertMessage.message = "Something went wrong, Please try again later.";
            }
        }

    }
};
</script>