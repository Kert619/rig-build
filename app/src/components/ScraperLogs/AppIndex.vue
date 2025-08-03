<template>
  <q-table
    :columns="columns"
    :rows="logs"
    row-key="scraper_log_id"
    flat
    bordered
    dense
    square
    :loading="loading"
  >
    <template #top>
      <div class="row items-center justify-between full-width">
        <q-btn
          label="Delete All"
          icon="delete"
          color="negative"
          dense
          size="sm"
          unelevated
          glossy
          @click="truncate"
        />

        <TextInput
          v-model="search"
          clearable
          debounce="600"
          placeholder="Search scraper logs"
          class="full-width"
          style="max-width: 30vw"
        >
          <template #prepend>
            <q-icon name="search" />
          </template>
        </TextInput>

        <span class="text-body2">Scraper Logs</span>
      </div>
    </template>

    <template #body="props">
      <AppEdit
        v-if="!scraperLogStore.refresh"
        :scraper-log="props.row"
        :key="props.row.scraper_log_id"
      />
    </template>
  </q-table>
</template>

<script setup lang="ts">
import { useQuasar, type QTableColumn } from 'quasar';
import { useScraperLogsStore } from 'src/stores/scraper-logs';
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';
import AppEdit from 'components/ScraperLogs/AppEdit.vue';
import TextInput from 'components/UI/TextInput.vue';

const $q = useQuasar();
const scraperLogStore = useScraperLogsStore();
const search = ref('');
const loading = ref(false);

const columns: QTableColumn[] = [
  {
    name: 'scraper_log_id',
    label: '#',
    field: 'scraper_log_id',
    align: 'left',
    sortable: true,
  },
  {
    name: 'scraper_name',
    label: 'Scraper Name',
    field: 'scraper_name',
    align: 'left',
    sortable: true,
  },
  {
    name: 'started',
    label: 'Started',
    field: 'started',
    align: 'left',
    sortable: true,
  },
  {
    name: 'ended',
    label: 'Ended',
    field: 'ended',
    align: 'left',
    sortable: true,
  },
  {
    name: 'price_count',
    label: 'Price Count',
    field: 'price_count',
    align: 'left',
    sortable: true,
  },
  {
    name: 'error_message',
    label: 'Error Message',
    field: 'error_message',
    align: 'left',
    sortable: true,
  },
];

onMounted(async () => {
  await loadLogs();
});

onUnmounted(() => {
  delete scraperLogStore.filter.scraper_id;
});

const loadLogs = async () => {
  try {
    loading.value = true;
    await scraperLogStore.fetchIndex();
  } finally {
    loading.value = false;
  }
};

const logs = computed(() => {
  if (search.value) {
    return scraperLogStore.index.filter((log) =>
      log.scraper.scraper_name.toLowerCase().includes(search.value.toLowerCase()),
    );
  } else {
    return scraperLogStore.index;
  }
});

const truncate = () => {
  $q.dialog({
    title: 'Confirm',
    message: 'Do you want to delete all scraper logs?',
    cancel: true,
  }).onOk(() => {
    void (async () => {
      try {
        loading.value = true;
        await scraperLogStore.truncate();
        await loadLogs();
        await handleRefresh();
      } finally {
        loading.value = false;
      }
    })();
  });
};

const handleRefresh = async () => {
  scraperLogStore.refresh = true;
  await nextTick();
  scraperLogStore.refresh = false;
};
</script>
