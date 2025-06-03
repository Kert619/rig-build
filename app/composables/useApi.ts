import type { FetchOptions } from "ofetch";

type HttpMethod =
  | "GET"
  | "POST"
  | "PUT"
  | "DELETE"
  | "PATCH"
  | "HEAD"
  | "OPTIONS"
  | "CONNECT"
  | "TRACE"
  | "get"
  | "post"
  | "put"
  | "delete"
  | "patch"
  | "head"
  | "options"
  | "connect"
  | "trace";

type FetchOption = FetchOptions & { method?: HttpMethod } & {
  useApiPrefix?: boolean;
};

export const useApi = (
  url: string,
  options: FetchOption = { useApiPrefix: true }
) => {
  const token = useCookie("XSRF-TOKEN");

  let headers = {
    accept: "application/json",
    ...options?.headers,
  };

  headers = {
    ...headers,
    ...(token.value ? { "X-XSRF-TOKEN": decodeURIComponent(token.value) } : {}),
  };

  if (import.meta.server) {
    headers = {
      ...headers,
      ...useRequestHeaders(["cookie"]),
      ...{ Referer: useRuntimeConfig().public.baseUrl },
    };
  }

  return $fetch(options.useApiPrefix ? `/api/${url}` : `/${url}`, {
    ...options,
    method: options.method ?? "GET",
    baseURL: useRuntimeConfig().public.apiBase,
    credentials: "include",
    headers,
  });
};
