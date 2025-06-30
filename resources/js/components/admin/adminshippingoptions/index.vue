<template>
    <div class="container-fluid ">
        <div class="card onewhitebg border-0   mt_12">
            <div class="card-body">
                <h4 class=" mb-3 border-bottom pb-2 coman_main_heading">Shipping Options for Claims</h4>

                <div class="d-flex justify-content-between flex-wrap align-items-start gap-3 ">
                    <button data-bs-toggle="modal" data-bs-target="#NewShippingOption" class="btn blogal_pbtn_padding def_14_size bg_blue wmax text-white d-flex align-items-center gap-10  rounded ">
                        <img :src="profileIc" width="22" height="22">
                        Add
                    </button>
                </div>

                <!-- Add shipping option Modal -->
                <div class="modal" id="NewShippingOption">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg id="fi_10109280" enable-background="new 0 0 32 32" height="30" viewBox="0 0 32 32" width="30" xmlns="http://www.w3.org/2000/svg"><path d="m3.9257812 24.1850586c0 2.675293 2.1767578 4.8520508 4.8515625 4.8520508h9.1875c.5527344 0 1-.4477539 1-1 0-.2763062-.1120605-.5263672-.2931519-.7073364-.1809082-.1807861-.4306641-.2926636-.7068481-.2926636h-9.1875c-1.5722656 0-2.8515625-1.2792969-2.8515625-2.8520508v-17.3330078c0-1.5727539 1.2792969-2.8520508 2.8515626-2.8520508h13.4814453c1.5732422 0 2.8525391 1.2792969 2.8525391 2.8520508v13.0380859c0 .2761841.1118774.5261841.2928467.7071533.1809082.1809082.4308472.2928467.7071533.2928467.5527344 0 1-.4477539 1-1v-13.0380859c-.0000001-2.675293-2.1767579-4.8520508-4.8525391-4.8520508h-13.4814453c-2.6748047 0-4.8515626 2.1767578-4.8515626 4.8520508z"></path><path d="m21.2958984 14.5185547h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 9.7036133h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m21.2958984 19.3334961h-7.703125c-.5527344 0-1 .4477539-1 1s.4472656 1 1 1h7.703125c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 9.7036133h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 14.5185547h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m9.7607422 19.3334961h-.0097656c-.5517578 0-.9951172.4477539-.9951172 1s.453125 1 1.0048828 1c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1z"></path><path d="m23.4614868 22.539978c-.1685791.1790161-.2759399.4168701-.2759399.6821899v1.8891602h-1.8896484c-.2648926 0-.5020142.1071777-.6808472.2750854-.1949768.1850147-.3185596.4377747-.3191528.7249146 0 .5522461.4472656 1 1 1h1.8896484v1.8886719c0 .5522461.4472656 1 1 1s1-.4477539 1-1v-1.8886719h1.8886719c.5527344 0 1-.4477539 1-1s-.4472656-1-1-1h-1.8886719v-1.8891601c0-.5522461-.4472656-1-1-1-.2868576.0005569-.5392342.1235504-.7240601.31781z"></path></svg>
                                <span>Add New Shipping Option</span>
                            </h4>
                            <button type="button" class="btn-close" id="addShippingOptionModalClose" @click="closeAddModal" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body onewhitebg">
                            <div>
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input v-model="name" type="text"  placeholder="Enter Option Name" :class="['form-control',{ 'is-invalid': validationErrors.name },]" />
                            <small v-if="validationErrors.name" ><ErrorMessage :msg="validationErrors.name"></ErrorMessage></small>
                            </div>
                            <div>
                            <label class="mt-3 form-label">Price <span class="text-danger">*</span></label>
                            <input v-model="price" type="text"  placeholder="Enter Price" :class="['form-control',{ 'is-invalid': validationErrors.price },]" />
                            <small v-if="validationErrors.price" ><ErrorMessage :msg="validationErrors.price"></ErrorMessage></small>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <!-- <button data-bs-dismiss="modal" @click="closeAddModal" class="btn btnblack text-white  rounded mr-2">
                                Cancel
                            </button> -->
                            <button @click="saveOption" class="btn bg_blue text-white  ">
                                Save
                            </button>

                        </div>

                        </div>
                    </div>
                </div>
                <!-- Add shipping option Modal End -->

                <!-- Edit shipping option Modal -->
                <div class="modal" id="EditShippingOption">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content onewhitebg">

                        <!-- Modal Header -->
                        <div class="modal-header align-items-center">
                            <h4 class="modal-title def_20_size d-flex gap-10 mb-0 align-items-center">
                                <svg id="fi_2356780" enable-background="new 0 0 511.984 511.984" height="30" viewBox="0 0 511.984 511.984" width="30" xmlns="http://www.w3.org/2000/svg"><g><path d="m415 221.984c-8.284 0-15 6.716-15 15v220c0 13.785-11.215 25-25 25h-320c-13.785 0-25-11.215-25-25v-320c0-13.785 11.215-25 25-25h220c8.284 0 15-6.716 15-15s-6.716-15-15-15h-220c-30.327 0-55 24.673-55 55v320c0 30.327 24.673 55 55 55h320c30.327 0 55-24.673 55-55v-220c0-8.284-6.716-15-15-15z"></path><path d="m501.749 38.52-28.285-28.285c-13.645-13.646-35.849-13.646-49.497 0l-226.273 226.274c-2.094 2.094-3.521 4.761-4.103 7.665l-14.143 70.711c-.983 4.918.556 10.002 4.103 13.548 2.841 2.841 6.668 4.394 10.606 4.394.979 0 1.963-.096 2.941-.291l70.711-14.143c2.904-.581 5.571-2.009 7.665-4.103l226.275-226.273s.001 0 .001-.001c13.645-13.645 13.645-35.849-.001-49.496zm-244.276 251.346-44.194 8.84 8.84-44.194 184.17-184.173 35.356 35.356zm223.063-223.062-17.678 17.678-35.356-35.356 17.677-17.677c1.95-1.95 5.122-1.951 7.072-.001l28.284 28.285c1.951 1.949 1.951 5.122.001 7.071z"></path></g></svg>
                                <span>Edit Shipping Option</span>
                            </h4>
                            <button type="button" class="btn-close" id="editShippingSupplyModalClose" data-bs-dismiss="modal"></button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body onewhitebg">
                            <div>
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input v-model="updatedName" type="text"  placeholder="Enter Option Name" :class="['form-control',{ 'is-invalid': validationErrors.updatedName },]" />
                            <small v-if="validationErrors.updatedName" ><ErrorMessage :msg="validationErrors.updatedName"></ErrorMessage></small>
                            </div>
                            <div>
                            <label class="mt-3 form-label">Price <span class="text-danger">*</span></label>
                            <input v-model="updatedPrice" type="text"  placeholder="Enter Price" :class="['form-control',{ 'is-invalid': validationErrors.updatedPrice },]" />
                            <small v-if="validationErrors.updatedPrice" ><ErrorMessage :msg="validationErrors.updatedPrice"></ErrorMessage></small>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="modal-footer onewhitebg">
                            <!-- <button data-bs-toggle="modal" data-bs-target="#EditShippingOption" @click="cancelEdit" class="btn btnblack text-white  rounded mr-2">
                                Cancel
                            </button> -->
                            <button @click="updateShippingOption" class="btn bg_blue text-white  ">
                                Save
                            </button>
                        </div>
                        </div>
                    </div>
                </div>
                <!-- Edit Permission Modal End -->

                <!-- Table to show the Shiping options -->
                <div class="table-responsive mt-3">
                    <table class="table table-bordered table-hover def_14_size">
                        <thead class="table-light">
                            <tr>
                                <th  width="60" >#</th>
                                <th >Name</th>
                                <th >Price</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="shippingOptions.length > 0" v-for="(shippingOption, index) in shippingOptions" :key="shippingOption.id">
                                <td>{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                                <td> {{ shippingOption.name }} </td>
                                <td> ${{ shippingOption.price }} </td>
                                <td>
                                    <div class="d-flex align-items-center gap-20">
                                    <button data-bs-toggle="modal" data-bs-target="#EditShippingOption" @click="editOption(shippingOption.id)" class="themetextcolor globaleditbtn extrabtns  rounded-pill d-flex align-items-center def_14_size gap-1  rounded editBtn"><img :src="editIc" width="35" height="35"><span class="edittext_Gl">Edit</span></button>
                                    <!-- <button @click="confirmDelete(shippingOption.id)" class=" bg-transparent text-white rounded"><img :src="deleteIc" width="28" height="28"></button> -->
                                    </div>
                                </td>
                            </tr>
                            <tr v-else>
                                <td colspan="4">
                                    No Shipping Options Found!
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="3" :paginate="getShippingOptions"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            shippingoptions: {
                type: Object
            },
            paginationdata: {
                type: Object
            },
        },
        data() {
            return {
                /**Icons */
                profileIc: this.$profileIcIcon,
                editIc: this.$editIcIcon,

                /** To store the listing data */
                shippingOptions : this.shippingoptions.data,
                pagination : this.paginationdata,

                /** To store the option id */
                optionId: '',

                /** To store the add form data */
                name: '',
                price: '',

                /** To store the edit form data */
                updatedName: '',
                updatedPrice: '',

                validationErrors: {},
            };
        },
        methods: {
            /** To show the add model */
            // openAddModal() {
            //     show_ajax_loader();
            //     this.showAddModal = true;
            //     this.name = '';
            //     this.price = '';
            //     this.validationErrors = {};
            //     hide_ajax_loader();
            // },

            /** To hide the add model */
            closeAddModal() {
                this.name = '';
                this.price = '';
                this.validationErrors = {};
            },

            /** To hide the edit model */
            // cancelEdit() {
            //     show_ajax_loader();
            //     this.showEditModal = false;
            //     this.updatedName = '';
            //     this.updatedPrice = '';
            //     this.validationErrors = {};
            //     hide_ajax_loader();
            // },

            /** To show the listing(Pagination) */
            async getShippingOptions(page = 1){
                show_ajax_loader();
                try {
                    const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/shipping-options?page=${page}`);
                    if (response.data.success == true) {
                        if (response.data.shippingOptionsData && response.data.pagination )
                        {
                            this.shippingOptions = response.data.shippingOptionsData.data;
                            this.pagination = response.data.pagination;
                            hide_ajax_loader();
                        } else {
                            hide_ajax_loader();
                            this.$alertMessage.success = false;
                            this.$alertMessage.message = "Something went wrong, Please try again later.";
                        }
                    }
                } catch (error) {
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                }
            },

            /** To save the shipping option data */
            async saveOption() {
                show_ajax_loader();
                let validationPassed = true;
                this.validationErrors = {};
                /* Validate add option form */
                if (!this.name) {
                    this.validationErrors.name = `Shipping option name can't be empty.`;
                    validationPassed = false;
                }

                if (!this.price) {
                    this.validationErrors.price = `Shipping option price can't be empty.`;
                    validationPassed = false;
                }

                /* Stop submission if validation fails */
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }

                try{
                    let name = this.name;
                    let price = this.price;

                    const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/shipping-options/store`, {name: name, price: price},);
                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        /* Hide the modal */
                        hide_admin_popup('#addShippingOptionModalClose');

                        /** Make input field empty */
                        this.name = '';
                        this.price = '';

                        /* Fetch/update data */
                        this.getShippingOptions();
                    } else if (response.data.errors ) {
                        if(response.data.errors.name){
                            this.validationErrors.name = response.data.errors.name[0];
                        }
                        if(response.data.errors.price){
                            this.validationErrors.price = response.data.errors.price[0];
                        }
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                        /* Hide the modal */
                        hide_admin_popup('#addShippingOptionModalClose');

                        /** Make input field empty */
                        this.name = '';
                        this.price = '';

                        /* Fetch/update data */
                        this.getShippingOptions();
                    }
                }
                catch(error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = error.response.data.error;
                        setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                        /* Hide the modal */
                        hide_admin_popup('#addShippingOptionModalClose');

                        /** Make input field empty */
                        this.name = '';
                        this.price = '';

                        /* Fetch/update data */
                        this.getShippingOptions();
                    }
                }
            },

            /** On click of edit button */
            async editOption(optionId) {
                show_ajax_loader();
                this.validationErrors = {};
                try {
                    this.optionId = optionId;
                    const response = await axios.get(`${this.$userAppUrl}smarttiusadmin/shipping-options/edit/${this.optionId}`);
                    if(response.data.editData && response.data.success == true) {
                        if(response.data.editData.name){
                            this.updatedName = response.data.editData.name;
                        }
                        if(response.data.editData.price){
                            this.updatedPrice = response.data.editData.price;
                        }
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                        /* Hide the modal */
                        hide_admin_popup('#editShippingSupplyModalClose');

                        /* Fetch/update data */
                        this.getShippingOptions();
                    }
                } catch(error){
                    if (error && error.response && error.response.data && error.response.data.error) {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = error.response.data.error;
                        setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                        /* Hide the modal */
                        hide_admin_popup('#editShippingSupplyModalClose');

                        /** Make input field empty */
                        this.updatedName = '';
                        this.updatedPrice = '';

                        /* Fetch/update data */
                        this.getShippingOptions();
                    }
                }
            },

            /** To update the option data */
            async updateShippingOption(e){
                e.preventDefault();
                show_ajax_loader();

                let validationPassed = true;
                this.validationErrors = {};

                /** Client side validations */
                if (!this.updatedName) {
                    this.validationErrors.updatedName = `Shipping option name can't be empty.`;
                    validationPassed = false;
                }
                if (!this.updatedPrice) {
                    this.validationErrors.updatedPrice = `Shipping option price can't be empty.`;
                    validationPassed = false;
                }

                /* Stop submission if validation fails */
                if (!validationPassed) {
                    hide_ajax_loader();
                    return;
                }
                try{
                    const optionId = this.optionId;
                    const name = this.updatedName;
                    const price = this.updatedPrice;
                    const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/shipping-options/update/${optionId}`,
                        {name: name, price: price},
                    )

                    if (response.data.success == true) {
                        hide_ajax_loader();
                        this.$alertMessage.success = true;
                        this.$alertMessage.message = response.data.msg;
                        /* Hide the modal */
                        hide_admin_popup('#editShippingSupplyModalClose');

                        /* Fetch/update data */
                        this.getShippingOptions();
                    } else if(response.data.errors && response.data.success == false) {
                        if(response.data.errors.name){
                            this.validationErrors.updatedName = response.data.errors.name[0];
                        }
                        if(response.data.errors.price){
                            this.validationErrors.updatedPrice = response.data.errors.price[0];
                        }
                        hide_ajax_loader();
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = "Something went wrong, Please try again later.";
                        /* Hide the modal */
                        hide_admin_popup('#editShippingSupplyModalClose');

                        /* Fetch/update data */
                        this.getShippingOptions();
                    }
                } catch(error) {
                    if (error && error.response && error.response.data && error.response.data.error) {
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = error.response.data.error;
                        setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                    } else {
                        hide_ajax_loader();
                        this.$alertMessage.success = false;
                        this.$alertMessage.message = 'Something went wrong, please try again.';
                        /* Hide the modal */
                        hide_admin_popup('#editShippingSupplyModalClose');

                        /* Fetch/update data */
                        this.getShippingOptions();
                    }
                }
            }
        },
    };
</script>