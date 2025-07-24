<template>
  <TextInput
    label="Container Regex"
    v-model="scraperRef.scraper_config.product.pagination.container_regex"
  >
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The regex for the pagination container</q-tooltip>
      </q-icon>
    </template>
  </TextInput>
  <TextInput
    label="Base Pagination Link"
    v-model="scraperRef.scraper_config.product.pagination.base_pagination_link"
  >
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The base URL for the pagination link</q-tooltip>
      </q-icon>
    </template>
  </TextInput>
  <TextInput label="Pages Regex" v-model="scraperRef.scraper_config.product.pagination.pages_regex">
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The regex to extract page numbers</q-tooltip>
      </q-icon>
    </template>
  </TextInput>
  <TextInput label="Page Query" v-model="scraperRef.scraper_config.product.pagination.page_query">
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The regex to extract the name of the page query. Ex. (page=)</q-tooltip>
      </q-icon>
    </template>
  </TextInput>
</template>

<script setup lang="ts">
import { type Scraper } from 'src/stores/scraper';
import { toRef } from 'vue';
import TextInput from 'components/UI/TextInput.vue';

const props = defineProps<{
  scraper: Scraper;
}>();

const scraperRef = toRef(props.scraper);
</script>
