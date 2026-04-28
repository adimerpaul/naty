<template>
  <q-layout view="lHh Lpr lFf">
    <!-- HEADER -->
    <q-header class="bg-white text-black" bordered>
      <q-toolbar>
        <q-btn
          flat
          color="primary"
          :icon="leftDrawerOpen ? 'keyboard_double_arrow_left' : 'keyboard_double_arrow_right'"
          aria-label="Menu"
          @click="toggleLeftDrawer"
          dense
        />

        <div class="row items-center q-gutter-sm">
          <div class="text-subtitle1 text-weight-medium" style="line-height: 0.9">
            Sistema de Gestion Naty <br>
            <q-badge color="warning" text-color="black" v-if="roleText" class="text-bold">
              {{ roleText }}
            </q-badge>
          </div>
        </div>

        <q-space />

        <div class="row items-center q-gutter-sm">
          <q-btn-dropdown flat unelevated no-caps dropdown-icon="expand_more">
            <template v-slot:label>
              <div class="row items-center no-wrap q-gutter-sm">
                <q-avatar rounded>
                  <q-img
                    v-if="$store.user && $store.user.avatar"
                    :src="`${$url}../../images/${$store.user.avatar}`"
                    width="40px"
                    height="40px"
                  />
                  <q-icon name="person" v-else />
                </q-avatar>

                <div class="text-left" style="line-height: 1">
                  <div class="ellipsis" style="max-width: 130px;">
                    {{ $store.user ? $store.user.username : '' }}
                  </div>
                  <q-chip
                    dense
                    size="10px"
                    :color="$filters.color($store.user ? $store.user.role : '')"
                    text-color="white"
                  >
                    {{ $store.user ? $store.user.role : '' }}
                  </q-chip>
                </div>
              </div>
            </template>

            <q-item clickable v-close-popup>
              <q-item-section>
                <q-item-label class="text-grey-7">
                  Permisos asignados
                </q-item-label>
                <q-item-label caption class="q-mt-xs">
                  <div class="row q-col-gutter-xs" style="min-width: 150px; max-width: 150px;">
                    <q-chip
                      v-for="(p, i) in $store.permissions"
                      :key="i"
                      dense
                      color="grey-3"
                      text-color="black"
                      size="12px"
                      class="q-mr-xs q-mb-xs"
                    >
                      {{ p }}
                    </q-chip>
                    <q-badge v-if="!$store.permissions || !$store.permissions.length" color="grey-5" outline>
                      Sin permisos
                    </q-badge>
                  </div>
                </q-item-label>
              </q-item-section>
            </q-item>

            <q-separator />

            <q-item clickable v-close-popup @click="$router.push('/cambiar-contrasena')">
              <q-item-section avatar>
                <q-icon name="vpn_key" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Cambiar Contraseña</q-item-label>
              </q-item-section>
            </q-item>

            <q-item clickable v-ripple @click="logout" v-close-popup>
              <q-item-section avatar>
                <q-icon name="logout" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Salir</q-item-label>
              </q-item-section>
            </q-item>
          </q-btn-dropdown>
        </div>
      </q-toolbar>
    </q-header>

    <!-- DRAWER -->
    <q-drawer
      v-model="leftDrawerOpen"
      bordered
      show-if-above
      :width="230"
      :breakpoint="500"
      class="drawer-gradient text-white"
    >
      <div class="drawer-header column items-center q-pt-lg q-pb-md">
        <q-avatar size="68px" class="q-mb-sm drawer-logo" rounded>
          <q-img src="/logo.png" width="90px" />
        </q-avatar>
        <div class="text-weight-bold text-white text-subtitle1">Naty</div>
        <div class="text-caption" style="color: rgba(255,255,255,0.7)">Compras y Ventas</div>
      </div>

      <q-separator style="background: rgba(255,255,255,0.15)" class="q-mx-md q-mb-sm" />

      <q-list class="q-pb-none">
        <div class="text-caption q-px-md q-mb-xs" style="color: rgba(255,255,255,0.55); letter-spacing: 0.07em; text-transform: uppercase;">
          Módulos
        </div>

        <!-- DASHBOARD -->
        <q-item
          dense
          to="/"
          exact
          clickable
          class="naty-menu-item"
          active-class="naty-menu-active"
          v-close-popup
          v-if="hasPermission('Dashboard')"
        >
          <q-item-section avatar>
            <q-icon name="dashboard" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Dashboard</q-item-label>
          </q-item-section>
        </q-item>

        <!-- USUARIOS -->
        <q-item
          dense
          to="/usuarios"
          exact
          clickable
          class="naty-menu-item"
          active-class="naty-menu-active"
          v-close-popup
          v-if="hasPermission('Usuarios')"
        >
          <q-item-section avatar>
            <q-icon name="people" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Usuarios</q-item-label>
          </q-item-section>
        </q-item>

        <!-- IMPUESTOS -->
        <q-item
          dense
          to="/impuestos"
          exact
          clickable
          class="naty-menu-item"
          active-class="naty-menu-active"
          v-close-popup
          v-if="hasPermission('Usuarios') || isAdmin"
        >
          <q-item-section avatar>
            <q-icon name="receipt" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Impuestos</q-item-label>
          </q-item-section>
        </q-item>

        <!-- INVENTARIO -->
        <q-item
          dense
          to="/inventarios"
          exact
          clickable
          class="naty-menu-item"
          active-class="naty-menu-active"
          v-close-popup
          v-if="hasPermission('Inventario') || isAdmin"
        >
          <q-item-section avatar>
            <q-icon name="inventory_2" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Inventario</q-item-label>
          </q-item-section>
        </q-item>

        <!-- CAJAS -->
        <q-item
          dense
          to="/cajas"
          exact
          clickable
          class="naty-menu-item"
          active-class="naty-menu-active"
          v-close-popup
          v-if="hasPermission('Cajas') || isAdmin"
        >
          <q-item-section avatar>
            <q-icon name="account_balance_wallet" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Cajas</q-item-label>
          </q-item-section>
        </q-item>

        <!-- PERSONAL (submenu popup) -->
        <q-item
          dense
          clickable
          class="naty-menu-item"
          :class="{ 'naty-menu-active': isSubmenuActive('personal') }"
          v-if="hasPermission('Personal') || hasPermission('Pagos Personal') || hasPermission('Historial Pagos Personal') || isAdmin"
        >
          <q-item-section avatar>
            <q-icon name="badge" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Personal</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-icon name="chevron_right" size="xs" style="color: rgba(255,255,255,0.7)" />
          </q-item-section>
          <q-menu anchor="top end" self="top start" auto-close class="naty-submenu-popup">
            <div class="naty-submenu-header">
              <q-icon name="badge" size="xs" class="q-mr-xs" />Personal
            </div>
            <q-list style="min-width: 210px">
              <q-item dense to="/personal" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Personal') || isAdmin">
                <q-item-section avatar><q-icon name="groups" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Personal</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/personal/pagos" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Pagos Personal') || isAdmin">
                <q-item-section avatar><q-icon name="payments" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Pagos</q-item-label></q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-item>

        <!-- DETALLE (submenu popup) -->
        <q-item
          dense
          clickable
          class="naty-menu-item"
          :class="{ 'naty-menu-active': isSubmenuActive('detalle') }"
          v-if="canDetalle"
        >
          <q-item-section avatar>
            <q-icon name="store" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Detalle</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-icon name="chevron_right" size="xs" style="color: rgba(255,255,255,0.7)" />
          </q-item-section>
          <q-menu anchor="top end" self="top start" auto-close class="naty-submenu-popup">
            <div class="naty-submenu-header">
              <q-icon name="store" size="xs" class="q-mr-xs" />Detalle
            </div>
            <q-list style="min-width: 210px">
              <q-item dense to="/clientes/detalle" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Cliente Detalle') || isAdmin">
                <q-item-section avatar><q-icon name="person_outline" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Cliente detalle</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/productos/detalle" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Producto Detalle') || isAdmin">
                <q-item-section avatar><q-icon name="sell" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Producto detalle</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/ventas/detalle" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Venta Detalle') || isAdmin">
                <q-item-section avatar><q-icon name="point_of_sale" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Venta detalle</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/ventas/detalle/nueva" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Venta Detalle') || isAdmin">
                <q-item-section avatar><q-icon name="add_shopping_cart" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Crear venta detalle</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/deudas/detalle" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Venta Detalle') || isAdmin">
                <q-item-section avatar><q-icon name="payments" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Deuda detalle</q-item-label></q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-item>

        <!-- LOCAL (submenu popup) -->
        <q-item
          dense
          clickable
          class="naty-menu-item"
          :class="{ 'naty-menu-active': isSubmenuActive('local') }"
          v-if="canLocal"
        >
          <q-item-section avatar>
            <q-icon name="local_mall" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Local</q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-icon name="chevron_right" size="xs" style="color: rgba(255,255,255,0.7)" />
          </q-item-section>
          <q-menu anchor="top end" self="top start" auto-close class="naty-submenu-popup">
            <div class="naty-submenu-header">
              <q-icon name="local_mall" size="xs" class="q-mr-xs" />Local
            </div>
            <q-list style="min-width: 210px">
              <q-item dense to="/clientes/local" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Cliente Local') || isAdmin">
                <q-item-section avatar><q-icon name="storefront" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Cliente local</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/productos/local" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Producto Local') || isAdmin">
                <q-item-section avatar><q-icon name="shopping_bag" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Producto local</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/ventas/local" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Venta Local') || isAdmin">
                <q-item-section avatar><q-icon name="receipt_long" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Venta local</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/ventas/local/nueva" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Venta Local') || isAdmin">
                <q-item-section avatar><q-icon name="add_shopping_cart" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Crear venta local</q-item-label></q-item-section>
              </q-item>
              <q-item dense to="/deudas/local" exact clickable active-class="naty-menu-popup-active" v-close-popup v-if="hasPermission('Venta Local') || isAdmin">
                <q-item-section avatar><q-icon name="account_balance" size="xs" color="primary" /></q-item-section>
                <q-item-section><q-item-label>Deuda local</q-item-label></q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-item>

        <!-- GRADERÍAS -->
        <q-item
          dense
          to="/mis-graderias"
          exact
          clickable
          class="naty-menu-item"
          active-class="naty-menu-active"
          v-close-popup
          v-if="hasPermission('Graderias')"
        >
          <q-item-section avatar>
            <q-icon name="stadium" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Compras y ventas</q-item-label>
          </q-item-section>
        </q-item>

        <q-item
          dense
          to="/mis-graderias/nueva"
          exact
          clickable
          class="naty-menu-item"
          active-class="naty-menu-active"
          v-close-popup
          v-if="hasPermission('Graderias')"
        >
          <q-item-section avatar>
            <q-icon name="add_box" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Nueva operacion</q-item-label>
          </q-item-section>
        </q-item>

        <!-- CAMBIAR CONTRASEÑA -->
        <q-item
          dense
          to="/cambiar-contrasena"
          exact
          clickable
          class="naty-menu-item"
          active-class="naty-menu-active"
          v-close-popup
        >
          <q-item-section avatar>
            <q-icon name="vpn_key" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Cambiar Contraseña</q-item-label>
          </q-item-section>
        </q-item>

        <q-separator style="background: rgba(255,255,255,0.15)" class="q-mx-md q-my-sm" />

        <!-- SALIR -->
        <q-item dense clickable class="naty-menu-item" @click="logout" v-close-popup>
          <q-item-section avatar>
            <q-icon name="logout" size="sm" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Salir</q-item-label>
          </q-item-section>
        </q-item>

        <!-- FOOTER -->
        <div class="q-pa-md q-pt-sm">
          <div class="text-caption" style="color: rgba(255,255,255,0.45)">
            Naty v{{ $version }} &nbsp;·&nbsp; © {{ new Date().getFullYear() }}
          </div>
        </div>
      </q-list>
    </q-drawer>

    <!-- PAGE -->
    <q-page-container class="bg-grey-2">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
