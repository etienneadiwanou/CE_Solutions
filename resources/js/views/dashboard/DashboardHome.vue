<!-- ============================================================
     resources/js/views/dashboard/DashboardHome.vue
     Tableau de bord principal
     ============================================================ -->

<template>
  <div class="dashboard-home">

    <!-- ── Bienvenue ─────────────────────────────────────── -->
    <div class="welcome-row animate-fade-up">
      <div>
        <h2 class="welcome-title">
          Bienvenue, <span class="text-gradient">{{ firstName }}</span> 👋
        </h2>
        <p class="welcome-sub">Voici un résumé de ton activité</p>
      </div>
      <RouterLink :to="{ name: 'new-order' }" class="btn-primary welcome-cta">
        <svg width="15" height="15" viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5"/>
          <path d="M8 5V11M5 8H11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        Nouvelle commande
      </RouterLink>
    </div>

    <!-- ── Cartes stats ───────────────────────────────────── -->
    <div class="stats-grid animate-fade-up-delay-1">

      <div class="stat-card glass">
        <div class="stat-icon stat-icon--purple" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <rect x="2" y="4" width="16" height="12" rx="2.5" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2 8H18" stroke="currentColor" stroke-width="1.5"/>
            <circle cx="6" cy="13" r="1.2" fill="currentColor"/>
          </svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">Solde disponible</span>
          <span class="stat-value">{{ formatBalance }} <span class="stat-unit">XOF</span></span>
        </div>
        <RouterLink :to="{ name: 'deposit' }" class="stat-action">Déposer →</RouterLink>
      </div>

      <div class="stat-card glass">
        <div class="stat-icon stat-icon--cyan" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <rect x="2" y="3" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.5"/>
            <path d="M2 8H18M6 3V5M14 3V5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <path d="M6 12H10M6 15H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">Commandes totales</span>
          <span class="stat-value">{{ stats.totalOrders }}</span>
        </div>
        <RouterLink :to="{ name: 'orders' }" class="stat-action">Voir tout →</RouterLink>
      </div>

      <div class="stat-card glass">
        <div class="stat-icon stat-icon--green" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
            <path d="M6.5 10.5L9 13L13.5 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">Commandes complétées</span>
          <span class="stat-value">{{ stats.completedOrders }}</span>
        </div>
        <span class="stat-badge stat-badge--green">{{ completedRate }}%</span>
      </div>

      <div class="stat-card glass">
        <div class="stat-icon stat-icon--gold" aria-hidden="true">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M10 2L12.4 7.6L18.5 8.2L14 12.3L15.4 18.3L10 15.3L4.6 18.3L6 12.3L1.5 8.2L7.6 7.6L10 2Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="stat-info">
          <span class="stat-label">Tickets ouverts</span>
          <span class="stat-value">{{ stats.openTickets }}</span>
        </div>
        <RouterLink :to="{ name: 'support' }" class="stat-action">Support →</RouterLink>
      </div>

    </div>

    <!-- ── Contenu principal : commandes + activité ───────── -->
    <div class="main-grid">

      <!-- Dernières commandes -->
      <section class="glass orders-section animate-fade-up-delay-2" aria-label="Dernières commandes">
        <div class="section-header">
          <h3 class="section-title">Dernières commandes</h3>
          <RouterLink :to="{ name: 'orders' }" class="section-link">Voir tout</RouterLink>
        </div>

        <!-- Skeleton loading -->
        <template v-if="ordersLoading">
          <div v-for="i in 4" :key="i" class="order-row-skeleton">
            <div class="shimmer skeleton-line" style="width:40%"></div>
            <div class="shimmer skeleton-line" style="width:20%"></div>
            <div class="shimmer skeleton-badge"></div>
          </div>
        </template>

        <!-- Liste commandes -->
        <template v-else-if="recentOrders.length">
          <div
            v-for="order in recentOrders"
            :key="order.id"
            class="order-row"
          >
            <div class="order-info">
              <span class="order-service">{{ order.service?.name ?? '—' }}</span>
              <span class="order-link">{{ truncate(order.link, 35) }}</span>
            </div>
            <div class="order-meta">
              <span class="order-qty">{{ order.quantity.toLocaleString('fr-FR') }}</span>
              <span class="order-status" :class="statusClass(order.status)">
                {{ statusLabel(order.status) }}
              </span>
            </div>
          </div>
        </template>

        <!-- Vide -->
        <div v-else class="empty-state">
          <svg width="40" height="40" viewBox="0 0 40 40" fill="none" aria-hidden="true">
            <rect x="4" y="8" width="32" height="24" rx="4" stroke="rgba(255,255,255,0.15)" stroke-width="2"/>
            <path d="M4 16H36M12 8V10M28 8V10" stroke="rgba(255,255,255,0.15)" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Aucune commande pour l'instant</p>
          <RouterLink :to="{ name: 'new-order' }" class="btn-ghost empty-btn">
            Passer une commande
          </RouterLink>
        </div>
      </section>

      <!-- Activité récente -->
      <section class="glass activity-section animate-fade-up-delay-3" aria-label="Activité récente">
        <div class="section-header">
          <h3 class="section-title">Activité récente</h3>
        </div>

        <template v-if="activityLoading">
          <div v-for="i in 5" :key="i" class="activity-row-skeleton">
            <div class="shimmer skeleton-circle"></div>
            <div style="flex:1;display:flex;flex-direction:column;gap:6px">
              <div class="shimmer skeleton-line" style="width:60%"></div>
              <div class="shimmer skeleton-line" style="width:35%"></div>
            </div>
            <div class="shimmer skeleton-line" style="width:18%"></div>
          </div>
        </template>

        <template v-else-if="recentActivity.length">
          <div
            v-for="tx in recentActivity"
            :key="tx.id"
            class="activity-row"
          >
            <div class="activity-icon" :class="txIconClass(tx.type)" aria-hidden="true">
              <!-- Crédit -->
              <svg v-if="tx.type === 'credit'" width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M7 11V3M7 3L4 6M7 3L10 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <!-- Débit -->
              <svg v-else width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M7 3V11M7 11L4 8M7 11L10 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="activity-info">
              <span class="activity-desc">{{ tx.description }}</span>
              <span class="activity-date">{{ formatDate(tx.created_at) }}</span>
            </div>
            <span class="activity-amount" :class="tx.type === 'credit' ? 'amount-credit' : 'amount-debit'">
              {{ tx.type === 'credit' ? '+' : '-' }}{{ Math.abs(tx.amount).toLocaleString('fr-FR') }} XOF
            </span>
          </div>
        </template>

        <div v-else class="empty-state">
          <svg width="40" height="40" viewBox="0 0 40 40" fill="none" aria-hidden="true">
            <circle cx="20" cy="20" r="16" stroke="rgba(255,255,255,0.15)" stroke-width="2"/>
            <path d="M20 12V20M20 20H26" stroke="rgba(255,255,255,0.15)" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>Aucune activité récente</p>
        </div>
      </section>

    </div>

    <!-- ── Raccourcis rapides ──────────────────────────────── -->
    <section class="shortcuts animate-fade-up-delay-4" aria-label="Raccourcis">
      <div
        v-for="shortcut in shortcuts"
        :key="shortcut.name"
        class="shortcut-card glass"
      >
        <RouterLink :to="{ name: shortcut.name }" class="shortcut-link">
          <div class="shortcut-icon" :style="{ background: shortcut.bg }" aria-hidden="true"
               v-html="shortcut.icon">
          </div>
          <span class="shortcut-label">{{ shortcut.label }}</span>
          <svg class="shortcut-arrow" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
            <path d="M3 7H11M11 7L7.5 3.5M11 7L7.5 10.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </RouterLink>
      </div>
    </section>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../../stores/auth.js'
