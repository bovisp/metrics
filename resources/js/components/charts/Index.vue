<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="row mb-2">
          <div class="col">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="radio" 
                id="annualDate" 
                value="annual" 
                v-model="dateType"
              >

              <label class="form-check-label" for="annualDate">
                Select a fiscal year
              </label>
            </div>
          </div>
        </div>

        <div class="row" v-if="dateType === 'annual'">
          <div class="col-md-6">
            <div class="form-group">
              <select id="fiscal" v-model="fiscal" class="form-control">
                <option value="">Fiscal year 2020</option>
                <option value="">Fiscal year 2019</option>
                <option value="">Fiscal year 2018</option>
                <option value="">Fiscal year 2017</option>
                <option value="">Fiscal year 2016</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="radio" 
                id="quarterlyDate" 
                value="quarter" 
                v-model="dateType"
              >

              <label class="form-check-label" for="quarterlyDate">
                Select a quarter
              </label>
            </div>
          </div>
        </div>

        <div class="row" v-if="dateType === 'quarter'">
          <div class="col-md-6">
            <div class="form-group">
              <select id="quarter" v-model="quarter" class="form-control">
                <option value="">Fiscal year 2020</option>
                <option value="">Fiscal year 2019</option>
                <option value="">Fiscal year 2018</option>
                <option value="">Fiscal year 2017</option>
                <option value="">Fiscal year 2016</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col">
            <div class="form-check">
              <input 
                class="form-check-input" 
                type="radio" 
                id="customDate" 
                value="custom" 
                v-model="dateType"
              >

              <label class="form-check-label" for="customDate">
                Select a custom date
              </label>
            </div>
          </div>
        </div>

        <div class="row" v-if="dateType === 'custom'">
          <div class="col-md-6">
            <div class="form-group">
              <label for="start">Start date</label>

              <datepicker
                input-class="form-control"
                v-model="start"
                id="start"
              ></datepicker>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label for="end">End date</label>

              <datepicker
                input-class="form-control"
                v-model="end"
                id="end"
              ></datepicker>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Datepicker from 'vuejs-datepicker';

export default {
  components: {
    Datepicker
  },

  data () {
    return {
      start: '',
      end: '',
      dateType: '',
      fiscal: '',
      quarter: ''
    }
  },

  async mounted () {
    let { data } = await axios.get('/api/charts')

    console.log(data)
  }
}
</script>