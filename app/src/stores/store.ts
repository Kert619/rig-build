import { defineStore } from 'pinia';
import { Notify, uid } from 'quasar';
import { api } from 'src/boot/axios';
import { useApiError } from 'src/composables/useApiError';
import { ref, type Ref } from 'vue';

export type Store = {
  $id?: string;
  store_name: string;
  country_code: string;
  store_url: string;
};

export type StoreError = Partial<Record<keyof Store, string[]>>;

export const useStoreStore = defineStore('store', () => {
  const stores: Ref<Store[]> = ref([]);
  const created: Ref<Map<string, Store>> = ref(new Map());
  const createdErrors: Ref<Map<string, StoreError>> = ref(new Map());
  const current: Ref<Map<string, Store>> = ref(new Map());
  const currentErrors: Ref<Map<string, StoreError>> = ref(new Map());

  const fetchIndex = async () => {
    try {
      const response = await api.get('/stores');
      stores.value = response.data;
    } catch (error) {
      useApiError(error);
    }
  };

  const create = (prefill: Partial<Store> = {}) => {
    const id = uid();

    created.value.set(id, { ...prefill, $id: id } as Store);
  };

  const store = async (id: string) => {
    try {
      createdErrors.value.delete(id);
      await api.post('/stores', created.value.get(id));
      created.value.delete(id);
      Notify.create({
        message: 'New store added',
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
      await api.put(`/stores/${id}`, current.value.get(id));
      current.value.delete(id);
      Notify.create({
        message: 'Store updated',
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
      await api.delete(`/stores/${id}`);
      Notify.create({
        message: 'Store is removed',
        type: 'positive',
      });
    } catch (error) {
      useApiError(error);
    }
  };

  return {
    stores,
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
