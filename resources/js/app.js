/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')
import store from './store'
import error from './mixins/errors'

window.Vue = require('vue')

import BootstrapVue from 'bootstrap-vue'
Vue.use(BootstrapVue)

window.events = new Vue()

import './helpers/interceptors'

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('comet-files', require('./components/comet/CometFiles.vue').default);
Vue.component('meted-scrape', require('./components/comet/MetedScrape.vue').default);
Vue.component('users-index', require('./components/users/Index.vue').default);
Vue.component('charts-index', require('./components/charts/Index.vue').default);

Vue.mixin(error);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app', 
    store
})
