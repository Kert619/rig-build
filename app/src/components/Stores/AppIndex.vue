<template>
  <div class="row" style="min-height: inherit">
    <div class="col overflow-auto" style="max-width: 350px; min-height: inherit; max-height: 90vh">
      <div class="col-12 row justify-between items-center q-pa-sm">
        <span class="text-caption">Stores List</span>
        <q-btn label="Create" icon="add" unelevated square size="sm" @click="storeStore.create()" />
      </div>

      <q-separator />

      <div class="col-12">
        <q-list separator>
          <q-item clickable v-ripple>
            <q-item-section> Item </q-item-section>
          </q-item>

          <q-item clickable v-ripple>
            <q-item-section> Item </q-item-section>
          </q-item>

          <q-item clickable v-ripple>
            <q-item-section> Item </q-item-section>
          </q-item>
        </q-list>
      </div>
    </div>

    <q-separator vertical />

    <div class="col overflow-auto" style="min-height: inherit; max-height: 90vh">
      <AppCreate
        v-for="create in created"
        :key="create.$id?.toString() ?? ''"
        :store="create"
        @delete="handleDelete"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useStoreStore } from 'src/stores/store';
import AppCreate from 'components/Stores/AppCreate.vue';
import { computed } from 'vue';

const storeStore = useStoreStore();

const created = computed(() => Array.from(storeStore.created.values()));

const handleDelete = (id: string | number) => {
  storeStore.created.delete(id as string);
};
</script>
