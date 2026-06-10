<!-- ============================================================
     resources/js/App.vue
     Point d'entrée Vue — restauration de session + splash screen
     ============================================================ -->

<template>
  <!-- Splash screen pendant le fetchMe() initial -->
  <Transition name="splash" mode="out-in">
    <div v-if="!initialized" class="splash-screen" aria-label="Chargement" role="status">
      <div class="splash-inner">
        <!-- Logo animé -->
        <div class="splash-logo pulse-glow">
          <svg width="52" height="52" viewBox="0 0 28 28" fill="none">
            <defs>
              <linearGradient id="splash-grad" x1="0" y1="0" x2="28" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#A78BFA"/>
                <stop offset="100%" stop-color="#06B6D4"/>
              </linearGradient>
            </defs>
            <path d="M14 2L25.2 8.5V21.5L14 28L2.8 21.5V8.5L14 2Z" fill="url(#splash-grad)" opacity="0.9"/>
            <path d="M14 7L20.6 10.75V18.25L14 22L7.4 18.25V10.75L14 7Z" fill="rgba(255,255,255,0.15)"/>
            <circle cx="14" cy="14" r="3.5" fill="white" opacity="0.95"/>
          </svg>
        </div>
        <span class="brand-logo splash-brand">BoostAfrik</span>
        <!-- Barre de progression indéterminée -->
        <div class="splash-bar" aria-hidden="true">
          <div class="splash-bar-inner"></div>
        </div>
      </div>
    </div>

    <!-- App principale -->
    <RouterView v-else />
  </Transition>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth.js'

const auth        = useAuthStore()
const initialized = computed(() => auth.initialized)

// Restaure la session depuis le token localStorage au premier chargement
onMounted(async () => {
  if (!auth.initialized) {
    await auth.fetchMe()
  }
})
</script>

<style scoped>
/* ── Splash screen ───────────────────────────────────────── */
.splash-screen {
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: #080612;
  display: flex;
  align-items: center;
  justify-content: center;
}

.splash-inner {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
}

.splash-logo {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 72px; height: 72px;
  border-radius: 20px;
  background: rgba(124, 58, 237, 0.12);
  border: 1px solid rgba(124, 58, 237, 0.25);
  margin-bottom: 0.25rem;
  animation: splash-pop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}

@keyframes splash-pop {
  from { opacity: 0; transform: scale(0.7); }
  to   { opacity: 1; transform: scale(1); }
}

.splash-brand {
  font-size: 1.5rem;
  animation: splash-fade 0.5s ease 0.2s both;
}

/* Barre de chargement */
.splash-bar {
  width: 120px;
  height: 3px;
  background: rgba(255, 255, 255, 0.07);
  border-radius: 99px;
  overflow: hidden;
  margin-top: 0.5rem;
  animation: splash-fade 0.5s ease 0.3s both;
}

.splash-bar-inner {
  height: 100%;
  width: 40%;
  border-radius: 99px;
  background: linear-gradient(90deg, #7C3AED, #06B6D4);
  animation: splash-progress 1.2s ease-in-out infinite;
}

@keyframes splash-progress {
  0%   { transform: translateX(-100%); }
  50%  { transform: translateX(150%); }
  100% { transform: translateX(250%); }
}

@keyframes splash-fade {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── Transition splash → app ─────────────────────────────── */
.splash-enter-active { transition: opacity 0.3s ease; }
.splash-leave-active { transition: opacity 0.4s ease, transform 0.4s ease; }
.splash-enter-from   { opacity: 0; }
.splash-leave-to     { opacity: 0; transform: scale(1.03); }
</style>