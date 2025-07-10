import { defineStore } from 'pinia';
import { Cookies } from 'quasar';
import { api, web } from 'src/boot/axios';
import { useApiError } from 'src/composables/useApiError';
import { ref, type Ref } from 'vue';

export type UserInfo = {
  user_id: number;
  email: string;
};

export type LoginInfo = {
  email: string;
  password: string;
};

export type LoginError = Partial<Record<keyof LoginInfo, string[]>>;

export const useAuthStore = defineStore('user-store', () => {
  const user: Ref<UserInfo | null> = ref(null);
  const loginError: Ref<LoginError | null> = ref(null);

  const login = async (loginInfo: LoginInfo) => {
    try {
      loginError.value = null;
      await web.get('/sanctum/csrf-cookie');

      await web.post('/login', loginInfo);
      await fetchUser();
    } catch (error) {
      loginError.value = useApiError(error);
    }
  };

  const logout = async () => {
    await web.post('/logout');
    window.location.href = '/auth/login';
    Cookies.remove('XSRF-TOKEN');
  };

  const fetchUser = async () => {
    try {
      const { data } = await api.get('/user');
      user.value = data;
    } catch {
      //do nothing
    }
  };

  return { login, logout, fetchUser, loginError, user };
});
