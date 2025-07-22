import { defineStore } from 'pinia';
import { Notify, uid } from 'quasar';
import { api } from 'src/boot/axios';
import { useApiError } from 'src/composables/useApiError';
import { ref, type Ref } from 'vue';

export type Scraper = {
  $id?: string;
  store_id: number;
  scraper_name: string;
  scraper_url: string;
  scraper_config: ScraperConfig;
  is_running: boolean;
  is_active: boolean;
  last_run: string;
  $loading?: boolean;
};

export type ScraperConfig = {
  category: ScraperConfigCategory;
  product: ScraperConfigProduct;
};

type ScraperConfigCategory = {
  container_regex: string;
  regex: string;
};

type ScraperConfigProduct = {
  container_regex: string | null;
  container_selector: string | null;
  regex: string | null;
  selector: string | null;
  format: ScraperConfigProductFormat;
  page_rules: string[];
  pagination: ScraperConfigProductPagination;
  ajax: ScraperConfigProductAjax | null;
};

type ScraperConfigProductFormat = {
  price_store_ident: string;
  price_name: string;
  price: string;
  stock_status: string | null;
  stock_quantity: string | null;
  rating: string | null;
  price_url: string;
  img_url: string | null;
  currency: string;
};

type ScraperConfigProductPagination = {
  container_regex: string;
  base_pagination_link: string;
  pages_regex: string;
  page_query: string;
};

type ScraperConfigProductAjax = {
  api_base_url: string;
  product_link_base_url: string;
};

export type ScraperError = {
  scraper_name?: string;
  scraper_url?: string;
};

export const useScraperStore = defineStore('scraper', () => {
  const created: Ref<Map<string, Scraper>> = ref(new Map());
  const createdErrors: Ref<Map<string, ScraperError>> = ref(new Map());

  const create = (prefill: Scraper = <Scraper>{}) => {
    const id = uid();

    created.value.set(id, { ...prefill, $id: id } as Scraper);

    return created.value.get(id)!;
  };

  const store = async (id: string) => {
    const scraper = created.value.get(id)!;
    try {
      createdErrors.value.delete(id);
      scraper.$loading = true;
      await api.post('/scrapers', scraper);
      created.value.delete(id);
      Notify.create({
        message: 'New scraper added',
        type: 'positive',
      });
    } catch (error) {
      const errors = useApiError(error);
      if (errors) {
        createdErrors.value.set(id, errors);
      }

      throw error;
    } finally {
      scraper.$loading = false;
    }
  };

  return { created, createdErrors, create, store };
});
