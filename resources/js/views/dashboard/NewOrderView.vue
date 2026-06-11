<!-- ============================================================
     resources/js/views/dashboard/NewOrderView.vue
     Nouvelle commande
     ============================================================ -->

<template>
  <div class="new-order">

    <!-- ── En-tête ────────────────────────────────────────── -->
    <div class="page-header animate-fade-up">
      <div>
        <h2 class="page-title">Nouvelle commande</h2>
        <p class="page-sub">Choisis un service et configure ta commande</p>
      </div>
      <div class="balance-pill">
        <svg width="13" height="13" viewBox="0 0 14 14" fill="none" aria-hidden="true">
          <rect x="1" y="3" width="12" height="8" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
          <path d="M1 6H13" stroke="currentColor" stroke-width="1.3"/>
        </svg>
        <span>{{ formatBalance }} XOF</span>
      </div>
    </div>

    <div class="order-layout">

      <!-- ── Colonne gauche : sélection service ────────────── -->
      <div class="order-left animate-fade-up-delay-1">

        <!-- Recherche + filtre catégorie -->
        <div class="glass filter-bar">
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
              placeholder="Rechercher un service…"
              :disabled="servicesLoading"
            />
          </div>

          <!-- Catégories -->
          <div class="categories-scroll" role="tablist" aria-label="Catégories">
            <button
              class="cat-btn"
              :class="{ active: !selectedCategory }"
              @click="selectedCategory = null"
              role="tab"
              :aria-selected="!selectedCategory"
            >
              Tous
            </button>
            <button
              v-for="cat in categories"
              :key="cat.id"
              class="cat-btn"
              :class="{ active: selectedCategory === cat.id }"
              @click="selectedCategory = cat.id"
              role="tab"
              :aria-selected="selectedCategory === cat.id"
            >
              {{ cat.icon }} {{ cat.name }}
            </button>
          </div>
        </div>

        <!-- Liste des services -->
        <div class="services-list" role="listbox" aria-label="Services disponibles">

          <!-- Skeleton -->
          <template v-if="servicesLoading">
            <div v-for="i in 6" :key="i" class="service-item-skeleton glass">
              <div class="shimmer skeleton-line" style="width:55%;height:12px"></div>
              <div class="shimmer skeleton-line" style="width:80%;height:9px;margin-top:6px"></div>
              <div style="display:flex;gap:8px;margin-top:10px">
                <div class="shimmer skeleton-badge"></div>
                <div class="shimmer skeleton-badge"></div>
              </div>
            </div>
          </template>

          <!-- Services filtrés -->
          <template v-else-if="filteredServices.length">
            <div
              v-for="svc in filteredServices"
              :key="svc.id"
              class="service-item glass"
              :class="{ selected: selectedService?.id === svc.id }"
              @click="selectService(svc)"
              role="option"
              :aria-selected="selectedService?.id === svc.id"
              tabindex="0"
              @keydown.enter="selectService(svc)"
              @keydown.space.prevent="selectService(svc)"
            >
              <div class="service-header-row">
                <span class="service-name">{{ svc.name }}</span>
                <span class="service-price">
                  {{ formatPrice(svc.price_per_1000) }} XOF
                  <span class="price-unit">/ 1000</span>
                </span>
              </div>
              <p class="service-desc">{{ truncate(svc.description, 80) }}</p>
              <div class="service-tags">
               <span class="service-tag">Min {{ (svc.min_quantity ?? 0).toLocaleString('fr-FR') }}</span>
