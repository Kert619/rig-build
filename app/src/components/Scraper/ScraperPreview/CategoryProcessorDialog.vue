<template>
  <q-card square flat bordered>
    <q-card-section
      class="q-pa-none absolute-top bg-grey-3"
      :class="{ 'bg-grey-10': $q.dark.isActive }"
      style="z-index: 1; height: 48px"
    >
      <div class="full-height q-px-md row justify-between items-center">
        <span class="text-caption">Categories Processor - {{ scraper.scraper_url }}</span>
        <q-btn dense size="sm" flat icon="close" color="negative" @click="emit('hide')" />
      </div>
    </q-card-section>

    <q-card-section
      class="overflow-auto q-pa-none"
      style="height: calc(100% - 48px); margin-top: 48px"
    >
      <q-table
        :columns="columns"
        :rows="categories"
        :loading="loading"
        :rows-per-page-options="[0]"
        row-key="category_link"
        flat
        bordered
        dense
        square
      >
        <template v-slot:body-cell-category_link="props">
          <q-td :props="props">
            <q-btn
              dense
              size="sm"
              flat
              icon="open_in_new"
              color="primary"
              @click="useOpenLink(props.row.category_link)"
            />
            <span class="q-ml-sm">{{ props.row.category_link }}</span>
          </q-td>
        </template>
      </q-table>
    </q-card-section>
  </q-card>
</template>

<script setup lang="ts">
import { type QTableColumn } from 'quasar';
import { useOpenLink } from 'src/composables/useOpenLink';
import { useScraperStore, type Scraper } from 'src/stores/scraper';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const emit = defineEmits<{
  hide: [];
}>();

const props = defineProps<{
  scraper: Scraper;
}>();

const scraperStore = useScraperStore();
const loading = ref(false);

const columns: QTableColumn[] = [
  {
    name: 'category_link',
    label: 'Category Link',
    field: 'category_link',
    align: 'left',
    sortable: true,
  },
  {
    name: 'category_name',
    label: 'Category Name',
    field: 'category_name',
    align: 'left',
    sortable: true,
  },
];

onMounted(async () => {
  await loadCategories();
});

onUnmounted(() => {
  scraperStore.categoryProcessor = [];
});

const loadCategories = async () => {
  try {
    loading.value = true;
    await scraperStore.processCategories(props.scraper.scraper_id);
  } finally {
    loading.value = false;
  }
};

const categories = computed(() => scraperStore.categoryProcessor);
</script>
