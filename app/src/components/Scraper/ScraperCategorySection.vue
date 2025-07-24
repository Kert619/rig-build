<template>
  <TextInput
    label="Container Regex"
    v-model="scraperRef.scraper_config.category.container_regex"
    :error="!!error?.scraper_config?.category.container_regex"
    :error-message="error?.scraper_config?.category.container_regex.toString()"
  >
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The regex for the category container</q-tooltip>
      </q-icon>
    </template>
  </TextInput>
  <TextInput
    label="Regex"
    v-model="scraperRef.scraper_config.category.regex"
    :error="!!error?.scraper_config?.category.regex"
    :error-message="error?.scraper_config?.category.regex.toString()"
  >
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The regex for the individual category item</q-tooltip>
      </q-icon>
    </template>
  </TextInput>
</template>

<script setup lang="ts">
import { type ScraperError, type Scraper } from 'src/stores/scraper';
import { toRef } from 'vue';
import TextInput from 'components/UI/TextInput.vue';

const props = defineProps<{
  scraper: Scraper;
  error?: ScraperError | undefined;
}>();

const scraperRef = toRef(props.scraper);
</script>
