<template>
  <q-tr>
    <q-td>
      <q-chip :label="scraperLog.scraper_log_id" size="sm" color="primary" />
    </q-td>
    <q-td>
      {{ scraperLog.scraper.scraper_name }}
    </q-td>
    <q-td>
      {{ scraperLog.started }}
    </q-td>
    <q-td>
      {{ scraperLog.ended }}
    </q-td>
    <q-td>
      {{ scraperLog.price_count }}
    </q-td>
    <q-td>
      {{ scraperLog.error_message }}
    </q-td>
  </q-tr>
</template>

<script setup lang="ts">
import { type ScraperLog } from 'src/stores/scraper-logs';

defineProps<{
  scraperLog: ScraperLog;
}>();
</script>
