import { useApi } from "~/composables/useApi";
import type { LoginSchema } from "~/schemas/auth";

export type User = {
  id: number;
  email: string;
  role: Role;
};

export type Role = "admin" | "customer";

export const useAuthStore = defineStore("user", () => {
  const user: Ref<User | null> = ref(null);

  const login = async (loginInfo: LoginSchema) => {
    await useApi("sanctum/csrf-cookie", { useApiPrefix: false });
    return await useApi("login", {
      method: "POST",
      body: loginInfo,
      useApiPrefix: false,
    });
  };

  const fetchUser = async () => {
    if (user.value) return;
    const data = await useApi("user");
    user.value = data;
  };

  return { login, fetchUser, user };
});
