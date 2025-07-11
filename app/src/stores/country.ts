import { defineStore } from 'pinia';
import { uid } from 'quasar';
import { api } from 'src/boot/axios';
import { ref, type Ref } from 'vue';

export type Country = {
  country_code: string;
  country_name: string;
};

export type CountryError = Partial<Record<keyof Country, string[]>>;

export const useCountryStore = defineStore('country', () => {
  const countries: Ref<Country[]> = ref([]);
  const created: Ref<Map<string, Country>> = ref(new Map());
  const createdErrors: Ref<CountryError | null> = ref(null);

  const create = (prefill: Partial<Country> = {}) => {
    const id = uid();

    created.value.set(id, { ...prefill } as Country);
  };

  const store = async (id: string) => {
    await api.post('/countries', created.value.get(id));
  };

  return { countries, created, createdErrors, create, store };
});
