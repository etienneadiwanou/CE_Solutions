<!-- ============================================================
     resources/js/views/auth/RegisterView.vue
     Page d'inscription — Glassmorphism
     ============================================================ -->

<template>
  <section class="auth-page">
    <div class="container-auth">

      <!-- En-tête -->
      <div class="auth-header animate-fade-up">
        <RouterLink :to="{ name: 'home' }" class="auth-logo" aria-label="Retour accueil">
          <svg width="32" height="32" viewBox="0 0 28 28" fill="none">
            <defs>
              <linearGradient id="logo-g3" x1="0" y1="0" x2="28" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#A78BFA"/>
                <stop offset="100%" stop-color="#06B6D4"/>
              </linearGradient>
            </defs>
            <path d="M14 2L25.2 8.5V21.5L14 28L2.8 21.5V8.5L14 2Z" fill="url(#logo-g3)" opacity="0.9"/>
            <path d="M14 7L20.6 10.75V18.25L14 22L7.4 18.25V10.75L14 7Z" fill="rgba(255,255,255,0.15)"/>
            <circle cx="14" cy="14" r="3.5" fill="white" opacity="0.95"/>
          </svg>
          <span class="brand-logo">BoostAfrik</span>
        </RouterLink>
        <h1 class="auth-title">Créer un compte 🚀</h1>
        <p class="auth-subtitle">Rejoins +2 400 clients qui boostent leur présence</p>
      </div>

      <!-- Card formulaire -->
      <div class="glass auth-card animate-fade-up-delay-1">

        <!-- Erreur globale -->
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

        <form @submit.prevent="handleRegister" novalidate>

          <!-- Nom complet -->
          <div class="form-group">
            <label for="name" class="form-label">Nom complet</label>
            <div class="input-wrapper">
              <span class="input-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <circle cx="8" cy="5.5" r="2.5" stroke="currentColor" stroke-width="1.4"/>
                  <path d="M2.5 13.5C2.5 11.015 5.015 9 8 9C10.985 9 13.5 11.015 13.5 13.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
              </span>
              <input
                id="name"
                v-model.trim="form.name"
                type="text"
                class="glass-input with-icon"
                :class="{ error: errors.name }"
                placeholder="Kofi Mensah"
                autocomplete="name"
                :disabled="loading"
                @input="clearFieldError('name')"
              />
            </div>
            <Transition name="fade-alert">
              <p v-if="errors.name" class="form-error" role="alert">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                {{ errors.name[0] }}
              </p>
            </Transition>
          </div>

          <!-- Email -->
          <div class="form-group">
            <label for="reg-email" class="form-label">Adresse e-mail</label>
            <div class="input-wrapper">
              <span class="input-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <rect x="1.5" y="3.5" width="13" height="9" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                  <path d="M1.5 5.5L8 9.5L14.5 5.5" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                </svg>
              </span>
              <input
                id="reg-email"
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
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                {{ errors.email[0] }}
              </p>
            </Transition>
          </div>

          <!-- Mot de passe -->
          <div class="form-group">
            <label for="reg-password" class="form-label">Mot de passe</label>
            <div class="input-wrapper">
              <span class="input-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <rect x="3.5" y="7" width="9" height="7" rx="1.5" stroke="currentColor" stroke-width="1.4"/>
                  <path d="M5.5 7V5C5.5 3.619 6.619 2.5 8 2.5C9.381 2.5 10.5 3.619 10.5 5V7" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                  <circle cx="8" cy="10.5" r="1" fill="currentColor" opacity="0.6"/>
                </svg>
              </span>
              <input
                id="reg-password"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="glass-input with-icon with-icon-right"
                :class="{ error: errors.password }"
                placeholder="Min. 8 caractères"
                autocomplete="new-password"
                :disabled="loading"
                @input="clearFieldError('password')"
              />
              <button
                type="button"
                class="input-icon-right"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Masquer' : 'Afficher'"
                :disabled="loading"
              >
                <svg v-if="!showPassword" width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M1 8C1 8 3.5 3 8 3C12.5 3 15 8 15 8C15 8 12.5 13 8 13C3.5 13 1 8 1 8Z" stroke="currentColor" stroke-width="1.4"/>
                  <circle cx="8" cy="8" r="2" stroke="currentColor" stroke-width="1.4"/>
                </svg>
                <svg v-else width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M2 2L14 14M6.5 6.7C6.19 7.02 6 7.49 6 8C6 9.105 6.895 10 8 10C8.51 10 8.98 9.81 9.3 9.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                  <path d="M4.15 4.33C2.8 5.26 1.8 6.72 1 8C1 8 3.5 13 8 13C9.45 13 10.72 12.56 11.77 11.88M13.36 10.2C14.26 9.15 14.82 8.38 15 8C15 8 12.5 3 8 3C7.38 3 6.79 3.09 6.23 3.26" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
            <!-- Indicateur de force -->
            <div class="password-strength" aria-live="polite" aria-label="Force du mot de passe">
              <div class="strength-bars">
                <div
                  v-for="i in 4" :key="i"
                  class="strength-bar"
                  :class="strengthBarClass(i)"
                ></div>
              </div>
              <span class="strength-label" :class="strengthColor">{{ strengthLabel }}</span>
            </div>
            <Transition name="fade-alert">
              <p v-if="errors.password" class="form-error" role="alert">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                {{ errors.password[0] }}
              </p>
            </Transition>
          </div>

          <!-- Confirmation mot de passe -->
          <div class="form-group">
            <label for="reg-password-confirm" class="form-label">Confirmer le mot de passe</label>
            <div class="input-wrapper">
              <span class="input-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M4 8.5L6.5 11L12 5.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.4"/>
                </svg>
              </span>
              <input
                id="reg-password-confirm"
                v-model="form.password_confirmation"
                :type="showPassword ? 'text' : 'password'"
                class="glass-input with-icon"
                :class="{ error: errors.password_confirmation, success: passwordMatch && form.password_confirmation }"
                placeholder="Répète ton mot de passe"
                autocomplete="new-password"
                :disabled="loading"
                @input="clearFieldError('password_confirmation')"
              />
            </div>
            <Transition name="fade-alert">
              <p v-if="errors.password_confirmation" class="form-error" role="alert">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                {{ errors.password_confirmation[0] }}
              </p>
            </Transition>
          </div>

          <!-- Code de parrainage (optionnel) -->
          <div class="form-group">
            <div class="label-row">
              <label for="referral" class="form-label">Code de parrainage</label>
              <span class="optional-badge">Optionnel</span>
            </div>
            <div class="input-wrapper">
              <span class="input-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                  <path d="M8 1.5L9.7 5.9L14.5 6.1L10.9 9.2L12.1 14L8 11.4L3.9 14L5.1 9.2L1.5 6.1L6.3 5.9L8 1.5Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
                </svg>
              </span>
              <input
                id="referral"
                v-model.trim="form.referral_code"
                type="text"
                class="glass-input with-icon"
                :class="{ error: errors.referral_code }"
                placeholder="BOOST2024"
                autocomplete="off"
                :disabled="loading"
                @input="clearFieldError('referral_code')"
              />
            </div>
            <Transition name="fade-alert">
              <p v-if="errors.referral_code" class="form-error" role="alert">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                {{ errors.referral_code[0] }}
              </p>
            </Transition>
          </div>

          <!-- CGU -->
          <div class="form-group cgu-group">
            <label class="cgu-label">
              <div class="custom-checkbox" :class="{ checked: form.acceptCgu, error: errors.cgu }">
                <input
                  type="checkbox"
                  v-model="form.acceptCgu"
                  class="sr-only"
                  :disabled="loading"
                  @change="clearFieldError('cgu')"
                />
                <svg v-if="form.acceptCgu" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                  <path d="M2 5L4.2 7.5L8 3" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <span class="cgu-text">
                J'accepte les
                <a href="#" class="cgu-link">conditions d'utilisation</a>
                et la
                <a href="#" class="cgu-link">politique de confidentialité</a>
              </span>
            </label>
            <Transition name="fade-alert">
              <p v-if="errors.cgu" class="form-error" role="alert">
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                {{ errors.cgu[0] }}
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
              <span v-if="loading" key="loading" class="btn-inner">
                <svg class="spinner" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <circle cx="8" cy="8" r="6" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                  <path d="M8 2C8 2 12 2 14 8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
                Création du compte…
              </span>
              <span v-else key="idle" class="btn-inner">
                Créer mon compte
                <svg width="15" height="15" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                  <path d="M3 8H13M13 8L8.5 3.5M13 8L8.5 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </span>
            </Transition>
          </button>

        </form>

        <!-- Switch vers login -->
        <p class="auth-switch">
          Déjà un compte ?
          <RouterLink :to="{ name: 'login' }" class="auth-switch-link">
            Se connecter
          </RouterLink>
        </p>

      </div>

      <!-- Avantages -->
      <div class="perks animate-fade-up-delay-2">
        <div v-for="perk in perks" :key="perk.label" class="perk-item">
          <span class="perk-icon" aria-hidden="true">{{ perk.icon }}</span>
          <span class="perk-label">{{ perk.label }}</span>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth.js'

