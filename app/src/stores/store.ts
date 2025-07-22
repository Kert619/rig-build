import { defineStore } from 'pinia';
import { Notify, type QSelectOption, uid } from 'quasar';
import { api } from 'src/boot/axios';
import { useApiError } from 'src/composables/useApiError';
import { ref, type Ref } from 'vue';

export type Store = {
  $id?: string;
  store_id: number;
  store_name: string;
  country_code: string;
  store_url: string;
  $loading?: boolean;
};

export type StoreError = Partial<Record<keyof Store, string[]>>;

export const useStoreStore = defineStore('store', () => {
  const index: Ref<Store[]> = ref([]);
  const created: Ref<Map<string, Store>> = ref(new Map());
  const createdErrors: Ref<Map<string, StoreError>> = ref(new Map());
  const current: Ref<Map<number, Store>> = ref(new Map());
  const currentErrors: Ref<Map<number, StoreError>> = ref(new Map());
  const options: Ref<QSelectOption<number>[]> = ref([]);
  const refresh = ref(false);

  const fetchIndex = async () => {
    try {
      const response = await api.get('/stores');
      index.value = response.data;
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const fetchOptions = async (force = false) => {
    try {
      if (force || !options.value.length) {
        const response = await api.get('/stores/options');
        options.value = response.data;
      }
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const show = async (id: number) => {
    try {
      const response = await api.get(`/stores/${id}`);
      current.value.set(id, response.data);
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const create = (prefill: Partial<Store> = {}) => {
    const id = uid();

    created.value.set(id, { ...prefill, $id: id } as Store);
  };

  const store = async (id: string) => {
    const store = created.value.get(id)!;

    try {
      createdErrors.value.delete(id);
      store.$loading = true;
      await api.post('/stores', store);
      created.value.delete(id);
      Notify.create({
        message: 'New store added',
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
      store.$loading = false;
    }
  };

  const update = async (id: number) => {
    const store = current.value.get(id)!;
    try {
      currentErrors.value.delete(id);
      store.$loading = true;
      await api.put(`/stores/${id}`, store);

      Notify.create({
        message: 'Store updated',
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
      store.$loading = false;
    }
  };

  const destroy = async (id: number) => {
    const store = current.value.get(id)!;

    try {
      store.$loading = true;
      await api.delete(`/stores/${id}`);
      current.value.delete(id);
      Notify.create({
        message: 'Store is removed',
        type: 'positive',
      });

      await fetchOptions(true);
    } catch (error) {
      useApiError(error);

      throw error;
    } finally {
      store.$loading = false;
    }
  };

  return {
    index,
    created,
    createdErrors,
    current,
    currentErrors,
    refresh,
    fetchIndex,
    fetchOptions,
    show,
    create,
    store,
    update,
    destroy,
  };
});
