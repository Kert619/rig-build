<template>
  <div class="q-my-md">
    <q-btn
      dense
      size="sm"
      unelevated
      label="Add Page Rule"
      color="positive"
      icon="add"
      @click="addPageRule"
    />

    <template v-if="scraperRef.scraper_config.product.page_rules.length">
      <TextInput
        v-for="rule in scraperRef.scraper_config.product.page_rules"
        :key="rule.id"
        v-model="rule.value"
      >
        <template #append>
          <q-btn
            dense
            size="sm"
            flat
            color="negative"
            icon="delete"
            @click="removePageRule(rule.id)"
          />
        </template>
      </TextInput>
    </template>

    <div v-else class="text-caption q-mt-md">No page rules added...</div>
  </div>
</template>

<script setup lang="ts">
import { type Scraper } from 'src/stores/scraper';
import { toRef } from 'vue';
import TextInput from 'components/UI/TextInput.vue';
import { uid } from 'quasar';

const props = defineProps<{
  scraper: Scraper;
}>();

const scraperRef = toRef(props.scraper);

const addPageRule = () => {
  scraperRef.value.scraper_config.product.page_rules.push({ id: uid(), value: '' });
};

const removePageRule = (id: string) => {
  const rules = scraperRef.value.scraper_config.product.page_rules;
  scraperRef.value.scraper_config.product.page_rules = rules.filter((rule) => rule.id != id);
};
</script>
