<template>
  <TextInput
    label="Currency"
    v-model="scraperRef.scraper_config.product.format.currency"
    :error="!!error?.scraper_config?.product.format.currency"
    :error-message="error?.scraper_config?.product.format.currency.toString()"
  />
  <TextInput
    label="Price Store Ident"
    v-model="scraperRef.scraper_config.product.format.price_store_ident"
  />
  <TextInput label="Price Name" v-model="scraperRef.scraper_config.product.format.price_name" />
  <TextInput label="Price" v-model="scraperRef.scraper_config.product.format.price" />
  <TextInput label="Stock Status" v-model="scraperRef.scraper_config.product.format.stock_status" />
  <TextInput
    label="Stock Quantity"
    v-model="scraperRef.scraper_config.product.format.stock_quantity"
  />
  <TextInput label="Rating" v-model="scraperRef.scraper_config.product.format.rating" />
  <TextInput label="Price URL" v-model="scraperRef.scraper_config.product.format.price_url" />
  <TextInput label="Image URL" v-model="scraperRef.scraper_config.product.format.img_url" />
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
