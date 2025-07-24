<template>
  <div v-if="loading" class="text-caption">Loading....</div>

  <div class="column" v-else-if="current && !storeStore.refresh">
    <AppEdit
      :store="current"
      :error="storeStore.currentErrors.get(current.store_id)"
      @save="handleUpdate"
      @delete="handleDeleteStore"
    />

    <q-table
      :columns="columns"
      :rows="scrapers"
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
            @click="scraperStore.create({ store_id: current.store_id } as Scraper)"
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

      <template #body="props">
        <ScraperEdit
          :scraper="props.row"
          :key="props.row.scraper_id"
          @delete="(id: number, scraper: Scraper) => handleDeleteScraper(id, true, scraper)"
          @preview="handleScraperPreview"
        />
      </template>
    </q-table>

    <q-dialog v-model="dialogOpen" full-width full-height square persistent>
      <ScraperDialog
        v-if="scraperPreview"
        :scraper="scraperPreview"
        @hide="handleScraperDialogHide"
      />
    </q-dialog>
  </div>
</template>

<script setup lang="ts">
import { useStoreStore } from 'src/stores/store';
import { computed, nextTick, onUnmounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import AppEdit from 'components/Stores/AppEdit.vue';
import { type QTableColumn, useQuasar } from 'quasar';
import { type Scraper, useScraperStore } from 'src/stores/scraper';
import ScraperCreate from 'components/Scraper/ScraperCreate.vue';
import ScraperEdit from 'components/Scraper/ScraperEdit.vue';
import ScraperDialog from 'components/Scraper/ScraperDialog.vue';

const $q = useQuasar();
const route = useRoute();
const router = useRouter();
const storeStore = useStoreStore();
const scraperStore = useScraperStore();
const loading = ref(false);
const dialogOpen = ref(false);
let scraperPreview: Scraper | null;

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
  delete scraperStore.filter.store_id;
});

const current = computed(() => {
  if (!route.params.id) return null;

  return storeStore.current.get(+route.params.id);
});

const createdScrapers = computed(() => Array.from(scraperStore.created.values()));

const scrapers = computed(() => scraperStore.index);

const loadStore = async () => {
  if (!route.params.id) return;
  await storeStore.show(+route.params.id);
};

const fetchScrapers = async () => {
  if (!route.params.id) return;
  scraperStore.filter.store_id = route.params.id as string;
  await scraperStore.fetchIndex();
};

const loadData = async () => {
  try {
    loading.value = true;
    await Promise.all([loadStore(), fetchScrapers()]);
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

const handleDeleteStore = (id: number) => {
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

const handleDeleteScraper = (id: string | number, edit = false, scraper: Scraper | null = null) => {
  if (edit) {
    $q.dialog({
      title: 'Confirm',
      message: 'Do you want to remove this scraper?',
      cancel: true,
    }).onOk(() => {
      void (async () => {
        if (!scraper) return;
        scraperStore.current.set(id as number, scraper);
        await scraperStore.destroy(id as number);
        await loadData();
        await handleRefresh();
      })();
    });
  } else {
    scraperStore.created.delete(id as string);
    scraperStore.createdErrors.delete(id as string);
  }
};

const handleSaveScraper = async (id: string) => {
  await scraperStore.store(id);
  await loadData();
};

const handleScraperPreview = (_id: number, scraper: Scraper) => {
  scraperPreview = scraper;
  dialogOpen.value = true;
};

const handleScraperDialogHide = () => {
  scraperPreview = null;
  dialogOpen.value = false;
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
      await loadData();
    }
  },
  { immediate: true },
);
</script>
