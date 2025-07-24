<template>
  <q-input
    v-model="model"
    dense
    square
    hide-bottom-space
    :label="label"
    :type="type"
    :error="error"
    :error-message="errorMessage"
    :lazy-rules="lazyRules"
    :rules="rules"
    :placeholder="placeholder"
    :borderless="borderless"
    :clearable="clearable"
    :debounce="debounce"
    :filled="filled"
    :outlined="outlined"
    :maxlength="maxLength"
    :readonly="readonly"
  >
    <template #prepend v-if="$slots.prepend">
      <slot name="prepend" />
    </template>

    <template #append v-if="$slots.append">
      <slot name="append" />
    </template>

    <template #before v-if="$slots.before">
      <slot name="before" />
    </template>

    <template #after v-if="$slots.after">
      <slot name="after" />
    </template>
  </q-input>
</template>

<script setup lang="ts">
import { type ValidationRule, type QInputType } from 'quasar';

defineProps<{
  label?: string;
  error?: boolean;
  errorMessage?: string | undefined;
  lazyRules?: boolean;
  rules?: ValidationRule[];
  type?: QInputType;
  filled?: boolean;
  outlined?: boolean;
  borderless?: boolean;
  placeholder?: string;
  clearable?: boolean;
  debounce?: string;
  maxLength?: number | string;
  readonly?: boolean;
}>();

const model = defineModel<number | string | null | undefined>();
</script>
