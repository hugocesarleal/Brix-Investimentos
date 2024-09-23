// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  devtools: { enabled: true },

  css: [
    'vuetify/lib/styles/main.sass',
    '@mdi/font/css/materialdesignicons.min.css'
  ],

  build: {
    transpile: ['vuetify']
  },

  compatibilityDate: '2024-07-07',

  runtimeConfig: {
    public: {
      apiBase: 'http://localhost:8000' // URL do seu servidor Laravel
    }
  }
})
