<template>
  <div>
    <h2>
      Update profile
    </h2>

    <form @submit.prevent="updateProfile">
      <div class="form-group">
        <label for="name">Name</label>
        
        <input 
          type="text" 
          id="name" 
          class="form-control"
          :class="{ 'is-invalid': errors.name }"
          v-model="name"
        >

        <div v-if="errors.name" class="text-danger">
          {{ errors.name[0] }}
        </div>
      </div>

      <div class="form-group">
        <label for="email">Name</label>
        
        <input 
          type="email" 
          id="email" 
          class="form-control"
          :class="{ 'is-invalid': errors.email }"
          v-model="email"
        >

        <div v-if="errors.email" class="text-danger">
          {{ errors.email[0] }}
        </div>
      </div>

      <button class="btn btn-primary">
        Update profile
      </button>
    </form>

    <h2 class="mt-5">
      Update password
    </h2>

    <form @submit.prevent="updatePassword">
      <div class="form-group">
        <label for="password">New password</label>
        
        <input 
          type="password" 
          id="password" 
          class="form-control"
          :class="{ 'is-invalid': errors.password }"
          v-model="password"
        >

        <div v-if="errors.password" class="text-danger">
          {{ errors.password[0] }}
        </div>
      </div>

      <div class="form-group">
        <label for="password-confirm">Confirm password</label>
        
        <input 
          type="password" 
          id="password-confirm" 
          class="form-control"
          :class="{ 'is-invalid': errors.password_confirmation }"
          v-model="passwordConfirm"
        >

        <div v-if="errors.password_confirmation" class="text-danger">
          {{ errors.password_confirmation[0] }}
        </div>
      </div>

      <button class="btn btn-primary">
        Update password
      </button>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      name: '',
      email: '',
      password: '',
      passwordConfirm: ''
    }
  },

  methods: {
    async updateProfile () {
      let { data } = await axios.put(`/api/users/${this.user.id}/profile`, {
        name: this.name,
        email: this.email
      })

      this.$bvToast.toast(`Profile updated.`, {
        autoHideDelay: 5000,
        variant: 'success',
        toaster: 'b-toaster-top-center'
      })
    },

    async updatePassword () {
      let { data } = await axios.put(`/api/users/${this.user.id}/password`, {
        password: this.password,
        password_confirmation: this.passwordConfirm
      })

      this.password = ''

      this.passwordConfirm = ''

      this.$bvToast.toast(`Password updated.`, {
        autoHideDelay: 5000,
        variant: 'success',
        toaster: 'b-toaster-top-center'
      })
    }
  },

  mounted () {
    this.email = this.user.email
    
    this.name = this.user.name
  }
}
</script>