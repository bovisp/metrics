<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <template v-if="uploading">
          <upload-form />

          <button 
            class="btn btn-primary mt-2"
            @click="submit"
            :disabled="isLoading"
          >
            <b-spinner small v-if="isLoading" class="mr-1"></b-spinner> {{ buttonText }}
          </button>
        </template>

        <template v-else>
          <CorrectCourses 
            v-if="uniqueUnmatchedCourses && courses && !showAlert"
            :courses="courses"
            :unique-unmatched-courses="uniqueUnmatchedCourses"
          />

          <template v-if="showAlert">
            <div 
              class="alert mb-0"
              :class="`alert-${alertType}`"
            >
              {{ alertMessage }}
            </div>

            <div class="d-flex">
              <a href="../../" class="btn btn-link ml-auto">
                &larr; Back to homepage
              </a>
            </div>
          </template>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import UploadForm from '../uploads/UploadForm'
import CorrectCourses from './CorrectCourses'
import { map, uniq, flatten, forEach } from 'lodash-es'

export default {
  components: {
    UploadForm,
    CorrectCourses
  },

  data () {
    return {
      files: [],
      courses: [],
      unmatchedCourses: [],
      uniqueUnmatchedCourses: [],
      uploading: true,
      alertMessage: '',
      alertType: 'success',
      showAlert: false,
      isLoading: false
    }
  },

  computed: {
    buttonText () {
      return this.isLoading === true ? 'Saving data...' : 'Submit'
    }
  },

  methods: {
    async submit () {
      this.isLoading = true

      let { data } = await axios.post('/comet/uploads/store', {
        files: this.files
      })

      this.courses = data.courseTitles

      this.unmatchedCourses = data.nonMatchedCourses

      this.uniqueUnmatchedCourses = await this.getUniqueUnmatchedCourses()

      this.uploading = false

      this.isLoading = false

      if (this.hasNoUnmatchedCourses() === true) {
        this.showAlert = true

        this.alertMessage = 'COMET statistics successfully added.'
      }
    },

    async store () {
      let { data } = await axios.post('/comet/corrections', {
        'completions': this.unmatchedCourses['completions'],
        'views': this.unmatchedCourses['views']
      })

      window.events.$emit('comet:corrections-saved')

      this.showAlert = true

      this.alertMessage = 'Module titles corrected and COMET statistics successfully added.'
    },

    hasNoUnmatchedCourses () {
      return this.unmatchedCourses['views'].length === 0 &&
       this.unmatchedCourses['completions'].length === 0
    },

    async getUniqueUnmatchedCourses () {
      let uniqueCourses = []

      await forEach(Object.keys(this.unmatchedCourses), type => {
        uniqueCourses.push(this.unmatchedCourses[type])
      })

      uniqueCourses = await map(flatten(uniqueCourses), course => {
        return course[10]
      })

      uniqueCourses = await uniq(uniqueCourses)

      return uniqueCourses
    },

    async correctCourses (course, corrected) {
      await forEach(Object.keys(this.unmatchedCourses), async (type) => {
        await forEach(this.unmatchedCourses[type], (unmatchedCourse, index) => {
          if (course === this.unmatchedCourses[type][index][10]) {
            this.unmatchedCourses[type][index][10] = corrected
            this.unmatchedCourses[type][index].push(course)
          }
        })
      })
    }
  },

  mounted () {
    window.events.$on('upload:finished', fileObject => this.files.push(fileObject.codedFilename))

    window.events.$on('comet:match', ({course, corrected}) => {
      this.correctCourses(course, corrected)
    })

    window.events.$on('comet:submit', () => this.store())
  }
}
</script>