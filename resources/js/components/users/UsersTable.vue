<template>
  <div>
    <b-table 
      :items="users"
      :fields="fields"
    >
      <template v-slot:cell(actions)="row">
        {{ row.item.id }}
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
  }
}
</script>