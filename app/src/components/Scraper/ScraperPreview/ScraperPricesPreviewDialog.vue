<template>
  <q-card square flat bordered>
    <q-card-section
      class="q-pa-none absolute-top bg-grey-3"
      :class="{ 'bg-grey-10': $q.dark.isActive }"
      style="z-index: 1; height: 48px"
    >
      <div class="full-height q-px-md row justify-between items-center">
        <span class="text-caption">Prices Preview - {{ scraper.scraper_url }}</span>
        <q-btn dense size="sm" flat icon="close" color="negative" @click="emit('hide')" />
      </div>
    </q-card-section>

    <q-card-section
      class="overflow-auto q-pa-none"
      style="height: calc(100% - 48px); margin-top: 48px"
    >
      <q-table
        :columns="columns"
        :rows="prices"
        row-key="price_store_ident"
        flat
        bordered
        dense
        square
        :loading="loading"
      >
        <template v-slot:body-cell-price_url="props">
          <q-td :props="props">
            <q-btn
              v-if="props.row.price_url"
              dense
              size="sm"
              flat
              icon="open_in_new"
              @click="useOpenLink(props.row.price_url)"
              color="primary"
            />
            <span class="q-ml-sm">{{ props.row.price_url }}</span>
          </q-td>
        </template>

        <template v-slot:body-cell-img_url="props">
          <q-td :props="props">
            <q-btn
              v-if="props.row.img_url"
              dense
              size="sm"
              flat
              icon="open_in_new"
              @click="useOpenLink(props.row.img_url)"
              color="primary"
            />
            <span class="q-ml-sm">{{ props.row.img_url }}</span>
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
    name: 'price_store_ident',
    label: 'Price Store Ident',
    field: 'price_store_ident',
    align: 'left',
    sortable: true,
  },
  {
    name: 'price_name',
    label: 'Price Name',
    field: 'price_name',
    align: 'left',
    sortable: true,
  },
  {
    name: 'currency',
    label: 'Currency',
    field: 'currency',
    align: 'left',
    sortable: true,
  },
  {
    name: 'price',
    label: 'Price',
    field: 'price',
    align: 'left',
    sortable: true,
  },
  {
    name: 'stock_status',
    label: 'Stock Status',
    field: 'stock_status',
    align: 'left',
    sortable: true,
  },
  {
    name: 'stock_quantity',
    label: 'Stock Quantity',
    field: 'stock_quantity',
    align: 'left',
    sortable: true,
  },
  {
    name: 'rating',
    label: 'Rating',
    field: 'rating',
    align: 'left',
    sortable: true,
  },
  {
    name: 'price_url',
    label: 'Price URL',
    field: 'price_url',
    align: 'left',
    sortable: true,
  },
  {
    name: 'img_url',
    label: 'Img URL',
    field: 'img_url',
    align: 'left',
    sortable: true,
  },
];

onMounted(async () => {
  await loadPreviewPrices();
});

const loadPreviewPrices = async () => {
  try {
    loading.value = true;
    await scraperStore.preview(props.scraper.scraper_id);
  } finally {
    loading.value = false;
  }
};

onUnmounted(() => {
  scraperStore.previewPrices = [];
});

const prices = computed(() => scraperStore.previewPrices);
</script>
