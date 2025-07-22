<template>
  <q-card square flat bordered>
    <q-card-section
      class="q-pa-none absolute-top bg-grey-3"
      :class="{ 'bg-grey-10': $q.dark.isActive }"
      style="z-index: 1; height: 48px"
    >
      <div class="full-height q-px-md row justify-between items-center">
        <q-chip dense label="New" icon="add" color="primary" />
        <q-btn dense size="sm" flat icon="close" color="negative" @click="emit('hide')" />
      </div>
    </q-card-section>

    <q-card-section class="overflow-auto" style="height: calc(100% - 96px); margin-block: 48px">
      <div>{{ scraper.$id }}</div>
    </q-card-section>

    <q-card-section
      class="q-pa-none absolute-bottom bg-grey-3"
      :class="{ 'bg-grey-10': $q.dark.isActive }"
      style="z-index: 1; height: 48px"
    >
      <div class="full-height q-px-md row justify-between items-center">Action</div>
    </q-card-section>
  </q-card>
</template>

<script setup lang="ts">
import { type Scraper } from 'src/stores/scraper';

const emit = defineEmits<{
  hide: [];
}>();

defineProps<{
  storeId: number;
  scraper: Scraper;
}>();
</script>
