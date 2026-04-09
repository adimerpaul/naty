/*
export function someMutation (state) {
}
*/
export function auth_request(state){
  state.status = 'loading'
}
export function auth_success(state, data){
  state.status = 'success'
  state.token = data.token
  state.user = data.user
  state.usuarios=data.user.permisos.find(p=>p.id===1)!=undefined
  state.clientes=data.user.permisos.find(p=>p.id===2)!=undefined
  state.productos=data.user.permisos.find(p=>p.id===3)!=undefined
  state.inventario=data.user.permisos.find(p=>p.id===4)!=undefined
  state.ventadetalle=data.user.permisos.find(p=>p.id===5)!=undefined
  state.historialventadetalle=data.user.permisos.find(p=>p.id===6)!=undefined
  state.ventalocal=data.user.permisos.find(p=>p.id===7)!=undefined
  state.historialventalocal=data.user.permisos.find(p=>p.id===8)!=undefined
  state.empleados=data.user.permisos.find(p=>p.id===9)!=undefined
  state.reportes=data.user.permisos.find(p=>p.id===10)!=undefined
  state.gastos=data.user.permisos.find(p=>p.id===11)!=undefined
  state.historialprestamo=data.user.permisos.find(p=>p.id===12)!=undefined
  state.historialventa=data.user.permisos.find(p=>p.id===13)!=undefined
  state.anularventa=data.user.permisos.find(p=>p.id===14)!=undefined
  state.ruta=data.user.permisos.find(p=>p.id===15)!=undefined
  state.anularprestamo=data.user.permisos.find(p=>p.id===16)!=undefined
  state.reimpresion=data.user.permisos.find(p=>p.id===17)!=undefined
  state.eliminargasto=data.user.permisos.find(p=>p.id===18)!=undefined
  state.cobrardetalle=data.user.permisos.find(p=>p.id===19)!=undefined
  state.cobrarlocal=data.user.permisos.find(p=>p.id===20)!=undefined
  state.cobrarruta=data.user.permisos.find(p=>p.id===21)!=undefined
  state.cajachica=data.user.permisos.find(p=>p.id===22)!=undefined
  state.editalmacen=data.user.permisos.find(p=>p.id===23)!=undefined
  state.editproducto=data.user.permisos.find(p=>p.id===24)!=undefined
  state.editinventario=data.user.permisos.find(p=>p.id===25)!=undefined
  state.editglosa=data.user.permisos.find(p=>p.id===26)!=undefined
  state.delprestamo=data.user.permisos.find(p=>p.id===27)!=undefined
  state.veralmacen=data.user.permisos.find(p=>p.id===28)!=undefined
  state.pagoalmacen=data.user.permisos.find(p=>p.id===29)!=undefined
  state.cajageneral=data.user.permisos.find(p=>p.id===30)!=undefined
  state.gastoreporteuser=data.user.permisos.find(p=>p.id===31)!=undefined
  state.reportepago=data.user.permisos.find(p=>p.id===32)!=undefined
  state.resumencontable=data.user.permisos.find(p=>p.id===33)!=undefined
  state.delpago=data.user.permisos.find(p=>p.id===34)!=undefined
  state.preciounitario=data.user.permisos.find(p=>p.id===35)!=undefined
  state.ingresoMaterial=data.user.permisos.find(p=>p.id===36)!=undefined
  state.egresoMaterial=data.user.permisos.find(p=>p.id===37)!=undefined
  state.registroMateriaPrima=data.user.permisos.find(p=>p.id===38)!=undefined
  state.registroProveedor=data.user.permisos.find(p=>p.id===39)!=undefined
  state.registroProducto=data.user.permisos.find(p=>p.id===40)!=undefined
  state.almacenCostoSubtotal=data.user.permisos.find(p=>p.id===41)!=undefined
  state.almacenCrearMateriaPrima= data.user.permisos.find(p=>p.id===42)!=undefined
  state.almacenHistorialPago=data.user.permisos.find(p=>p.id===43)!=undefined
  state.empleadoSueldo=data.user.permisos.find(p=>p.id===44)!=undefined
  state.editcajachica=data.user.permisos.find(p=>p.id===45)!=undefined
  state.editcliente=data.user.permisos.find(p=>p.id===46)!=undefined
}
export function   auth_error(state){
  state.status = 'error'
}
export function salir(state){
  state.status = ''
  state.token = ''
  state.usuarios=false
  state.clientes=false
  state.productos=false
  state.inventario=false
  state.ventadetalle=false
  state.historialventadetalle=false
  state.ventalocal=false
  state.historialventalocal=false
  state.empleados=false
  state.reportes=false
  state.gastos=false
  state.historialprestamo=false
  state.historialventa=false
  state.anularventa=false
  state.ruta=false
  state.anularprestamo=false
  state.reimpresion=false
  state.eliminargasto=false
  state.cobrardetalle=false
  state.cobrarlocal=false
  state.cobrarruta=false
  state.cajachica=false
  state.editalmacen=false
  state.editproducto=false
  state.editinventario=false
  state.editglosa=false
  state.delprestamo=false
  state.veralmacen=false
  state.pagoalmacen=false
  state.cajageneral=false
  state.gastoreporteuser=false
  state.reportepago=false
  state.resumencontable=false
  state.delpago=false
  state.preciounitario=false
  state.ingresoMaterial=false
  state.egresoMaterial=false
  state.registroMateriaPrima=false
  state.registroProveedor=false
  state.registroProducto=false
  state.almacenCostoSubtotal=false
  state.almacenCrearMateriaPrima= false
  state.almacenHistorialPago=false
  state.empleadoSueldo=false
  state.editcajachica=false
}
