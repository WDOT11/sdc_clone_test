<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0 ">
            <div class="card-body">
                <h4 class=" border-bottom pb-2 coman_main_heading">Role Setting</h4>

                <!-- For Personal Users -->
                <div class="form-group">
                    <label for="domain" class="form-label mt-3">Role for the personal coverage users <span class="text-danger">*</span></label>
                    <select class="form-control def_14_size" name="personalCoverage" v-model="personalCoverageUserRole">
                        <option value="">Select Role</option>
                        <option v-for="(roles, index) in roleData" :value="roles.id" :key="index"> {{ roles.name }}</option>
                    </select>
                    <small v-if="validationMessage.personalCoverageUserRole" ><ErrorMessage :msg="validationMessage.personalCoverageUserRole"></ErrorMessage></small>
                </div>

                <!-- For Educational Users -->
                <div class="form-group">
                    <label for="domain" class="form-label mt-3">Role for the educational covergae users <span class="text-danger">*</span></label>
                    <select class="form-control def_14_size" name="educationalCoverage" v-model="EducationalCoverageUserRole">
                        <option value="">Select Role</option>
                        <option v-for="(roles, index) in roleData" :value="roles.id" :key="index"> {{ roles.name }}</option>
                    </select>
                    <small v-if="validationMessage.EducationalCoverageUserRole" ><ErrorMessage :msg="validationMessage.EducationalCoverageUserRole"></ErrorMessage></small>
                </div>

                <!-- For New Register Users -->
                <div class="form-group">
                    <label for="domain" class="form-label mt-3">Role for the new register users <span class="text-danger">*</span></label>
                    <select class="form-control def_14_size" name="newUser" v-model="NewRegisterUserRole">
                        <option value="">Select Role</option>
                        <option v-for="(roles, index) in roleData" :value="roles.id" :key="index"> {{ roles.name }}</option>
                    </select>
                    <small v-if="validationMessage.NewRegisterUserRole" ><ErrorMessage :msg="validationMessage.NewRegisterUserRole"></ErrorMessage></small>
                </div>
                <div class="mt-2 justify-content-start">
                    <button type="button" class="btn bg_blue text-white" @click="saveSetting()">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default{
    props: {

        roles: {
            type: Object,
        },
        persoanlcoveragerole: {
            type: String,
        },
        educoveragerole: {
            type: String,
        },
        newuserrole: {
            type: String,
        }

    },
    data() {

        return {
            /** To store the roles */
            roleData: this.roles,

            /** To store the role id from the form */
            personalCoverageUserRole: this.persoanlcoveragerole,
            EducationalCoverageUserRole: this.educoveragerole,
            NewRegisterUserRole: this.newuserrole,

            /** To manage the validation messages */
            validationMessage: {},
        };

    },
    methods: {

        async saveSetting(){
            show_ajax_loader();

            this.validationMessage = {};
            let validationPassed = true;

            if(this.personalCoverageUserRole == ''){
                this.validationMessage.personalCoverageUserRole = "Please select the role for personal coverage users.";
                validationPassed = false;
            }
            if(this.EducationalCoverageUserRole == ''){
                this.validationMessage.EducationalCoverageUserRole = "Please select the role for educational coverage users.";
                validationPassed = false;
            }
            if(this.NewRegisterUserRole == ''){
                this.validationMessage.NewRegisterUserRole = "Please select the role for new register users.";
                validationPassed = false;
            }
            if(!validationPassed){
                hide_ajax_loader();
                return;
            }
            try {
                let response = await axios.post(`${this.$userAppUrl}smarttiusadmin/role-setting/store`, {
                    personalCoverageUserRole: this.personalCoverageUserRole,
                    EducationalCoverageUserRole: this.EducationalCoverageUserRole,
                    NewRegisterUserRole: this.NewRegisterUserRole,
                });

                if(response.data.success == true){
                    hide_ajax_loader();
                    this.$alertMessage.success = true;
                    this.$alertMessage.message = response.data.msg;
                } else if(response.data.errors && response.data.success == false) {
                    hide_ajax_loader();
                    if(response.data.errors.personalCoverageUserRole){
                        this.validationMessage.personalCoverageUserRole = response.data.errors.personalCoverageUserRole[0];
                    }
                    if(response.data.errors.EducationalCoverageUserRole){
                        this.validationMessage.EducationalCoverageUserRole = response.data.errors.EducationalCoverageUserRole[0];
                    }
                    if(response.data.errors.NewRegisterUserRole){
                        this.validationMessage.NewRegisterUserRole = response.data.errors.NewRegisterUserRole[0];
                    }
                } else {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = "something went wrong. Please try again.";
                }
            } catch (error) {
                if (error && error.response && error.response.data && error.response.data.error) {
                    hide_ajax_loader();
                    this.$alertMessage.success = false;
                    this.$alertMessage.message = error.response.data.error;
                    setTimeout(() => (location.href = `${this.$userAppUrl}smarttiusadmin`), 3000);
                }
            }
        }

    }
}
</script>