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
          size="sm"
          @click="emit('delete', storeRef.$id?.toString()!)"
        />
      </q-card-section>
      <q-form class="row q-col-gutter-md" @submit="handleSubmit">
        <div class="col-6">
          <TextInput
            v-model="storeRef.store_name"
            label="Store Name"
            :error="!!error?.store_name"
            :error-message="error?.store_name?.toString()"
          />
        </div>

        <div class="col-6">
          <SelectOptions
            v-model="storeRef.country_code"
            :options="countryStore.options"
            :error="!!error?.country_code"
            :error-message="error?.country_code?.toString()"
            label="Country"
          />
        </div>

        <div class="col-12">
          <TextInput
            v-model="storeRef.store_url"
            label="Store Url"
            :error="!!error?.store_url"
            :error-message="error?.store_url?.toString()"
          />
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
            glossy
            :loading="storeRef.$loading"
          />
        </div>
      </q-form>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { type StoreError, type Store } from 'src/stores/store';
import { toRef } from 'vue';
import SelectOptions from 'components/UI/SelectOptions.vue';
import TextInput from 'components/UI/TextInput.vue';
import { useCountryStore } from 'src/stores/country';

const emit = defineEmits<{
  save: [id: string];
  delete: [id: string];
}>();
const props = defineProps<{
  store: Store;
  error?: StoreError | undefined;
}>();

const countryStore = useCountryStore();

const storeRef = toRef(props.store);

const handleSubmit = () => {
  emit('save', storeRef.value.$id?.toString() ?? '');
};
</script>
