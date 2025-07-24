<template>
  <TextInput label="API Base URL" v-model="scraperRef.scraper_config.product.ajax.api_base_url">
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The base URL for the Ajax request</q-tooltip>
      </q-icon>
    </template>
  </TextInput>
  <TextInput
    label="Product Link Base URL"
    v-model="scraperRef.scraper_config.product.ajax.product_link_base_url"
  >
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The base URL for the product page</q-tooltip>
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
