<template>
  <q-card flat bordered square class="q-pa-sm">
    <q-card-section class="row justify-between q-pt-none q-px-none">
      <q-chip size="sm" :label="`Store ID: ${store.store_id}`" color="primary" />
      <q-btn
        dense
        icon="close"
        color="negative"
        flat
        size="sm"
        :disable="storeRef.$loading"
        @click="emit('delete', storeRef.store_id)"
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
        >
          <template #append>
            <q-btn dense size="sm" flat icon="open_in_new" @click="openLink(storeRef.store_url)" />
          </template>
        </TextInput>
      </div>

      <div class="col-12">
        <q-btn
          label="Update"
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
</template>

<script setup lang="ts">
import { useCountryStore } from 'src/stores/country';
import { type StoreError, type Store } from 'src/stores/store';
import { toRef } from 'vue';
import TextInput from 'components/UI/TextInput.vue';
import SelectOptions from 'components/UI/SelectOptions.vue';

const emit = defineEmits<{
  save: [id: number];
  delete: [id: number];
}>();

const props = defineProps<{
  store: Store;
  error?: StoreError | undefined;
}>();

const countryStore = useCountryStore();

const storeRef = toRef(props.store);

const handleSubmit = () => {
  emit('save', storeRef.value.store_id);
};

const openLink = (link: string) => {
  if (!link.trim()) return;
  window.open(link, '_blank');
};
</script>