<span class="service-tag">Max {{ (svc.max_quantity ?? 0).toLocaleString('fr-FR') }}</span>
                <span v-if="svc.refill" class="service-tag tag--purple">♻ Refill</span>
                <span v-if="svc.cancel" class="service-tag tag--cyan">✕ Annulable</span>
              </div>
            </div>
          </template>

          <div v-else class="empty-state">
            <p>Aucun service trouvé</p>
          </div>

        </div>
      </div>

      <!-- ── Colonne droite : formulaire commande ───────────── -->
      <div class="order-right animate-fade-up-delay-2">
        <div class="glass order-form-card">

          <!-- Aucun service sélectionné -->
          <div v-if="!selectedService" class="no-service">
            <div class="no-service-icon" aria-hidden="true">
              <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                <circle cx="18" cy="18" r="16" stroke="rgba(124,58,237,0.3)" stroke-width="1.5" stroke-dasharray="4 3"/>
                <path d="M18 12V18M18 18H22M18 18H14" stroke="rgba(124,58,237,0.4)" stroke-width="1.5" stroke-linecap="round"/>
              </svg>
            </div>
            <p class="no-service-text">Sélectionne un service<br>pour configurer ta commande</p>
          </div>

          <!-- Formulaire actif -->
          <template v-else>
            <div class="form-service-recap">
              <div>
                <span class="recap-label">Service sélectionné</span>
                <span class="recap-name">{{ selectedService.name }}</span>
              </div>
              <button class="recap-clear" @click="selectedService = null" aria-label="Désélectionner">
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                  <path d="M3 3L11 11M11 3L3 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
              </button>
            </div>

            <form @submit.prevent="handleOrder" novalidate>

              <!-- Lien -->
              <div class="form-group">
                <label for="order-link" class="form-label">Lien / URL cible</label>
                <div class="input-wrapper">
                  <span class="input-icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 15 15" fill="none">
                      <path d="M6.5 8.5C7.328 9.328 8.672 9.328 9.5 8.5L11.5 6.5C12.328 5.672 12.328 4.328 11.5 3.5C10.672 2.672 9.328 2.672 8.5 3.5L7.5 4.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                      <path d="M8.5 6.5C7.672 5.672 6.328 5.672 5.5 6.5L3.5 8.5C2.672 9.328 2.672 10.672 3.5 11.5C4.328 12.328 5.672 12.328 6.5 11.5L7.5 10.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                    </svg>
                  </span>
                  <input
                    id="order-link"
                    v-model.trim="form.link"
                    type="url"
                    class="glass-input with-icon"
                    :class="{ error: errors.link }"
                    placeholder="https://instagram.com/ton_compte"
                    inputmode="url"
                    :disabled="loading"
                    @input="delete errors.link"
                  />
                </div>
                <Transition name="fade-alert">
                  <p v-if="errors.link" class="form-error">
                    <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                    {{ errors.link[0] }}
                  </p>
                </Transition>
              </div>

              <!-- Quantité -->
              <div class="form-group">
                <div class="label-row">
                  <label for="order-qty" class="form-label">Quantité</label>
                  <span class="qty-range">
                    {{ selectedService.min_quantity.toLocaleString('fr-FR') }} –
                    {{ selectedService.max_quantity.toLocaleString('fr-FR') }}
                  </span>
                </div>
                <input
                  id="order-qty"
                  v-model.number="form.quantity"
                  type="number"
                  class="glass-input"
                  :class="{ error: errors.quantity }"
                  :min="selectedService.min_quantity ?? 0"
                :max="selectedService.max_quantity ?? 0"
                    :placeholder="selectedService.min_quantity ?? 0"
                  :disabled="loading"
                  @input="delete errors.quantity"
                />
                <!-- Slider -->
                <input
                  type="range"
                  class="qty-slider"
                  v-model.number="form.quantity"
                  :min="selectedService.min_quantity"
                  :max="selectedService.max_quantity"
                  :step="Math.max(1, Math.floor(selectedService.min_quantity / 10))"
                  :disabled="loading"
                  aria-label="Quantité via slider"
                />
                <Transition name="fade-alert">
                  <p v-if="errors.quantity" class="form-error">
                    <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                    {{ errors.quantity[0] }}
                  </p>
                </Transition>
              </div>

              <!-- Récap prix -->
              <div class="price-recap glass">
                <div class="price-row">
                  <span class="price-row-label">Prix unitaire</span>
                  <span class="price-row-value">{{ formatPrice(selectedService.price_per_1000) }} XOF / 1000</span>
                </div>
                <div class="price-row">
                  <span class="price-row-label">Quantité</span>
                  <span class="price-row-value">{{ (form.quantity || 0).toLocaleString('fr-FR') }}</span>
                </div>
                <div class="price-divider"></div>
                <div class="price-row price-total-row">
                  <span class="price-total-label">Total</span>
                  <span class="price-total-value" :class="{ 'price-over': !canAfford }">
                    {{ formatPrice(totalCost) }} XOF
                  </span>
                </div>
                <Transition name="fade-alert">
                  <p v-if="!canAfford && form.quantity" class="form-error" style="margin-top:.5rem">
                    <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="5" stroke="#FCA5A5" stroke-width="1.2"/><path d="M6 4V6.5" stroke="#FCA5A5" stroke-width="1.2" stroke-linecap="round"/><circle cx="6" cy="8" r="0.6" fill="#FCA5A5"/></svg>
                    Solde insuffisant.
                    <RouterLink :to="{ name: 'deposit' }" class="error-link">Déposer des fonds →</RouterLink>
                  </p>
                </Transition>
              </div>

              <!-- Erreur globale -->
              <Transition name="fade-alert">
                <div v-if="globalError" class="alert alert-error" role="alert">
                  <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="#EF4444" stroke-width="1.4"/><path d="M8 5V8.5" stroke="#EF4444" stroke-width="1.4" stroke-linecap="round"/><circle cx="8" cy="10.5" r="0.7" fill="#EF4444"/></svg>
                  {{ globalError }}
                </div>
              </Transition>

              <!-- Succès -->
              <Transition name="fade-alert">
                <div v-if="successMsg" class="alert alert-success" role="status">
                  <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6.5" stroke="#10B981" stroke-width="1.4"/><path d="M5 8L7.2 10.5L11 6" stroke="#10B981" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  {{ successMsg }}
                </div>
              </Transition>

              <button
                type="submit"
                class="btn-primary"
                :disabled="loading || !canAfford"
              >
                <Transition name="btn-content" mode="out-in">
                  <span v-if="loading" key="loading" class="btn-inner">
                    <svg class="spinner" width="15" height="15" viewBox="0 0 16 16" fill="none">
                      <circle cx="8" cy="8" r="6" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                      <path d="M8 2C8 2 12 2 14 8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Commande en cours…
                  </span>
                  <span v-else key="idle" class="btn-inner">
                    Passer la commande
                    <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                      <path d="M3 8H13M13 8L8.5 3.5M13 8L8.5 12.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </span>
                </Transition>
              </button>

            </form>
          </template>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../../stores/auth.js'
