import { defineStore } from 'pinia';
import { Notify, type QSelectOption, uid } from 'quasar';
import { api } from 'src/boot/axios';
import { useApiError } from 'src/composables/useApiError';
import { ref, type Ref } from 'vue';

export type Country = {
  $id?: string;
  country_code: string;
  country_name: string;
  $loading?: boolean;
};

export type CountryError = Partial<Record<keyof Country, string[]>>;

export const useCountryStore = defineStore('country', () => {
  const index: Ref<Country[]> = ref([]);
  const created: Ref<Map<string, Country>> = ref(new Map());
  const createdErrors: Ref<Map<string, CountryError>> = ref(new Map());
  const current: Ref<Map<string, Country>> = ref(new Map());
  const currentErrors: Ref<Map<string, CountryError>> = ref(new Map());
  const options: Ref<QSelectOption<string>[]> = ref([]);
  const refresh = ref(false);

  const fetchIndex = async () => {
    try {
      const response = await api.get('/countries');
      index.value = response.data;
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const fetchOptions = async (force = false) => {
    try {
      if (force || !options.value.length) {
        const response = await api.get('/countries/options');
        options.value = response.data;
      }
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const create = (prefill: Partial<Country> = {}) => {
    const id = uid();

    created.value.set(id, { ...prefill, $id: id } as Country);
  };

  const store = async (id: string) => {
    const country = created.value.get(id)!;
    try {
      createdErrors.value.delete(id);
      country.$loading = true;
      await api.post('/countries', country);
      created.value.delete(id);
      Notify.create({
        message: 'New country added',
        type: 'positive',
      });

      await fetchOptions(true);
    } catch (error) {
      const errors = useApiError(error);
      if (errors) {
        createdErrors.value.set(id, errors);
      }

      throw error;
    } finally {
      country.$loading = false;
    }
  };

  const update = async (id: string) => {
    const country = current.value.get(id)!;
    try {
      currentErrors.value.delete(id);
      country.$loading = true;
      await api.put(`/countries/${id}`, country);
      Notify.create({
        message: 'Country updated',
        type: 'positive',
      });

      await fetchOptions(true);
    } catch (error) {
      const errors = useApiError(error);
      if (errors) {
        currentErrors.value.set(id, errors);
      }

      throw error;
    } finally {
      current.value.delete(id);
      country.$loading = false;
    }
  };

  const destroy = async (id: string) => {
    const country = current.value.get(id)!;

    try {
      country.$loading = true;
      await api.delete(`/countries/${id}`);
      Notify.create({
        message: 'Country is removed',
        type: 'positive',
      });

      await fetchOptions(true);
    } catch (error) {
      useApiError(error);

      throw error;
    } finally {
      current.value.delete(id);
      country.$loading = false;
    }
  };

  return {
    index,
    options,
    created,
    createdErrors,
    current,
    currentErrors,
    refresh,
    fetchIndex,
    fetchOptions,
    create,
    store,
    update,
    destroy,
  };
});
