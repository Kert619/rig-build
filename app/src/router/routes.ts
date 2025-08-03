import { authGuard, guestGuard } from 'src/middleware/guards';
import type { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    beforeEnter: [authGuard],
    children: [
      {
        path: 'stores',
        component: () => import('pages/Stores/IndexPage.vue'),
        children: [{ path: ':id', component: () => import('pages/Stores/StorePage.vue') }],
      },
      { path: 'countries', component: () => import('pages/CountriesPage.vue') },
      {
        path: 'scraper-logs',
        component: () => import('pages/ScraperLogs.vue'),
      },
    ],
  },
  {
    path: '/auth',
    component: () => import('layouts/AuthLayout.vue'),
    children: [
      { path: '', redirect: '/auth/login' },
      {
        path: 'login',
        component: () => import('pages/auth/AppLogin.vue'),
        beforeEnter: [guestGuard],
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
