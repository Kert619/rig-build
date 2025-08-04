<template>
  <div class="q-my-sm">
    <q-btn-toggle
      unelevated
      size="sm"
      dense
      glossy
      v-model="scraperRef.scraper_config.settings"
      toggle-color="primary"
      :options="[
        { label: 'Puppeteer', value: 'puppeteer' },
        { label: 'Ajax', value: 'ajax' },
        { label: 'Curl', value: 'curl' },
      ]"
    />
  </div>

  <TextInput
    v-model="scraperRef.scraper_url"
    label="Store Scraper URL"
    :error="!!error?.scraper_url"
    :error-message="error?.scraper_url?.toString()"
  >
    <template #prepend>
      <q-btn
        dense
        size="sm"
        flat
        icon="open_in_new"
        color="primary"
        @click="useOpenLink(scraperRef.scraper_url)"
      />
    </template>

    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The base URL of the scraper</q-tooltip>
      </q-icon>
    </template>

    <template #label>
      <div class="q-mb-md">
        <span>Store Scraper URL</span>
        <span class="text-negative text-h6">*</span>
      </div>
    </template>
  </TextInput>

  <TextInput
    v-model="scraperRef.scraper_name"
    label="Scraper Name"
    :error="!!error?.scraper_name"
    :error-message="error?.scraper_name?.toString()"
  >
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip
          >The name of the scraper, it can be whatever you want as long as it's unique.</q-tooltip
        >
      </q-icon>
    </template>

    <template #label>
      <div class="q-mb-md">
        <span>Scraper Name</span>
        <span class="text-negative text-h6">*</span>
      </div>
    </template>
  </TextInput>
</template>

<script setup lang="ts">
import { type ScraperError, type Scraper } from 'src/stores/scraper';
import { toRef } from 'vue';
import TextInput from 'components/UI/TextInput.vue';
import { useOpenLink } from 'src/composables/useOpenLink';

const props = defineProps<{
  scraper: Scraper;
  error?: ScraperError | undefined;
}>();

const scraperRef = toRef(props.scraper);
</script>
