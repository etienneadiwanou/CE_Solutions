<!-- ============================================================
     resources/js/layouts/PublicLayout.vue
     Layout public — fond animé glassmorphism
     Utilisé par : HomeView, ServicesView, LoginView, RegisterView
     ============================================================ -->

<template>
  <!-- Fond animé fixe -->
  <div class="bg-animated" aria-hidden="true">
    <div class="orb-3"></div>
    <div class="bg-grid"></div>
  </div>

  <!-- Navbar publique -->
  <header class="public-header">
    <nav class="public-nav" role="navigation" aria-label="Navigation principale">

      <!-- Logo -->
      <RouterLink :to="{ name: 'home' }" class="nav-logo" aria-label="BoostAfrik — Accueil">
        <span class="logo-icon" aria-hidden="true">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
            <defs>
              <linearGradient id="logo-grad" x1="0" y1="0" x2="28" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#A78BFA"/>
                <stop offset="100%" stop-color="#06B6D4"/>
              </linearGradient>
            </defs>
            <path d="M14 2L25.2 8.5V21.5L14 28L2.8 21.5V8.5L14 2Z"
                  fill="url(#logo-grad)" opacity="0.9"/>
            <path d="M14 7L20.6 10.75V18.25L14 22L7.4 18.25V10.75L14 7Z"
                  fill="rgba(255,255,255,0.15)"/>
            <circle cx="14" cy="14" r="3.5" fill="white" opacity="0.95"/>
          </svg>
        </span>
        <span class="brand-logo">BoostAfrik</span>
      </RouterLink>

      <!-- Liens centraux (masqués sur mobile) -->
      <ul class="nav-links" role="list">
        <li>
          <RouterLink :to="{ name: 'services' }" class="nav-link">
            Services
          </RouterLink>
        </li>
        <li>
          <a href="#pricing" class="nav-link">Tarifs</a>
        </li>
        <li>
          <a href="#faq" class="nav-link">FAQ</a>
        </li>
      </ul>

      <!-- Actions droite -->
      <div class="nav-actions">
        <RouterLink
          v-if="!isAuthenticated"
          :to="{ name: 'login' }"
          class="btn-ghost nav-btn-ghost"
        >
          Connexion
        </RouterLink>
        <RouterLink
          v-if="!isAuthenticated"
          :to="{ name: 'register' }"
          class="btn-primary nav-btn-primary"
        >
          Commencer
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M3 8H13M13 8L8.5 3.5M13 8L8.5 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </RouterLink>

        <!-- Si connecté : lien dashboard -->
        <RouterLink
          v-if="isAuthenticated"
          :to="{ name: 'dashboard' }"
          class="btn-primary nav-btn-primary"
        >
          Dashboard
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <rect x="2" y="2" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.6"/>
            <rect x="9" y="2" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.6"/>
            <rect x="2" y="9" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.6"/>
            <rect x="9" y="9" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.6"/>
          </svg>
        </RouterLink>

        <!-- Burger mobile -->
        <button
          class="nav-burger"
          :class="{ open: mobileOpen }"
          @click="mobileOpen = !mobileOpen"
          :aria-expanded="mobileOpen"
          aria-label="Menu"
          aria-controls="mobile-menu"
        >
          <span></span>
          <span></span>
          <span></span>
        </button>
      </div>
    </nav>

    <!-- Menu mobile -->
    <Transition name="slide-down">
      <div
        v-if="mobileOpen"
        id="mobile-menu"
        class="mobile-menu"
        role="dialog"
        aria-modal="true"
        aria-label="Menu mobile"
      >
        <ul role="list">
          <li>
            <RouterLink :to="{ name: 'services' }" class="mobile-link" @click="mobileOpen = false">
              Services
            </RouterLink>
          </li>
          <li>
            <a href="#pricing" class="mobile-link" @click="mobileOpen = false">Tarifs</a>
          </li>
          <li>
            <a href="#faq" class="mobile-link" @click="mobileOpen = false">FAQ</a>
          </li>
        </ul>
        <div class="mobile-actions">
          <RouterLink
            v-if="!isAuthenticated"
            :to="{ name: 'login' }"
            class="btn-ghost mobile-btn"
            @click="mobileOpen = false"
          >
            Connexion
          </RouterLink>
          <RouterLink
            v-if="!isAuthenticated"
            :to="{ name: 'register' }"
            class="btn-primary mobile-btn"
            @click="mobileOpen = false"
          >
            Créer un compte
          </RouterLink>
          <RouterLink
            v-if="isAuthenticated"
            :to="{ name: 'dashboard' }"
            class="btn-primary mobile-btn"
            @click="mobileOpen = false"
          >
            Dashboard
          </RouterLink>
        </div>
      </div>
    </Transition>
  </header>

  <!-- Contenu de la page courante -->
  <main class="public-main" role="main">
    <RouterView v-slot="{ Component, route }">
      <Transition :name="route.meta.transition ?? 'fade'" mode="out-in">
        <component :is="Component" :key="route.fullPath" />
      </Transition>
    </RouterView>
  </main>

  <!-- Footer minimal -->
  <footer class="public-footer" role="contentinfo">
    <div class="footer-inner">
      <span class="brand-logo footer-brand">BoostAfrik</span>
      <p class="footer-copy">
        © {{ year }} BoostAfrik. Tous droits réservés.
        <span class="footer-dot" aria-hidden="true">·</span>
        SMM Panel #1 en Afrique
      </p>
      <div class="footer-links">
        <a href="#" class="footer-link">CGU</a>
        <a href="#" class="footer-link">Confidentialité</a>
        <a href="#" class="footer-link">Contact</a>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref, computed } from 'vue'
