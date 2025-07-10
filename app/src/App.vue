<template>
  <router-view />
</template>

<script setup lang="ts">
import { useQuasar } from 'quasar';

const $q = useQuasar();
$q.dark.set(localStorage.getItem('color_scheme') == 'true' ? true : false);
</script>
