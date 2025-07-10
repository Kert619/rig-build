import { defineBoot } from '@quasar/app-vite/wrappers';
import { Cookies } from 'quasar';
import { useAuthStore } from 'src/stores/auth';

export default defineBoot(async () => {
  const xsrf = Cookies.get('XSRF-TOKEN');

  if (xsrf) {
    const authStore = useAuthStore();
    await authStore.fetchUser();
  }
});
