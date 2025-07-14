<template>
  <q-tr>
    <q-td>
      <q-input
        v-model="countryRef.country_code"
        dense
        borderless
        hide-bottom-space
        :error="!!error?.country_code"
        :error-message="error?.country_code?.toString()"
      />
    </q-td>
    <q-td>
      <q-input
        v-model="countryRef.country_name"
        dense
        borderless
        hide-bottom-space
        :error="!!error?.country_name"
        :error-message="error?.country_name?.toString()"
      />
    </q-td>
    <q-td auto-width>
      <TableAction>
        <q-item clickable v-close-popup @click="emit('save', country.$id?.toString()!)">
          <q-item-section>Save</q-item-section>
        </q-item>
        <q-item clickable v-close-popup @click="emit('delete', country.$id?.toString()!)">
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