import { servicesApi, ordersApi } from '../../services/api.js'

const auth = useAuthStore()

// ── Balance ───────────────────────────────────────────────
const balance     = computed(() => auth.balance)
const formatBalance = computed(() => new Intl.NumberFormat('fr-FR').format(balance.value))

// ── Services ──────────────────────────────────────────────
const allServices    = ref([])
const categories     = ref([])
const servicesLoading = ref(true)
const selectedService = ref(null)
const selectedCategory = ref(null)
const search = ref('')

onMounted(async () => {
  try {
    const [svcData, catData] = await Promise.all([
  servicesApi.list({ per_page: 100 }),
  servicesApi.categories(),
])

// Extraire les services depuis les catégories imbriquées
const grouped = svcData.data ?? svcData
allServices.value = grouped.flatMap(cat =>
  (cat.services ?? []).map(s => ({ ...s, category: cat }))
)
categories.value = catData.data ?? catData
  } catch (_) {
    allServices.value = []
  } finally {
    servicesLoading.value = false
  }
})

const filteredServices = computed(() => {
  let list = allServices.value
  if (selectedCategory.value) {
    list = list.filter(s => s.category_id === selectedCategory.value)
  }
  if (search.value.trim()) {
    const q = search.value.toLowerCase()
    list = list.filter(s =>
      s.name.toLowerCase().includes(q) ||
      (s.description ?? '').toLowerCase().includes(q)
    )
  }
  return list
})

