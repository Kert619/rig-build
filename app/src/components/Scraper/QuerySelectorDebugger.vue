<template>
  <q-dialog v-model="model" full-width full-height square persistent>
    <q-card square flat bordered>
      <q-card-section
        class="q-pa-none absolute-top bg-grey-3"
        :class="{ 'bg-grey-10': $q.dark.isActive }"
        style="z-index: 1; height: 48px"
      >
        <div class="full-height q-px-md row justify-between items-center">
          <span class="text-caption">Query Selector Debugger</span>
          <q-btn dense size="sm" flat icon="close" color="negative" @click="model = false" />
        </div>
      </q-card-section>

      <q-card-section class="overflow-auto" style="height: calc(100% - 48px); margin-top: 48px">
        <div class="q-mb-md">
          <TextInput v-model="selector" placeholder="Selector" filled>
            <template #prepend>
              <q-icon name="code" />
            </template>

            <template #before>
              <q-btn
                dense
                unelevated
                square
                label="Execute"
                icon="directions_run"
                color="positive"
                @click="handleExecute"
              />
            </template>
          </TextInput>
        </div>

        <div class="row" style="height: calc(100% - 56px)">
          <div class="col-6 full-height">
            <q-input
              v-model="htmlSource"
              borderless
              type="textarea"
              class="full-height"
              input-style="resize:none;font-family:monospace;"
              placeholder="Enter your html text"
              square
              spellcheck="false"
            />
          </div>

          <div
            class="col-6 full-height overflow-auto bg-grey-2"
            :class="{ 'bg-grey-9': $q.dark.isActive }"
          >
            <pre style="font-family: monospace">{{ result }}</pre>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import TextInput from 'components/UI/TextInput.vue';
import prettier from 'prettier/standalone';
import parserHtml from 'prettier/plugins/html';

const model = defineModel<boolean>({ default: false });

const selector = ref('');
const htmlSource = ref('');
const result = ref('');

const handleExecute = async () => {
  if (!htmlSource.value.trim() || !selector.value.trim()) return;

  try {
    // 1. Parse the HTML string into a document
    const parser = new DOMParser();
    const doc = parser.parseFromString(htmlSource.value, 'text/html');

    // 2. Get all matched elements
    const matchedElements = Array.from(doc.querySelectorAll(selector.value));

    let output = '';

    matchedElements.forEach((el) => {
      output += el.outerHTML;
      output += '\n\n';
    });

    const formatted = await prettier.format(output, {
      parser: 'html',
      plugins: [parserHtml],
    });

    result.value = formatted;
  } catch {
    result.value = 'Invalid selector';
  }
};
</script>

<style scoped lang="scss">
:deep(.q-field__control, .q-field__native) {
  height: 100%;
}
</style>
