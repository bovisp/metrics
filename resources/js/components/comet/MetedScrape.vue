<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <button 
          class="btn btn-primary"
          @click="scrape"
          :disable="started"
        >
          {{ buttonText }}
        </button>

        <div 
          class="m-progress mt-3 position-relative" 
          v-if="totalPages !== 0 && completedPage !== totalPages && !this.cancel"
        >
          <div class="m-progress__label">
            {{ completedPage }} of {{ totalPages }} pages completed
          </div>

          <div 
            class="m-progress__fill" 
            :style="{ 'width': `${percentage}%` }"
          ></div>

          <div class="m-progress__percentage">
            {{ percentage }}%
          </div>
        </div>

        <div>
          <button 
            class="btn btn-sm btn-text text-danger mt-3"
            v-if="started && !cancel"
            @click="cancelling"
          >
            Cancel MetEd scrape
          </button>
        </div>

        <div 
          class="alert alert-success mt-3"
          v-if="finished"
        >
          All MetEd modules successfully scraped.
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      completedPage: 0,
      totalPages: 0,
      percentage: 0,
      cancel: false,
      started: false,
      finished: false
    }
  },

  computed: {
    buttonText () {
      return this.started ? 'Scraping MetEd pages...' : 'Perform MetEd Scrape'
    }
  },

  methods: {
    async scrape () {
      this.cancel = false

      this.started = true

      this.finished = false

      let { data } = await axios.get('/api/comet/scrape')

      if (this.cancel) {
        this.started = false

        return
      }

      this.totalPages = data

      for (let i = 1; i <= data; i++) {
        if (this.cancel) {
          this.reset()

          return
        }

        let { data } = await axios.get(`/api/comet/scrape/${i}`)

        this.completedPage = data
        
        this.percentage = Math.floor((this.completedPage / this.totalPages) * 100)
      }

      this.finished = true

      this.reset()
    },

    cancelling () {
      this.cancel = true

      this.$bvToast.toast(`MetEd scrape cancelled. This may take a minute...`, {
        autoHideDelay: 5000,
        variant: 'danger',
        toaster: 'b-toaster-top-center'
      })
    },

    reset () {
      this.totalPages = 0

      this.completedPage = 0

      this.percentage = 0

      this.started = false
    }
  }
}
</script>