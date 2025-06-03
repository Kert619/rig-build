// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: "2025-05-15",
  devtools: { enabled: true },
  modules: ["@nuxt/ui", "@pinia/nuxt"],
  css: ["~/assets/css/main.css"],
  app: {
    head: {
      title: "E-commerce",
    },
  },
  runtimeConfig: {
    public: {
      apiBase: process.env.API_URL,
      baseUrl: process.env.BASE_URL,
    },
  },
  pinia: {
    storesDirs: ["./stores/**"],
  },
});

