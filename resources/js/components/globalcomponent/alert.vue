<template>
    <div v-if="visible" class="fixed fadeIn  z-50  w-3/4 max-w-96  overflow-hidden  shadow-lg rounded-xl      main_toast_wrap" :class="message.success ? 'toastsuccess' : 'toasterror'">
        <div class="card onewhitebg p-4 border-0" >
            <div class="card-body">
              <div class="d-flex justify-content-center" v-if="message.success == true">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g fill-rule="evenodd" clip-rule="evenodd"><path fill="#d5fad6" d="M256 0C114.8 0 0 114.8 0 256s114.8 256 256 256 256-114.8 256-256S397.2 0 256 0z" opacity="1" data-original="#4bae4f" class=""></path><path fill="#08b80d" d="M379.8 169.7c6.2 6.2 6.2 16.4 0 22.6l-150 150c-3.1 3.1-7.2 4.7-11.3 4.7s-8.2-1.6-11.3-4.7l-75-75c-6.2-6.2-6.2-16.4 0-22.6s16.4-6.2 22.6 0l63.7 63.7 138.7-138.7c6.2-6.3 16.4-6.3 22.6 0z" opacity="1" data-original="#ffffff" class=""></path></g></g></svg>
              </div>
              <div class="d-flex justify-content-center" v-if="message.success == false">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g transform="matrix(1.0799999999999994,0,0,1.0799999999999994,-0.9599999999999973,-0.9599999999999973)"><g fill="#000"><circle cx="12" cy="12" r="11" opacity="1" fill="#ffdcdc" data-original="#00000040" class=""></circle><path fill-rule="evenodd" d="M13 7a1 1 0 1 0-2 0v6a1 1 0 1 0 2 0zm-1 11a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" clip-rule="evenodd" fill="#ff0000" opacity="1" data-original="#000000" class=""></path></g></g></svg>
              </div>


                <p class="mb-0 mt-3 text-center footertext">
                    {{ message.message }}
                </p>


            </div>
            <button @click="closePopup" class=" cursor-pointer toast_close focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"  x="0" y="0" viewBox="0 0 329.269 329" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M194.8 164.77 323.013 36.555c8.343-8.34 8.343-21.825 0-30.164-8.34-8.34-21.825-8.34-30.164 0L164.633 134.605 36.422 6.391c-8.344-8.34-21.824-8.34-30.164 0-8.344 8.34-8.344 21.824 0 30.164l128.21 128.215L6.259 292.984c-8.344 8.34-8.344 21.825 0 30.164a21.266 21.266 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25l128.21-128.214 128.216 128.214a21.273 21.273 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25 8.343-8.34 8.343-21.824 0-30.164zm0 0" fill="#a9a9a9" opacity="1" data-original="#000000" class=""></path></g></svg>

            </button>
        </div>
        <div class="progressr active"></div>
    </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  props: {
    message: {
      type: Object,
      required: true,
    },
  },
  setup(props, { emit }) {
    const visible = ref(true);

    const closePopup = () => {
      visible.value = false;
      emit("close");

      const progress = document.querySelector(".progressr");
      if (progress) progress.classList.remove("active");
    };

    onMounted(() => {
      const progress = document.querySelector(".progressr");
      if (progress) progress.classList.add("active");

      setTimeout(() => {
        closePopup();
      }, 5000);
    });

    return { visible, closePopup };
  },
};
</script>


<style>
.toastsuccess {
    /* background-color: #92C12F; */
    color: #fff;
    z-index: 999999 !important;
}
.toasterror {
    /* background-color: #FF062C; */
    color: #fff;
    z-index: 999999 !important;
}
@keyframes slide-down {
    from {
        transform: translate(-50%, -100%);
        opacity: 0;
    }

    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

.toastsuccess .toast_close svg path {
    fill: #92C12F;
}
.toasterror .toast_close svg path {
    fill: #FF062C;
}
.toast_close svg  {
    width: 16px;
    height: 16px;
}
.toast_close  {
  position: absolute;
  right: 16px;
  top: 16px;
}
.animate-slide-down {
    animation: slide-down 0.5s ease-out;
}
.main_toast_wrap {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.main_toast_wrap .progressr {

    bottom: 0;
    left: 0;
    height: 3px !important;
    width: 100%;
  background-color: transparent !important;
  }

  .main_toast_wrap .progressr:before {
    content: "";
    position: absolute;
    bottom: 0;
    right: 0;
    height: 3px;
    width: 100%;
    background-color: #000 !important;
  }

  .main_toast_wrap .progressr.active:before {
    animation: progress 5s linear forwards;
  }

  @keyframes progress {
    100% {
      right: 100%;
    }
  }
</style>
