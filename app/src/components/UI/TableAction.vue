<template>
  <q-btn dense size="sm" flat icon="more_vert" :loading="loading">
    <q-menu class="no-box-shadow" square>
      <q-list dense bordered>
        <slot />
      </q-list>
    </q-menu>
  </q-btn>
</template>

<script setup lang="ts">
defineProps<{
  loading?: boolean | undefined;
}>();
</script>
