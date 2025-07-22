<template>
  <q-page>
    <div class="window-height row flex-center q-pa-sm">
      <q-card class="login-card" square>
        <q-card-section>
          <div class="text-h5 text-center">Sign In</div>
        </q-card-section>

        <q-separator inset />

        <q-card-section class="q-mt-lg">
          <div class="column q-gutter-y-lg">
            <TextInput
              v-model="form.email"
              label="Email"
              :error="!!authStore.loginError?.email"
              :error-message="authStore.loginError?.email?.toString()"
            >
              <template v-slot:prepend>
                <q-icon name="email" />
              </template>
            </TextInput>

            <TextInput
              v-model="form.password"
              label="Password"
              type="password"
              :error="!!authStore.loginError?.password"
              :error-message="authStore.loginError?.password?.toString()"
            >
              <template v-slot:prepend>
                <q-icon name="lock" />
              </template>
            </TextInput>
            <q-btn label="Login" color="positive" unelevated @click="login" :loading="loading" />
            <div class="row justify-center">
              Forgot Password?
              <a class="q-ml-sm link text-primary">Click Here</a>
            </div>
          </div>
        </q-card-section>

        <q-separator inset />
        <q-card-section>
          <div class="text-body2 text-center">❤️ Rig Build ❤️</div>
        </q-card-section>
      </q-card>
    </div>
  </q-page>
</template>

<script setup lang="ts">
import { useAuthStore, type LoginInfo } from 'src/stores/auth';
import { ref, type Ref } from 'vue';
import { useRouter } from 'vue-router';
import TextInput from 'components/UI/TextInput.vue';

const authStore = useAuthStore();
const router = useRouter();
const loading = ref(false);

const form: Ref<LoginInfo> = ref({
  email: '',
  password: '',
});

const login = async () => {
  loading.value = true;
  await authStore.login(form.value);
  loading.value = false;

  if (authStore.user) await router.replace('/');
};
</script>

<style scoped lang="scss">
.login-card {
  width: 100%;
  max-width: 400px;
}
</style>
