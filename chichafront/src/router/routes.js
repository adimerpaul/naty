import Login from 'pages/Login';
import Cliente from "pages/Cliente";
import Clientelocal from "pages/Clientelocal";
import Producto from "pages/Producto";
import Venta from "pages/Venta";
import Garantia from "pages/Garantia";
import Inventario from "pages/Inventario";
import Empledo from "pages/Empledo";
import Ventadirecta from "pages/Ventadirecta";
import Ventalocal from "pages/Ventalocal";
import Reporteuser from "pages/Reporteuser";
import User from "pages/User";
import Gasto from "pages/Gasto";
import Ventadetalle from "pages/Ventadetalle";
import Prestamos from "pages/Prestamos";
import Prestamoslocal from "pages/Prestamoslocal";
import Historial from "pages/Historial";
import Cxcdetalle from "pages/Cxcdetalle";
import Cxclocal from "pages/Cxclocal";
import Cxcruta from "pages/Cxcruta";
import Historialruta from "pages/Historialruta";
import Caja from "pages/Caja";
import Cajageneral from "pages/Cajageneral";
import Planillas from "pages/Planillas";
import Almacen from "pages/Almacen";
import Contable from "pages/Contable";
import Sale from "pages/Sale";
import Almacen2 from "pages/Almacen2.vue";
import Almacen3 from "pages/Almacen3.vue";
// <<<<<<< HEAD
// import User from "pages/User";
// =======
// import Usuario from "pages/Usuario";
// import Gasto from "pages/Gasto";
// >>>>>>> a64c39b0b8007f2636b9bda84ee6276a638bee19

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/Index.vue') },
      { path: 'login', component: Login },
      { path: 'cliente', component: Cliente,meta: {requiresAuth: true} },
      { path: 'clientelocal', component: Clientelocal,meta: {requiresAuth: true} },
      { path: 'producto', component: Producto,meta: {requiresAuth: true} },
      { path: 'venta', component: Ventadetalle,meta: {requiresAuth: true} },
      // { path: 'ventadirecta', component: Ventadirecta,meta: {requiresAuth: true} },
      { path: 'ventalocal', component: Ventalocal,meta: {requiresAuth: true} },
      { path: 'garantia', component: Garantia,meta: {requiresAuth: true} },
      { path: 'inventario', component: Inventario,meta: {requiresAuth: true} },
      { path: 'empleado', component: Empledo,meta: {requiresAuth: true} },
      // empleadoSueldo
      { path: 'empleadosueldo', component: () => import('pages/EmpleadoSueldos.vue'),meta: {requiresAuth: true} },
      { path: 'reporteuser', component: Reporteuser,meta: {requiresAuth: true} },
      { path: 'user', component: User,meta: {requiresAuth: true} },
      { path: 'gasto', component: Gasto,meta: {requiresAuth: true} },
      { path: 'prestamos', component: Prestamos,meta: {requiresAuth: true} },
      { path: 'prestamoslocal', component: Prestamoslocal,meta: {requiresAuth: true} },
      { path: 'historial', component: Historial,meta: {requiresAuth: true} },
      { path: 'cxcdetalle', component: Cxcdetalle,meta: {requiresAuth: true} },
      { path: 'cxclocal', component: Cxclocal,meta: {requiresAuth: true} },
      { path: 'cxcruta', component: Cxcruta,meta: {requiresAuth: true} },
      { path: 'historialruta', component: Historialruta,meta: {requiresAuth: true} },
      { path: 'caja', component: Caja,meta: {requiresAuth: true} },
      { path: 'cajageneral', component: Cajageneral,meta: {requiresAuth: true} },
      { path: 'planillas', component: Planillas,meta: {requiresAuth: true} },
      { path: 'almacen', component: Almacen,meta: {requiresAuth: true} },
      { path: 'almacen2', component: Almacen2,meta: {requiresAuth: true} },
      { path: 'almacen3', component: Almacen3,meta: {requiresAuth: true} },
      { path: 'contable', component: Contable,meta: {requiresAuth: true} },
      { path: 'sale/:type', component: Sale,meta: {requiresAuth: true} },

// <<<<<<< HEAD
//       { path: 'user', component: User,meta: {requiresAuth: true} },
// =======
//       { path: 'usuario', component: Usuario,meta: {requiresAuth: true} },
//       { path: 'gasto', component: Gasto,meta: {requiresAuth: true} },
// >>>>>>> a64c39b0b8007f2636b9bda84ee6276a638bee19
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
