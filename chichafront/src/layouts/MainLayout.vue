<template>
  <q-layout view="lHh Lpr lFf">
    <q-header>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          {{ this.$store.state.login.user.name }}
          <span style="font-size: 16px">{{$version}}</span>
        </q-toolbar-title>

        <div>
          Chicha doña nati
          <q-btn @click="logout" label="Salir" color="negative" icon="logout" v-if="$store.getters['login/isLoggedIn']"
                 dense no-caps/>
        </div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      class="bg-grey-1"
    >
      <q-list dense>
        <q-item-label
          header
          class="text-grey-8 text-bold"
        >
          Opciones del menu
        </q-item-label>
        <q-item
          v-if="$store.state.login.usuarios"
          active-class="my-menu-link"
          clickable
          exact
          to="/user"
        >
          <q-item-section
            avatar
          >
            <q-icon name="engineering" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>USUARIOS</q-item-label>
            <q-item-label caption>
              Control de usuarios
            </q-item-label>
          </q-item-section>
        </q-item>


        <q-item
          v-if="!$store.getters['login/isLoggedIn']"
          active-class="my-menu-link"
          clickable
          exact
          to="/login"
        >
          <q-item-section
            avatar
          >
            <q-icon name="login" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>LOGIN</q-item-label>
            <q-item-label caption>
              Ingreso al sistema
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item
          clickable
          @click="detalleShowing = !detalleShowing"
          v-if="$store.state.login.clientes || $store.state.login.ventadetalle || $store.state.login.historialprestamo || $store.state.login.cobrardetalle"
        >
          <q-item-section avatar>
            <q-icon name="people_alt" size="lg"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>DETALLE</q-item-label>
            <q-item-label caption>
            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-btn
              flat
              round
              dense
              icon="keyboard_arrow_down"
              @click="detalleShowing = !detalleShowing"
            >
              <q-menu v-model="detalleShowing">
                <q-list style="min-width: 100px">
                  <q-item
                    v-if="$store.state.login.clientes"
                    active-class="my-menu-link"
                    clickable
                    exact
                    to="/cliente"
                  >
                    <q-item-section
                      avatar
                    >
                      <q-icon name="people" size="lg"/>
                    </q-item-section>

                    <q-item-section>
                      <q-item-label>CLIENTES DETALLE</q-item-label>
                      <q-item-label caption>
                        Control de cLientes
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                  <q-item v-if="$store.state.login.ventadetalle" active-class="my-menu-link" clickable exact
                          to="/sale/detalle">
                    <q-item-section avatar>
                      <q-icon name="shopping_cart" size="lg"/>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>VENTA DETALLE</q-item-label>
                    </q-item-section>
                  </q-item>
                  <q-item
                    v-if="$store.state.login.historialprestamo"
                    active-class="my-menu-link"
                    clickable
                    exact
                    to="/prestamos"
                  >
                    <q-item-section
                      avatar
                    >
                      <q-icon name="task" size="lg"/>
                    </q-item-section>

                    <q-item-section>
                      <q-item-label>PRESTAMO DETALLE</q-item-label>
                      <q-item-label caption>
                        Historial de prestamos
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                  <q-item
                    v-if="$store.state.login.cobrardetalle"
                    active-class="my-menu-link"
                    clickable
                    exact
                    to="/cxcdetalle"
                  >
                    <q-item-section
                      avatar
                    >
                      <q-icon name="task" size="lg"/>
                    </q-item-section>

                    <q-item-section>
                      <q-item-label>CxC DETALLE</q-item-label>
                      <q-item-label caption>
                        Historial de ventas
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>
          </q-item-section>
        </q-item>

        <q-item
          clickable
          @click="detalleShowing2 = !detalleShowing2"
          v-if="$store.state.login.clientes || $store.state.login.ventadetalle || $store.state.login.historialprestamo || $store.state.login.cobrarlocal"
        >
          <q-item-section avatar>
            <q-icon name="task" size="lg"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>LOCAL</q-item-label>
            <q-item-label caption>

            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-btn
              flat
              round
              dense
              @click="detalleShowing2 = !detalleShowing2"
              icon="keyboard_arrow_down"
            >
              <q-menu v-model="detalleShowing2">
                <q-item
                  v-if="$store.state.login.clientes"
                  active-class="my-menu-link"
                  clickable
                  exact
                  to="/clientelocal"
                >
                  <q-item-section
                    avatar
                  >
                    <q-icon name="contact_phone" size="lg"/>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>CLIENTES LOCAL</q-item-label>
                    <q-item-label caption>
                      Control de cLientes Local
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item v-if="$store.state.login.ventadetalle" active-class="my-menu-link" clickable exact
                        to="/sale/local">
                  <q-item-section avatar>
                    <q-icon name="shopping_cart" size="lg"/>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>VENTA LOCAL</q-item-label>
                    <!--            <q-item-label caption>-->
                    <!--              Historial de ventas-->
                    <!--            </q-item-label>-->
                  </q-item-section>
                </q-item>
                <q-item
                  v-if="$store.state.login.historialprestamo"
                  active-class="my-menu-link"
                  clickable
                  exact
                  to="/prestamoslocal"
                >
                  <q-item-section
                    avatar
                  >
                    <q-icon name="task" size="lg"/>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>PRESTAMO LOCAL</q-item-label>
                    <q-item-label caption>
                      Historial de prestamos
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item
                  v-if="$store.state.login.cobrarlocal"
                  active-class="my-menu-link"
                  clickable
                  exact
                  to="/cxclocal"
                >
                  <q-item-section
                    avatar
                  >
                    <q-icon name="task" size="lg"/>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>CxC LOCAL</q-item-label>
                    <q-item-label caption>
                      Historial de venta
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-menu>
            </q-btn>
          </q-item-section>
        </q-item>
        <q-item
          v-if="$store.state.login.gastos"
          active-class="my-menu-link"
          clickable
          exact
          to="/gasto"
        >
          <q-item-section
            avatar
          >
            <q-icon name="paid" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>RESUMEN DE GASTOS Y VENTAS</q-item-label>
            <q-item-label caption>
              Control de gastos
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item
          v-if="$store.state.login.productos"
          active-class="my-menu-link"
          clickable
          exact
          to="/producto"
        >
          <q-item-section
            avatar
          >
            <q-icon name="sports_bar" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>PRODUCTOS</q-item-label>
            <q-item-label caption>
              Informacion de Productos
            </q-item-label>
          </q-item-section>
        </q-item>


        <q-item
          v-if="$store.state.login.inventario"
          active-class="my-menu-link"
          clickable
          exact
          to="/inventario"
        >
          <q-item-section
            avatar
          >
            <q-icon name="list" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>INVENTARIO</q-item-label>
            <q-item-label caption>
              Informacion de Materiales
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item
          clickable
          @click="detalleShowing4 = !detalleShowing4"
          v-if="$store.state.login.cobrarruta || $store.state.login.historialventa"
        >
          <q-item-section avatar>
            <q-icon name="task" size="lg"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>RUTAS</q-item-label>
            <q-item-label caption>

            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-btn
              flat
              round
              dense
              @click="detalleShowing4 = !detalleShowing4"
              icon="keyboard_arrow_down"
            >
              <q-menu v-model="detalleShowing4">
                <q-item
                  v-if="$store.state.login.cobrarruta"
                  active-class="my-menu-link"
                  clickable
                  exact
                  to="/cxcruta"
                >
                  <q-item-section
                    avatar
                  >
                    <q-icon name="task" size="lg"/>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>CxC RUTA</q-item-label>
                    <q-item-label caption>
                      Historial de venta
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item
                  v-if="$store.state.login.historialventa"
                  active-class="my-menu-link"
                  clickable
                  exact
                  to="/historialruta"
                >
                  <q-item-section
                    avatar
                  >
                    <q-icon name="task" size="lg"/>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>HISTORIAL DE RUTAS</q-item-label>
                    <q-item-label caption>
                      Historial de venta Ruta
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-menu>
            </q-btn>
          </q-item-section>
        </q-item>

        <q-item
          v-if="$store.state.login.historialventa"
          active-class="my-menu-link"
          clickable
          exact
          to="/historial"
        >
          <q-item-section
            avatar
          >
            <q-icon name="task" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>HISTORIAL DE VENTA</q-item-label>
            <q-item-label caption>
              Historial de venta
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="$store.state.login.empleados" active-class="my-menu-link" clickable exact to="/empleado">
          <q-item-section avatar>
            <q-icon name="people" size="lg"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>EMPLEADO</q-item-label>
            <q-item-label caption>
              Controlde empleados
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="$store.state.login.empleadoSueldo" active-class="my-menu-link" clickable exact
                to="/empleadoSueldo">
          <q-item-section avatar>
            <q-icon name="o_people" size="lg"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>EMPLEADO SUELDOS</q-item-label>
            <q-item-label caption>
              Control de sueldos
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="$store.state.login.veralmacen" active-class="my-menu-link" clickable exact to="/almacen2">
          <q-item-section avatar>
            <q-icon name="o_store" size="lg"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>ALMACEN</q-item-label>
            <q-item-label caption>
              Materiales
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item v-if="$store.state.login.veralmacen" active-class="my-menu-link" clickable exact to="/almacen3">
          <q-item-section avatar>
            <q-icon name="storefront" size="lg"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>LISTA DE COMPRAS</q-item-label>
            <q-item-label caption>
              Materiales
            </q-item-label>
          </q-item-section>
        </q-item>
        <!--        <q-item-->
        <!--          clickable-->
        <!--          @click="detalleShowing5 = !detalleShowing5"-->
        <!--          v-if="$store.state.login.veralmacen"-->
        <!--        >-->
        <!--          <q-item-section avatar>-->
        <!--            <q-icon name="o_store" size="lg" />-->
        <!--          </q-item-section>-->
        <!--          <q-item-section>-->
        <!--            <q-item-label>ALMACEN</q-item-label>-->
        <!--            <q-item-label caption>-->
        <!--            </q-item-label>-->
        <!--          </q-item-section>-->
        <!--          <q-item-section side>-->
        <!--            <q-btn-->
        <!--              flat-->
        <!--              round-->
        <!--              dense-->
        <!--              @click="detalleShowing5 = !detalleShowing5"-->
        <!--              icon="keyboard_arrow_down"-->
        <!--            >-->
        <!--              <q-menu v-model="detalleShowing5">-->
        <!--                <q-item-->
        <!--                  v-if="$store.state.login.cajachica"-->
        <!--                  active-class="my-menu-link"-->
        <!--                  clickable-->
        <!--                  exact-->
        <!--                  to="/almacen2"-->
        <!--                >-->
        <!--                  <q-item-section-->
        <!--                    avatar-->
        <!--                  >-->
        <!--                    <q-icon name="point_of_sale" size="lg" />-->
        <!--                  </q-item-section>-->

        <!--                  <q-item-section>-->
        <!--                    <q-item-label>Almacen</q-item-label>-->
        <!--                    <q-item-label caption>-->
        <!--                      Almacen de Materiales-->
        <!--                    </q-item-label>-->
        <!--                  </q-item-section>-->
        <!--                </q-item>-->
        <!--                <q-item-->
        <!--                  v-if="$store.state.login.cajageneral"-->
        <!--                  active-class="my-menu-link"-->
        <!--                  clickable-->
        <!--                  exact-->
        <!--                  to="/almacenProveedor"-->
        <!--                >-->
        <!--                  <q-item-section-->
        <!--                    avatar-->
        <!--                  >-->
        <!--                    <q-icon name="point_of_sale" size="lg" />-->
        <!--                  </q-item-section>-->

        <!--                  <q-item-section>-->
        <!--                    <q-item-label>CAJA GENERAL</q-item-label>-->
        <!--                    <q-item-label caption>-->
        <!--                      Efectivo de Caja-->
        <!--                    </q-item-label>-->
        <!--                  </q-item-section>-->
        <!--                </q-item>-->
        <!--              </q-menu>-->
        <!--            </q-btn>-->
        <!--          </q-item-section>-->
        <!--        </q-item>-->

        <q-item
          clickable
          @click="detalleShowing3 = !detalleShowing3"
          v-if="$store.state.login.cajachica || $store.state.login.cajageneral"
        >
          <q-item-section avatar>
            <q-icon name="point_of_sale" size="lg"/>
          </q-item-section>
          <q-item-section>
            <q-item-label>CAJAS</q-item-label>
            <q-item-label caption>

            </q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-btn
              flat
              round
              dense
              @click="detalleShowing3 = !detalleShowing3"
              icon="keyboard_arrow_down"
            >
              <q-menu v-model="detalleShowing3">
                <q-item
                  v-if="$store.state.login.cajachica"
                  active-class="my-menu-link"
                  clickable
                  exact
                  to="/caja"
                >
                  <q-item-section
                    avatar
                  >
                    <q-icon name="point_of_sale" size="lg"/>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>CAJA CHICA</q-item-label>
                    <q-item-label caption>
                      Efectivo de Caja
                    </q-item-label>
                  </q-item-section>
                </q-item>
                <q-item
                  v-if="$store.state.login.cajageneral"
                  active-class="my-menu-link"
                  clickable
                  exact
                  to="/cajageneral"
                >
                  <q-item-section
                    avatar
                  >
                    <q-icon name="point_of_sale" size="lg"/>
                  </q-item-section>

                  <q-item-section>
                    <q-item-label>CAJA GENERAL</q-item-label>
                    <q-item-label caption>
                      Efectivo de Caja
                    </q-item-label>
                  </q-item-section>
                </q-item>
              </q-menu>
            </q-btn>
          </q-item-section>
        </q-item>
        <q-item
          v-if="$store.state.login.reportes"
          active-class="my-menu-link"
          clickable
          exact
          to="/reporteuser"
        >
          <q-item-section
            avatar
          >
            <q-icon name="shop" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>REPORTE USUARIOS</q-item-label>
            <q-item-label caption>
              reporte de Ventas
            </q-item-label>
          </q-item-section>
        </q-item>

        <q-item
          v-if="$store.state.login.resumencontable"
          active-class="my-menu-link"
          clickable
          exact
          to="/contable"
        >
          <q-item-section
            avatar
          >
            <q-icon name="point_of_sale" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>RESUMEN CONTABLE</q-item-label>
            <q-item-label caption>
              reporte de Ingresos y Egresos
            </q-item-label>
          </q-item-section>
        </q-item>
        <q-item
          clickable
          active-class="my-menu-link"
          to="/"
          exact>
          <q-item-section
            avatar>
            <q-icon name="home" size="lg"/>
          </q-item-section>

          <q-item-section>
            <q-item-label>HOME</q-item-label>
            <q-item-label caption>
              Pagina principal
            </q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view/>
    </q-page-container>
  </q-layout>
</template>

<script>
import {defineComponent, ref} from 'vue'

export default defineComponent({
  name: 'MainLayout',

  components: {
    // EssentialLink
  },

  setup() {
    const leftDrawerOpen = ref(false)

    return {
      // essentialLinks: linksList,
      leftDrawerOpen,
      detalleShowing: ref(false),
      detalleShowing2: ref(false),
      detalleShowing3: ref(false),
      detalleShowing4: ref(false),
      detalleShowing5: ref(false),
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value
      }
    }
  },
  methods: {
    logout() {
      this.$q.loading.show()
      this.$store.dispatch('login/logout')
        .then(() => {
          this.$q.loading.hide()
          this.$router.push('/login')
        })
    }
  },
  mounted() {
  }
})
</script>
<style lang="sass">
.my-menu-link
  color: white
  background: #9C27B0
</style>
