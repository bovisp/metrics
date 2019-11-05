<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <p>yo</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  async mounted () {
    let { data } = await axios.get('/api/charts')

    console.log(data)
  }
}
</script>