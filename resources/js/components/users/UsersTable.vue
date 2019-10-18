<template>
  <div>
    <b-table 
      :items="users"
      :fields="fields"
    >
      <template v-slot:cell(actions)="row">
        <button 
          class="btn btn-primary btn-sm"
          @click="edit(row.item)"
        >
          Edit
        </button>

        <b-button 
          v-b-modal.delete-user
          variant="text"
          class="text-danger"
          @click="sendInfo(row.item)"
        >
          Delete
        </b-button>
      </template>
    </b-table>

    <b-modal 
      id="delete-user"
      title="Delete user"
      @cancel="user = {}"
      @ok="destroy"
    >
      <p class="my-4">{{ user }}</p>
    </b-modal>
  </div>
</template>

<script>
export default {
  props: {
    users: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      fields: [
        { key: 'name', label: 'Name' },
        { key: 'email', label: 'Email' },
        { key: 'actions', label: 'Actions' },
      ],
      user: {}
    }
  },

  methods: {
    edit (user) {
      window.events.$emit('user:edit', user)
    },

    sendInfo (user) {
      this.user = user
    },

    async destroy () {
      let { data } = await axios.delete(`/api/users/${this.user.id}`)

      this.$bvToast.toast(`${this.user.name} has been deleted. Refreshing page.`, {
        autoHideDelay: 3000,
        variant: 'success',
        toaster: 'b-toaster-top-center'
      })

      setTimeout(() => {
        location.reload()
      }, 3000)
    }
  }
}
</script>