// ============================================================
//  resources/js/stores/auth.js
//  Pinia Store — Authentification
//  Dépendances : authApi, tokenStorage  (services/api.js)
// ============================================================

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authApi, tokenStorage } from '../services/api.js'

export const useAuthStore = defineStore('auth', () => {

  // ── State ─────────────────────────────────────────────────
  const user        = ref(null)      // objet User Laravel
  const token       = ref(tokenStorage.get() ?? null)  // token en mémoire + localStorage
  const loading     = ref(false)     // requête en cours
  const error       = ref(null)      // ApiError | null
  const initialized = ref(false)     // fetchMe() a été appelé au moins une fois

  // ── Getters ───────────────────────────────────────────────
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const isAdmin         = computed(() => user.value?.role === 'admin')
  const fullName        = computed(() => user.value?.name ?? '')
  const balance         = computed(() => parseFloat(user.value?.balance ?? 0))

  // ── Helpers internes ──────────────────────────────────────
  function _setSession(userData, tokenValue) {
    user.value  = userData
    token.value = tokenValue
    tokenStorage.set(tokenValue)
  }

  function _clearSession() {
    user.value  = null
    token.value = null
    tokenStorage.remove()
  }

  function _clearError() {
    error.value = null
  }

  // ── Actions ───────────────────────────────────────────────

  /**
   * Inscription
   * @param {{ name, email, password, password_confirmation, referral_code? }} payload
   * @returns {{ success: boolean, errors?: object }}
   */
  async function register(payload) {
    _clearError()
    loading.value = true
    try {
      const data = await authApi.register(payload)
      _setSession(data.user, data.token)
      return { success: true }
    } catch (err) {
      error.value = err
      return {
        success: false,
        errors: err.errors ?? {},       // { email: ['…'], name: ['…'] }
        message: err.message,
      }
    } finally {
      loading.value = false
    }
  }

  /**
   * Connexion
   * @param {{ email, password }} payload
   * @returns {{ success: boolean, errors?: object }}
   */
  async function login(payload) {
    _clearError()
    loading.value = true
    try {
      const data = await authApi.login(payload)
      _setSession(data.user, data.token)
      return { success: true }
    } catch (err) {
      error.value = err
      return {
        success: false,
        errors: err.errors ?? {},
        message: err.message,
      }
    } finally {
      loading.value = false
    }
  }

  /**
   * Déconnexion — supprime le token serveur + nettoie le store
   */
  async function logout() {
    loading.value = true
    try {
      if (token.value) await authApi.logout()
    } catch (_) {
      // On déconnecte quand même côté front si le serveur répond mal
    } finally {
      _clearSession()
      loading.value = false
    }
  }

  /**
   * Récupère l'utilisateur courant via GET /api/auth/me
   * Appelé au boot de l'app (App.vue onMounted) pour restaurer la session
   */
  async function fetchMe() {
    if (!token.value) {
      initialized.value = true
      return
    }
    loading.value = true
    try {
      const data = await authApi.me()
      user.value = data
    } catch (err) {
      // Token invalide ou expiré → nettoyage silencieux
      // L'interceptor 401 s'est déjà chargé de la redirection
      _clearSession()
    } finally {
      loading.value    = false
      initialized.value = true
    }
  }

  /**
   * Met à jour les données user localement (après edit profil par ex.)
   * @param {object} partial — champs à fusionner
   */
  function patchUser(partial) {
    if (user.value) {
      user.value = { ...user.value, ...partial }
    }
  }

  /**
   * Met à jour la balance localement (après dépôt / commande)
   * @param {number} newBalance
   */
  function setBalance(newBalance) {
    if (user.value) {
      user.value.balance = newBalance
    }
  }

  // ── Expose ────────────────────────────────────────────────
  return {
    // state
    user,
    token,
    loading,
    error,
    initialized,
    // getters
    isAuthenticated,
    isAdmin,
    fullName,
    balance,
    // actions
    register,
    login,
    logout,
    fetchMe,
    patchUser,
    setBalance,
  }
})