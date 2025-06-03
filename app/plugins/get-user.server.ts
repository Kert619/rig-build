export default defineNuxtPlugin(async () => {
  const route = useRoute();
  const xsrfToken = useCookie("XSRF-TOKEN");

  //fetch the user if it has xsrf token cookie
  if (xsrfToken.value) {
    try {
      const authStore = useAuthStore();
      await authStore.fetchUser();
    } catch (error) {}
  }
});
