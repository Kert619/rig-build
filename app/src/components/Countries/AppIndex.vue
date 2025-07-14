<template>
  <div>
    <q-table
      :columns="columns"
      :rows="countries"
      row-key="country_code"
      flat
      bordered
      dense
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
            @click="countryStore.create()"
          />

          <q-input
            v-model="search"
            dense
            outlined
            square
            clearable
            debounce="600"
            placeholder="Search country"
            class="full-width"
            style="max-width: 30vw"
          >
            <template #prepend>
              <q-icon name="search" />
            </template>
          </q-input>

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

      <template #body="props" v-if="!refresh">
        <AppEdit
          :country="props.row"
          @delete="handleDelete(props.row.country_code, true)"
          @save="handleUpdated"
        />
      </template>
    </q-table>
  </div>
</template>

<script setup lang="ts">
import { useQuasar, type QTableColumn } from 'quasar';
import AppCreate from 'components/Countries/AppCreate.vue';
import AppEdit from 'components/Countries/AppEdit.vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import { type Country, useCountryStore } from 'src/stores/country';

const $q = useQuasar();
const countryStore = useCountryStore();
const loading = ref(false);
const search = ref('');
const refresh = ref(false);

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
  loading.value = true;
  await countryStore.fetchIndex();
  loading.value = false;
};

const countries = computed(() => {
  if (search.value.trim()) {
    return countryStore.countries.filter((country) => {
      const hasCountryCode = country.country_code
        .toLowerCase()
        .includes(search.value.trim().toLowerCase());
      const hasCountryName = country.country_name
        .toLowerCase()
        .includes(search.value.trim().toLowerCase());

      return hasCountryCode || hasCountryName;
    });
  } else {
    return countryStore.countries;
  }
});

const created = computed(() => Array.from(countryStore.created.values()));

const handleDelete = (id: string, edit = false) => {
  if (edit) {
    $q.dialog({
      title: 'Confirm',
      message: 'Do you want to remove this country?',
      cancel: true,
    }).onOk(() => {
      void (async () => {
        loading.value = true;
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
  loading.value = true;
  await countryStore.store(id);
  await loadCountries();
  await handleRefresh();
};

const handleUpdated = async (id: string, country: Country) => {
  loading.value = true;
  countryStore.current.set(id, country);
  await countryStore.update(id);
  await loadCountries();
  await handleRefresh();
};

const handleRefresh = async () => {
  refresh.value = true;
  await nextTick();
  refresh.value = false;
};
</script>
