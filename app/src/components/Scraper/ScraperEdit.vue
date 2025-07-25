<template>
  <q-tr>
    <q-td>
      <q-chip :label="scraperRef.scraper_id" color="primary" size="sm" />
    </q-td>
    <q-td>
      {{ scraperRef.scraper_name }}
    </q-td>
    <q-td>
      {{ scraperRef.scraper_url }}
    </q-td>
    <q-td>
      <q-btn-toggle
        unelevated
        size="sm"
        dense
        glossy
        :model-value="scraperRef.is_running"
        toggle-color="primary"
        :options="[
          { label: 'Yes', value: true },
          { label: 'No', value: false },
        ]"
        disable
      />
    </q-td>
    <q-td>
      <q-btn-toggle
        unelevated
        size="sm"
        dense
        glossy
        v-model="scraperRef.is_active"
        toggle-color="primary"
        :options="[
          { label: 'Yes', value: true },
          { label: 'No', value: false },
        ]"
        @update:model-value="emit('active', scraperRef.scraper_id)"
      />
    </q-td>
    <q-td>
      <span class="text-caption">{{ scraperRef.last_run ? scraperRef.last_run : 'N/A' }}</span>
    </q-td>
    <q-td auto-width>
      <TableAction :loading="scraperRef.$loading">
        <q-item clickable v-close-popup @click="emit('preview', scraperRef.scraper_id, scraperRef)">
          <q-item-section>Preview</q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="emit('delete', scraperRef.scraper_id, scraperRef)">
          <q-item-section>Delete</q-item-section>
        </q-item>
      </TableAction>
    </q-td>
  </q-tr>
</template>

<script setup lang="ts">
import { type Scraper } from 'src/stores/scraper';
import { toRef } from 'vue';
import TableAction from 'components/UI/TableAction.vue';

const emit = defineEmits<{
  delete: [id: number, scraper: Scraper];
  preview: [id: number, scraper: Scraper];
  active: [id: number];
}>();

const props = defineProps<{
  scraper: Scraper;
}>();

const scraperRef = toRef(props.scraper);
</script>
