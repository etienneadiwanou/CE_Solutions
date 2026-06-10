<!-- ============================================================
     resources/js/views/auth/LoginView.vue
     Page de connexion — Glassmorphism
     ============================================================ -->

<template>
  <section class="auth-page">
    <div class="container-auth">

      <!-- En-tête -->
      <div class="auth-header animate-fade-up">
        <RouterLink :to="{ name: 'home' }" class="nav-logo auth-logo" aria-label="Retour accueil">
          <svg width="32" height="32" viewBox="0 0 28 28" fill="none">
            <defs>
              <linearGradient id="logo-g2" x1="0" y1="0" x2="28" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#A78BFA"/>
                <stop offset="100%" stop-color="#06B6D4"/>
              </linearGradient>
            </defs>
            <path d="M14 2L25.2 8.5V21.5L14 28L2.8 21.5V8.5L14 2Z" fill="url(#logo-g2)" opacity="0.9"/>
            <path d="M14 7L20.6 10.75V18.25L14 22L7.4 18.25V10.75L14 7Z" fill="rgba(255,255,255,0.15)"/>
            <circle cx="14" cy="14" r="3.5" fill="white" opacity="0.95"/>
          </svg>
          <span class="brand-logo">BoostAfrik</span>
        </RouterLink>
        <h1 class="auth-title">Bon retour 👋</h1>
        <p class="auth-subtitle">Connecte-toi pour gérer tes commandes</p>
      </div>

      <!-- Alerte session expirée -->
      <Transition name="fade-alert">
        <div v-if="sessionExpired" class="alert alert-warning animate-fade-up" role="alert">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M8 1.5L14.5 13H1.5L8 1.5Z" stroke="#F59E0B" stroke-width="1.5" stroke-linejoin="round"/>
            <path d="M8 6V9" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="8" cy="11.5" r="0.75" fill="#F59E0B"/>
          </svg>
          Session expirée. Reconnecte-toi.
        </div>
      </Transition>

      <!-- Card formulaire -->
      <div class="glass auth-card animate-fade-up-delay-1">

        <!-- Erreur globale (403 compte désactivé, etc.) -->
        <Transition name="fade-alert">
          <div v-if="globalError" class="alert alert-error" role="alert">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="6.5" stroke="#EF4444" stroke-width="1.5"/>
              <path d="M8 5V8.5" stroke="#EF4444" stroke-width="1.5" stroke-linecap="round"/>
              <circle cx="8" cy="10.5" r="0.75" fill="#EF4444"/>
            </svg>
            {{ globalError }}
          </div>
        </Transition>

        <form @submit.prevent="handleLogin" novalidate>

          <!-- Email -->
          <div class="form-group">
            <label for="email" class="form-label">Adresse e-mail</label>
            <div class="input-wrapper">
              <span class="input-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <rect x="1.5" y="3.5" width="13" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                  <path d="M1.5 5.5L8 9.5L14.5 5.5" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                </svg>
              </span>
              <input
                id="email"
                v-model.trim="form.email"
                type="email"
                class="glass-input with-icon"
                :class="{ error: errors.email }"
                placeholder="toi@exemple.com"
                autocomplete="email"
                inputmode="email"
                :disabled="loading"
                @input="clearFieldError('email')"
              />
            </div>
            <Transition name="fade-alert">
              <p v-if="errors.email" class="form-error" role="alert">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                  <circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/>
                  <path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/>
                  <circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/>
                </svg>
                {{ errors.email[0] }}
              </p>
            </Transition>
          </div>

          <!-- Mot de passe -->
          <div class="form-group">
            <div class="label-row">
              <label for="password" class="form-label">Mot de passe</label>
              <a href="#" class="forgot-link">Mot de passe oublié ?</a>
            </div>
            <div class="input-wrapper">
              <span class="input-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <rect x="3.5" y="7" width="9" height="7" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                  <path d="M5.5 7V5C5.5 3.619 6.619 2.5 8 2.5C9.381 2.5 10.5 3.619 10.5 5V7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                  <circle cx="8" cy="10.5" r="1" fill="currentColor" opacity="0.6"/>
                </svg>
              </span>
              <input
                id="password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="glass-input with-icon with-icon-right"
                :class="{ error: errors.password }"
                placeholder="••••••••"
                autocomplete="current-password"
                :disabled="loading"
                @input="clearFieldError('password')"
              />
              <button
                type="button"
                class="input-icon-right"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'"
                :disabled="loading"
              >
                <!-- Œil ouvert -->
                <svg v-if="!showPassword" width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M1 8C1 8 3.5 3 8 3C12.5 3 15 8 15 8C15 8 12.5 13 8 13C3.5 13 1 8 1 8Z" stroke="currentColor" stroke-width="1.4"/>
                  <circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.4"/>
                </svg>
                <!-- Œil barré -->
                <svg v-else width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M2 2L14 14M6.5 6.7C6.19 7.02 6 7.49 6 8C6 9.105 6.895 10 8 10C8.51 10 8.98 9.81 9.3 9.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                  <path d="M4.15 4.33C2.8 5.26 1.8 6.72 1 8C1 8 3.5 13 8 13C9.45 13 10.72 12.56 11.77 11.88M13.36 10.2C14.26 9.15 14.82 8.38 15 8C15 8 12.5 3 8 3C7.38 3 6.79 3.09 6.23 3.26" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
            <Transition name="fade-alert">
              <p v-if="errors.password" class="form-error" role="alert">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                  <circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/>
                  <path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/>
                  <circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/>
                </svg>
                {{ errors.password[0] }}
              </p>
            </Transition>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            class="btn-primary"
            :disabled="loading"
            :aria-busy="loading"
          >
            <Transition name="btn-content" mode="out-in">
              <!-- Loading -->
              <span v-if="loading" key="loading" class="btn-inner">
                <svg class="spinner" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <circle cx="8" cy="8" r="6" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                  <path d="M8 2C8 2 12 2 14 8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Connexion en cours…
              </span>
              <!-- Idle -->
              <span v-else key="idle" class="btn-inner">
                Se connecter
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L8.5 3.5M13 8L8.5 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
            </Transition>
          </button>

        </form>

        <!-- Lien register -->
        <p class="auth-switch">
          Pas encore de compte ?
          <RouterLink :to="{ name: 'register' }" class="auth-switch-link">
            Créer un compte
          </RouterLink>
        </p>

      </div>

      <!-- Social proof -->
      <div class="social-proof animate-fade-up-delay-2">
        <div class="proof-avatars" aria-hidden="true">
          <span class="proof-avatar" style="background: linear-gradient(135deg,#7C3AED,#A78BFA)">A</span>
          <span class="proof-avatar" style="background: linear-gradient(135deg,#06B6D4,#67E8F9)">K</span>
          <span class="proof-avatar" style="background: linear-gradient(135deg,#F59E0B,#FDE68A)">M</span>
        </div>
        <p class="proof-text">
          <strong>+2 400 clients</strong> font confiance à BoostAfrik
        </p>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth.js'

