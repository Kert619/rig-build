<template>
  <q-card square flat bordered>
    <q-card-section
      class="q-pa-none absolute-top bg-grey-3"
      :class="{ 'bg-grey-10': $q.dark.isActive }"
      style="z-index: 1; height: 48px"
    >
      <div class="full-height q-px-md row justify-between items-center">
        <q-chip size="sm" :label="`Scraper ${scraperRef.scraper_id}`" color="primary" />
        <div v-if="hasUnsaveChanges" class="text-negative">You have unsave changes!</div>
        <q-btn dense size="sm" flat icon="close" color="negative" @click="emit('hide')" />
      </div>
    </q-card-section>

    <q-card-section
      class="overflow-auto q-pa-none"
      style="height: calc(100% - 96px); margin-block: 48px"
    >
      <div class="row full-height">
        <div class="col-5 border q-pa-md column q-gutter-y-md">
          <div>
            <q-chip label="Scraper Settings" size="sm" color="primary" />
            <ScraperDialogScraperSection
              :scraper="scraperRef"
              :error="scraperStore.currentErrors.get(scraperRef.scraper_id)"
            />
          </div>

          <div>
            <q-chip label="Category" size="sm" color="primary" />
            <ScraperDialogCategorySection
              :scraper="scraperRef"
              :error="scraperStore.currentErrors.get(scraperRef.scraper_id)"
              @process-category="emit('processCategory')"
            />
          </div>

          <div>
            <q-chip label="Product" size="sm" color="primary" />
            <ScraperDialogProductSection
              :scraper="scraperRef"
              :error="scraperStore.currentErrors.get(scraperRef.scraper_id)"
            />
          </div>

          <div>
            <q-chip label="Pagination" size="sm" color="primary" />
            <ScraperDialogPaginationSection :scraper="scraperRef" />
          </div>

          <div>
            <q-chip label="Ajax" size="sm" color="primary" />
            <ScraperDialogAjaxSection :scraper="scraperRef" />
          </div>
        </div>
        <div class="col-5 border q-pa-md">
          <q-chip label="Page Rules" size="sm" color="primary" />
          <ScraperDialogPageRulesSection
            :scraper="scraperRef"
            :error="scraperStore.currentErrors.get(scraperRef.scraper_id)"
          />
        </div>
        <div class="col-2 border q-pa-md column q-gutter-y-md">
          <q-chip label="Format" size="sm" color="primary" class="self-start" icon-right="info">
            <q-tooltip>Format Example: "1,1" (Except for Currency)</q-tooltip>
          </q-chip>
          <ScraperDialogFormatSection
            :scraper="scraperRef"
            :error="scraperStore.currentErrors.get(scraperRef.scraper_id)"
          />
        </div>
      </div>
    </q-card-section>

    <q-card-section
      class="q-pa-none absolute-bottom bg-grey-3"
      :class="{ 'bg-grey-10': $q.dark.isActive }"
      style="z-index: 1; height: 48px"
    >
      <div class="full-height q-px-md row justify-between items-center q-gutter-x-md">
        <q-space />
        <q-btn
          dense
          size="sm"
          unelevated
          label="Query Selector Debugger"
          icon="code"
          @click="emit('openSelectorDebug')"
        />

        <q-btn
          dense
          size="sm"
          unelevated
          color="primary"
          label="Preview Prices"
          icon="visibility"
          @click="emit('preview', scraperRef.scraper_id)"
        />

        <q-btn
          dense
          size="sm"
          unelevated
          color="positive"
          label="Save"
          icon="save"
          :loading="scraperStore.current.get(scraperRef.scraper_id)?.$loading"
          @click="handleSave"
        />
      </div>
    </q-card-section>
  </q-card>
</template>

<script setup lang="ts">
import { useScraperStore, type Scraper } from 'src/stores/scraper';
import { computed, ref, toRef } from 'vue';
import ScraperDialogScraperSection from 'components/Scraper/ScraperDialogScraperSection.vue';
import ScraperDialogCategorySection from 'components/Scraper/ScraperDialogCategorySection.vue';
import ScraperDialogProductSection from 'components/Scraper/ScraperDialogProductSection.vue';
import ScraperDialogFormatSection from 'components/Scraper/ScraperDialogFormatSection.vue';
import ScraperDialogPageRulesSection from 'components/Scraper/ScraperDialogPageRulesSection.vue';
import ScraperDialogPaginationSection from 'components/Scraper/ScraperDialogPaginationSection.vue';
import ScraperDialogAjaxSection from 'components/Scraper/ScraperDialogAjaxSection.vue';
import { useQuasar } from 'quasar';

const emit = defineEmits<{
  hide: [];
  run: [];
  preview: [id: number];
  openSelectorDebug: [];
  processCategory: [];
}>();

const props = defineProps<{
  scraper: Scraper;
}>();

const $q = useQuasar();
const scraperStore = useScraperStore();
const scraperRef = toRef(props.scraper);
const original = ref(JSON.parse(JSON.stringify(scraperRef.value)));

const hasUnsaveChanges = computed(() => {
  return JSON.stringify(original.value) != JSON.stringify(scraperRef.value);
});

const handleSave = () => {
  $q.dialog({
    title: 'Confirm',
    message: 'Do you want to save this scraper?',
    cancel: true,
  }).onOk(() => {
    void (async () => {
      scraperStore.current.set(scraperRef.value.scraper_id, scraperRef.value);
      await scraperStore.update(scraperRef.value.scraper_id);
      original.value = JSON.parse(JSON.stringify(scraperRef.value));
    })();
  });
};
</script>

<style scoped lang="scss">
.body--light .border:not(:last-child) {
  border-right: 1px solid $separator-color;
}

.body--dark .border:not(:last-child) {
  border-right: 1px solid $separator-dark-color;
}
</style>
