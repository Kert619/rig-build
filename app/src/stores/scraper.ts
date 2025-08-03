import { defineStore } from 'pinia';
import { Notify, uid } from 'quasar';
import { api } from 'src/boot/axios';
import { useApiError } from 'src/composables/useApiError';
import { ref, type Ref } from 'vue';

export type Scraper = {
  $id?: string;
  scraper_id: number;
  store_id: number;
  scraper_name: string;
  scraper_url: string;
  scraper_config: ScraperConfig;
  is_running: boolean;
  is_active: boolean;
  last_run: string;
  $loading?: boolean;
};

export type PreviewPrices = {
  price_store_ident: string;
  price_name: string;
  currency: string;
  price: number;
  stock_status: string;
  stock_quantity: number;
  rating: string;
  price_url: string;
  img_url: string;
};

export type ScraperConfig = {
  settings: ScraperSettings;
  category: ScraperConfigCategory;
  product: ScraperConfigProduct;
};

export type ExtractMethod = 'regex' | 'selector';

export type ScraperSettings = 'puppeteer' | 'ajax' | 'curl';

type ScraperConfigCategory = {
  container_extract_method: ExtractMethod;
  container_regex: string;
  container_selector: string;
  regex: string;
};

type ScraperConfigProduct = {
  method: ExtractMethod;
  container_regex: string | null;
  container_selector: string | null;
  regex: string | null;
  selector: string | null;
  format: ScraperConfigProductFormat;
  page_rules: PageRule[];
  pagination: ScraperConfigProductPagination;
  ajax: ScraperConfigProductAjax;
};

type PageRule = {
  id: string;
  value: string;
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

export type CategoryProcessor = {
  category_link: string;
  category_name: string;
};

export type ScraperError = {
  scraper_name?: string;
  scraper_url?: string;
  scraper_config?: ScraperConfig;
};

type Filter = {
  store_id?: string;
};

export const useScraperStore = defineStore('scraper', () => {
  const filter: Ref<Filter> = ref({});
  const index: Ref<Scraper[]> = ref([]);
  const created: Ref<Map<string, Scraper>> = ref(new Map());
  const current: Ref<Map<number, Scraper>> = ref(new Map());
  const createdErrors: Ref<Map<string, ScraperError>> = ref(new Map());
  const currentErrors: Ref<Map<number, ScraperError>> = ref(new Map());
  const previewPrices: Ref<PreviewPrices[]> = ref([]);
  const categoryProcessor: Ref<CategoryProcessor[]> = ref([]);

  const fetchIndex = async () => {
    try {
      const params = new URLSearchParams();

      Object.entries(filter.value).forEach(([key, value]) => {
        if (value) {
          params.append(key, value);
        }
      });
      const response = await api.get(`/scrapers?${params.toString()}`);
      index.value = response.data;
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

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

  const update = async (id: number) => {
    const scraper = current.value.get(id)!;
    try {
      const scraperConfig = scraper.scraper_config;
      if (scraperConfig.product.method == 'regex') {
        scraperConfig.product.container_selector = '';
        scraperConfig.product.selector = '';
      } else {
        scraperConfig.product.container_regex = '';
        scraperConfig.product.regex = '';
      }

      if (scraperConfig.category.container_extract_method == 'regex') {
        scraperConfig.category.container_selector = '';
      } else {
        scraperConfig.category.container_regex = '';
      }

      currentErrors.value.delete(id);
      scraper.$loading = true;
      await api.put(`/scrapers/${id}`, {
        scraper_name: scraper.scraper_name,
        scraper_url: scraper.scraper_url,
        scraper_config: scraperConfig,
      });
      current.value.delete(id);
      Notify.create({
        message: 'Scraper updated',
        type: 'positive',
      });
    } catch (error) {
      const errors = useApiError(error);
      if (errors) {
        normalizeScraperUpdateError(errors, id);
      }

      throw error;
    } finally {
      scraper.$loading = false;
    }
  };

  const destroy = async (id: number) => {
    const scraper = current.value.get(id)!;

    try {
      scraper.$loading = true;
      await api.delete(`/scrapers/${id}`);
      current.value.delete(id);
      Notify.create({
        message: 'Scraper is removed',
        type: 'positive',
      });
    } catch (error) {
      useApiError(error);

      throw error;
    } finally {
      scraper.$loading = false;
    }
  };

  const setActive = async (id: number) => {
    try {
      const response = await api.post(`scrapers/set-active/${id}`);
      Notify.create({
        message: response.data.message,
        type: 'positive',
      });
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const preview = async (id: number) => {
    try {
      const response = await api.get(`scrapers/preview/${id}`);
      previewPrices.value = response.data;
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const processCategories = async (id: number) => {
    try {
      const response = await api.post(`scrapers/process-categories/${id}`);
      categoryProcessor.value = response.data;
    } catch (error) {
      useApiError(error);

      throw error;
    }
  };

  const normalizeScraperUpdateError = (errors: object, id: number) => {
    const err: ScraperError = {
      scraper_name: '',
      scraper_url: '',
      scraper_config: {
        settings: 'puppeteer',
        category: {
          container_regex: '',
          regex: '',
          container_selector: '',
        } as ScraperConfigCategory,
        product: {
          container_regex: '',
          container_selector: '',
          regex: '',
          selector: '',
          ajax: { api_base_url: '', product_link_base_url: '' },
          pagination: {
            base_pagination_link: '',
            container_regex: '',
            page_query: '',
            pages_regex: '',
          },
          format: {
            currency: '',
            img_url: '',
            price: '',
            price_name: '',
            price_store_ident: '',
            price_url: '',
            rating: '',
            stock_quantity: '',
            stock_status: '',
          },
        } as ScraperConfigProduct,
      },
    };

    Object.entries(errors).forEach(([key, value]) => {
      const keys = key.split('.');

      let current: Record<string, unknown> = err;

      for (let i = 0; i < keys.length - 1; i++) {
        const key = keys[i]!;

        current = current[key] as Record<string, unknown>;
      }

      // Assign the value to the last key
      const lastKey = keys[keys.length - 1]!;
      const val = value as [];
      current[lastKey] = val.toString().replaceAll('.', ' ');
    });

    currentErrors.value.set(id, err);
  };

  return {
    filter,
    index,
    created,
    createdErrors,
    current,
    currentErrors,
    previewPrices,
    categoryProcessor,
    fetchIndex,
    create,
    store,
    update,
    destroy,
    setActive,
    preview,
    processCategories,
  };
});
