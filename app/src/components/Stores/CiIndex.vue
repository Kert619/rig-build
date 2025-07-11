<template>
  <div class="row" style="min-height: inherit">
    <div class="col overflow-auto" style="max-width: 350px; min-height: inherit; max-height: 90vh">
      <div class="col-12 row justify-between items-center q-pa-sm">
        <span class="text-caption">Stores List</span>
        <q-btn label="Create" icon="add" unelevated square size="sm" />
      </div>

      <q-separator />

      <div class="col-12">
        <q-list separator>
          <q-item clickable v-ripple>
            <q-item-section> Item </q-item-section>
          </q-item>

          <q-item clickable v-ripple>
            <q-item-section> Item </q-item-section>
          </q-item>

          <q-item clickable v-ripple>
            <q-item-section> Item </q-item-section>
          </q-item>
        </q-list>
      </div>
    </div>

    <q-separator vertical />

    <div class="col">Store</div>
    <div class="col">Scraper</div>
  </div>
</template>

<script setup lang="ts"></script>
