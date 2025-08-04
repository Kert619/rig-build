<template>
  <q-card square flat bordered>
    <q-card-section
      class="q-pa-none absolute-top bg-grey-3"
      :class="{ 'bg-grey-10': $q.dark.isActive }"
      style="z-index: 1; height: 48px"
    >
      <div class="full-height q-px-md row justify-between items-center">
        <span class="text-caption">Pagination Processor - {{ scraper.scraper_url }}</span>
        <q-btn dense size="sm" flat icon="close" color="negative" @click="emit('hide')" />
      </div>
    </q-card-section>

    <q-card-section
      class="overflow-auto q-pa-none"
      style="height: calc(100% - 48px); margin-top: 48px"
    >
      <q-table
        :columns="columns"
        :rows="pagination"
        :loading="loading"
        :rows-per-page-options="[0]"
        row-key="category_link"
        flat
        bordered
        dense
        square
      >
        <template v-slot:body="props">
          <q-tr :props="props">
            <q-td key="category_link" :props="props">
              <q-btn
                dense
                size="sm"
                flat
                icon="open_in_new"
                color="primary"
                @click="useOpenLink(props.row.category_link)"
              />
              <span>
                {{ props.row.category_link }}
              </span>

              <q-chip
                :label="`${getPages(props.row.category_link).length + 1} page(s)`"
                dense
                color="primary"
              />

              <q-icon
                :name="props.row.$expanded ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"
                class="cursor-pointer"
                size="sm"
                @click="props.row.$expanded = !props.row.$expanded"
              />
            </q-td>
          </q-tr>

          <template v-if="props.row.$expanded">
            <q-tr :props="props" v-for="page in getPages(props.row.category_link)" :key="page">
              <q-td key="pages">
                <div class="q-pl-lg">
                  <q-btn
                    dense
                    size="sm"
                    flat
                    icon="open_in_new"
                    color="primary"
                    @click="useOpenLink(page)"
                  />
                  <span>{{ page }}</span>
                </div>
              </q-td>
            </q-tr>
          </template>
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
];

onMounted(async () => {
  await loadPagination();
});

onUnmounted(() => {
  scraperStore.paginationProcessor = [];
});

const loadPagination = async () => {
  try {
    loading.value = true;
    await scraperStore.processPagination(props.scraper.scraper_id);
  } finally {
    loading.value = false;
  }
};

const pagination = computed(() => scraperStore.paginationProcessor);

const getPages = (categoryLink: string) => {
  return pagination.value.find((item) => item.category_link == categoryLink)?.pages ?? [];
};
</script>