function selectService(svc) {
  selectedService.value = svc
  form.quantity = svc.min_quantity
  form.link     = ''
  Object.keys(errors).forEach(k => delete errors[k])
  globalError.value = null
  successMsg.value  = null
}

// ── Formulaire ────────────────────────────────────────────
const form = reactive({ link: '', quantity: 0 })
const errors      = reactive({})
const globalError = ref(null)
const successMsg  = ref(null)
const loading     = ref(false)

// Coût calculé
const totalCost = computed(() => {
  if (!selectedService.value || !form.quantity) return 0
  return (form.quantity / 1000) * parseFloat(selectedService.value.price_per_1000)
})

const canAfford = computed(() =>
  !form.quantity || totalCost.value <= balance.value
)

// Reset form quand on change de service
watch(selectedService, () => {
  Object.keys(errors).forEach(k => delete errors[k])
  globalError.value = null
  successMsg.value  = null
})

// ── Soumission ────────────────────────────────────────────
async function handleOrder() {
  Object.keys(errors).forEach(k => delete errors[k])
  globalError.value = null
  successMsg.value  = null

  // Validation front
  if (!form.link) { errors.link = ['Le lien est requis.']; return }
  if (!form.quantity || form.quantity < selectedService.value.min_quantity) {
    errors.quantity = [`Quantité minimum : ${selectedService.value.min_quantity.toLocaleString('fr-FR')}`]
    return
  }
  if (form.quantity > selectedService.value.max_quantity) {
    errors.quantity = [`Quantité maximum : ${selectedService.value.max_quantity.toLocaleString('fr-FR')}`]
    return
  }
  if (!canAfford.value) return

  loading.value = true
  try {
    await ordersApi.create({
      service_id: selectedService.value.id,
      link:       form.link,
      quantity:   form.quantity,
    })

    // Met à jour la balance localement
    auth.setBalance(balance.value - totalCost.value)

    successMsg.value = `Commande passée avec succès ! Livraison en cours.`
    form.link     = ''
    form.quantity = selectedService.value.min_quantity

  } catch (err) {
    if (err.errors && Object.keys(err.errors).length) {
      Object.assign(errors, err.errors)
    } else {
      globalError.value = err.message ?? 'Une erreur est survenue.'
    }
  } finally {
    loading.value = false
  }
}

// ── Helpers ───────────────────────────────────────────────
function formatPrice(val) {
  return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 2 }).format(val)
}

function truncate(str, max) {
  if (!str) return ''
  return str.length > max ? str.slice(0, max) + '…' : str
}
</script>

<style scoped>
.new-order { display: flex; flex-direction: column; gap: 1.25rem; }

/* Header */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.page-title { font-size: 1.3rem; font-weight: 700; color: #fff; margin-bottom: .2rem; }
.page-sub   { font-size: .84rem; color: rgba(226,232,240,.4); }

.balance-pill {
  display: flex;
  align-items: center;
  gap: .4rem;
  padding: .4rem .9rem;
  border-radius: 99px;
  background: rgba(124,58,237,.12);
  border: 1px solid rgba(124,58,237,.2);
  color: #A78BFA;
  font-size: .8rem;
  font-weight: 600;
  font-family: var(--font-mono, monospace);
}

/* Layout 2 colonnes */
.order-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 1.25rem;
  align-items: start;
}

/* ── Gauche ─────────────────────────────────────────────── */
.order-left { display: flex; flex-direction: column; gap: .75rem; }

.filter-bar { padding: .85rem 1rem; display: flex; flex-direction: column; gap: .75rem; }

