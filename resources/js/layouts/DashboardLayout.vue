<!-- ============================================================
     resources/js/layouts/DashboardLayout.vue
     Layout dashboard — sidebar + topbar glassmorphism
     ============================================================ -->

<template>
  <div class="dashboard-wrap">

    <!-- Fond animé -->
    <div class="bg-animated" aria-hidden="true">
      <div class="orb-3"></div>
      <div class="bg-grid"></div>
    </div>

    <!-- Overlay mobile (ferme la sidebar) -->
    <Transition name="overlay">
      <div
        v-if="sidebarOpen"
        class="sidebar-overlay"
        @click="sidebarOpen = false"
        aria-hidden="true"
      ></div>
    </Transition>

    <!-- ── Sidebar ────────────────────────────────────────── -->
    <aside
      class="sidebar"
      :class="{ open: sidebarOpen, collapsed: sidebarCollapsed }"
      role="navigation"
      aria-label="Navigation dashboard"
    >
      <!-- Logo -->
      <div class="sidebar-logo">
        <RouterLink :to="{ name: 'home' }" class="logo-link" @click="sidebarOpen = false">
          <svg width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
            <defs>
              <linearGradient id="sb-grad" x1="0" y1="0" x2="28" y2="28" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#A78BFA"/>
                <stop offset="100%" stop-color="#06B6D4"/>
              </linearGradient>
            </defs>
            <path d="M14 2L25.2 8.5V21.5L14 28L2.8 21.5V8.5L14 2Z" fill="url(#sb-grad)" opacity="0.9"/>
            <path d="M14 7L20.6 10.75V18.25L14 22L7.4 18.25V10.75L14 7Z" fill="rgba(255,255,255,0.15)"/>
            <circle cx="14" cy="14" r="3.5" fill="white" opacity="0.95"/>
          </svg>
          <span class="brand-logo sidebar-brand" v-show="!sidebarCollapsed">BoostAfrik</span>
        </RouterLink>

        <!-- Bouton collapse (desktop) -->
        <button
          class="collapse-btn"
          @click="sidebarCollapsed = !sidebarCollapsed"
          :aria-label="sidebarCollapsed ? 'Déplier la sidebar' : 'Réduire la sidebar'"
          :title="sidebarCollapsed ? 'Déplier' : 'Réduire'"
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
            :style="{ transform: sidebarCollapsed ? 'rotate(180deg)' : 'none', transition: 'transform .3s' }">
            <path d="M10 3L5 8L10 13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>

      <!-- Balance rapide -->
      <div class="sidebar-balance" v-show="!sidebarCollapsed">
        <span class="balance-label">Solde disponible</span>
        <span class="balance-amount">{{ formatBalance }} <span class="balance-currency">XOF</span></span>
        <RouterLink :to="{ name: 'deposit' }" class="balance-cta" @click="sidebarOpen = false">
          + Déposer
        </RouterLink>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <ul role="list">
          <li v-for="item in navItems" :key="item.name">
            <RouterLink
              :to="{ name: item.name }"
              class="nav-item"
              :class="{ active: currentRoute === item.name }"
              @click="sidebarOpen = false"
              :title="sidebarCollapsed ? item.label : ''"
            >
              <span class="nav-icon" aria-hidden="true" v-html="item.icon"></span>
              <span class="nav-label" v-show="!sidebarCollapsed">{{ item.label }}</span>
              <span v-if="item.badge && !sidebarCollapsed" class="nav-badge">{{ item.badge }}</span>
            </RouterLink>
          </li>
        </ul>
      </nav>

      <!-- Bas sidebar : profil + déconnexion -->
      <div class="sidebar-footer">
        <RouterLink
          :to="{ name: 'profile' }"
          class="sidebar-profile"
          :class="{ collapsed: sidebarCollapsed }"
          @click="sidebarOpen = false"
          :title="sidebarCollapsed ? fullName : ''"
        >
          <div class="profile-avatar" aria-hidden="true">
            {{ avatarInitial }}
          </div>
          <div class="profile-info" v-show="!sidebarCollapsed">
            <span class="profile-name">{{ fullName }}</span>
            <span class="profile-role">{{ isAdmin ? 'Administrateur' : 'Client' }}</span>
          </div>
        </RouterLink>

        <button
          class="logout-btn"
          @click="handleLogout"
          :disabled="logoutLoading"
          :title="sidebarCollapsed ? 'Déconnexion' : ''"
          aria-label="Se déconnecter"
        >
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
            <path d="M6 2H3C2.448 2 2 2.448 2 3V13C2 13.552 2.448 14 3 14H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M10.5 5L14 8L10.5 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 8H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <span v-show="!sidebarCollapsed">Déconnexion</span>
        </button>
      </div>
    </aside>

    <!-- ── Contenu principal ──────────────────────────────── -->
    <div class="dashboard-main" :class="{ expanded: sidebarCollapsed }">

      <!-- Topbar -->
      <header class="topbar" role="banner">

        <!-- Burger mobile -->
        <button
          class="topbar-burger"
          @click="sidebarOpen = !sidebarOpen"
          :aria-expanded="sidebarOpen"
          aria-label="Menu"
          aria-controls="sidebar"
        >
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M3 5H17M3 10H17M3 15H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
        </button>

        <!-- Titre de la page courante -->
        <div class="topbar-title">
          <h1 class="page-title">{{ currentPageTitle }}</h1>
        </div>

        <!-- Actions topbar droite -->
        <div class="topbar-actions">

          <!-- Balance (mobile) -->
          <RouterLink :to="{ name: 'deposit' }" class="topbar-balance">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
              <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.4"/>
              <path d="M8 5V8M8 8H10.5M8 8H5.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
            </svg>
            <span>{{ formatBalance }} XOF</span>
          </RouterLink>

          <!-- Notifications (placeholder) -->
          <button class="topbar-icon-btn" aria-label="Notifications">
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
              <path d="M9 2C6.239 2 4 4.239 4 7V11L2.5 13H15.5L14 11V7C14 4.239 11.761 2 9 2Z" stroke="currentColor" stroke-width="1.4"/>
              <path d="M7 13C7 14.105 7.895 15 9 15C10.105 15 11 14.105 11 13" stroke="currentColor" stroke-width="1.4"/>
            </svg>
          </button>

          <!-- Avatar -->
          <RouterLink :to="{ name: 'profile' }" class="topbar-avatar" aria-label="Mon profil">
            {{ avatarInitial }}
          </RouterLink>
        </div>
      </header>

      <!-- Vue courante -->
      <main class="dashboard-content" role="main">
        <RouterView v-slot="{ Component, route }">
          <Transition name="page-fade" mode="out-in">
            <component :is="Component" :key="route.fullPath" />
          </Transition>
        </RouterView>
      </main>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth.js'

