import { defineBoot } from '#q-app/wrappers'
import axios from 'axios'
import {Alert} from "src/addons/Alert";
import {useCounterStore} from "stores/example-store";
import VueApexCharts from "vue3-apexcharts";

const api = axios.create({ baseURL: 'https://api.example.com' })

function syncSessionData (payload) {
  const store = useCounterStore()
  const user = payload?.user || {}
  const permissions = payload?.permissions || user?.permissions || []
  const env = payload?.datos || {}

  store.isLogged = true
  store.user = user
  store.permissions = permissions.map(p => p.name || p)
  store.env = env

  localStorage.setItem('user', JSON.stringify(user))
  localStorage.setItem('permissionsNaty', JSON.stringify(store.permissions))
  localStorage.setItem('envNaty', JSON.stringify(env))
}

export default defineBoot(({ app,router }) => {
  app.use(VueApexCharts);

  app.config.globalProperties.$axios = axios.create({ baseURL: import.meta.env.VITE_API_BACK })
  app.config.globalProperties.$alert = Alert
  app.config.globalProperties.$store = useCounterStore()
  app.config.globalProperties.$url = import.meta.env.VITE_API_BACK
  app.config.globalProperties.$version = import.meta.env.VITE_VERSION
  app.config.globalProperties.$filters = {
    dateDmYHis (value) {
      const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic']
      const mes = meses[moment(String(value)).format('MM') - 1]
      if (!value) return ''
      return moment(String(value)).format('DD') + ' ' + mes + ' ' + moment(String(value)).format('YYYY') + ' ' + moment(String(value)).format('hh:mm A')
    },
    date: (value) => {
      if (!value) return ''
      return new Date(value).toLocaleDateString()
    },
    time: (value) => {
      if (!value) return ''
      return new Date(value).toLocaleTimeString()
    },
    textCapitalize: (value) => {
      if (!value) return ''
      const lower = value.toLowerCase()
      return lower.charAt(0).toUpperCase() + lower.slice(1)
    },
    color(role) {
      if (role === 'Administrador') return 'red'
      if (role === 'Docente') return 'green'
      if (role === 'Estudiante') return 'blue'
      return 'grey'
    },
    colorAgencia(agencia) {
      if (agencia === 'Oasis') return 'indigo'
      if (agencia === 'Clinica') return 'info'
      return 'red'
    }
  }

  const token = localStorage.getItem('tokenNaty')
  if (token) {
    app.config.globalProperties.$axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
    app.config.globalProperties.$axios.get('me').then(response => {
      syncSessionData(response.data)
    }).catch(error => {
      console.log(error)
      router.push('/login')
      localStorage.removeItem('tokenNaty')
      localStorage.removeItem('user')
      localStorage.removeItem('permissionsNaty')
      localStorage.removeItem('envNaty')
      useCounterStore().isLogged = false
      useCounterStore().permissions = []
      useCounterStore().user = {}
      useCounterStore().env = {}
    })
  }
  app.config.globalProperties.$api = api

})

export { api }

