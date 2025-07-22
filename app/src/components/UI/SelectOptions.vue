<template>
  <q-select
    v-model="model"
    :options="selectOptions"
    :label="label"
    emit-value
    map-options
    hide-bottom-space
    dense
    use-input
    input-debounce="300"
    clearable
    :error="error"
    :error-message="errorMessage"
    @filter="filterFn"
  >
    <template v-slot:option="scope">
      <q-item v-bind="scope.itemProps" dense>
        <q-item-section>
          <q-item-label>{{ scope.opt.label }}</q-item-label>
          <q-item-label caption>{{ scope.opt.description }}</q-item-label>
        </q-item-section>
      </q-item>
    </template>
  </q-select>
</template>

<script setup lang="ts">
import { type QSelectOption } from 'quasar';
import { ref, type Ref } from 'vue';

const props = defineProps<{
  options: QSelectOption[];
  label?: string;
  error?: boolean;
  errorMessage?: string | undefined;
}>();

const selectOptions: Ref<QSelectOption[]> = ref(props.options);

const model = defineModel<number | string | null | undefined>();

const filterFn = (val: string, update: (arg0: { (): void; (): void }) => void) => {
  if (val === '') {
    update(() => {
      selectOptions.value = props.options;

      // here you have access to "ref" which
      // is the Vue reference of the QSelect
    });
    return;
  }

  update(() => {
    const needle = val.toLowerCase();
    selectOptions.value = props.options.filter((v) => v.label.toLowerCase().indexOf(needle) > -1);
  });
};
</script>