import { ordersApi, profileApi } from '../../services/api.js'

const auth = useAuthStore()

// ── User data ─────────────────────────────────────────────
const firstName   = computed(() => auth.fullName.split(' ')[0] || 'toi')
const balance     = computed(() => auth.balance)
const formatBalance = computed(() =>
  new Intl.NumberFormat('fr-FR').format(balance.value)
)

// ── Stats locales (calculées depuis les données) ──────────
const stats = ref({
  totalOrders:     0,
  completedOrders: 0,
  openTickets:     0,
})

const completedRate = computed(() => {
  if (!stats.value.totalOrders) return 0
  return Math.round((stats.value.completedOrders / stats.value.totalOrders) * 100)
})

// ── Commandes récentes ────────────────────────────────────
const recentOrders  = ref([])
const ordersLoading = ref(true)

// ── Activité récente (transactions) ──────────────────────
const recentActivity  = ref([])
const activityLoading = ref(true)

// ── Chargement des données ────────────────────────────────
onMounted(async () => {
  // Commandes
  try {
    const data = await ordersApi.list({ per_page: 5 })
    recentOrders.value = data.data ?? data
    stats.value.totalOrders     = data.total ?? recentOrders.value.length
    stats.value.completedOrders = recentOrders.value.filter(o => o.status === 'completed').length
  } catch (_) {
    recentOrders.value = []
  } finally {
    ordersLoading.value = false
  }

  // Transactions
  try {
    const data = await profileApi.transactions({ per_page: 6 })
    recentActivity.value = data.data ?? data
  } catch (_) {
    recentActivity.value = []
  } finally {
    activityLoading.value = false
  }
})

