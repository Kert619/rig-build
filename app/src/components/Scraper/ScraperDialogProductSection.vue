<template>
  <div class="q-my-sm">
    <q-btn-toggle
      unelevated
      size="sm"
      dense
      glossy
      v-model="scraperRef.scraper_config.product.method"
      toggle-color="primary"
      :options="[
        { label: 'Regex', value: 'regex' },
        { label: 'Selector', value: 'selector' },
      ]"
    />
  </div>

  <template v-if="scraperRef.scraper_config.product.method == 'regex'">
    <TextInput
      label="Container Regex"
      v-model="scraperRef.scraper_config.product.container_regex"
      :error="!!error?.scraper_config?.product.container_regex"
      :error-message="error?.scraper_config?.product.container_regex?.toString()"
    >
      <template #append>
        <q-icon name="info" size="xs">
          <q-tooltip>The regex for the product container</q-tooltip>
        </q-icon>
      </template>
    </TextInput>
    <TextInput
      label="Regex"
      v-model="scraperRef.scraper_config.product.regex"
      :error="!!error?.scraper_config?.product.regex"
      :error-message="error?.scraper_config?.product.regex?.toString()"
    >
      <template #append>
        <q-icon name="info" size="xs">
          <q-tooltip>The regex for the individual product item</q-tooltip>
        </q-icon>
      </template>
    </TextInput>
  </template>

  <template v-if="scraperRef.scraper_config.product.method == 'selector'">
    <TextInput
      label="Container Selector"
      v-model="scraperRef.scraper_config.product.container_selector"
      :error="!!error?.scraper_config?.product.container_selector"
      :error-message="error?.scraper_config?.product.container_selector?.toString()"
    >
      <template #append>
        <q-icon name="info" size="xs">
          <q-tooltip>The selector for the product container</q-tooltip>
        </q-icon>
      </template>
    </TextInput>
    <TextInput
      label="Selector"
      v-model="scraperRef.scraper_config.product.selector"
      :error="!!error?.scraper_config?.product.selector"
      :error-message="error?.scraper_config?.product.selector?.toString()"
    >
      <template #append>
        <q-icon name="info" size="xs">
          <q-tooltip>The selector for the individual product item</q-tooltip>
        </q-icon>
      </template>
    </TextInput>
  </template>
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
