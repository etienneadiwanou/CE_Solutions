// ============================================================
//  resources/js/services/api.js
//  Client HTTP — Laravel Sanctum (Personal Access Token)
//  Base URL : /api  (même domaine, Vite proxy ou Laravel direct)
// ============================================================

import axios from 'axios'

// ── Constantes ──────────────────────────────────────────────
const TOKEN_KEY = 'ba_token'   // clé localStorage

// ── Instance Axios ───────────────────────────────────────────
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  withCredentials: false,   // pas de cookies (token-based)
  timeout: 15_000,
})

// ── Helpers token ────────────────────────────────────────────
export const tokenStorage = {
  get()        { return localStorage.getItem(TOKEN_KEY) },
  set(token)   { localStorage.setItem(TOKEN_KEY, token) },
  remove()     { localStorage.removeItem(TOKEN_KEY) },
}

// ── Request interceptor — injecte le Bearer token ────────────
api.interceptors.request.use(
  (config) => {
    const token = tokenStorage.get()
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error),
)

// ── Response interceptor — gestion globale des erreurs ───────
api.interceptors.response.use(
  // ✅ Succès : on retourne directement data
  (response) => response,

  // ❌ Erreur
  async (error) => {
    const { response } = error

    // Pas de réponse serveur (réseau, timeout…)
    if (!response) {
      return Promise.reject(
        createApiError('NETWORK_ERROR', 'Impossible de joindre le serveur. Vérifie ta connexion.')
      )
    }

    const { status, data } = response

    switch (status) {
      // 401 — token invalide ou expiré → déconnexion propre
      case 401: {
        tokenStorage.remove()
        // Import dynamique du router pour éviter la dépendance circulaire
        const { default: router } = await import('../router/index.js')
        const currentRoute = router.currentRoute.value
        if (currentRoute.name !== 'login') {
          router.push({ name: 'login', query: { expired: '1' } })
        }
        return Promise.reject(createApiError('UNAUTHORIZED', 'Session expirée. Reconnecte-toi.', status))
      }

      // 403 — compte désactivé ou accès refusé
      case 403:
        return Promise.reject(
          createApiError('FORBIDDEN', data?.message ?? 'Accès refusé.', status)
        )

      // 422 — erreurs de validation Laravel
      case 422:
        return Promise.reject(
          createApiError('VALIDATION_ERROR', data?.message ?? 'Données invalides.', status, data?.errors ?? {})
        )

      // 429 — rate limiting
      case 429:
        return Promise.reject(
          createApiError('RATE_LIMIT', 'Trop de tentatives. Attends quelques secondes.', status)
        )

      // 500+ — erreur serveur
      default:
        if (status >= 500) {
          return Promise.reject(
            createApiError('SERVER_ERROR', 'Erreur serveur. Réessaie plus tard.', status)
          )
        }
        return Promise.reject(
          createApiError('HTTP_ERROR', data?.message ?? 'Une erreur est survenue.', status)
        )
    }
  },
)

// ── Fabrique d'erreur normalisée ─────────────────────────────
function createApiError(code, message, status = null, errors = {}) {
  const err = new Error(message)
  err.code    = code
  err.status  = status
  err.errors  = errors   // champs Laravel : { email: ['…'], password: ['…'] }
  return err
}

// ============================================================
//  Endpoints Auth
// ============================================================
export const authApi = {
  /**
   * POST /api/auth/register
   * @param {{ name, email, password, password_confirmation, referral_code? }} payload
   * @returns {{ user, token }}
   */
  register(payload) {
    return api.post('/auth/register', payload).then((r) => r.data)
  },

  /**
   * POST /api/auth/login
   * @param {{ email, password }} payload
   * @returns {{ user, token }}
   */
  login(payload) {
    return api.post('/auth/login', payload).then((r) => r.data)
  },

  /**
   * POST /api/auth/logout
   * Supprime le token côté serveur
   */
  logout() {
    return api.post('/auth/logout').then((r) => r.data)
  },

  /**
   * GET /api/auth/me
   * @returns {User}
   */
  me() {
    return api.get('/auth/me').then((r) => r.data)
  },
}

// ============================================================
//  Endpoints Services
// ============================================================
export const servicesApi = {
  /** GET /api/services — liste paginée */
  list(params = {}) {
    return api.get('/services', { params }).then((r) => r.data)
  },

  /** GET /api/services/:id */
  show(id) {
    return api.get(`/services/${id}`).then((r) => r.data)
  },

  /** GET /api/categories */
  categories() {
    return api.get('/categories').then((r) => r.data)
  },
}

// ============================================================
//  Endpoints Orders
// ============================================================
export const ordersApi = {
  /** POST /api/orders */
  create(payload) {
    return api.post('/orders', payload).then((r) => r.data)
  },

  /** GET /api/orders — liste de l'utilisateur connecté */
  list(params = {}) {
    return api.get('/orders', { params }).then((r) => r.data)
  },

  /** GET /api/orders/:id */
  show(id) {
    return api.get(`/orders/${id}`).then((r) => r.data)
  },
}

// ============================================================
//  Endpoints Paiements / Dépôt
// ============================================================
export const paymentsApi = {
  /** POST /api/payments — initier un dépôt */
  initiate(payload) {
    return api.post('/payments', payload).then((r) => r.data)
  },

  /** GET /api/payments — historique */
  history(params = {}) {
    return api.get('/payments', { params }).then((r) => r.data)
  },
}

// ============================================================
//  Endpoints Tickets Support
// ============================================================
export const ticketsApi = {
  /** GET /api/tickets */
  list() {
    return api.get('/tickets').then((r) => r.data)
  },

  /** POST /api/tickets */
  create(payload) {
    return api.post('/tickets', payload).then((r) => r.data)
  },

  /** GET /api/tickets/:id */
  show(id) {
    return api.get(`/tickets/${id}`).then((r) => r.data)
  },

  /** POST /api/tickets/:id/messages */
  reply(id, payload) {
    return api.post(`/tickets/${id}/messages`, payload).then((r) => r.data)
  },
}

// ============================================================
//  Endpoints Profil utilisateur
// ============================================================
export const profileApi = {
  /** PUT /api/profile */
  update(payload) {
    return api.put('/profile', payload).then((r) => r.data)
  },

  /** PUT /api/profile/password */
  changePassword(payload) {
    return api.put('/profile/password', payload).then((r) => r.data)
  },

  /** GET /api/transactions — historique de la balance */
  transactions(params = {}) {
    return api.get('/transactions', { params }).then((r) => r.data)
  },
}

// ── Export par défaut : l'instance brute (pour cas spéciaux) ─
export default api