const route  = useRoute()
const router = useRouter()
const auth   = useAuthStore()

// ── Sidebar state ─────────────────────────────────────────
const sidebarOpen      = ref(false)
const sidebarCollapsed = ref(false)
const logoutLoading    = ref(false)

// ── Auth data ─────────────────────────────────────────────
const fullName    = computed(() => auth.fullName || 'Utilisateur')
const isAdmin     = computed(() => auth.isAdmin)
const balance     = computed(() => auth.balance)
const formatBalance = computed(() =>
  new Intl.NumberFormat('fr-FR').format(balance.value)
)
const avatarInitial = computed(() =>
  fullName.value.charAt(0).toUpperCase()
)

// ── Route courante ────────────────────────────────────────
const currentRoute = computed(() => route.name)

const pageTitles = {
  'dashboard':  'Tableau de bord',
  'new-order':  'Nouvelle commande',
  'orders':     'Mes commandes',
  'deposit':    'Déposer des fonds',
  'profile':    'Mon profil',
  'support':    'Support',
}

const currentPageTitle = computed(() =>
  pageTitles[route.name] ?? 'Dashboard'
)

// ── Navigation items ──────────────────────────────────────
const navItems = [
  {
    name: 'dashboard',
    label: 'Tableau de bord',
    icon: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <rect x="2" y="2" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
      <rect x="10" y="2" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
      <rect x="2" y="10" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
      <rect x="10" y="10" width="6" height="6" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
    </svg>`,
  },
  {
    name: 'new-order',
    label: 'Nouvelle commande',
    icon: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <circle cx="9" cy="9" r="7" stroke="currentColor" stroke-width="1.5"/>
      <path d="M9 6V12M6 9H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>`,
  },
  {
    name: 'orders',
    label: 'Mes commandes',
    icon: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <rect x="2" y="3" width="14" height="12" rx="2" stroke="currentColor" stroke-width="1.5"/>
      <path d="M5 7H13M5 10H10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>`,
  },
  {
    name: 'deposit',
    label: 'Déposer des fonds',
    icon: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <rect x="2" y="5" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.5"/>
      <path d="M2 8H16" stroke="currentColor" stroke-width="1.5"/>
      <path d="M5 3H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>`,
  },
  {
    name: 'support',
    label: 'Support',
    icon: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <path d="M9 2C5.134 2 2 5.134 2 9C2 10.39 2.396 11.686 3.079 12.782L2 16L5.218 14.921C6.314 15.604 7.61 16 9 16C12.866 16 16 12.866 16 9C16 5.134 12.866 2 9 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
      <path d="M7 7.5C7 6.395 7.895 5.5 9 5.5C10.105 5.5 11 6.395 11 7.5C11 8.605 10.105 9.5 9 9.5V10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
      <circle cx="9" cy="12.5" r="0.75" fill="currentColor"/>
    </svg>`,
  },
  {
    name: 'profile',
    label: 'Mon profil',
    icon: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none">
      <circle cx="9" cy="6.5" r="3" stroke="currentColor" stroke-width="1.5"/>
      <path d="M3 15.5C3 12.739 5.686 10.5 9 10.5C12.314 10.5 15 12.739 15 15.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
    </svg>`,
  },
]

