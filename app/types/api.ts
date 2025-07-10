export type ErrorResponse<T = unknown> = {
  message: string;
  errors?: Partial<Record<keyof T, string[]>>;
};