.search-wrap { flex: 1; }

.categories-scroll {
  display: flex;
  gap: .4rem;
  overflow-x: auto;
  padding-bottom: 2px;
  scrollbar-width: none;
}
.categories-scroll::-webkit-scrollbar { display: none; }

.cat-btn {
  flex-shrink: 0;
  padding: .35rem .75rem;
  border-radius: 99px;
  font-size: .78rem;
  font-weight: 500;
  font-family: var(--font-body, sans-serif);
  border: 1px solid rgba(255,255,255,.08);
  background: rgba(255,255,255,.04);
  color: rgba(226,232,240,.5);
  cursor: pointer;
  transition: all .2s;
  white-space: nowrap;
}
.cat-btn:hover  { background: rgba(255,255,255,.07); color: rgba(226,232,240,.8); }
.cat-btn.active { background: rgba(124,58,237,.2); border-color: rgba(124,58,237,.4); color: #A78BFA; }

/* Services list */
.services-list {
  display: flex;
  flex-direction: column;
  gap: .5rem;
  max-height: 520px;
  overflow-y: auto;
  padding-right: 2px;
}

.service-item {
  padding: .9rem 1rem;
  cursor: pointer;
  transition: border-color .2s, background .2s, transform .15s;
}
.service-item:hover   { border-color: rgba(124,58,237,.3); background: rgba(255,255,255,.08); }
.service-item.selected {
  border-color: rgba(124,58,237,.5) !important;
  background: rgba(124,58,237,.1) !important;
  box-shadow: 0 0 0 2px rgba(124,58,237,.15), var(--glass-shadow);
}

.service-item-skeleton { padding: .9rem 1rem; }

.service-header-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: .5rem;
  margin-bottom: .3rem;
}

