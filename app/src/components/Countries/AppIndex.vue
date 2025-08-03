<template>
  <q-table
    :columns="columns"
    :rows="countries"
    row-key="country_code"
    flat
    bordered
    dense
    square
    :loading="loading"
  >
    <template #top>
      <div class="row items-center justify-between full-width">
        <q-btn
          label="Add Country"
          icon="add"
          color="primary"
          dense
          size="sm"
          unelevated
          glossy
          @click="countryStore.create()"
        />

        <TextInput
          v-model="search"
          clearable
          debounce="600"
          placeholder="Search country"
          class="full-width"
          style="max-width: 30vw"
        >
          <template #prepend>
            <q-icon name="search" />
          </template>
        </TextInput>

        <span class="text-body2">Countries</span>
      </div>
    </template>

    <template #top-row>
      <AppCreate
        v-for="create in created"
        :key="create.$id?.toString() ?? ''"
        :country="create"
        :error="countryStore.createdErrors.get(create.$id?.toString()!)"
        @delete="handleDelete"
        @save="handleSave"
      />
    </template>

    <template #body="props">
      <AppEdit
        v-if="!countryStore.refresh"
        :country="props.row"
        :error="countryStore.currentErrors.get(props.row.country_code)"
        @delete="(id: string, country: Country) => handleDelete(id, true, country)"
        @save="handleUpdated"
        :key="props.row.country_code"
      />
    </template>
  </q-table>
</template>

<script setup lang="ts">
import { useQuasar, type QTableColumn } from 'quasar';
import AppCreate from 'components/Countries/AppCreate.vue';
import AppEdit from 'components/Countries/AppEdit.vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import { type Country, useCountryStore } from 'src/stores/country';
import TextInput from 'components/UI/TextInput.vue';

const $q = useQuasar();
const countryStore = useCountryStore();
const loading = ref(false);
const search = ref('');

const columns: QTableColumn[] = [
  {
    name: 'country_code',
    label: 'Country Code',
    field: 'country_code',
    align: 'left',
    sortable: true,
  },
  {
    name: 'country_name',
    label: 'Country Name',
    field: 'country_name',
    align: 'left',
    sortable: true,
  },
  {
    name: '',
    label: 'Action',
    field: '',
  },
];

onMounted(async () => {
  await loadCountries();
});

const loadCountries = async () => {
  try {
    loading.value = true;
    await countryStore.fetchIndex();
  } finally {
    loading.value = false;
  }
};

const countries = computed(() => {
  if (search.value) {
    return countryStore.index.filter((country) => {
      const hasCountryCode = country.country_code
        .toLowerCase()
        .includes(search.value.toLowerCase());
      const hasCountryName = country.country_name
        .toLowerCase()
        .includes(search.value.toLowerCase());

      return hasCountryCode || hasCountryName;
    });
  } else {
    return countryStore.index;
  }
});

const created = computed(() => Array.from(countryStore.created.values()));

const handleDelete = (id: string, edit = false, country: Country | null = null) => {
  if (edit) {
    $q.dialog({
      title: 'Confirm',
      message: 'Do you want to remove this country?',
      cancel: true,
    }).onOk(() => {
      void (async () => {
        if (!country) return;
        countryStore.current.set(id, country);
        await countryStore.destroy(id);
        await loadCountries();
        await handleRefresh();
      })();
    });
  } else {
    countryStore.created.delete(id);
    countryStore.createdErrors.delete(id);
  }
};

const handleSave = async (id: string) => {
  await countryStore.store(id);
  await loadCountries();
  await handleRefresh();
};

const handleUpdated = async (id: string, country: Country) => {
  countryStore.current.set(id, country);
  await countryStore.update(id);
  await loadCountries();
  await handleRefresh();
};

const handleRefresh = async () => {
  countryStore.refresh = true;
  await nextTick();
  countryStore.refresh = false;
};
</script>
