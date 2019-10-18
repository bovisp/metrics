<template>
  <form @submit.prevent="invite">
    <div class="form-group">
      <label for="email">Enter email address of new user to invite</label>

      <input 
        type="email" 
        class="form-control"
        :class="{ 'is-invalid': errors.email }" 
        v-model="email" 
        id="email"
      >

      <div v-if="errors.email" class="text-danger">
        {{ errors.email[0] }}
      </div>
    </div>

    <button class="btn btn-primary">Invite user</button>
  </form>
</template>

<script>
export default {
  data () {
    return {
      email: ''
    }
  },

  methods: {
    async invite () {
      let { data } = await axios.post('/api/users', {
        email: this.email
      })

      this.email = ''

      window.events.$emit('user:invited')
    }
  }
}
</script>