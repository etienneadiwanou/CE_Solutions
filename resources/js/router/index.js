// ============================================================
//  resources/js/router/index.js
//  Vue Router 4 — Routes + Navigation Guards
// ============================================================

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

// ── Lazy imports — chaque vue chargée à la demande ───────────
const HomeView        = () => import('../views/HomeView.vue')
const ServicesView    = () => import('../views/ServicesView.vue')

const LoginView       = () => import('../views/auth/LoginView.vue')
const RegisterView    = () => import('../views/auth/RegisterView.vue')

const DashboardHome   = () => import('../views/dashboard/DashboardHome.vue')
const NewOrderView    = () => import('../views/dashboard/NewOrderView.vue')
const OrdersView      = () => import('../views/dashboard/OrdersView.vue')
const DepositView     = () => import('../views/dashboard/DepositView.vue')
const ProfileView     = () => import('../views/dashboard/ProfileView.vue')
const SupportView     = () => import('../views/dashboard/SupportView.vue')

// ── Définition des routes ────────────────────────────────────
const routes = [

  // ── Pages publiques ────────────────────────────────────────
  {
    path: '/',
    component: () => import('../layouts/PublicLayout.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: HomeView,
        meta: { title: 'BoostAfrik — SMM Panel #1 en Afrique' },
      },
      {
        path: 'services',
        name: 'services',
        component: ServicesView,
        meta: { title: 'Nos Services — BoostAfrik' },
      },
    ],
  },

  // ── Pages Auth (guest only) ────────────────────────────────
  {
    path: '/auth',
    component: () => import('../layouts/PublicLayout.vue'),
    meta: { guestOnly: true },
    children: [
      {
        path: 'login',
        name: 'login',
        component: LoginView,
        meta: {
          title: 'Connexion — BoostAfrik',
          guestOnly: true,
        },
      },
      {
        path: 'register',
        name: 'register',
        component: RegisterView,
        meta: {
          title: 'Créer un compte — BoostAfrik',
          guestOnly: true,
        },
      },
    ],
  },

  // ── Dashboard (auth requis) ────────────────────────────────
  {
    path: '/dashboard',
    component: () => import('../layouts/DashboardLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'dashboard',
        component: DashboardHome,
        meta: {
          title: 'Dashboard — BoostAfrik',
          requiresAuth: true,
        },
      },
      {
        path: 'new-order',
        name: 'new-order',
        component: NewOrderView,
        meta: {
          title: 'Nouvelle Commande — BoostAfrik',
          requiresAuth: true,
        },
      },
      {
        path: 'orders',
        name: 'orders',
        component: OrdersView,
        meta: {
          title: 'Mes Commandes — BoostAfrik',
          requiresAuth: true,
        },
      },
      {
        path: 'deposit',
        name: 'deposit',
        component: DepositView,
        meta: {
          title: 'Déposer des fonds — BoostAfrik',
          requiresAuth: true,
        },
      },
      {
        path: 'profile',
        name: 'profile',
        component: ProfileView,
        meta: {
          title: 'Mon Profil — BoostAfrik',
          requiresAuth: true,
        },
      },
      {
        path: 'support',
        name: 'support',
        component: SupportView,
        meta: {
          title: 'Support — BoostAfrik',
          requiresAuth: true,
        },
      },
    ],
  },

  // ── 404 ───────────────────────────────────────────────────
 /* {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../views/NotFoundView.vue'),
    meta: { title: 'Page introuvable — BoostAfrik' },
  },*/
]

// ── Instance Router ──────────────────────────────────────────
const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    if (to.hash) return { el: to.hash, behavior: 'smooth' }
    return { top: 0, behavior: 'smooth' }
  },
})

// ── Navigation Guard global ──────────────────────────────────
router.beforeEach(async (to, from, next) => {
  const auth = useAuthStore()

  // 1️⃣ Premier chargement : restaurer la session depuis le token localStorage
  //    On attend que fetchMe() soit terminé avant de décider quoi faire
  if (!auth.initialized) {
    await auth.fetchMe()
  }

  const isAuth     = auth.isAuthenticated
  const needsAuth  = to.matched.some((r) => r.meta.requiresAuth)
  const guestOnly  = to.matched.some((r) => r.meta.guestOnly)

  // 2️⃣ Route protégée → pas connecté : redirige vers login
  if (needsAuth && !isAuth) {
    return next({
      name: 'login',
      query: { redirect: to.fullPath },   // pour revenir après connexion
    })
  }

  // 3️⃣ Route guest-only → déjà connecté : redirige vers dashboard
  if (guestOnly && isAuth) {
    return next({ name: 'dashboard' })
  }

  // 4️⃣ Mise à jour du <title> de la page
  if (to.meta?.title) {
    document.title = to.meta.title
  }

  next()
})

export default router