const router = useRouter()
const auth   = useAuthStore()

// ── State ────────────────────────────────────────────────────
const form = reactive({
  name:                  '',
  email:                 '',
  password:              '',
  password_confirmation: '',
  referral_code:         '',
  acceptCgu:             false,
})

const errors      = reactive({})
const globalError = ref(null)
const showPassword = ref(false)
const loading      = computed(() => auth.loading)

// ── Avantages ─────────────────────────────────────────────────
const perks = [
  { icon: '⚡', label: 'Livraison rapide' },
  { icon: '🔒', label: 'Paiement sécurisé' },
  { icon: '🌍', label: 'MTN, Wave, Orange' },
]

// ── Indicateur de force du mot de passe ──────────────────────
const passwordStrength = computed(() => {
  const p = form.password
  if (!p) return 0
  let score = 0
  if (p.length >= 8)  score++
  if (p.length >= 12) score++
  if (/[A-Z]/.test(p) && /[a-z]/.test(p)) score++
  if (/[0-9]/.test(p) && /[^A-Za-z0-9]/.test(p)) score++
  return score
})

const strengthLabel = computed(() => {
  const labels = ['', 'Faible', 'Moyen', 'Bien', 'Fort']
  return labels[passwordStrength.value] ?? ''
})

const strengthColor = computed(() => {
  const colors = ['', 'strength-weak', 'strength-medium', 'strength-good', 'strength-strong']
  return colors[passwordStrength.value] ?? ''
})

