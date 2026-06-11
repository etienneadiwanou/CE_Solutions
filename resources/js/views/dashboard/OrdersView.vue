<!-- ============================================================
     resources/js/views/dashboard/OrdersView.vue
     Historique des commandes
     ============================================================ -->

<template>
  <div class="orders-view">

    <!-- ── En-tête ────────────────────────────────────────── -->
    <div class="page-header animate-fade-up">
      <div>
        <h2 class="page-title">Mes commandes</h2>
        <p class="page-sub">Suis l'état de toutes tes commandes</p>
      </div>
      <RouterLink :to="{ name: 'new-order' }" class="btn-primary header-cta">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true">
          <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.5"/>
          <path d="M8 5V11M5 8H11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        Nouvelle commande
      </RouterLink>
    </div>

    <!-- ── Filtres ────────────────────────────────────────── -->
    <div class="glass filters-bar animate-fade-up-delay-1">
      <!-- Recherche -->
      <div class="input-wrapper search-wrap">
        <span class="input-icon" aria-hidden="true">
          <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
            <circle cx="6.5" cy="6.5" r="5" stroke="currentColor" stroke-width="1.4"/>
            <path d="M10.5 10.5L13.5 13.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          </svg>
        </span>
        <input
          v-model="search"
          type="search"
          class="glass-input with-icon"
          placeholder="Rechercher par service, lien…"
        />
      </div>

      <!-- Filtre statut -->
      <div class="filter-group">
        <label for="filter-status" class="sr-only">Filtrer par statut</label>
        <select id="filter-status" v-model="filterStatus" class="glass-select">
          <option value="">Tous les statuts</option>
          <option value="pending">En attente</option>
          <option value="processing">En cours</option>
          <option value="completed">Complétée</option>
          <option value="partial">Partielle</option>
          <option value="cancelled">Annulée</option>
          <option value="failed">Échouée</option>
        </select>
      </div>

      <!-- Tri -->
      <div class="filter-group">
        <label for="filter-sort" class="sr-only">Trier par</label>
        <select id="filter-sort" v-model="sortBy" class="glass-select">
          <option value="desc">Plus récent</option>
          <option value="asc">Plus ancien</option>
        </select>
      </div>
    </div>

    <!-- ── Stats rapides ──────────────────────────────────── -->
    <div class="quick-stats animate-fade-up-delay-1">
      <div
        v-for="stat in quickStats"
        :key="stat.label"
        class="quick-stat glass"
        :class="{ active: filterStatus === stat.status }"
        @click="filterStatus = filterStatus === stat.status ? '' : stat.status"
        role="button"
        tabindex="0"
        @keydown.enter="filterStatus = filterStatus === stat.status ? '' : stat.status"
      >
        <span class="qs-value" :class="stat.color">{{ stat.count }}</span>
        <span class="qs-label">{{ stat.label }}</span>
      </div>
    </div>

    <!-- ── Table des commandes ────────────────────────────── -->
    <div class="glass orders-table-wrap animate-fade-up-delay-2">

      <!-- Header table (desktop) -->
      <div class="table-header" role="row" aria-hidden="true">
        <span class="th th-id">#</span>
        <span class="th th-service">Service</span>
        <span class="th th-link">Lien</span>
        <span class="th th-qty">Quantité</span>
        <span class="th th-price">Montant</span>
        <span class="th th-status">Statut</span>
        <span class="th th-date">Date</span>
      </div>

      <!-- Skeleton -->
      <template v-if="loading">
        <div v-for="i in 8" :key="i" class="table-row skeleton-row">
          <div class="shimmer skeleton-line" style="width:40px;height:10px"></div>
          <div class="shimmer skeleton-line" style="width:140px;height:10px"></div>
          <div class="shimmer skeleton-line" style="width:120px;height:10px"></div>
          <div class="shimmer skeleton-line" style="width:60px;height:10px"></div>
          <div class="shimmer skeleton-line" style="width:80px;height:10px"></div>
          <div class="shimmer skeleton-badge"></div>
          <div class="shimmer skeleton-line" style="width:80px;height:10px"></div>
        </div>
      </template>

      <!-- Rows -->
      <template v-else-if="paginatedOrders.length">
        <div
          v-for="order in paginatedOrders"
          :key="order.id"
          class="table-row"
          @click="openDetail(order)"
          role="row"
          tabindex="0"
          @keydown.enter="openDetail(order)"
          :aria-label="`Commande #${order.id}`"
        >
          <span class="td td-id">#{{ order.id }}</span>
          <span class="td td-service">{{ order.service?.name ?? '—' }}</span>
          <span class="td td-link">
            <a :href="order.link" target="_blank" rel="noopener" class="link-url" @click.stop>
              {{ truncate(order.link, 30) }}
            </a>
          </span>
          <span class="td td-qty">{{ order.quantity.toLocaleString('fr-FR') }}</span>
          <span class="td td-price">{{ formatPrice(order.charge) }} XOF</span>
          <span class="td td-status">
            <span class="status-badge" :class="statusClass(order.status)">
              <span class="status-dot" aria-hidden="true"></span>
              {{ statusLabel(order.status) }}
            </span>
          </span>
          <span class="td td-date">{{ formatDate(order.created_at) }}</span>
        </div>
      </template>

      <!-- Vide -->
      <div v-else class="empty-state">
        <svg width="44" height="44" viewBox="0 0 44 44" fill="none" aria-hidden="true">
          <rect x="4" y="8" width="36" height="28" rx="4" stroke="rgba(255,255,255,0.12)" stroke-width="2"/>
          <path d="M4 18H40M14 8V10M30 8V10" stroke="rgba(255,255,255,0.12)" stroke-width="2" stroke-linecap="round"/>
          <path d="M14 26H22M14 31H18" stroke="rgba(255,255,255,0.12)" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <p>Aucune commande trouvée</p>
        <RouterLink :to="{ name: 'new-order' }" class="btn-ghost empty-btn">
          Passer une commande
        </RouterLink>
      </div>

    </div>

    <!-- ── Pagination ─────────────────────────────────────── -->
    <div v-if="totalPages > 1" class="pagination animate-fade-up-delay-3">
      <button
        class="page-btn"
        :disabled="currentPage === 1"
        @click="currentPage--"
        aria-label="Page précédente"
      >
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
          <path d="M9 3L5 7L9 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <button
        v-for="page in visiblePages"
        :key="page"
        class="page-btn"
        :class="{ active: page === currentPage, ellipsis: page === '…' }"
        :disabled="page === '…'"
        @click="page !== '…' && (currentPage = page)"
        :aria-label="`Page ${page}`"
        :aria-current="page === currentPage ? 'page' : undefined"
      >
        {{ page }}
      </button>

      <button
        class="page-btn"
        :disabled="currentPage === totalPages"
        @click="currentPage++"
        aria-label="Page suivante"
      >
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
          <path d="M5 3L9 7L5 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <span class="page-info">
        {{ (currentPage - 1) * perPage + 1 }}–{{ Math.min(currentPage * perPage, filteredOrders.length) }}
        sur {{ filteredOrders.length }}
      </span>
    </div>

    <!-- ── Drawer détail commande ─────────────────────────── -->
    <Transition name="drawer">
      <div
        v-if="detailOrder"
        class="drawer-overlay"
        @click.self="detailOrder = null"
        role="dialog"
        aria-modal="true"
        aria-label="Détail commande"
      >
        <div class="drawer glass">
          <div class="drawer-header">
            <h3 class="drawer-title">Commande #{{ detailOrder.id }}</h3>
            <button class="drawer-close" @click="detailOrder = null" aria-label="Fermer">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M3 3L13 13M13 3L3 13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
              </svg>
            </button>
          </div>

          <div class="drawer-body">
            <div class="detail-row">
              <span class="detail-label">Service</span>
              <span class="detail-value">{{ detailOrder.service?.name ?? '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Lien</span>
              <a :href="detailOrder.link" target="_blank" rel="noopener" class="detail-link">
                {{ detailOrder.link }}
              </a>
            </div>
            <div class="detail-row">
              <span class="detail-label">Quantité</span>
              <span class="detail-value">{{ detailOrder.quantity.toLocaleString('fr-FR') }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Départ</span>
              <span class="detail-value">{{ detailOrder.start_count?.toLocaleString('fr-FR') ?? '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Reste</span>
              <span class="detail-value">{{ detailOrder.remains?.toLocaleString('fr-FR') ?? '—' }}</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Montant</span>
              <span class="detail-value">{{ formatPrice(detailOrder.charge) }} XOF</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Statut</span>
              <span class="status-badge" :class="statusClass(detailOrder.status)">
                <span class="status-dot"></span>
                {{ statusLabel(detailOrder.status) }}
              </span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Date</span>
              <span class="detail-value">{{ formatDateLong(detailOrder.created_at) }}</span>
            </div>
            <div v-if="detailOrder.completed_at" class="detail-row">
              <span class="detail-label">Complétée le</span>
              <span class="detail-value">{{ formatDateLong(detailOrder.completed_at) }}</span>
            </div>
          </div>

          <!-- Progression -->
          <div v-if="showProgress(detailOrder)" class="drawer-progress">
            <div class="progress-label">
              <span>Progression</span>
              <span>{{ progressPercent(detailOrder) }}%</span>
            </div>
            <div class="progress-bar-bg">
              <div
                class="progress-bar-fill"
                :style="{ width: progressPercent(detailOrder) + '%' }"
              ></div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { ordersApi } from '../../services/api.js'

// ── State ─────────────────────────────────────────────────
const allOrders   = ref([])
const loading     = ref(true)
const search      = ref('')
const filterStatus = ref('')
const sortBy      = ref('desc')
const currentPage = ref(1)
const perPage     = 10
const detailOrder = ref(null)

// ── Fetch ─────────────────────────────────────────────────
onMounted(async () => {
  try {
    const data = await ordersApi.list({ per_page: 200 })
    allOrders.value = data.data ?? data
  } catch (_) {
    allOrders.value = []
  } finally {
    loading.value = false
  }
})

// ── Filtres + tri ─────────────────────────────────────────
const filteredOrders = computed(() => {
  let list = [...allOrders.value]

  if (filterStatus.value) {
    list = list.filter(o => o.status === filterStatus.value)
  }

  if (search.value.trim()) {
    const q = search.value.toLowerCase()
    list = list.filter(o =>
      (o.service?.name ?? '').toLowerCase().includes(q) ||
      (o.link ?? '').toLowerCase().includes(q) ||
      String(o.id).includes(q)
    )
  }

  list.sort((a, b) => {
    const da = new Date(a.created_at)
    const db = new Date(b.created_at)
    return sortBy.value === 'desc' ? db - da : da - db
  })

  return list
})

// Reset page quand filtre change
watch([search, filterStatus, sortBy], () => { currentPage.value = 1 })

// ── Pagination ────────────────────────────────────────────
const totalPages = computed(() => Math.ceil(filteredOrders.value.length / perPage))

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredOrders.value.slice(start, start + perPage)
})

const visiblePages = computed(() => {
  const total = totalPages.value
  const cur   = currentPage.value
  if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1)
  if (cur <= 4)   return [1, 2, 3, 4, 5, '…', total]
  if (cur >= total - 3) return [1, '…', total-4, total-3, total-2, total-1, total]
  return [1, '…', cur-1, cur, cur+1, '…', total]
})

// ── Stats rapides ─────────────────────────────────────────
const quickStats = computed(() => [
  { label: 'Total',      status: '',           count: allOrders.value.length,                                        color: 'color-white'  },
  { label: 'En cours',   status: 'processing', count: allOrders.value.filter(o => o.status === 'processing').length, color: 'color-blue'   },
  { label: 'Complétées', status: 'completed',  count: allOrders.value.filter(o => o.status === 'completed').length,  color: 'color-green'  },
  { label: 'En attente', status: 'pending',    count: allOrders.value.filter(o => o.status === 'pending').length,    color: 'color-yellow' },
  { label: 'Annulées',   status: 'cancelled',  count: allOrders.value.filter(o => o.status === 'cancelled').length,  color: 'color-red'    },
])

// ── Détail ────────────────────────────────────────────────
function openDetail(order) { detailOrder.value = order }

function showProgress(order) {
  return ['processing', 'partial'].includes(order.status) &&
    order.start_count != null && order.remains != null
}

function progressPercent(order) {
  const total = order.quantity
  const done  = total - (order.remains ?? total)
  return Math.min(100, Math.round((done / total) * 100))
}

// ── Helpers ───────────────────────────────────────────────
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

function formatPrice(val) {
  return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(val ?? 0)
}

function truncate(str, max) {
  if (!str) return '—'
  return str.length > max ? str.slice(0, max) + '…' : str
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(dateStr))
}

function formatDateLong(dateStr) {
  if (!dateStr) return '—'
  return new Intl.DateTimeFormat('fr-FR', {
    day: '2-digit', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  }).format(new Date(dateStr))
}
</script>

<style scoped>
.orders-view { display: flex; flex-direction: column; gap: 1.25rem; }

/* Header */
.page-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .75rem; }
.page-title  { font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: .2rem; }
.page-sub    { font-size: .84rem; color: rgba(226,232,240,.4); }
.header-cta  { width: auto; padding: .55rem 1.1rem; font-size: .85rem; text-decoration: none; }