export default {
  name: 'MainLayout',
  data () {
    return {
      leftDrawerOpen: false
    }
  },
  computed: {
    canDetalle () {
      return this.isAdmin || this.hasPermission('Cliente Detalle') || this.hasPermission('Producto Detalle') || this.hasPermission('Venta Detalle')
    },
    canLocal () {
      return this.isAdmin || this.hasPermission('Cliente Local') || this.hasPermission('Producto Local') || this.hasPermission('Venta Local')
    },
    isAdmin () {
      const role = this.$store && this.$store.user ? this.$store.user.role : ''
      return role === 'Administrador'
    },
    roleText () {
      const role = this.$store && this.$store.user ? this.$store.user.role : ''
      return role || ''
    }
  },
  methods: {
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
    isSubmenuActive (section) {
      const path = this.$route.path
      if (section === 'personal') return path.startsWith('/personal')
      if (section === 'detalle') return path.endsWith('/detalle') || path.includes('/detalle/')
      if (section === 'local') return path.endsWith('/local') || path.includes('/local/')
      return false
    },
    hasPermission (perm) {
      return this.$store && this.$store.permissions
        ? this.$store.permissions.includes(perm)
        : false
    },
    logout () {
      this.$alert.dialog('¿Desea salir del sistema?')
        .onOk(() => {
          this.$axios.post('/logout')
            .then(() => {
              this.$store.isLogged = false
              this.$store.user = {}
              this.$store.permissions = []
              this.$store.env = {}
              localStorage.removeItem('tokenNaty')
              localStorage.removeItem('user')
              localStorage.removeItem('permissionsNaty')
              localStorage.removeItem('envNaty')
              this.$router.push('/login')
            })
            .catch(() => {
              this.$store.isLogged = false
              this.$store.user = {}
              this.$store.permissions = []
              this.$store.env = {}
              localStorage.removeItem('tokenNaty')
              localStorage.removeItem('user')
              localStorage.removeItem('permissionsNaty')
              localStorage.removeItem('envNaty')
              this.$router.push('/login')
            })
        })
    }
  }
}
</script>

