<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <upload-form />

        <button 
          class="btn btn-primary mt-2"
          @click="submit"
        >Submit</button>
      </div>
    </div>
  </div>
</template>

<script>
import UploadForm from '../uploads/UploadForm'

export default {
  components: {
    UploadForm
  },

  data () {
    return {
      files: []
    }
  },

  methods: {
    submit () {
      axios.post('/comet/uploads/store', {
        files: this.files
      })
        .then(({ data }) => {
          console.log(data)
        })
        .catch(error => {

        })
    }
  },

  mounted () {
    window.events.$on('upload:finished', fileObject => this.files.push(fileObject.codedFilename))
  }
}
</script>