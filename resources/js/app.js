
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import moment from 'moment';
import { Form, HasError, AlertError } from 'vform'

import Gate from "./Gate";
Vue.prototype.$gate = new Gate(window.user);


import Swal from 'sweetalert2'
window.Swal = Swal;

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});
window.Toast = Swal;


window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

Vue.component('pagination', require('laravel-vue-pagination'));

import PrettyCheckbox from 'pretty-checkbox-vue';

Vue.use(PrettyCheckbox);


import VueRouter from 'vue-router'
Vue.use(VueRouter)

import VueProgressBar from 'vue-progressbar'

Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '2px'
})

let routes = [
  { path: '/dashboard', component: require('./components/Dashboard.vue').default },
  { path: '/developer', component: require('./components/Developer.vue').default },
  { path: '/users', component: require('./components/Users.vue').default },
  { path: '/profile', component: require('./components/Profile.vue').default },
  { path: '/tahun-ajaran', component: require('./components/TahunAjaran.vue').default },
  { path: '/ruangan', component: require('./components/Ruangan.vue').default },
  { path: '/inventory', component: require('./components/Inventory.vue').default },
  { path: '/permintaan-aplikasi', component: require('./components/PermintaanAplikasi.vue').default },
  { path: '/masalah-lab', component: require('./components/MasalahLab.vue').default },
  { path: '/uang-kas', component: require('./components/UangKas.vue').default },
  { path: '/catatan-beli', component: require('./components/CatatanBeli.vue').default },
  { path: '/koperasi', component: require('./components/Koperasi.vue').default },
  { path: '/barang-hilang', component: require('./components/BarangHilang.vue').default },
  { path: '/barang-hilang/:id', component: require('./components/DetailBarangHilang.vue').default },
  { path: '/peminjaman-inventory', component: require('./components/PeminjamanInventory.vue').default },
  { path: '*', component: require('./components/404NotFound.vue').default }
]

const router = new VueRouter({
  mode: 'history',
  routes // short for `routes: routes`
})

Vue.filter('upText',function(text){
    return text.charAt(0).toUpperCase() + text.slice(1)
});
Vue.filter('myDate',function(created){
  moment.locale('id'); 
  return moment(created).format('MMMM Do YYYY');
});
Vue.filter('myDateTime',function(created){
  moment.locale('id'); 
  return moment(created).format('dddd MMM YYYY, h:mm');;
});
import { Datetime } from 'vue-datetime';
 
Vue.component('datetime', Datetime);



window.Fire = new Vue();
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue').default
);

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue').default
);

Vue.component(
  'not-found',
  require('./components/404NotFound.vue').default
);

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    components: {
      "vue-datetime-picker": require("vue-datetime-picker")
    },
    data:{
      search: ''
    },
    methods:{
      formatDatetime: function(datetime) {
        if (datetime === null) {
          return "[null]";
        } else {
          return datetime.format("YYYY-MM-DD HH:mm:ss");
        }
      },
      searchit: _.debounce(()=>{
        Fire.$emit('searching');
      },500)
    }
});
