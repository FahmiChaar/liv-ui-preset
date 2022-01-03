import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify)

const opts = {
    icons: {
        iconfont: 'md'
    },
    theme: {
        themes: {
            light: {
                primary: '#2748AB',
                secondary: '#527BF7',
                default: '#94a3b8',
                danger: '#f44336',
            },
            dark: {
                primary: '#000'
            }
        },
    },
    rtl: window.appLocal == 'ar'
}

export default new Vuetify(opts)