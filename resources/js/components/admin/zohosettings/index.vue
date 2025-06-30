<template>
    <div class="container-fluid mt_12">
        <div class="card onewhitebg border-0 ">
            <div class="card-body">
                <h4 class=" border-bottom pb-2 coman_main_heading">Manage Zoho Setting</h4>
                <!-- Domain Field -->
                <div class="mb-3 row mt-3">

                    <div class="form-group">
                        <label for="domain" class="form-label">Domain</label>
                        <input type="text" v-model="domainName" class="form-control" id="domain" placeholder="Enter Domain">
                    </div>
                </div>

                <!-- Client ID Field -->
                <div class="mb-3 row">

                    <div class="form-group ">
                        <label for="zohoClientId" class="form-label">Client ID</label>
                        <input type="text" v-model="clientId" class="form-control" id="zohoClientId" placeholder="Enter Client ID">
                    </div>
                </div>

                <!-- Client Secret Field -->
                <div class="mb-3 row">

                    <div class="form-group">
                        <label for="zohoClientSecret" class="form-label">Client Secret</label>
                        <input type="text" v-model="clientSecret" class="form-control" id="zohoClientSecret" placeholder="Enter Client Secret">
                    </div>
                </div>

                <!-- Authorization Redirect URL Field -->
                <div class="mb-3 row">

                    <div class="form-group">
                        <label for="zohoAuthRedirecturl" class="form-label">Authorization Redirect URL</label>
                        <input type="text" v-model="authRedirectionUrl" class="form-control" id="zohoAuthRedirecturl" placeholder="Enter Authorization Redirect URL">
                    </div>
                </div>

                <!-- Buttons -->
                <!-- <div class="row mt-5">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="button" class="btn btn-success me-2" @click="verifyData()">Verify</button>
                        <button type="button" class="btn bg_blue text-white" @click="refreshToken()">Refresh Token</button>
                    </div>
                </div> -->
                <!-- <div>
                    <a :href="authUrl" class="btn btn-info">
                        Get Access Token
                    </a>
                </div> -->
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        data(){
            return {
                domainName: 'zoho.com',
                clientId: '1000.OX647ZLFPSUUHTKTM3POGHNEG4L7GB',
                clientSecret: '5e16b604449b7fa9caab7d1970740a2bf2a4f2c942',
                authRedirectionUrl: 'https://app.smarttechinsurance.com/zoho/first-handshake/',
                // 1000.0c0c0c24aa56a7b790fdb1c62daeac33.b69deea1c7cec85abf9085f9b57dac00
            }
        },
        computed: {
            authUrl() {
            return `https://accounts.${this.domainName}/oauth/v2/auth?scope=ZohoCRM.modules.ALL&client_id=${this.clientId}&redirect_uri=${this.authRedirectionUrl}&response_type=code&prompt=consent&access_type=offline`;
            },
        },
        methods: {

            /** on click of varify to get the code*/
            async verifyData(){
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'This is under development.';

                // const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/zoho-setting/verifydata`,{
                //     domainName: this.domainName,
                //     clientId: this.clientId,
                //     clientSecret: this.clientSecret,
                //     authRedirectionUrl: this.authRedirectionUrl
                // });
                // console.log("response>> ", response);

            },

            /** Varify data after getting the auth code*/
            /**
            async verifyCallback(){
                console.log("generate key clicked");
                const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/zoho-setting/verifydata`,{
                    domainName: this.domainName,
                    clientId: this.clientId,
                    clientSecret: this.clientSecret,
                    authRedirectionUrl: this.authRedirectionUrl
                });
                console.log("response>> ", response);
            },
            */

            async refreshToken(){
                this.$alertMessage.success = false;
                this.$alertMessage.message = 'This is under development.';
                // console.log("refresh tokenbutton clicked");
                // const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/zoho-setting/refreshtoken`);

                // console.log("response>> ", response);

                // if(response.data.success == true){
                //     console.log("if true");
                // } else {
                //     console.log("else true");
                // }
            },
        }
    }
</script>