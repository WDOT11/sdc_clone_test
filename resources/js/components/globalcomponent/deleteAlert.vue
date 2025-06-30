<style scoped>
  .overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.3); /* Optional: Adds a dark overlay effect */
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999999; /* Ensures it appears on top */
  }
.popupdel .media {
  position: relative;
}
 .boxclose {
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background-color: transparent;
    position: absolute;
    top: 25px;
    right: 30px;
    cursor: pointer;
  }

  .popupH {
    background-color: #ffffff;
    border-radius: 5px 5px 0 0;
  }

</style>


<template>
  <!-- To show the delete confirmation popup -->
  <div v-if="isDelete" class="overlay popupdel">
    <div class="media p-4 border-20  onewhitebg fadeIn">
      <button @click="handleClose" class="boxclose">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" x="0" y="0" viewBox="0 0 329.269 329" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M194.8 164.77 323.013 36.555c8.343-8.34 8.343-21.825 0-30.164-8.34-8.34-21.825-8.34-30.164 0L164.633 134.605 36.422 6.391c-8.344-8.34-21.824-8.34-30.164 0-8.344 8.34-8.344 21.824 0 30.164l128.21 128.215L6.259 292.984c-8.344 8.34-8.344 21.825 0 30.164a21.266 21.266 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25l128.21-128.214 128.216 128.214a21.273 21.273 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25 8.343-8.34 8.343-21.824 0-30.164zm0 0" fill="#a9a9a9" opacity="1" data-original="#000000" class=""></path></g></svg>

      </button>
      <div class="popupdelcdcontent   ">
        <div class="popupH p-2 d-flex align-items-center  justify-content-center">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><circle r="256" cx="256" cy="256" fill="#ffe0e3" shape="circle" transform="matrix(1,0,0,1,0,0)"></circle><g transform="matrix(0.4999999999999998,0,0,0.4999999999999998,128.00000000000006,128.00000000000006)"><path d="M56 180h400M216 100h80M121.352 100l16.51-52.097C142.989 31.725 158.125 20 176 20h160c17.875 0 33.011 11.725 38.138 27.903L390.648 100M416 180l-40.396 277.651C372.858 477.067 356.173 492 336 492H176c-20.173 0-36.858-14.933-39.604-34.349L96 180M216 260v152M296 260v152" style="stroke-width:40;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;" fill="none" stroke="#dc3545" stroke-width="40" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" data-original="#000000" opacity="1" class=""></path></g></svg>
        </div>
        <h4 class="def_18_size fw-bold mt-3 text-center footertext">{{ heading }}</h4>
        <div class="px-2 pb-4"><p class="popupdedescription mb-0 footertext">{{ message }}</p></div>
      </div>
      <div class="d-flex justify-content-start align-items-center gap-2 p-2">
        <button @click="handleClose" class="btn border bg-white text-dark w-100 cancel_btn_hover">Cancel</button>
        <button @click="handleConfirm" class="btn btn-danger w-100">Delete</button>
      </div>

    </div>
  </div>

  <!-- To show the logout confirmation popup -->
  <div v-if="isLogout" class="overlay popupdel">
    <div class="media p-4 border-20  onewhitebg fadeIn">
      <button @click="handleClose" class="boxclose">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" x="0" y="0" viewBox="0 0 329.269 329" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M194.8 164.77 323.013 36.555c8.343-8.34 8.343-21.825 0-30.164-8.34-8.34-21.825-8.34-30.164 0L164.633 134.605 36.422 6.391c-8.344-8.34-21.824-8.34-30.164 0-8.344 8.34-8.344 21.824 0 30.164l128.21 128.215L6.259 292.984c-8.344 8.34-8.344 21.825 0 30.164a21.266 21.266 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25l128.21-128.214 128.216 128.214a21.273 21.273 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25 8.343-8.34 8.343-21.824 0-30.164zm0 0" fill="#a9a9a9" opacity="1" data-original="#000000" class=""></path></g></svg>

      </button>
      <div class="popupdelcdcontent   ">
        <div class="popupH p-2 d-flex align-items-center  justify-content-center">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 24 24" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><circle r="12" cx="12" cy="12" fill="#ffe0e3" shape="circle"></circle><g transform="matrix(0.6200000000000001,0,0,0.6200000000000001,4.560000317096709,4.559999909400939)"><path d="M18.488 1.99h-7.485a1 1 0 1 0 0 2h7.485c.843 0 1.529.683 1.529 1.523v12.974c0 .84-.686 1.524-1.529 1.524h-7.485a1 1 0 1 0 0 2h7.485a3.53 3.53 0 0 0 3.529-3.524V5.513a3.53 3.53 0 0 0-3.529-3.524z" fill="#dc3545" opacity="1" data-original="#000000" class=""></path><path d="M5.28 13.003h9.687a1 1 0 1 0 0-2H5.3l3.062-3.27a1 1 0 1 0-1.459-1.367l-4.599 4.911a.988.988 0 0 0-.248.369c-.026.065-.03.134-.041.202-.009.053-.032.1-.032.155l.002.006a.996.996 0 0 0 .269.69l4.65 4.964a.996.996 0 0 0 1.412.047 1 1 0 0 0 .046-1.414z" fill="#dc3545" opacity="1" data-original="#000000" class=""></path></g></svg>
        </div>
        <h4 class="def_18_size fw-bold mt-3 text-center footertext">Ready to Log Out?</h4>
        <div class="px-2 pb-4"><p class="popupdedescription mb-0 footertext">You’ll need to log in again to access your account.</p></div>
      </div>
      <div class="d-flex justify-content-start align-items-center gap-2 p-2">
        <button @click="handleClose" class="btn border bg-white text-dark w-100 cancel_btn_hover">Cancel</button>
        <button @click="handleConfirm" class="btn logout_btn w-100">Log Out</button>
      </div>

    </div>
  </div>

  <!-- To show the change status confirmation popup -->
  <div v-if="isChangeStatus" class="overlay popupdel">
    <div class="media border-20 p-4 onewhitebg fadeIn">
      <button @click="handleClose" class="boxclose">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="16" height="16" x="0" y="0" viewBox="0 0 329.269 329" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M194.8 164.77 323.013 36.555c8.343-8.34 8.343-21.825 0-30.164-8.34-8.34-21.825-8.34-30.164 0L164.633 134.605 36.422 6.391c-8.344-8.34-21.824-8.34-30.164 0-8.344 8.34-8.344 21.824 0 30.164l128.21 128.215L6.259 292.984c-8.344 8.34-8.344 21.825 0 30.164a21.266 21.266 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25l128.21-128.214 128.216 128.214a21.273 21.273 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25 8.343-8.34 8.343-21.824 0-30.164zm0 0" fill="#a9a9a9" opacity="1" data-original="#000000" class=""></path></g></svg>
      </button>
      <div class="popupdelcdcontent  ">
        <div class="popupH p-2 d-flex align-items-center  justify-content-center">
          <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 100 100" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><circle r="50" cx="50" cy="50" fill="#d7ffea" shape="circle"></circle><g transform="matrix(0.7,0,0,0.7,15,15)"><path d="M87 24H25c-4.4 0-8 3.6-8 8v3c0 1.1.9 2 2 2s2-.9 2-2v-3c0-.4.1-.8.2-1.2L43.6 50 21.2 69.2c-.1-.4-.2-.8-.2-1.2v-3c0-1.1-.9-2-2-2s-2 .9-2 2v3c0 4.4 3.6 8 8 8h62c4.4 0 8-3.6 8-8V32c0-4.4-3.6-8-8-8zm-62.8 4.1c.2-.1.5-.1.8-.1h62c.3 0 .6 0 .8.1L57.3 54.2c-.8.6-1.8.6-2.6 0zM87 72H25c-.3 0-.6 0-.8-.1l22.5-19.3 5.4 4.7c1.1 1 2.5 1.5 3.9 1.5s2.8-.5 3.9-1.5l5.4-4.7 22.5 19.3c-.2.1-.5.1-.8.1zm4-4c0 .4-.1.8-.2 1.2L68.4 50l22.4-19.2c.1.4.2.8.2 1.2zM11 45c0-1.1.9-2 2-2h12c1.1 0 2 .9 2 2s-.9 2-2 2H13c-1.1 0-2-.9-2-2zm14 12H7c-1.1 0-2-.9-2-2s.9-2 2-2h18c1.1 0 2 .9 2 2s-.9 2-2 2z" fill="#036934" opacity="1" data-original="#000000" class=""></path></g></svg>
        </div>
        <h4 class="def_18_size fw-bold mt-3 text-center footertext">{{ heading }}</h4>
        <div class="px-2 pb-4"><p class="popupdedescription text-center mb-0 footertext">{{ message }}</p></div>
      </div>
      <div class="d-flex justify-content-center align-items-center gap-2 p-2">
        <button @click="handleClose" class="btn border bg-white text-dark w-100 cancel_btn_hover">Cancel</button>
        <button @click="handleConfirm" class="btn confirmsendemail text-white w-100 ">Confirm</button>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'DeleteAlert',
  computed: {
    isDelete() {
      return this.$deleteAlertMessage.data.isDelete
    },
    isChangeStatus(){
      return this.$deleteAlertMessage.data.isChangeStatus
    },
    isLogout(){
      return this.$deleteAlertMessage.data.isLogout
    },
    heading() {
      return this.$deleteAlertMessage.data.heading
    },
    message() {
      return this.$deleteAlertMessage.data.message
    }
  },
  methods: {
    handleClose() {
      this.$deleteAlertMessage.data.isChangeStatus = false;
      this.$deleteAlertMessage.data.isLogout = false;
      this.$deleteAlertMessage.data.isDelete = false;
      this.$deleteAlertMessage.data.itemId = null;
      this.$deleteAlertMessage.data.callback = null;
    },
    handleConfirm() {
      if (this.$deleteAlertMessage.data.callback) {
        this.$deleteAlertMessage.data.callback(this.$deleteAlertMessage.data.itemId);
      }
      this.handleClose();
    }
  }
}
</script>