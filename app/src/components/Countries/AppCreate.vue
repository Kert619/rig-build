<template>
  <q-tr>
    <q-td>
      <TextInput
        v-model="countryRef.country_code"
        borderless
        max-length="2"
        :error="!!error?.country_code"
        :error-message="error?.country_code?.toString()"
      >
        <template #before>
          <q-chip label="New" icon="add" color="primary" size="sm" />
        </template>
      </TextInput>
    </q-td>
    <q-td>
      <TextInput
        v-model="countryRef.country_name"
        borderless
        :error="!!error?.country_name"
        :error-message="error?.country_name?.toString()"
      />
    </q-td>
    <q-td auto-width>
      <TableAction :loading="countryRef.$loading">
        <q-item clickable v-close-popup @click="emit('save', countryRef.$id?.toString()!)">
          <q-item-section>Save</q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="emit('delete', countryRef.$id?.toString()!)">
          <q-item-section>Delete</q-item-section>
        </q-item>
      </TableAction>
    </q-td>
  </q-tr>
</template>

<script setup lang="ts">
import { type CountryError, type Country } from 'src/stores/country';
import { toRef } from 'vue';
import TableAction from 'components/UI/TableAction.vue';
import TextInput from 'components/UI/TextInput.vue';

const emit = defineEmits<{
  delete: [id: string];
  save: [id: string];
}>();

const props = defineProps<{
  country: Country;
  error?: CountryError | undefined;
}>();

const countryRef = toRef(props.country);
</script>