// ── Ferme sidebar sur changement de route (mobile) ───────
watch(() => route.name, () => {
  sidebarOpen.value = false
})

// ── Déconnexion ───────────────────────────────────────────
async function handleLogout() {
  logoutLoading.value = true
  await auth.logout()
  router.push({ name: 'login' })
  logoutLoading.value = false
}
</script>

<style scoped>
/* ── Wrap global ─────────────────────────────────────────── */
.dashboard-wrap {
  display: flex;
  min-height: 100vh;
  position: relative;
}

/* ── Overlay mobile ──────────────────────────────────────── */
.sidebar-overlay {
  position: fixed;
  inset: 0;
  z-index: 40;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(2px);
}

/* ── Sidebar ─────────────────────────────────────────────── */
.sidebar {
  position: fixed;
  top: 0; left: 0; bottom: 0;
  z-index: 50;
  width: 240px;
  display: flex;
  flex-direction: column;
  background: rgba(8, 6, 18, 0.85);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border-right: 1px solid rgba(255, 255, 255, 0.07);
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
              transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
}

.sidebar.collapsed { width: 68px; }

/* Logo */
.sidebar-logo {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1rem 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  flex-shrink: 0;
}

.logo-link {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  text-decoration: none;
  overflow: hidden;
}

.sidebar-brand {
  font-size: 1.1rem;
  white-space: nowrap;
}

.collapse-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(226, 232, 240, 0.3);
  padding: 4px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  transition: color 0.2s, background 0.2s;
  flex-shrink: 0;
}
.collapse-btn:hover { color: rgba(226, 232, 240, 0.7); background: rgba(255,255,255,0.06); }

/* Balance */
.sidebar-balance {
  margin: 0.75rem 0.85rem;
  padding: 0.85rem 1rem;
  background: rgba(124, 58, 237, 0.1);
  border: 1px solid rgba(124, 58, 237, 0.2);
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  flex-shrink: 0;
  overflow: hidden;
}

.balance-label {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: rgba(167, 139, 250, 0.6);
  font-weight: 500;
}

.balance-amount {
  font-size: 1.15rem;
  font-weight: 700;
  color: #fff;
  font-family: var(--font-mono, monospace);
  white-space: nowrap;
}

.balance-currency {
  font-size: 0.72rem;
  color: rgba(226, 232, 240, 0.4);
  font-weight: 400;
}

