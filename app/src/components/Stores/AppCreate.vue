<template>
  <div class="q-pa-md">
    <q-card flat bordered square class="q-pa-sm">
      <q-card-section class="row justify-between q-pt-none q-px-none">
        <q-chip label="New" icon="add" color="primary" size="sm" />
        <q-btn
          dense
          flat
          icon="close"
          color="negative"
          @click="emit('delete', storeRef.$id?.toString()!)"
        />
      </q-card-section>
      <q-form class="row q-col-gutter-md" @submit="handleSubmit">
        <div class="col-6">
          <q-input
            v-model="storeRef.store_name"
            label="Store Name"
            dense
            hide-bottom-space
            square
          />
        </div>

        <div class="col-6">
          <q-input v-model="storeRef.country_code" label="Country" dense hide-bottom-space square />
        </div>

        <div class="col-12">
          <q-input v-model="storeRef.store_url" label="Store Url" dense hide-bottom-space square />
        </div>

        <div class="col-12">
          <q-btn
            label="Save"
            type="submit"
            color="positive"
            dense
            size="sm"
            unelevated
            icon="save"
          />
        </div>
      </q-form>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { type Store } from 'src/stores/store';
import { toRef } from 'vue';

const emit = defineEmits<{
  save: [id: string, store: Store];
  delete: [id: string];
}>();
const props = defineProps<{
  store: Store;
}>();

const storeRef = toRef(props.store);

const handleSubmit = () => {
  console.log('Submitted');
};
</script>
