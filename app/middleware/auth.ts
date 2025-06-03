export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore();

  const isComingFromAdmin = from.path.startsWith("/admin");

  if (!authStore.user && isComingFromAdmin) {
    return navigateTo("/admin/login");
  }
});
