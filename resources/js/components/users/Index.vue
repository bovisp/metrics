<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="d-flex mb-3">
          <button 
            class="btn btn-primary ml-auto"
            @click="creating = true"
            v-if="!creating && !editing"
          >
            Add user
          </button>

          <button 
            class="btn btn-text ml-auto"
            @click="cancel"
            v-if="creating || editing"
          >
            {{ creating ? 'Cancel' : 'Back to users page' }}
          </button>
        </div>

        <users-table 
          :users="users" 
          v-if="!editing && !creating"
        />

        <users-create 
          v-if="creating"
        />

        <users-edit
          v-if="editing"
          :user="user"
        />
      </div>
    </div>
  </div>
</template>

<script>
import UsersTable from './UsersTable'
import UsersCreate from './UsersCreate'
import UsersEdit from './UsersEdit'

export default {
  components: {
    UsersTable,
    UsersCreate,
    UsersEdit
  },

  data () {
    return {
      users: [],
      user: {},
      editing: false,
      creating: false
    }
  },

  methods: {
    async fetch () {
      let { data } = await axios.get('/api/users')

      this.users = data
    },

    cancel () {
      this.creating = false

      this.editing = false
    }
  },

  mounted () {
    this.fetch()

    window.events.$on('user:invited', () => {
      this.creating = false

      this.$bvToast.toast(`User invited.`, {
        autoHideDelay: 5000,
        variant: 'success',
        toaster: 'b-toaster-top-center'
      })
    })

    window.events.$on('user:edit', user => {
      this.editing = true

      this.user = user
    })
  }
}
</script>