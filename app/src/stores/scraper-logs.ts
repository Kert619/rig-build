import { defineStore } from 'pinia';
import { Notify } from 'quasar';
import { api } from 'src/boot/axios';
import { useApiError } from 'src/composables/useApiError';
import { ref, type Ref } from 'vue';

export type ScraperLog = {
  scraper_log_id: number;
  scraper_id: number;
  started: string;
  ended: string;
  error_message: string;
  price_count: number;
  scraper: Scraper;
  $loading?: boolean;
};

type Scraper = {
  scraper_id: number;
  scraper_name: string;
};

type Filter = {
  scraper_id?: string;
};

export const useScraperLogsStore = defineStore('scraper-logs', () => {
  const filter: Ref<Filter> = ref({});
  const index: Ref<ScraperLog[]> = ref([]);
  const current: Ref<Map<number, ScraperLog>> = ref(new Map());
  const refresh = ref(false);

  const fetchIndex = async () => {
    try {
      const searchParams = new URLSearchParams();

      Object.entries(filter.value).forEach(([key, value]) => {
        if (value) {
          searchParams.append(key, value);
        }
      });

      const response = await api.get(`/scraper-logs?${searchParams.toString()}`);
      index.value = response.data;
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const truncate = async () => {
    try {
      await api.delete('/scraper-logs/truncate');
      Notify.create({
        message: 'All scraper logs has been deleted',
        type: 'positive',
      });
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  return {
    filter,
    index,
    current,
    refresh,
    fetchIndex,
    truncate,
  };
});
