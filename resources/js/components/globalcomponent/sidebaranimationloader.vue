<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

.loader-wrap {
  padding: 20px;
  text-align: center;
}
</style>
<template>
    <!-- view section loader -->
    <div v-if="isshow" class="skeleton-wrapper" id="skeleton-wrapper">
        <div class="skeleton title"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line short"></div>
        <div class="skeleton title"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line short"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line short"></div>
        <div class="skeleton line short">
        </div>
        <div class="skeleton line short"></div>
        <div class="skeleton title"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line"></div>
        <div class="skeleton line short"></div>
    </div>
</template>
<script>
export default {
     props: {
        isshow: {
            type: Boolean,
        },
    },
}
</script>