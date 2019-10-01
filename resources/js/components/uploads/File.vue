<template>
  <div class="dragndrop__file">
    <div class="m-progress">
      <div class="m-progress__label">
        {{ file.file.name }} 

        <span v-if="!file.failed && !file.finished && !file.failed">
          ({{ file.secondsRemaining }} seconds remaining)
        </span> 
      </div>

      <div 
        class="m-progress__fill" 
        :style="{ 'width': `${file.progress}%` }"
        :class="{ 
          'm-progress__fill--finished': file.finished,
          'm-progress__fill--failed': file.failed || file.cancelled 
        }"
      ></div>

      <div class="m-progress__percentage">
        <span v-if="file.failed">Failed</span>

        <span v-if="file.finished">Complete</span>

        <span v-if="file.cancelled">Cancelled</span>

        <span v-if="!file.finished && !file.failed && !file.cancelled">
          {{ file.progress }}%
        </span>
      </div>

      <a 
        href="#" 
        @click.prevent="cancel"
        v-if="!file.finished && !file.cancelled"
      >
        Cancel
      </a>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    file: {
      type: Object,
      required: true
    }
  },

  methods: {
    updateFileObjectProgress (fileObject, e) {
      if (!e.lengthComputable) {
        return
      }

      fileObject.loadedBytes = e.loaded

      fileObject.totalBytes = e.total

      fileObject.progress = Math.ceil((e.loaded / e.total) * 100)

      console.log(fileObject.progress)
    },

    cancel () {
      this.file.xhr()

      this.file.cancelled = true
    }
  },

  mounted () {
    window.events.$on('upload:progress', (fileObject, e) => {
      this.updateFileObjectProgress(fileObject, e)
    })

    window.events.$on('upload:finished', (fileObject, e) => {
      if (fileObject.id === this.file.id) {
        this.file.finished = true
      }
    })

    window.events.$on('upload:failed', (fileObject, e) => {
      if (fileObject.id === this.file.id) {
        this.file.failed = true
      }
    })
  }
}
</script>