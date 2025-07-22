<template>
  <div class="row" style="min-height: inherit">
    <div class="col overflow-auto" style="max-width: 350px; min-height: inherit; max-height: 90vh">
      <div class="col-12 row justify-between items-center q-pa-sm">
        <span class="text-caption">Stores List</span>
        <q-btn
          label="Create"
          icon="add"
          unelevated
          square
          dense
          size="sm"
          @click="storeStore.create()"
        />
      </div>

      <q-separator />

      <div class="col-12">
        <q-list separator v-if="!storeStore.refresh" dense>
          <q-item-label header v-if="loading">Loading...</q-item-label>

          <template v-else>
            <q-item
              clickable
              v-ripple
              v-for="store in stores"
              :key="store.store_id"
              :to="`/stores/${store.store_id}`"
            >
              <q-item-section> {{ store.store_name }} </q-item-section>
            </q-item>
          </template>
        </q-list>
      </div>
    </div>

    <q-separator vertical />

    <div class="col overflow-auto" style="min-height: inherit; max-height: 90vh">
      <AppCreate
        v-for="create in created"
        :key="create.$id?.toString() ?? ''"
        :store="create"
        :error="storeStore.createdErrors.get(create.$id?.toString()!)"
        @delete="handleDelete"
        @save="handleSave"
      />

      <div class="q-pa-md">
        <RouterView />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useStoreStore } from 'src/stores/store';
import AppCreate from 'components/Stores/AppCreate.vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import { useCountryStore } from 'src/stores/country';

const storeStore = useStoreStore();
const countryStore = useCountryStore();
const loading = ref(false);

onMounted(async () => {
  await Promise.all([countryStore.fetchOptions(), loadStores()]);
});

const loadStores = async () => {
  loading.value = true;
  await storeStore.fetchIndex();
  loading.value = false;
};

const created = computed(() => Array.from(storeStore.created.values()));

const stores = computed(() => storeStore.index);

const handleDelete = (id: string | number) => {
  storeStore.created.delete(id as string);
};

const handleSave = async (id: string) => {
  await storeStore.store(id);
  await loadStores();
  await handleRefresh();
};

const handleRefresh = async () => {
  storeStore.refresh = true;
  await nextTick();
  storeStore.refresh = false;
};
</script>