/* Filtres */
.filters-bar {
  padding: .85rem 1rem;
  display: flex;
  gap: .75rem;
  align-items: center;
  flex-wrap: wrap;
}

.search-wrap { flex: 1; min-width: 180px; }

.filter-group { flex-shrink: 0; }

.glass-select {
  background: rgba(255,255,255,.05);
  border: 1px solid rgba(255,255,255,.10);
  border-radius: 8px;
  color: rgba(226,232,240,.7);
  font-family: var(--font-body, sans-serif);
  font-size: .84rem;
  padding: .65rem .9rem;
  outline: none;
  cursor: pointer;
  transition: border-color .2s;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='rgba(226,232,240,0.4)' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right .75rem center;
  padding-right: 2rem;
}
.glass-select:focus { border-color: rgba(124,58,237,.5); }
.glass-select option { background: #1a1530; }

/* Quick stats */
.quick-stats {
  display: flex;
  gap: .6rem;
  flex-wrap: wrap;
}

.quick-stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .2rem;
  padding: .6rem 1rem;
  cursor: pointer;
  border-radius: 10px;
  transition: all .2s;
  min-width: 80px;
  text-align: center;
}
.quick-stat:hover { border-color: rgba(124,58,237,.3); background: rgba(255,255,255,.08); }
.quick-stat.active { border-color: rgba(124,58,237,.5); background: rgba(124,58,237,.12); }

