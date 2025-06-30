<template>
  <div v-if="active">
    <div class="inneroverlay">
      <div class="overlay_inner">
        <svg width="129" height="129" viewBox="-16.125 -16.125 161.25 161.25" version="1.1"
          xmlns="http://www.w3.org/2000/svg" style="transform:rotate(-90deg)">
          <circle r="54.5" cx="64.5" cy="64.5" fill="transparent" stroke="#cccccc" stroke-width="9"></circle>
          <circle r="54.5" cx="64.5" cy="64.5" fill="transparent" stroke="#15c129" stroke-width="9"
            stroke-linecap="round" :stroke-dasharray="circumference" :stroke-dashoffset="strokeOffset"
            style="transition: stroke-dashoffset 0.3s ease" />
          <text x="31px" y="78px" fill="#16b144" font-size="30px" font-weight="bold"
            style="transform:rotate(90deg) translate(0px, -125px)">{{ progressPercentage }}%</text>
        </svg>
        <div class="progress-stats bg-white p-3 rounded mt-2">
          <!-- <div>Total: {{ progress.totalRecords }}</div>
         <div>Processed: {{ processedCount }}</div>
         <div>Inserted: {{ progress.inserted }}</div>
        <div>Updated: {{ progress.updated }}</div>
        <div>Skipped: {{ progress.skipped }}</div> -->

          <div>Processed <b>#{{ processedCount }}</b> Total Records <b>#{{ progress.totalRecords }}</b></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['importid', 'devicedata'],
  data() {
    return {
      importId: this.importid,
      deviceData: this.devicedata,
      active: false,
      progress: {
        totalRecords: 0,
        total: 0,
        inserted: 0,
        updated: 0,
        skipped: 0
      },
      pollInterval: null,
      pollFrequency: 800, // Milliseconds between polls
      retryCount: 0,
      maxRetries: 10
    }
  },
  computed: {
    progressPercentage() {
     /*  if (this.progress.totalRecords == 0) return 0;
      return Math.round((this.processedCount / this.progress.totalRecords) * 100); */
      if (this.progress.totalRecords == 0) return 0;
      const percentage = Math.round((this.processedCount / Number(this.progress.totalRecords)) * 100);
      return Math.min(percentage, 100); // Ensure it never goes over 100
    },
    processedCount() {
      return Number(this.progress.inserted) + Number(this.progress.updated) + Number(this.progress.skipped);
    },
    circumference() {
      return 2 * Math.PI * 54.5;
    },
    strokeOffset() {
      const progress = Math.min(this.progressPercentage, 100);
      return this.circumference * (1 - progress / 100);
      // return this.circumference * (1 - this.progressPercentage / 100);
    }
  },
  mounted() {
    this.startPolling();
  },
  beforeDestroy() {
    this.stopPolling();
  },
  methods: {
    startPolling() {
      this.active = true;
      this.pollInterval = setInterval(this.checkProgress, this.pollFrequency);
    },
    stopPolling() {
      clearInterval(this.pollInterval);
      this.pollInterval = null;
    },
    async checkProgress() {
      try {
        const response = await axios.post(`${this.$userAppUrl}smarttiusadmin/import-devices/progress/${this.importId}`, this.deviceData);

        this.progress = response.data.progress;

        if (response.data.progress.completed) {
          this.stopPolling();
          this.$emit('completed');
        }

        this.retryCount = 0; /* Reset retry counter on success */
      } catch (error) {
        this.retryCount++;

        if (this.retryCount >= this.maxRetries) {
          this.stopPolling();
          this.$emit('error', 'Failed to get progress updates');
        }
      }
    }
  }
}
</script>

<style scoped>
.inneroverlay {
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.79);

  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(5px);
  -webkit-backdrop-filter: blur(5px);
  border: 1px solid rgba(255, 255, 255, 0.3);
  position: absolute;
  left: 0;
  top: 0;
  z-index: 999;
}

.overlay_inner {
  height: 100%;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  gap: 15px;
}
</style>