function strengthBarClass(index) {
  const s = passwordStrength.value
  if (index > s) return 'bar-empty'
  if (s === 1) return 'bar-weak'
  if (s === 2) return 'bar-medium'
  if (s === 3) return 'bar-good'
  return 'bar-strong'
}

// ── Correspondance des mots de passe ─────────────────────────
const passwordMatch = computed(() =>
  form.password && form.password === form.password_confirmation
)

// ── Helpers ───────────────────────────────────────────────────
function clearFieldError(field) {
  delete errors[field]
  globalError.value = null
}

// ── Validation front minimale ─────────────────────────────────
function validate() {
  let valid = true
  if (!form.name)  { errors.name  = ['Le nom est requis.']; valid = false }
  if (!form.email) { errors.email = ['L\'e-mail est requis.']; valid = false }
  if (!form.password || form.password.length < 8) {
    errors.password = ['Minimum 8 caractères.']
    valid = false
  }
  if (form.password !== form.password_confirmation) {
    errors.password_confirmation = ['Les mots de passe ne correspondent pas.']
    valid = false
  }
  if (!form.acceptCgu) {
    errors.cgu = ['Tu dois accepter les conditions.']
    valid = false
  }
  return valid
}

// ── Soumission ────────────────────────────────────────────────
async function handleRegister() {
  Object.keys(errors).forEach((k) => delete errors[k])
  globalError.value = null

  if (!validate()) return

  const payload = {
    name:                  form.name,
    email:                 form.email,
    password:              form.password,
    password_confirmation: form.password_confirmation,
  }
  if (form.referral_code) payload.referral_code = form.referral_code

  const result = await auth.register(payload)

  if (result.success) {
    router.push({ name: 'dashboard' })
    return
  }

  if (result.errors && Object.keys(result.errors).length) {
    Object.assign(errors, result.errors)
    return
  }

  globalError.value = result.message ?? 'Une erreur est survenue.'
}
</script>

