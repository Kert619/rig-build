<template>
  <div class="q-my-sm row justify-between q-gutter-x-md">
    <q-btn-toggle
      unelevated
      size="sm"
      dense
      glossy
      v-model="scraperRef.scraper_config.category.container_extract_method"
      toggle-color="primary"
      :options="[
        { label: 'Regex', value: 'regex' },
        { label: 'Selector', value: 'selector' },
      ]"
    />

    <q-btn
      dense
      size="sm"
      label="Process Categories"
      unelevated
      color="primary"
      icon="settings"
      @click="emit('processCategory')"
    />
  </div>
  <TextInput
    v-if="scraperRef.scraper_config.category.container_extract_method == 'regex'"
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
    v-if="scraperRef.scraper_config.category.container_extract_method == 'selector'"
    label="Container Selector"
    v-model="scraperRef.scraper_config.category.container_selector"
    :error="!!error?.scraper_config?.category.container_selector"
    :error-message="error?.scraper_config?.category.container_selector.toString()"
  >
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The selector for the category container</q-tooltip>
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

const emit = defineEmits<{
  processCategory: [];
}>();

const props = defineProps<{
  scraper: Scraper;
  error?: ScraperError | undefined;
}>();

const scraperRef = toRef(props.scraper);
</script>
