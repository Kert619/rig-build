import { type ErrorResponse } from 'app/types/api';
import { type AxiosError } from 'axios';
import { Notify } from 'quasar';

export const useApiError = <T>(error: unknown, fallback = 'Something went wrong') => {
  const err = error as AxiosError<ErrorResponse<T>>;
  const message = err.response?.data.message ?? fallback;

  if (err.response?.status == 422) {
    return err.response?.data.errors ?? {};
  } else {
    Notify.create({
      message,
      type: 'negative',
    });
    return null;
  }
};