const router = useRouter()
const route  = useRoute()
const auth   = useAuthStore()

// ── State ────────────────────────────────────────────────────
const form = reactive({ email: '', password: '' })
const errors       = reactive({})
const globalError  = ref(null)
const showPassword = ref(false)
const loading      = computed(() => auth.loading)

// Détection session expirée (query param injecté par le router guard 401)
const sessionExpired = computed(() => route.query.expired === '1')

// ── Nettoyage erreur par champ ────────────────────────────────
function clearFieldError(field) {
  delete errors[field]
  globalError.value = null
}

// ── Soumission ────────────────────────────────────────────────
async function handleLogin() {
  // Reset erreurs
  Object.keys(errors).forEach((k) => delete errors[k])
  globalError.value = null

  // Validation côté client minimale
  if (!form.email)    { errors.email    = ['L\'adresse e-mail est requise.']; return }
  if (!form.password) { errors.password = ['Le mot de passe est requis.']; return }

  const result = await auth.login({ email: form.email, password: form.password })

  if (result.success) {
    // Redirige vers la page demandée ou le dashboard
    const redirect = route.query.redirect ?? '/dashboard'
    router.push(redirect)
    return
  }

  // Erreurs de validation Laravel (422) → champ par champ
  if (result.errors && Object.keys(result.errors).length) {
    Object.assign(errors, result.errors)
    return
  }

  // Erreur globale (403 compte désactivé, réseau…)
  globalError.value = result.message ?? 'Une erreur est survenue.'
}