.service-name  { font-size: .87rem; font-weight: 600; color: rgba(226,232,240,.9); }
.service-price { font-size: .82rem; font-weight: 700; color: #A78BFA; white-space: nowrap; font-family: var(--font-mono, monospace); }
.price-unit    { font-size: .68rem; font-weight: 400; color: rgba(226,232,240,.35); }

.service-desc  { font-size: .75rem; color: rgba(226,232,240,.35); line-height: 1.4; margin-bottom: .5rem; }

.service-tags  { display: flex; gap: .35rem; flex-wrap: wrap; }

.service-tag {
  font-size: .68rem;
  font-weight: 500;
  padding: .15rem .5rem;
  border-radius: 99px;
  background: rgba(255,255,255,.06);
  color: rgba(226,232,240,.45);
  border: 1px solid rgba(255,255,255,.07);
}
.tag--purple { background: rgba(124,58,237,.15); color: #A78BFA; border-color: rgba(124,58,237,.2); }
.tag--cyan   { background: rgba(6,182,212,.12);  color: #67E8F9; border-color: rgba(6,182,212,.2); }

.skeleton-badge { width: 55px; height: 20px; border-radius: 99px; background: rgba(255,255,255,.06); }
.skeleton-line  { height: 10px; border-radius: 6px; }

/* ── Droite ─────────────────────────────────────────────── */
.order-form-card {
  padding: 1.5rem;
  position: sticky;
  top: 80px;
}

.no-service {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .85rem;
  padding: 2.5rem 1rem;
  text-align: center;
}

.no-service-text {
  font-size: .85rem;
  color: rgba(226,232,240,.35);
  line-height: 1.55;
}

/* Recap service sélectionné */
.form-service-recap {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: .5rem;
  padding: .75rem .9rem;
  border-radius: 10px;
  background: rgba(124,58,237,.1);
  border: 1px solid rgba(124,58,237,.2);
  margin-bottom: 1.25rem;
}

.recap-label { display: block; font-size: .68rem; text-transform: uppercase; letter-spacing: .07em; color: rgba(167,139,250,.6); margin-bottom: .2rem; }
.recap-name  { font-size: .87rem; font-weight: 600; color: #fff; }

.recap-clear {
  background: none;
  border: none;
  cursor: pointer;
  color: rgba(226,232,240,.3);
  padding: 3px;
  border-radius: 5px;
  transition: color .2s, background .2s;
  flex-shrink: 0;
}
.recap-clear:hover { color: rgba(226,232,240,.7); background: rgba(255,255,255,.06); }

/* Form groups */
.form-group { margin-bottom: 1rem; }

.label-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: .4rem; }

.qty-range {
  font-size: .72rem;
  color: rgba(226,232,240,.35);
  font-family: var(--font-mono, monospace);
}

/* Slider */
.qty-slider {
  width: 100%;
  margin-top: .5rem;
  -webkit-appearance: none;
  appearance: none;
  height: 4px;
  border-radius: 99px;
  background: rgba(255,255,255,.08);
  outline: none;
  cursor: pointer;
}
.qty-slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 16px; height: 16px;
  border-radius: 50%;
  background: #7C3AED;
  border: 2px solid rgba(167,139,250,.5);
  box-shadow: 0 0 8px rgba(124,58,237,.5);
  cursor: pointer;
}
.qty-slider::-moz-range-thumb {
  width: 16px; height: 16px;
  border-radius: 50%;
  background: #7C3AED;
  border: 2px solid rgba(167,139,250,.5);
  cursor: pointer;
}

/* Prix récap */
.price-recap {
  padding: .9rem 1rem;
  margin-bottom: 1rem;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  gap: .45rem;
}

.price-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.price-row-label { font-size: .78rem; color: rgba(226,232,240,.4); }
.price-row-value { font-size: .8rem;  color: rgba(226,232,240,.7); font-family: var(--font-mono, monospace); }

.price-divider { height: 1px; background: rgba(255,255,255,.07); margin: .1rem 0; }

.price-total-row { margin-top: .1rem; }
.price-total-label { font-size: .87rem; font-weight: 600; color: #fff; }
.price-total-value { font-size: 1.05rem; font-weight: 700; color: #A78BFA; font-family: var(--font-mono, monospace); }
.price-over        { color: #FCA5A5 !important; }

/* Alertes */
.alert { display:flex; align-items:center; gap:.6rem; padding:.65rem .85rem; border-radius:8px; font-size:.82rem; font-weight:500; margin-bottom:.85rem; }
.alert-error   { background: rgba(239,68,68,.1);   border: 1px solid rgba(239,68,68,.25);   color: #FCA5A5; }
.alert-success { background: rgba(16,185,129,.1);  border: 1px solid rgba(16,185,129,.25);  color: #6EE7B7; }

.error-link { color: #A78BFA; text-decoration: underline; font-weight: 600; }

.btn-inner  { display: inline-flex; align-items: center; gap: .5rem; }
.spinner    { animation: spin .8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.empty-state { display:flex;flex-direction:column;align-items:center;gap:.75rem;padding:2rem 1rem;text-align:center; }
.empty-state p { font-size:.84rem; color:rgba(226,232,240,.3); }

/* Transitions */
.fade-alert-enter-active { transition: all .22s ease; }
.fade-alert-leave-active { transition: all .18s ease; }
.fade-alert-enter-from   { opacity: 0; transform: translateY(-5px); }
.fade-alert-leave-to     { opacity: 0; }

.btn-content-enter-active,
.btn-content-leave-active { transition: all .16s ease; }
.btn-content-enter-from   { opacity: 0; transform: translateY(5px); }
.btn-content-leave-to     { opacity: 0; transform: translateY(-5px); }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 1024px) {
  .order-layout { grid-template-columns: 1fr; }
  .order-form-card { position: static; }
  .services-list { max-height: 360px; }
}

@media (max-width: 640px) {
  .page-header { flex-direction: column; align-items: flex-start; }
  .service-header-row { flex-direction: column; gap: .25rem; }
}
</style>