import { RouterLink, RouterView } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const auth          = useAuthStore()
const isAuthenticated = computed(() => auth.isAuthenticated)
const mobileOpen    = ref(false)
const year          = new Date().getFullYear()
</script>

<style scoped>
/* ── Header ─────────────────────────────────────────────── */
.public-header {
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 100;
  padding: 0 1.5rem;
}

.public-nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 1200px;
  margin: 0.75rem auto 0;
  padding: 0.65rem 1.25rem;
  background: rgba(8, 6, 18, 0.6);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.35);
  transition: background 0.3s;
}

/* Logo */
.nav-logo {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  text-decoration: none;
  flex-shrink: 0;
}

.logo-icon {
  display: flex;
  align-items: center;
  transition: transform 0.3s var(--ease-spring, cubic-bezier(0.34,1.56,0.64,1));
}

.nav-logo:hover .logo-icon {
  transform: rotate(-8deg) scale(1.1);
}

/* Nav links */
.nav-links {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  list-style: none;
  padding: 0;
  margin: 0;
}

.nav-link {
  display: inline-block;
  padding: 0.45rem 0.85rem;
  font-size: 0.88rem;
  font-weight: 500;
  color: rgba(226, 232, 240, 0.6);
  text-decoration: none;
  border-radius: 8px;
  transition: color 0.2s, background 0.2s;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #E2E8F0;
  background: rgba(255, 255, 255, 0.06);
}

/* Actions droite */
.nav-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.nav-btn-ghost {
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
  text-decoration: none;
}

.nav-btn-primary {
  padding: 0.5rem 1.1rem;
  font-size: 0.85rem;
  width: auto;
  text-decoration: none;
}

/* Burger */
.nav-burger {
  display: none;
  flex-direction: column;
  gap: 5px;
  padding: 6px;
  background: none;
  border: none;
  cursor: pointer;
  border-radius: 6px;
  transition: background 0.2s;
}

.nav-burger:hover { background: rgba(255,255,255,0.06); }

.nav-burger span {
  display: block;
  width: 22px;
  height: 2px;
  background: rgba(226, 232, 240, 0.7);
  border-radius: 99px;
  transition: all 0.3s;
  transform-origin: center;
}

.nav-burger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.nav-burger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
.nav-burger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

/* ── Mobile menu ─────────────────────────────────────────── */
.mobile-menu {
  max-width: 1200px;
  margin: 0.4rem auto 0;
  padding: 1rem 1.25rem;
  background: rgba(8, 6, 18, 0.92);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
}

.mobile-menu ul {
  list-style: none;
  padding: 0; margin: 0 0 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.mobile-link {
  display: block;
  padding: 0.7rem 0.85rem;
  font-size: 0.92rem;
  font-weight: 500;
  color: rgba(226, 232, 240, 0.7);
  text-decoration: none;
  border-radius: 8px;
  transition: color 0.2s, background 0.2s;
}

.mobile-link:hover,
.mobile-link.router-link-active {
  color: #E2E8F0;
  background: rgba(255,255,255,0.06);
}

.mobile-actions {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  padding-top: 0.75rem;
  border-top: 1px solid rgba(255, 255, 255, 0.07);
}

.mobile-btn {
  width: 100%;
  text-decoration: none;
  justify-content: center;
}

/* ── Main ────────────────────────────────────────────────── */
.public-main {
  position: relative;
  z-index: 1;
  min-height: 100vh;
  padding-top: 90px; /* compense le header fixed */
}

/* ── Footer ──────────────────────────────────────────────── */
.public-footer {
  position: relative;
  z-index: 1;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  padding: 2rem 1.5rem;
}

.footer-inner {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
  text-align: center;
}

.footer-brand {
  font-size: 1.1rem;
}

.footer-copy {
  font-size: 0.8rem;
  color: rgba(226, 232, 240, 0.3);
}

.footer-dot {
  margin: 0 0.4rem;
  opacity: 0.4;
}

.footer-links {
  display: flex;
  gap: 1.2rem;
}

.footer-link {
  font-size: 0.78rem;
  color: rgba(226, 232, 240, 0.3);
  text-decoration: none;
  transition: color 0.2s;
}

.footer-link:hover { color: rgba(167, 139, 250, 0.8); }

/* ── Transitions de page ─────────────────────────────────── */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

/* Menu slide-down */
.slide-down-enter-active { transition: all 0.28s cubic-bezier(0.34,1.56,0.64,1); }
.slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from   { opacity: 0; transform: translateY(-10px) scaleY(0.95); }
.slide-down-leave-to     { opacity: 0; transform: translateY(-6px); }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 768px) {
  .nav-links { display: none; }

  .nav-btn-ghost,
  .nav-btn-primary { display: none; }

  .nav-burger { display: flex; }

  .public-nav { padding: 0.6rem 1rem; }
}

@media (max-width: 480px) {
  .public-header { padding: 0 0.75rem; }
  .public-main   { padding-top: 82px; }
}
</style>