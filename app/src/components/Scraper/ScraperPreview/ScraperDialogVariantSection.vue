<template>
  <TextInput v-model="scraperRef.scraper_config.product.variant_flag.regex" label="Regex">
    <template #append>
      <q-icon name="info" size="xs">
        <q-tooltip>The regex to use for determining if a product has variant</q-tooltip>
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
