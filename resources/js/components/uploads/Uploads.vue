<template>
  <div>
    <div class="dragndrop__status" v-if="files.length">
      <ul class="m-list-inline">
        <li class="m-list-inline__item">Files: {{ files.length }}</li>

        <li class="m-list-inline__item">Percentage: {{ overallProgress }}%</li>

        <li class="m-list-inline__item m-list-inline__item--last">Time remaining: {{ timeRemaining }}</li>
      </ul>
    </div>

    <File 
      v-for="file in files"
      :key="file.id"
      :file="file"
    />
  </div>
</template>

<script>
import File from './File'
import timeremaining from '../../helpers/timeremaining'
import pad from '../../helpers/pad'

export default {
  components: {
    File
  },

  props: {
    files: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      overallProgress: 0,
      secondsRemaining: 0,
      timeRemaining: 'Calculating'
    }
  },

  methods: {
    unfinishedFiles () {
      let i, files = []

      for (i = 0; i < this.files.length; i++) {
        if (this.files[i].finished || this.files[i].cancelled) {
          continue
        }
        
        files.push(this.files[i])
      }

      return files
    },

    updateOverallProgress () {
      let unfinishedFiles = this.unfinishedFiles()

      let totalProgress = 0

      unfinishedFiles.forEach(file => {
        totalProgress += file.progress
      })

      this.overallProgress = parseInt(totalProgress / unfinishedFiles.length || 0)
    },

    updateTimeRemaining () {
      let minutes, seconds

      this.secondsRemaining = 0

      this.unfinishedFiles().forEach(file => {
        file.secondsRemaining = timeremaining.calculate(
          file.totalBytes , file.loadedBytes , file.timeStarted 
        )

        this.secondsRemaining += file.secondsRemaining
      })

      minutes = Math.floor(this.secondsRemaining / 60)

      seconds = this.secondsRemaining - minutes * 60

      this.timeRemaining = pad.left('00', minutes) + ':' + pad.left('00', seconds)
    }
  },

  mounted () {
    window.events.$on('upload:progress', (fileObject, e) => {
      this.updateOverallProgress()
    })

    window.events.$on('upload:init', () => {
      var interval = null

      interval = setInterval(() => {
        if (this.unfinishedFiles().length === 0) {
          this.updateOverallProgress()

           

          clearInterval(interval)

          interval = null
        }

        this.updateTimeRemaining()
       }, 1000)
    })
  }
}
</script>