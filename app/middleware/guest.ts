export default defineNuxtRouteMiddleware((to, from) => {
  const authStore = useAuthStore();

  if (authStore.user && authStore.user.role == "admin") {
    return navigateTo("/admin/dashboard");
  }
});
