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
      </template>
    </b-table>
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
      ]
    }
  },

  methods: {
    edit (user) {
      window.events.$emit('user:edit', user)
    }
  }
}
</script>