// ── Helpers ───────────────────────────────────────────────
function truncate(str, max) {
  if (!str) return '—'
  return str.length > max ? str.slice(0, max) + '…' : str
}

function formatDate(dateStr) {
  if (!dateStr) return ''
  return new Intl.DateTimeFormat('fr-FR', {
    day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit',
  }).format(new Date(dateStr))
}

const statusLabels = {
  pending:    'En attente',
  processing: 'En cours',
  completed:  'Complétée',
  partial:    'Partielle',
  cancelled:  'Annulée',
  failed:     'Échouée',
}

function statusLabel(s) { return statusLabels[s] ?? s }

function statusClass(s) {
  const map = {
    pending:    'status--yellow',
    processing: 'status--blue',
    completed:  'status--green',
    partial:    'status--cyan',
    cancelled:  'status--gray',
    failed:     'status--red',
  }
  return map[s] ?? 'status--gray'
}

function txIconClass(type) {
  return type === 'credit' ? 'tx-credit' : 'tx-debit'
}

// ── Raccourcis ────────────────────────────────────────────
const shortcuts = [
  {
    name: 'new-order',
    label: 'Nouvelle commande',
    bg: 'rgba(124,58,237,0.15)',
    icon: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="8" stroke="#A78BFA" stroke-width="1.5"/><path d="M10 7V13M7 10H13" stroke="#A78BFA" stroke-width="1.5" stroke-linecap="round"/></svg>`,
  },
  {
    name: 'deposit',
    label: 'Déposer des fonds',
    bg: 'rgba(6,182,212,0.12)',
    icon: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><rect x="2" y="5" width="16" height="11" rx="2" stroke="#67E8F9" stroke-width="1.5"/><path d="M2 9H18" stroke="#67E8F9" stroke-width="1.5"/><path d="M5 3H15" stroke="#67E8F9" stroke-width="1.5" stroke-linecap="round"/></svg>`,
  },
  {
    name: 'orders',
    label: 'Mes commandes',
    bg: 'rgba(16,185,129,0.12)',
    icon: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><rect x="2" y="3" width="16" height="14" rx="2" stroke="#34D399" stroke-width="1.5"/><path d="M6 8H14M6 11H11" stroke="#34D399" stroke-width="1.5" stroke-linecap="round"/></svg>`,
  },
  {
    name: 'support',
    label: 'Contacter le support',
    bg: 'rgba(245,158,11,0.12)',
    icon: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 2C6.134 2 3 5.134 3 9C3 10.654 3.578 12.173 4.538 13.362L3 17L6.638 15.462C7.827 16.422 9.346 17 11 17C14.866 17 18 13.866 18 10" stroke="#FCD34D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M15 2V6M13 4H17" stroke="#FCD34D" stroke-width="1.5" stroke-linecap="round"/></svg>`,
  },
]
</script>

