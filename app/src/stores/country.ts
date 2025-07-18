import { defineStore } from 'pinia';
import { Notify, uid } from 'quasar';
import { api } from 'src/boot/axios';
import { useApiError } from 'src/composables/useApiError';
import { ref, type Ref } from 'vue';

export type Country = {
  $id?: string;
  country_code: string;
  country_name: string;
};

export type CountryError = Partial<Record<keyof Country, string[]>>;

export const useCountryStore = defineStore('country', () => {
  const countries: Ref<Country[]> = ref([]);
  const created: Ref<Map<string, Country>> = ref(new Map());
  const createdErrors: Ref<Map<string, CountryError>> = ref(new Map());
  const current: Ref<Map<string, Country>> = ref(new Map());
  const currentErrors: Ref<Map<string, CountryError>> = ref(new Map());

  const fetchIndex = async () => {
    try {
      const response = await api.get('/countries');
      countries.value = response.data;
    } catch (error) {
      useApiError(error);
    }
  };

  const create = (prefill: Partial<Country> = {}) => {
    const id = uid();

    created.value.set(id, { ...prefill, $id: id } as Country);
  };

  const store = async (id: string) => {
    try {
      createdErrors.value.delete(id);
      await api.post('/countries', created.value.get(id));
      created.value.delete(id);
      Notify.create({
        message: 'New country added',
        type: 'positive',
      });
    } catch (error) {
      const errors = useApiError(error);
      if (errors) {
        createdErrors.value.set(id, errors);
      }
    }
  };

  const update = async (id: string) => {
    try {
      currentErrors.value.delete(id);
      await api.put(`/countries/${id}`, current.value.get(id));
      current.value.delete(id);
      Notify.create({
        message: 'Country updated',
        type: 'positive',
      });
    } catch (error) {
      const errors = useApiError(error);
      if (errors) {
        currentErrors.value.set(id, errors);
      }
    }
  };

  const destroy = async (id: string) => {
    try {
      await api.delete(`/countries/${id}`);
      Notify.create({
        message: 'Country is removed',
        type: 'positive',
      });
    } catch (error) {
      useApiError(error);
    }
  };

  return {
    countries,
    created,
    createdErrors,
    current,
    currentErrors,
    fetchIndex,
    create,
    store,
    update,
    destroy,
  };
});