<style scoped>
.auth-page {
  min-height: calc(100vh - 90px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 0 3rem;
}

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

.auth-title    { font-size: 1.65rem; font-weight: 700; color: #fff; margin-bottom: 0.4rem; letter-spacing: -0.03em; }
.auth-subtitle { font-size: 0.88rem; color: rgba(226,232,240,0.45); }

.auth-card { padding: 2rem; margin-bottom: 1.25rem; }

/* Alertes */
.alert { display:flex; align-items:center; gap:.6rem; padding:.7rem .9rem; border-radius:8px; font-size:.83rem; font-weight:500; margin-bottom:1.25rem; }
.alert-error { background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.25); color:#FCA5A5; }

/* Groupes */
.form-group { margin-bottom: 1.05rem; }

.label-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.4rem;
}

.optional-badge {
  font-size: 0.72rem;
  padding: 0.15rem 0.5rem;
  border-radius: 99px;
  background: rgba(124,58,237,0.15);
  color: rgba(167,139,250,0.7);
  border: 1px solid rgba(124,58,237,0.2);
}

/* Input icons */
.input-wrapper   { position: relative; }
.input-icon      { position:absolute; left:.85rem; top:50%; transform:translateY(-50%); color:rgba(226,232,240,0.3); pointer-events:none; display:flex; align-items:center; transition:color .2s; }
.input-icon-right { position:absolute; right:.75rem; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:rgba(226,232,240,0.35); display:flex; align-items:center; padding:4px; border-radius:4px; transition:color .2s,background .2s; }
.input-icon-right:hover { color:rgba(226,232,240,0.7); background:rgba(255,255,255,0.06); }
.input-wrapper:focus-within .input-icon { color:rgba(124,58,237,0.7); }

.glass-input.with-icon       { padding-left: 2.6rem; }
.glass-input.with-icon-right { padding-right: 2.6rem; }
.glass-input.success { border-color: rgba(16,185,129,0.5); box-shadow: 0 0 0 3px rgba(16,185,129,0.1); }

/* Force du mot de passe */
.password-strength {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  margin-top: 0.45rem;
}

.strength-bars {
  display: flex;
  gap: 4px;
  flex: 1;
}

.strength-bar {
  height: 3px;
  flex: 1;
  border-radius: 99px;
  transition: background 0.35s;
}

.bar-empty  { background: rgba(255,255,255,0.08); }
.bar-weak   { background: #EF4444; }
.bar-medium { background: #F59E0B; }
.bar-good   { background: #06B6D4; }
.bar-strong { background: #10B981; }

.strength-label { font-size: 0.72rem; font-weight: 600; min-width: 40px; }
.strength-weak   { color: #EF4444; }
.strength-medium { color: #F59E0B; }
.strength-good   { color: #06B6D4; }
.strength-strong { color: #10B981; }

/* CGU checkbox */
.cgu-group { margin-top: 0.25rem; }

.cgu-label {
  display: flex;
  align-items: flex-start;
  gap: 0.65rem;
  cursor: pointer;
}

.custom-checkbox {
  width: 18px; height: 18px;
  flex-shrink: 0;
  border-radius: 4px;
  border: 1px solid rgba(255,255,255,0.15);
  background: rgba(255,255,255,0.04);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 2px;
  transition: all 0.2s;
}

.custom-checkbox.checked {
  background: var(--ba-purple, #7C3AED);
  border-color: var(--ba-purple, #7C3AED);
  box-shadow: 0 0 0 3px rgba(124,58,237,0.2);
}

.custom-checkbox.error {
  border-color: rgba(239,68,68,0.6);
}

.sr-only {
  position: absolute;
  width: 1px; height: 1px;
  padding: 0; margin: -1px;
  overflow: hidden;
  clip: rect(0,0,0,0);
  white-space: nowrap;
  border: 0;
}

.cgu-text { font-size: 0.82rem; color: rgba(226,232,240,0.5); line-height: 1.5; }
.cgu-link { color: rgba(167,139,250,0.8); text-decoration: none; transition: color .2s; }
.cgu-link:hover { color: #A78BFA; text-decoration: underline; }

/* Submit */
.btn-inner { display:inline-flex; align-items:center; gap:.5rem; }
.spinner { animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.auth-switch { text-align:center; margin-top:1.25rem; font-size:.84rem; color:rgba(226,232,240,0.4); }
.auth-switch-link { color:#A78BFA; text-decoration:none; font-weight:500; transition:color .2s; }
.auth-switch-link:hover { color:#C4B5FD; text-decoration:underline; }

/* Perks */
.perks {
  display: flex;
  justify-content: center;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.perk-item {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  font-size: 0.78rem;
  color: rgba(226,232,240,0.35);
}

.perk-icon { font-size: 0.9rem; }

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
  .auth-card  { padding: 1.5rem 1.25rem; }
  .auth-title { font-size: 1.4rem; }
  .perks      { gap: 1rem; }
}
</style>