.balance-cta {
  font-size: 0.75rem;
  font-weight: 600;
  color: #A78BFA;
  text-decoration: none;
  margin-top: 0.15rem;
  transition: color 0.2s;
}
.balance-cta:hover { color: #C4B5FD; }

/* Nav */
.sidebar-nav {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
  padding: 0.5rem 0.6rem;
}

.sidebar-nav ul {
  list-style: none;
  padding: 0; margin: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.85rem;
  border-radius: 10px;
  text-decoration: none;
  color: rgba(226, 232, 240, 0.5);
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s;
  white-space: nowrap;
  position: relative;
  overflow: hidden;
}

.nav-item:hover {
  color: rgba(226, 232, 240, 0.85);
  background: rgba(255, 255, 255, 0.05);
}

.nav-item.active {
  color: #fff;
  background: rgba(124, 58, 237, 0.18);
  border: 1px solid rgba(124, 58, 237, 0.25);
  box-shadow: 0 2px 12px rgba(124, 58, 237, 0.15);
}

.nav-item.active .nav-icon { color: #A78BFA; }

.nav-icon {
  display: flex;
  align-items: center;
  flex-shrink: 0;
  transition: color 0.2s;
}

.nav-label { flex: 1; }

.nav-badge {
  font-size: 0.65rem;
  font-weight: 700;
  padding: 0.1rem 0.45rem;
  border-radius: 99px;
  background: rgba(124, 58, 237, 0.3);
  color: #A78BFA;
  border: 1px solid rgba(124, 58, 237, 0.2);
}

/* Sidebar footer */
.sidebar-footer {
  padding: 0.75rem 0.6rem;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  flex-shrink: 0;
}

.sidebar-profile {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  padding: 0.6rem 0.75rem;
  border-radius: 10px;
  text-decoration: none;
  transition: background 0.2s;
  overflow: hidden;
}
.sidebar-profile:hover { background: rgba(255,255,255,0.05); }

.profile-avatar {
  width: 32px; height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
}

.profile-info {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
  overflow: hidden;
}

.profile-name {
  font-size: 0.82rem;
  font-weight: 600;
  color: rgba(226, 232, 240, 0.85);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.profile-role {
  font-size: 0.7rem;
  color: rgba(226, 232, 240, 0.35);
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  width: 100%;
  padding: 0.6rem 0.75rem;
  border-radius: 10px;
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(239, 68, 68, 0.6);
  font-size: 0.85rem;
  font-weight: 500;
  font-family: var(--font-body, sans-serif);
  transition: all 0.2s;
  white-space: nowrap;
  overflow: hidden;
}
.logout-btn:hover {
  color: #EF4444;
  background: rgba(239, 68, 68, 0.08);
}

/* ── Main ────────────────────────────────────────────────── */
.dashboard-main {
  flex: 1;
  margin-left: 240px;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  z-index: 1;
}

.dashboard-main.expanded { margin-left: 68px; }

/* Topbar */
.topbar {
  position: sticky;
  top: 0;
  z-index: 30;
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem 1.5rem;
  background: rgba(8, 6, 18, 0.7);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.topbar-burger {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(226, 232, 240, 0.6);
  padding: 6px;
  border-radius: 8px;
  transition: all 0.2s;
}
.topbar-burger:hover { background: rgba(255,255,255,0.06); color: #E2E8F0; }

.topbar-title { flex: 1; }

.page-title {
  font-size: 1.05rem;
  font-weight: 600;
  color: rgba(226, 232, 240, 0.9);
  letter-spacing: -0.01em;
}

.topbar-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.topbar-balance {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  padding: 0.4rem 0.85rem;
  border-radius: 99px;
  background: rgba(124, 58, 237, 0.12);
  border: 1px solid rgba(124, 58, 237, 0.2);
  color: #A78BFA;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  font-family: var(--font-mono, monospace);
  transition: all 0.2s;
  white-space: nowrap;
}
.topbar-balance:hover { background: rgba(124,58,237,0.2); }

.topbar-icon-btn {
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(226, 232, 240, 0.4);
  padding: 6px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  transition: all 0.2s;
}
.topbar-icon-btn:hover { background: rgba(255,255,255,0.06); color: rgba(226,232,240,0.8); }

.topbar-avatar {
  width: 34px; height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #7C3AED, #06B6D4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.8rem;
  font-weight: 700;
  color: white;
  text-decoration: none;
  border: 2px solid rgba(124, 58, 237, 0.3);
  transition: border-color 0.2s;
}
.topbar-avatar:hover { border-color: rgba(124,58,237,0.6); }

/* Contenu */
.dashboard-content {
  flex: 1;
  padding: 1.75rem 1.5rem;
}

/* ── Transitions ─────────────────────────────────────────── */
.overlay-enter-active { transition: opacity 0.25s ease; }
.overlay-leave-active { transition: opacity 0.2s ease; }
.overlay-enter-from, .overlay-leave-to { opacity: 0; }

.page-fade-enter-active { transition: opacity 0.22s ease, transform 0.22s ease; }
.page-fade-leave-active { transition: opacity 0.18s ease; }
.page-fade-enter-from   { opacity: 0; transform: translateY(8px); }
.page-fade-leave-to     { opacity: 0; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 1024px) {
  .sidebar {
    transform: translateX(-100%);
    width: 240px !important;
  }

  .sidebar.open { transform: translateX(0); }

  .dashboard-main,
  .dashboard-main.expanded {
    margin-left: 0 !important;
  }

  .topbar-burger { display: flex; }

  .collapse-btn { display: none; }
}

@media (max-width: 640px) {
  .topbar        { padding: 0.65rem 1rem; }
  .dashboard-content { padding: 1.25rem 1rem; }
  .topbar-balance { display: none; }
}
</style>