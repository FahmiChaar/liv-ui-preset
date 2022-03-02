require('./bootstrap');

import Vue from 'vue'
import { App, plugin } from '@inertiajs/inertia-vue'
import { InertiaProgress } from '@inertiajs/progress'
import vuetify from './plugins/vuetify'
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import { ValidationProvider, ValidationObserver } from 'vee-validate';
// import * as rules from 'vee-validate/dist/rules';
import Toast from "vue-toastification"

InertiaProgress.init()
Vue.use(plugin)

Vue.use(VueSweetalert2);

Vue.use(Toast, {
  transition: "Vue-Toastification__bounce",
  maxToasts: 20,
  newestOnTop: true,
  position: "bottom-right",
});

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);
// for (let [rule, validation] of Object.entries(rules)) {
//   extend(rule, {
//     ...validation
//   });
// }

Vue.mixin({
  methods: {
    __(value, params = null) {
      return value;
    },
    fireError(title, text = null, confirmButtonText = null) {
      return this.$swal({
        title,
        text: typeof (text) === 'string' ? text : text.response ? text.response.data.message : '',
        icon: 'error',
        confirmButtonText: confirmButtonText || 'Ok',
      })
    },
    alert(title, text = null, icon = 'warning', confirmButtonText = null) {
      return this.$swal({
        title,
        text,
        icon,
        confirmButtonText: confirmButtonText || 'Ok',
      })
    },
    confirmation(title, text = null, icon = 'warning', confirmButtonText = null, cancelButtonText = null) {
      return this.$swal({
        title,
        text: typeof (text) === 'string' ? text : text.response ? text.response.data.message : '',
        icon,
        showCancelButton: true,
        cancelButtonText: cancelButtonText || 'Anuller',
        confirmButtonText: confirmButtonText || 'Oui',
      })
    },
    route,
    $role(roles) {
      if (!Array.isArray(roles)) {
        roles = roles.split('|')
      }
      const userRoles = this.$page.props.auth.user.roles.map(r => r.name)
      return roles.every(r => userRoles.includes(r))
    }
  }
});

Vue.prototype.$appLocal = window.appLocal
Vue.prototype.$user = window.user
Vue.prototype.$isAuth = window.isAuthenticated

// Import all components
const customComponents = require.context('./components', true, /\.vue$/i);
customComponents.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], customComponents(key).default));

const sharedLayouts = require.context('./Layouts', true, /\.vue$/i);
sharedLayouts.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], sharedLayouts(key).default));

const inertiaApp = document.getElementById('app')

const vueApp = new Vue({
  el: inertiaApp,
  vuetify,
  data() {
    return {
      drawer: null,
      mini: true,
    }
  },
  render: h => h(App, {
    props: {
      initialPage: JSON.parse(inertiaApp.dataset.page),
      resolveComponent: name => require(`./Pages/${name}`).default,
    }
  })
})

Vue.prototype.$modal = {
  show: component => vueApp.$emit('modal::show', component),
  close: component => vueApp.$emit('modal::close', component)
}