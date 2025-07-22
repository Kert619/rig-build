<template>
  <div class="column" v-if="current && !storeStore.refresh && !loading">
    <AppEdit
      :store="current"
      :error="storeStore.currentErrors.get(current.store_id)"
      @save="handleUpdate"
      @delete="handleDelete"
    />

    <q-table
      :columns="columns"
      :rows="[]"
      row-key="scraper_id"
      flat
      bordered
      dense
      square
      :loading="loading"
      class="q-mt-md"
    >
      <template #top>
        <div class="row">
          <q-btn
            size="sm"
            dense
            label="Add Scraper"
            unelevated
            icon="add"
            @click="scraperStore.create()"
          />
        </div>
      </template>

      <template #top-row>
        <ScraperCreate
          v-for="create in createdScrapers"
          :key="create.$id?.toString()!"
          :scraper="create"
          @delete="handleDeleteScraper"
          @save="handleSaveScraper"
          :error="scraperStore.createdErrors.get(create.$id?.toString()!)"
        />
      </template>
    </q-table>

    <!-- <q-dialog v-model="dialogOpen" full-width full-height square persistent>
      <ScraperEdit
        :store-id="current.store_id"
        :scraper="create"
        @hide="handlehide"
      />
    </q-dialog> -->
  </div>

  <div v-else-if="loading" class="text-caption">Loading....</div>
</template>

<script setup lang="ts">
import { useStoreStore } from 'src/stores/store';
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppEdit from 'components/Stores/AppEdit.vue';
import { type QTableColumn, useQuasar } from 'quasar';
import { useScraperStore } from 'src/stores/scraper';
import ScraperCreate from 'components/Scraper/ScraperCreate.vue';

const $q = useQuasar();
const route = useRoute();
const router = useRouter();
const storeStore = useStoreStore();
const scraperStore = useScraperStore();
const loading = ref(false);

const columns: QTableColumn[] = [
  {
    name: 'scraper_id',
    label: '#',
    field: 'scraper_id',
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
    name: 'scraper_url',
    label: 'Scraper URL',
    field: 'scraper_url',
    align: 'left',
    sortable: true,
  },
  {
    name: 'is_running',
    label: 'Is Running',
    field: 'is_running',
    align: 'left',
    sortable: true,
  },
  {
    name: 'is_active',
    label: 'Is Active',
    field: 'is_active',
    align: 'left',
    sortable: true,
  },
  {
    name: 'last_run',
    label: 'Last Run',
    field: 'last_run',
    align: 'left',
    sortable: true,
  },
  {
    name: '',
    label: 'Action',
    field: '',
  },
];

onUnmounted(() => {
  removeCurrent();
});

const current = computed(() => {
  if (!route.params.id) return null;

  return storeStore.current.get(+route.params.id);
});

const createdScrapers = computed(() => Array.from(scraperStore.created.values()));

const loadStore = async () => {
  if (!route.params.id) return;
  try {
    loading.value = true;
    await storeStore.show(+route.params.id);
  } finally {
    loading.value = false;
  }
};

const removeCurrent = () => {
  if (!route.params.id) return;
  storeStore.current.delete(+route.params.id);
};

const handleUpdate = async (id: number) => {
  await storeStore.update(id);
  await storeStore.fetchIndex();
  await handleRefresh();
};

const handleDelete = (id: number) => {
  $q.dialog({
    title: 'Confirm',
    message: 'Do you want to remove this store?',
    cancel: true,
  }).onOk(() => {
    void (async () => {
      await storeStore.destroy(id);
      await router.replace('/stores');
      await storeStore.fetchIndex();
    })();
  });
};

const handleDeleteScraper = (id: string) => {
  scraperStore.created.delete(id);
  scraperStore.createdErrors.delete(id);
};

const handleSaveScraper = async (id: string) => {
  await scraperStore.store(id);
};

const handleRefresh = async () => {
  storeStore.refresh = true;
  await nextTick();
  storeStore.refresh = false;
};

watch(
  () => route.params.id,
  async (id) => {
    if (id) {
      removeCurrent();
      await loadStore();
    }
  },
  { immediate: true },
);
</script>
