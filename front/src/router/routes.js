const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/IndexPage.vue'), meta: {requiresAuth: true} },
      {
        path: '/usuarios',
        component: () => import('pages/usuarios/Usuarios.vue'),
        meta: {requiresAuth: true, perm: 'Usuarios'}
      },
      // to="/cambiar-contrasena"
      {
        path:'/cambiar-contrasena',
        component: () => import('pages/cambiar-contrasena/CambiarContrasena.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/clientes/:tipo(detalle|local)',
        component: () => import('pages/clientes/Clientes.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/productos/:tipo(detalle|local)',
        component: () => import('pages/productos/Productos.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/inventarios',
        component: () => import('pages/inventarios/Inventarios.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/cajas',
        component: () => import('pages/cajas/Cajas.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/personal',
        component: () => import('pages/personal/Personal.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/ventas/:tipo(detalle|local)',
        component: () => import('pages/ventas/Ventas.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/deudas/:tipo(detalle|local)',
        component: () => import('pages/ventas/DeudasDetalle.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/ventas/:tipo(detalle|local)/nueva',
        component: () => import('pages/ventas/VentaNueva.vue'),
        meta: { requiresAuth: true }
      }

    ]
  },
  {
    path: '/login',
    component: () => import('layouts/Login.vue'),
  },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
