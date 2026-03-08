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
<!--            cambiar contrseÃ±a-->
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
      :width="220"
      :breakpoint="500"
      class="bg-primary text-white"
    >
      <q-list class="q-pb-none">
        <q-item-label header class="text-center q-pa-none q-pt-md">
          <q-avatar size="64px" class="q-mb-sm bg-white" rounded>
            <q-img src="/logo.png" width="90px" />
          </q-avatar>
          <div class="text-weight-bold text-white">Naty</div>
          <div class="text-caption text-white">Compras y Ventas</div>
        </q-item-label>

        <q-item-label header class="q-px-md text-grey-3 q-mt-sm">
          Modulos del Sistema Naty
        </q-item-label>

        <!-- DASHBOARD -->
        <q-item
          dense
          to="/"
          exact
          clickable
          class="menu-item"
          active-class="menu-active"
          v-close-popup
          v-if="hasPermission('Dashboard')"
        >
          <q-item-section avatar>
            <q-icon name="dashboard" class="text-white" />
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Dashboard</q-item-label>
          </q-item-section>
        </q-item>

        <!-- USUARIOS -->
        <q-item
          dense
          to="/usuarios"
          exact
          clickable
          class="menu-item"
          active-class="menu-active"
          v-close-popup
          v-if="hasPermission('Usuarios')"
        >
          <q-item-section avatar>
            <q-icon name="people" class="text-white" />
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Usuarios</q-item-label>
          </q-item-section>
        </q-item>
        <q-item
          dense
          to="/inventarios"
          exact
          clickable
          class="menu-item"
          active-class="menu-active"
          v-close-popup
          v-if="hasPermission('Inventario') || isAdmin"
        >
          <q-item-section avatar>
            <q-icon name="inventory_2" class="text-white" />
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Inventario</q-item-label>
          </q-item-section>
        </q-item>
        <q-item
          dense
          to="/personal"
          exact
          clickable
          class="menu-item"
          active-class="menu-active"
          v-close-popup
          v-if="hasPermission('Personal') || isAdmin"
        >
          <q-item-section avatar>
            <q-icon name="badge" class="text-white" />
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Personal</q-item-label>
          </q-item-section>
        </q-item>

        <q-expansion-item
          dense
          icon="store"
          label="Detalle"
          class="text-white"
          header-class="menu-item text-white"
          expand-icon-class="text-white"
          v-if="canDetalle"
        >
          <q-item dense to="/clientes/detalle" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPermission('Cliente Detalle') || isAdmin">
            <q-item-section avatar><q-icon name="person_outline" class="text-white" /></q-item-section>
            <q-item-section><q-item-label class="text-white">Cliente detalle</q-item-label></q-item-section>
          </q-item>
          <q-item dense to="/productos/detalle" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPermission('Producto Detalle') || isAdmin">
            <q-item-section avatar><q-icon name="sell" class="text-white" /></q-item-section>
            <q-item-section><q-item-label class="text-white">Producto detalle</q-item-label></q-item-section>
          </q-item>
          <q-item dense to="/ventas/detalle" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPermission('Venta Detalle') || isAdmin">
            <q-item-section avatar><q-icon name="point_of_sale" class="text-white" /></q-item-section>
            <q-item-section><q-item-label class="text-white">Venta detalle</q-item-label></q-item-section>
          </q-item>
          <q-item dense to="/deudas/detalle" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPermission('Venta Detalle') || isAdmin">
            <q-item-section avatar><q-icon name="payments" class="text-white" /></q-item-section>
            <q-item-section><q-item-label class="text-white">Deuda detalle</q-item-label></q-item-section>
          </q-item>
        </q-expansion-item>

        <q-expansion-item
          dense
          icon="local_mall"
          label="Local"
          class="text-white"
          header-class="menu-item text-white"
          expand-icon-class="text-white"
          v-if="canLocal"
        >
          <q-item dense to="/clientes/local" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPermission('Cliente Local') || isAdmin">
            <q-item-section avatar><q-icon name="storefront" class="text-white" /></q-item-section>
            <q-item-section><q-item-label class="text-white">Cliente local</q-item-label></q-item-section>
          </q-item>
          <q-item dense to="/productos/local" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPermission('Producto Local') || isAdmin">
            <q-item-section avatar><q-icon name="shopping_bag" class="text-white" /></q-item-section>
            <q-item-section><q-item-label class="text-white">Producto local</q-item-label></q-item-section>
          </q-item>
          <q-item dense to="/ventas/local" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPermission('Venta Local') || isAdmin">
            <q-item-section avatar><q-icon name="receipt_long" class="text-white" /></q-item-section>
            <q-item-section><q-item-label class="text-white">Venta local</q-item-label></q-item-section>
          </q-item>
          <q-item dense to="/deudas/local" exact clickable class="menu-item" active-class="menu-active" v-close-popup v-if="hasPermission('Venta Local') || isAdmin">
            <q-item-section avatar><q-icon name="account_balance" class="text-white" /></q-item-section>
            <q-item-section><q-item-label class="text-white">Deuda local</q-item-label></q-item-section>
          </q-item>
        </q-expansion-item>

        <!-- ========================= -->
        <!-- GRADERÃAS (NUEVO MÃ“DULO) -->
        <!-- ========================= -->

        <!-- MIS GRADERÃAS -->
        <q-item
          dense
          to="/mis-graderias"
          exact
          clickable
          class="menu-item"
          active-class="menu-active"
          v-close-popup
          v-if="hasPermission('Graderias')"
        >
          <q-item-section avatar>
            <q-icon name="stadium" class="text-white" />
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Compras y ventas</q-item-label>
          </q-item-section>
        </q-item>

        <!-- CREAR GRADERÃA -->
        <q-item
          dense
          to="/mis-graderias/nueva"
          exact
          clickable
          class="menu-item"
          active-class="menu-active"
          v-close-popup
          v-if="hasPermission('Graderias')"
        >
          <q-item-section avatar>
            <q-icon name="add_box" class="text-white" />
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Nueva operacion</q-item-label>
          </q-item-section>
        </q-item>