.qs-value { font-size: 1.2rem; font-weight: 700; font-family: var(--font-mono, monospace); }
.qs-label { font-size: .7rem; color: rgba(226,232,240,.4); text-transform: uppercase; letter-spacing: .06em; }

.color-white  { color: #fff; }
.color-blue   { color: #93C5FD; }
.color-green  { color: #34D399; }
.color-yellow { color: #FCD34D; }
.color-red    { color: #FCA5A5; }

/* Table */
.orders-table-wrap { overflow: hidden; }

.table-header {
  display: grid;
  grid-template-columns: 60px 1fr 1fr 90px 110px 120px 100px;
  padding: .65rem 1.25rem;
  border-bottom: 1px solid rgba(255,255,255,.06);
}

.th {
  font-size: .72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .07em;
  color: rgba(226,232,240,.3);
}

.table-row {
  display: grid;
  grid-template-columns: 60px 1fr 1fr 90px 110px 120px 100px;
  padding: .75rem 1.25rem;
  border-bottom: 1px solid rgba(255,255,255,.04);
  align-items: center;
  cursor: pointer;
  transition: background .15s;
}
.table-row:last-child { border-bottom: none; }
.table-row:hover { background: rgba(255,255,255,.04); }

.skeleton-row { cursor: default; gap: 1rem; }

.td { font-size: .83rem; color: rgba(226,232,240,.7); }

.td-id    { color: rgba(226,232,240,.3); font-family: var(--font-mono, monospace); font-size: .78rem; }
.td-service { font-weight: 500; color: rgba(226,232,240,.85); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: .5rem; }
.td-price { font-family: var(--font-mono, monospace); font-size: .8rem; }

.link-url {
  font-size: .75rem;
  color: rgba(167,139,250,.7);
  text-decoration: none;
  font-family: var(--font-mono, monospace);
  transition: color .2s;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
}
.link-url:hover { color: #A78BFA; text-decoration: underline; }

/* Status badge */
.status-badge {
  display: inline-flex;
  align-items: center;
  gap: .35rem;
  font-size: .72rem;
  font-weight: 600;
  padding: .22rem .6rem;
  border-radius: 99px;
  white-space: nowrap;
}

.status-dot {
  width: 5px; height: 5px;
  border-radius: 50%;
  background: currentColor;
}

.status--green  { background: rgba(16,185,129,.15);  color: #34D399; border: 1px solid rgba(16,185,129,.2); }
.status--blue   { background: rgba(59,130,246,.15);  color: #93C5FD; border: 1px solid rgba(59,130,246,.2); }
.status--yellow { background: rgba(245,158,11,.15);  color: #FCD34D; border: 1px solid rgba(245,158,11,.2); }
.status--cyan   { background: rgba(6,182,212,.12);   color: #67E8F9; border: 1px solid rgba(6,182,212,.2); }
.status--red    { background: rgba(239,68,68,.12);   color: #FCA5A5; border: 1px solid rgba(239,68,68,.2); }
.status--gray   { background: rgba(255,255,255,.06); color: rgba(226,232,240,.4); border: 1px solid rgba(255,255,255,.08); }

.skeleton-badge { width: 80px; height: 22px; border-radius: 99px; background: rgba(255,255,255,.06); }
.skeleton-line  { border-radius: 6px; }

/* Empty */
.empty-state { display:flex;flex-direction:column;align-items:center;gap:.75rem;padding:3rem 1rem;text-align:center; }
.empty-state p { font-size:.85rem; color:rgba(226,232,240,.3); }
.empty-btn { font-size:.82rem; padding:.5rem 1rem; text-decoration:none; }

/* Pagination */
.pagination {
  display: flex;
  align-items: center;
  gap: .4rem;
  flex-wrap: wrap;
}

.page-btn {
  min-width: 34px; height: 34px;
  padding: 0 .6rem;
  border-radius: 8px;
  border: 1px solid rgba(255,255,255,.08);
  background: rgba(255,255,255,.04);
  color: rgba(226,232,240,.55);
  font-size: .82rem;
  font-family: var(--font-body, sans-serif);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .2s;
}
.page-btn:hover:not(:disabled) { background: rgba(124,58,237,.15); border-color: rgba(124,58,237,.3); color: #A78BFA; }
.page-btn.active { background: rgba(124,58,237,.25); border-color: rgba(124,58,237,.5); color: #fff; font-weight: 600; }
.page-btn:disabled { opacity: .35; cursor: not-allowed; }
.page-btn.ellipsis { cursor: default; }

.page-info {
  margin-left: auto;
  font-size: .78rem;
  color: rgba(226,232,240,.3);
}

/* Drawer */
.drawer-overlay {
  position: fixed;
  inset: 0;
  z-index: 200;
  background: rgba(0,0,0,.55);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: flex-end;
}

.drawer {
  width: 100%;
  max-width: 400px;
  height: 100%;
  border-radius: 0;
  border-left: 1px solid rgba(255,255,255,.1);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(255,255,255,.07);
  flex-shrink: 0;
}

.drawer-title { font-size: 1rem; font-weight: 600; color: #fff; }

.drawer-close {
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(226,232,240,.4);
  padding: 5px;
  border-radius: 6px;
  transition: all .2s;
}
.drawer-close:hover { background: rgba(255,255,255,.06); color: rgba(226,232,240,.8); }

.drawer-body {
  padding: 1.25rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: .1rem;
  flex: 1;
}

.detail-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  padding: .6rem 0;
  border-bottom: 1px solid rgba(255,255,255,.05);
}
.detail-row:last-child { border-bottom: none; }

.detail-label { font-size: .78rem; color: rgba(226,232,240,.35); text-transform: uppercase; letter-spacing: .06em; flex-shrink: 0; }
.detail-value { font-size: .85rem; color: rgba(226,232,240,.8); text-align: right; font-family: var(--font-mono, monospace); }
.detail-link  { font-size: .78rem; color: #A78BFA; text-decoration: none; text-align: right; word-break: break-all; }
.detail-link:hover { text-decoration: underline; }

/* Progress */
.drawer-progress {
  padding: 1rem 1.5rem;
  border-top: 1px solid rgba(255,255,255,.07);
  flex-shrink: 0;
}

.progress-label {
  display: flex;
  justify-content: space-between;
  font-size: .78rem;
  color: rgba(226,232,240,.4);
  margin-bottom: .5rem;
}

.progress-bar-bg {
  height: 6px;
  background: rgba(255,255,255,.07);
  border-radius: 99px;
  overflow: hidden;
}

.progress-bar-fill {
  height: 100%;
  border-radius: 99px;
  background: linear-gradient(90deg, #7C3AED, #06B6D4);
  transition: width .5s ease;
}

/* Drawer transition */
.drawer-enter-active { transition: opacity .25s ease, transform .25s ease; }
.drawer-leave-active { transition: opacity .2s ease, transform .2s ease; }
.drawer-enter-from   { opacity: 0; }
.drawer-leave-to     { opacity: 0; }
.drawer-enter-from .drawer { transform: translateX(100%); }
.drawer-leave-to .drawer   { transform: translateX(100%); }

.sr-only { position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0; }

/* Responsive */
@media (max-width: 900px) {
  .table-header { display: none; }
  .table-row {
    grid-template-columns: 1fr;
    gap: .4rem;
    padding: .85rem 1rem;
  }
  .td::before {
    content: attr(data-label) ' : ';
    font-size: .68rem;
    color: rgba(226,232,240,.3);
    text-transform: uppercase;
    letter-spacing: .06em;
    display: block;
  }
  .td-id { display: none; }
}

@media (max-width: 640px) {
  .filters-bar { flex-direction: column; align-items: stretch; }
  .quick-stats { gap: .4rem; }
  .quick-stat  { min-width: 65px; padding: .5rem .7rem; }
  .drawer      { max-width: 100%; border-left: none; border-top: 1px solid rgba(255,255,255,.1); border-radius: 14px 14px 0 0; height: auto; max-height: 85vh; position: fixed; bottom: 0; left: 0; right: 0; }
  .drawer-overlay { align-items: flex-end; justify-content: stretch; }
}
</style>