<style scoped>
.dashboard-home {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* ── Bienvenue ───────────────────────────────────────────── */
.welcome-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.welcome-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 0.25rem;
}

.welcome-sub {
  font-size: 0.85rem;
  color: rgba(226, 232, 240, 0.4);
}

.welcome-cta {
  width: auto;
  padding: 0.6rem 1.25rem;
  font-size: 0.875rem;
  text-decoration: none;
  white-space: nowrap;
}

/* ── Stats grid ──────────────────────────────────────────── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.stat-card {
  padding: 1.1rem 1.15rem;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  border-radius: 99px 99px 0 0;
}

.stat-icon {
  width: 38px; height: 38px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-icon--purple { background: rgba(124,58,237,0.15); color: #A78BFA; }
.stat-icon--cyan   { background: rgba(6,182,212,0.12);  color: #67E8F9; }
.stat-icon--green  { background: rgba(16,185,129,0.12); color: #34D399; }
.stat-icon--gold   { background: rgba(245,158,11,0.12); color: #FCD34D; }

.stat-card:nth-child(1)::before { background: #7C3AED; }
.stat-card:nth-child(2)::before { background: #06B6D4; }
.stat-card:nth-child(3)::before { background: #10B981; }
.stat-card:nth-child(4)::before { background: #F59E0B; }

.stat-info {
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
}

.stat-label {
  font-size: 0.75rem;
  color: rgba(226, 232, 240, 0.4);
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-weight: 500;
}

.stat-value {
  font-size: 1.35rem;
  font-weight: 700;
  color: #fff;
  font-family: var(--font-mono, monospace);
  line-height: 1;
}

.stat-unit {
  font-size: 0.7rem;
  color: rgba(226,232,240,0.35);
  font-weight: 400;
}

.stat-action {
  font-size: 0.75rem;
  color: rgba(167, 139, 250, 0.7);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
  margin-top: auto;
}
.stat-action:hover { color: #A78BFA; }

.stat-badge {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 0.2rem 0.55rem;
  border-radius: 99px;
  align-self: flex-start;
  margin-top: auto;
}
.stat-badge--green { background: rgba(16,185,129,0.15); color: #34D399; border: 1px solid rgba(16,185,129,0.2); }

/* ── Main grid ───────────────────────────────────────────── */
.main-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.orders-section,
.activity-section {
  padding: 1.25rem;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.section-title {
  font-size: 0.95rem;
  font-weight: 600;
  color: rgba(226, 232, 240, 0.85);
}

.section-link {
  font-size: 0.78rem;
  color: rgba(167, 139, 250, 0.7);
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s;
}
.section-link:hover { color: #A78BFA; }

/* Order rows */
.order-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 0.65rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}
.order-row:last-child { border-bottom: none; }

.order-info {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  min-width: 0;
}

.order-service {
  font-size: 0.83rem;
  font-weight: 500;
  color: rgba(226,232,240,0.8);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.order-link {
  font-size: 0.72rem;
  color: rgba(226,232,240,0.3);
  font-family: var(--font-mono, monospace);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.order-meta {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-shrink: 0;
}

.order-qty {
  font-size: 0.78rem;
  color: rgba(226,232,240,0.4);
  font-family: var(--font-mono, monospace);
}

.order-status {
  font-size: 0.7rem;
  font-weight: 600;
  padding: 0.2rem 0.55rem;
  border-radius: 99px;
}

.status--green  { background: rgba(16,185,129,0.15);  color: #34D399; border: 1px solid rgba(16,185,129,0.2); }
.status--blue   { background: rgba(59,130,246,0.15);  color: #93C5FD; border: 1px solid rgba(59,130,246,0.2); }
.status--yellow { background: rgba(245,158,11,0.15);  color: #FCD34D; border: 1px solid rgba(245,158,11,0.2); }
.status--cyan   { background: rgba(6,182,212,0.12);   color: #67E8F9; border: 1px solid rgba(6,182,212,0.2); }
.status--red    { background: rgba(239,68,68,0.12);   color: #FCA5A5; border: 1px solid rgba(239,68,68,0.2); }
.status--gray   { background: rgba(255,255,255,0.06); color: rgba(226,232,240,0.4); border: 1px solid rgba(255,255,255,0.08); }

/* Skeletons */
.order-row-skeleton {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.04);
  gap: 0.75rem;
}
.skeleton-line   { height: 10px; border-radius: 6px; background: rgba(255,255,255,0.06); }
.skeleton-badge  { width: 60px; height: 22px; border-radius: 99px; background: rgba(255,255,255,0.06); }
.skeleton-circle { width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.06); flex-shrink:0; }

/* Activity rows */
.activity-row-skeleton {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.04);
}

.activity-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}
.activity-row:last-child { border-bottom: none; }

.activity-icon {
  width: 32px; height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.tx-credit { background: rgba(16,185,129,0.15);  color: #34D399; }
.tx-debit  { background: rgba(239,68,68,0.12);   color: #FCA5A5; }

.activity-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  min-width: 0;
}

.activity-desc {
  font-size: 0.82rem;
  color: rgba(226,232,240,0.75);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.activity-date {
  font-size: 0.7rem;
  color: rgba(226,232,240,0.3);
}

.activity-amount {
  font-size: 0.82rem;
  font-weight: 600;
  font-family: var(--font-mono, monospace);
  white-space: nowrap;
  flex-shrink: 0;
}

.amount-credit { color: #34D399; }
.amount-debit  { color: #FCA5A5; }

/* Empty state */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 2rem 1rem;
  text-align: center;
}

.empty-state p {
  font-size: 0.85rem;
  color: rgba(226,232,240,0.3);
}

.empty-btn {
  font-size: 0.82rem;
  padding: 0.5rem 1rem;
  text-decoration: none;
}

/* ── Raccourcis ──────────────────────────────────────────── */
.shortcuts {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
}

.shortcut-card {
  transition: transform 0.2s;
}
.shortcut-card:hover { transform: translateY(-2px); }

.shortcut-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.9rem 1rem;
  text-decoration: none;
}

.shortcut-icon {
  width: 36px; height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.shortcut-label {
  flex: 1;
  font-size: 0.82rem;
  font-weight: 500;
  color: rgba(226, 232, 240, 0.7);
}

.shortcut-arrow {
  color: rgba(226,232,240,0.2);
  transition: color 0.2s, transform 0.2s;
}
.shortcut-card:hover .shortcut-arrow {
  color: rgba(167,139,250,0.7);
  transform: translateX(3px);
}

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 1200px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 900px) {
  .main-grid  { grid-template-columns: 1fr; }
  .shortcuts  { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 640px) {
  .stats-grid    { grid-template-columns: 1fr 1fr; }
  .shortcuts     { grid-template-columns: 1fr 1fr; }
  .welcome-title { font-size: 1.2rem; }
  .welcome-cta   { width: 100%; justify-content: center; }
  .welcome-row   { flex-direction: column; align-items: flex-start; }
}

@media (max-width: 420px) {
  .stats-grid { grid-template-columns: 1fr; }
  .shortcuts  { grid-template-columns: 1fr; }
}
</style>