<!--        cambiar contraseÃ±a-->
        <q-item
          dense
          to="/cambiar-contrasena"
          exact
          clickable
          class="menu-item"
          active-class="menu-active"
          v-close-popup
        >
          <q-item-section avatar>
            <q-icon name="vpn_key" class="text-white" />
          </q-item-section>
          <q-item-section>
            <q-item-label class="text-white">Cambiar Contraseña</q-item-label>
          </q-item-section>
        </q-item>

        <!-- FOOTER -->
        <div class="q-pa-md">
          <div class="text-white-7 text-caption">
            Naty v{{ $version }}
          </div>
          <div class="text-white-7 text-caption">
            @ {{ new Date().getFullYear() }} Sistema de Gestion Naty
          </div>
        </div>

        <q-item clickable class="text-white" @click="logout" v-close-popup>
          <q-item-section avatar>
            <q-icon name="logout" />
          </q-item-section>
          <q-item-section>
            <q-item-label>Salir</q-item-label>
          </q-item-section>
        </q-item>
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
  mounted () {
    // this.fetchMenuEventos()
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
      if (!role) return ''
      return role
    }
  },
  methods: {
    // fetchMenuEventos () {
    //   this.$axios.get('/eventosMenu')
    //     .then(res => {
    //       this.$store.menuEventosByPais = res.data.items || []
    //     })
    //     .catch(() => {
    //       this.$store.menuEventosByPais = []
    //     })
    // },
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
    hasPermission (perm) {
      return this.$store && this.$store.permissions
        ? this.$store.permissions.includes(perm)
        : false
    },
    logout () {
      this.$alert.dialog('Â¿Desea salir del sistema?')
        .onOk(() => {
          this.$axios.post('/logout')
            .then(() => {
              this.$store.isLogged = false
              this.$store.user = {}
              this.$store.permissions = []
              localStorage.removeItem('tokenNaty')
              this.$router.push('/login')
            })
            .catch(() => {
              this.$store.isLogged = false
              this.$store.user = {}
              this.$store.permissions = []
              localStorage.removeItem('tokenNaty')
              this.$router.push('/login')
            })
        })
    }
  }
}
</script>

<style scoped>
.menu-item {
  border-radius: 10px;
  margin: 4px 8px;
  padding: 4px 6px;
}
.menu-active {
  background: rgba(255, 255, 255, 0.15);
  color: #fff !important;
  border-radius: 10px;
}
:deep(.q-expansion-item__content .menu-item) {
  margin-left: 16px;
}
</style>
