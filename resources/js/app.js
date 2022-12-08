import './bootstrap'

import { createApp } from 'vue'

import App from './components/App.vue'

import moment from 'moment'

const app = createApp(App)

app.config.globalProperties.$filters = {
    datetime(date) {
        return moment.utc(date).utcOffset(5).format('DD.MM.YYYY H:mm')
    },
    date(date) {
        return moment.utc(date).utcOffset(5).format('DD.MM.YYYY')
    },
    currencyFormat(value) {
        if (!value) return '0'
        return parseFloat(value).toFixed(0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ") 
    }
}

app.mount('#app')