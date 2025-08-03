<template>
  <q-layout view="lHh Lpr lFf">
    <q-header bordered>
      <q-toolbar>
        <q-btn flat dense round icon="menu" aria-label="Menu" @click="toggleLeftDrawer" />

        <q-toolbar-title> Rig Build </q-toolbar-title>

        <div>
          <q-toggle
            v-model="darkMode"
            :label="darkMode ? 'Dark' : 'Light'"
            unchecked-icon="brightness_5"
            checked-icon="bedtime"
            color="dark"
          />
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer v-model="leftDrawerOpen" show-if-above bordered behavior="mobile">
      <q-list>
        <q-item-label header> Rig Build </q-item-label>

        <q-expansion-item expand-separator icon="storage" label="Database">
          <q-item :inset-level="1" clickable to="/countries">
            <q-item-section avatar>
              <q-icon name="flag" />
            </q-item-section>

            <q-item-section>
              <q-item-label>Countries</q-item-label>
            </q-item-section>
          </q-item>

          <q-item :inset-level="1" clickable to="/stores">
            <q-item-section avatar>
              <q-icon name="storefront" />
            </q-item-section>

            <q-item-section>
              <q-item-label>Stores</q-item-label>
            </q-item-section>
          </q-item>

          <q-item :inset-level="1" clickable to="/product-hierarchy">
            <q-item-section avatar>
              <q-icon name="polyline" />
            </q-item-section>

            <q-item-section>
              <q-item-label>Product Hierarchy</q-item-label>
            </q-item-section>
          </q-item>

          <q-item :inset-level="1" clickable to="/categories">
            <q-item-section avatar>
              <q-icon name="category" />
            </q-item-section>

            <q-item-section>
              <q-item-label>Categories</q-item-label>
            </q-item-section>
          </q-item>

          <q-expansion-item expand-separator icon="history" label="Logs" :header-inset-level="1">
            <q-item :inset-level="2" clickable to="/scraper-logs">
              <q-item-section avatar>
                <q-icon name="computer" />
              </q-item-section>

              <q-item-section>
                <q-item-label>Scraper Logs</q-item-label>
              </q-item-section>
            </q-item>
          </q-expansion-item>
        </q-expansion-item>

        <q-item clickable @click="logout">
          <q-item-section avatar>
            <q-icon name="logout" />
          </q-item-section>

          <q-item-section>
            <q-item-label>Logout</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { useQuasar } from 'quasar';
import { useAuthStore } from 'src/stores/auth';

const $q = useQuasar();
const authStore = useAuthStore();
const leftDrawerOpen = ref(false);
const darkMode = ref(localStorage.getItem('color_scheme') == 'true' ? true : false);

const toggleLeftDrawer = () => {
  leftDrawerOpen.value = !leftDrawerOpen.value;
};

const logout = async () => {
  await authStore.logout();
};

watch(darkMode, (newVal) => {
  $q.dark.set(newVal);
  localStorage.setItem('color_scheme', newVal ? 'true' : 'false');
});
</script>
