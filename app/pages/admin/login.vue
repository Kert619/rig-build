<template>
  <UContainer
    class="min-h-screen min-w-screen flex justify-center items-center"
  >
    <UCard variant="subtle" class="max-w-sm w-full">
      <div class="flex flex-col items-center space-y-3 mb-5">
        <UIcon name="line-md:account" class="size-10" />
        <span class="text-xl">Sign In</span>
      </div>

      <UForm
        :schema="loginSchema"
        :state="state"
        class="space-y-4"
        @submit.prevent="handleSubmit"
      >
        <UFormField label="Email" name="email">
          <UInput v-model="state.email" />
        </UFormField>

        <UFormField label="Password" name="password">
          <UInput v-model="state.password" type="password" />
        </UFormField>

        <UButton type="submit" block> Login </UButton>
        <USeparator label="Or" />
        <UButton variant="link" block class="underline" color="neutral">
          Forgot Password
        </UButton>
      </UForm>
    </UCard>
  </UContainer>
</template>

<script setup lang="ts">
import type { FormSubmitEvent } from "@nuxt/ui";
import type { LoginSchema } from "~/schemas/auth";
import { loginSchema } from "~/schemas/auth";

definePageMeta({
  layout: "auth",
  middleware: ["guest"],
});

const toast = useToast();
const authStore = useAuthStore();

const state = reactive<Partial<LoginSchema>>({
  email: undefined,
  password: undefined,
});

const handleSubmit = async (event: FormSubmitEvent<typeof state>) => {
  await authStore.login(event.data as LoginSchema);
  await authStore.fetchUser();
  await navigateTo("/admin/dashboard");
  toast.add({
    title: "Success",
    description: "You've logged in successfully!",
    color: "success",
    icon: "line-md:circle-twotone-to-confirm-circle-transition",
    progress: false,
  });
};
</script>
