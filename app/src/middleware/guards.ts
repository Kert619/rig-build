import { useAuthStore } from 'src/stores/auth';
import { type NavigationGuardNext, type RouteLocationNormalized } from 'vue-router';

export const authGuard = (
  _to: RouteLocationNormalized,
  _from: RouteLocationNormalized,
  next: NavigationGuardNext,
) => {
  const authStore = useAuthStore();

  if (authStore.user) {
    next();
  } else {
    next('/auth/login');
  }
};

export const guestGuard = (
  _to: RouteLocationNormalized,
  _from: RouteLocationNormalized,
  next: NavigationGuardNext,
) => {
  const authStore = useAuthStore();

  if (authStore.user) {
    next('/');
  } else {
    next();
  }
};
