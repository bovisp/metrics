<template>
  <div>
    <div class="alert alert-warning">
      There are <strong>{{ uniqueUnmatchedCourses.length }}</strong> module titles that need to be corrected. 
      Please use the dropdown menu to the right of each incorrect title to select the 
      correct title. You may narrow your search results by typing in the dropdown menu. When
      You are finished, click "Submit".
    </div>

    <div class="d-flex">
      <button 
        class="btn btn-primary ml-auto mb-2"
        @click="submit"
        :disabled="isLoading"
      >
        <b-spinner small v-if="isLoading" class="mr-1"></b-spinner> {{ buttonText }}
      </button>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th style ="width: 50%">
            Unmatched courses
          </th>

          <th>
            All MetEd Courses
          </th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="(course, index) in uniqueUnmatchedCourses" :key="index">
          <td style ="width: 50%">{{ course }}</td>

          <td>
            <multiselect 
              :options="flattenedCourses"
              v-model="values[index]"
              @input="changed(course, $event)"
            />
          </td>
        </tr>
      </tbody>
    </table>

    <div class="d-flex">
      <button 
        class="btn btn-primary ml-auto"
        @click="submit"
        :disabled="isLoading"
      >
        <b-spinner small v-if="isLoading" class="mr-1"></b-spinner> {{ buttonText }}
      </button>
    </div>
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import { map } from 'lodash-es'

export default {
  components: {
    Multiselect
  },

  props: {
    courses: {
      type: Array,
      required: true
    },
    uniqueUnmatchedCourses: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      values: [],
      isLoading: false
    }
  },

  computed: {
    flattenedCourses () {
      return map(this.courses, course => {
        return course.title
      })
    },

    buttonText () {
      return this.isLoading === true ? 'Saving data...' : 'Submit'
    }
  },

  methods: {
    changed (course, e) {
      window.events.$emit('comet:match', {course, corrected: e})
    },

    submit () {
      if (this.allCoursesCorrected() === false) {
        this.$bvToast.toast(`You have not corrected some of the titles. Please correct all titles before submitting.`, {
          autoHideDelay: 5000,
          variant: 'danger',
          toaster: 'b-toaster-top-center'
        })

        return
      }

      this.isLoading = true

      window.events.$emit('comet:submit')
    },

    allCoursesCorrected () {
      return this.values.filter(v => v !== null).length === this.uniqueUnmatchedCourses.length
    }
  },

  mounted () {
    window.events.$on('comet:corrections-saved', () => this.isLoading = false)
  }
}
</script>