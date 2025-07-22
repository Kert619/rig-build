<template>
  <q-tr>
    <q-td>
      <q-chip label="New" icon="add" color="primary" size="sm" />
    </q-td>
    <q-td>
      <TextInput
        v-model="scraperRef.scraper_name"
        borderless
        :error="!!error?.scraper_name"
        :error-message="error?.scraper_name?.toString()"
      />
    </q-td>
    <q-td>
      <TextInput
        v-model="scraperRef.scraper_url"
        borderless
        :error="!!error?.scraper_url"
        :error-message="error?.scraper_url?.toString()"
      />
    </q-td>
    <q-td></q-td>
    <q-td></q-td>
    <q-td></q-td>
    <q-td auto-width>
      <TableAction :loading="scraperRef.$loading">
        <q-item clickable v-close-popup @click="emit('save', scraperRef.$id?.toString()!)">
          <q-item-section>Save</q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="emit('delete', scraperRef.$id?.toString()!)">
          <q-item-section>Delete</q-item-section>
        </q-item>
      </TableAction>
    </q-td>
  </q-tr>
</template>

<script setup lang="ts">
import { type ScraperError, type Scraper } from 'src/stores/scraper';
import { toRef } from 'vue';
import TextInput from 'components/UI/TextInput.vue';
import TableAction from 'components/UI/TableAction.vue';

const emit = defineEmits<{
  delete: [id: string];
  save: [id: string];
}>();

const props = defineProps<{
  scraper: Scraper;
  error?: ScraperError | undefined;
}>();

const scraperRef = toRef(props.scraper);
</script>