<style>
/* ─── Drawer fondo degradado ─────────────────────── */
.drawer-gradient {
  background: linear-gradient(180deg, #4a0e8f 0%, #6a1b9a 55%, #8e24aa 100%) !important;
}

/* El q-drawer__content y el q-list deben ser transparentes
   para que el degradado del drawer se vea a través de ellos */
.drawer-gradient .q-drawer__content,
.drawer-gradient .q-list {
  background: transparent !important;
}

.drawer-gradient .q-item {
  background: transparent !important;
}

.drawer-header {
  background: rgba(0, 0, 0, 0.18);
}

.drawer-logo {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
  border: 2px solid rgba(255, 255, 255, 0.3);
}

/* ─── Menu items ─────────────────────────────────── */
.naty-menu-item {
  border-radius: 10px;
  margin: 2px 8px;
  padding: 5px 6px;
  color: rgba(255, 255, 255, 0.92) !important;
  transition: background 0.15s ease;
}

.naty-menu-item:hover {
  background: rgba(255, 255, 255, 0.13) !important;
}

.naty-menu-active {
  background: rgba(255, 255, 255, 0.2) !important;
  box-shadow: inset 3px 0 0 rgba(255, 255, 255, 0.6);
  color: #fff !important;
  font-weight: 600;
}

/* ─── Submenu popup ──────────────────────────────── */
.naty-submenu-popup {
  border-radius: 10px !important;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.22) !important;
  overflow: hidden;
}

.naty-submenu-header {
  padding: 8px 14px 6px;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #6a1b9a;
  background: #f3e5f5;
  display: flex;
  align-items: center;
}

.naty-menu-popup-active {
  background: rgba(106, 27, 154, 0.1) !important;
  color: #6a1b9a !important;
  font-weight: 600;
}
</style>
