/** App.vue */
<template>
    <alert-component
        v-if="showAlert"
        :message="alertMessage"
        @close="handleAlertClose"
    />
    <delete-alert />

</template>

<script>
import { computed } from "vue";

export default {
    computed: {
        showAlert() {
            return this.$alertMessage.message !== "";
        },
        alertMessage() {
            return this.$alertMessage;
        },

    },

    methods: {
        handleAlertClose() {
            this.$alertMessage.message = "";
            this.$alertMessage.success = false;
        },
        

    },
};
</script>