// ── Focus auto sur email au montage ──────────────────────────
onMounted(() => {
  document.getElementById('email')?.focus()
})
</script>

<style scoped>
.auth-page {
  min-height: calc(100vh - 90px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 0 3rem;
}

/* En-tête */
.auth-header {
  text-align: center;
  margin-bottom: 1.75rem;
}

.auth-logo {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  margin-bottom: 1.25rem;
  transition: opacity 0.2s;
}
.auth-logo:hover { opacity: 0.8; }

.auth-title {
  font-size: 1.65rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 0.4rem;
  letter-spacing: -0.03em;
}

.auth-subtitle {
  font-size: 0.88rem;
  color: rgba(226, 232, 240, 0.45);
}

/* Card */
.auth-card {
  padding: 2rem;
  margin-bottom: 1.25rem;
}

/* Alertes */
.alert {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.7rem 0.9rem;
  border-radius: 8px;
  font-size: 0.83rem;
  font-weight: 500;
  margin-bottom: 1.25rem;
}

.alert-warning {
  background: rgba(245, 158, 11, 0.1);
  border: 1px solid rgba(245, 158, 11, 0.25);
  color: #FCD34D;
}

.alert-error {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.25);
  color: #FCA5A5;
}

/* Groupes de champs */
.form-group {
  margin-bottom: 1.1rem;
}

.label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.4rem;
}

.forgot-link {
  font-size: 0.78rem;
  color: rgba(167, 139, 250, 0.7);
  text-decoration: none;
  transition: color 0.2s;
}
.forgot-link:hover { color: #A78BFA; }

/* Input avec icônes */
.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 0.85rem;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(226, 232, 240, 0.3);
  pointer-events: none;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}

.input-icon-right {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(226, 232, 240, 0.35);
  display: flex;
  align-items: center;
  padding: 4px;
  border-radius: 4px;
  transition: color 0.2s, background 0.2s;
}
.input-icon-right:hover { color: rgba(226, 232, 240, 0.7); background: rgba(255,255,255,0.06); }

.glass-input.with-icon       { padding-left: 2.6rem; }
.glass-input.with-icon-right { padding-right: 2.6rem; }

/* Focus : icône change aussi de couleur */
.input-wrapper:focus-within .input-icon {
  color: rgba(124, 58, 237, 0.7);
}

/* Bouton submit */
.btn-inner {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.spinner {
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Switch vers register */
.auth-switch {
  text-align: center;
  margin-top: 1.25rem;
  font-size: 0.84rem;
  color: rgba(226, 232, 240, 0.4);
}

.auth-switch-link {
  color: #A78BFA;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.auth-switch-link:hover { color: #C4B5FD; text-decoration: underline; }

/* Social proof */
.social-proof {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
}

.proof-avatars {
  display: flex;
}

.proof-avatar {
  width: 28px; height: 28px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
  font-weight: 700;
  color: white;
  border: 2px solid #080612;
  margin-left: -7px;
}
.proof-avatar:first-child { margin-left: 0; }

.proof-text {
  font-size: 0.78rem;
  color: rgba(226, 232, 240, 0.35);
}

.proof-text strong {
  color: rgba(226, 232, 240, 0.65);
  font-weight: 600;
}

/* Transitions */
.fade-alert-enter-active { transition: all 0.25s ease; }
.fade-alert-leave-active { transition: all 0.2s ease; }
.fade-alert-enter-from   { opacity: 0; transform: translateY(-6px); }
.fade-alert-leave-to     { opacity: 0; transform: translateY(-4px); }

.btn-content-enter-active,
.btn-content-leave-active { transition: all 0.18s ease; }
.btn-content-enter-from   { opacity: 0; transform: translateY(6px); }
.btn-content-leave-to     { opacity: 0; transform: translateY(-6px); }

/* Responsive */
@media (max-width: 480px) {
  .auth-card    { padding: 1.5rem 1.25rem; }
  .auth-title   { font-size: 1.4rem; }
}
</style>