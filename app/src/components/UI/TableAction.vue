<template>
  <q-btn dense flat icon="more_vert">
    <q-menu class="no-box-shadow" square>
      <q-list dense bordered>
        <slot />
      </q-list>
    </q-menu>
  </q-btn>
</template>
