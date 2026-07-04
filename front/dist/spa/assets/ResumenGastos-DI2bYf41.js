import{Q as y}from"./QSpace-CVl3L71R.js";import{_ as v,c as x,o as p,w as i,a,b as l,Q as r,e as m,f,aa as w,a9 as T,ag as _,ap as E,t as d,a8 as g}from"./index-DQ5bRCRF.js";import{Q as u,a as c}from"./QTable-B-0GnEzx.js";import{Q as S}from"./format-ZVyYXXb9.js";import{Q as C}from"./QPage-2o6D8a81.js";import{I as L}from"./Imprimir-CB5jjQ9t.js";import"./QList-SxxwMeeI.js";import"./_commonjsHelpers-CqkleIqs.js";const A={name:"ResumenGastos",data(){const n=new Date().toISOString().slice(0,10);return{loading:!1,ventas:[],movimientos:[],pagos:[],totales:{total_ventas:0,total_pagado:0,total_saldo:0,total_gastos:0},paginationVentas:{page:1,rowsPerPage:25},paginationMovimientos:{page:1,rowsPerPage:25},paginationPagos:{page:1,rowsPerPage:25},filters:{date_from:n,date_to:n,one_day:!0},colsVentas:[{name:"id",label:"Id",field:"id",align:"left",sortable:!0},{name:"fecha",label:"Fecha",field:"fecha",align:"left"},{name:"tipo_venta",label:"Tipo",field:"tipo_venta",align:"left"},{name:"cliente_nombre",label:"Cliente",field:"cliente_nombre",align:"left"},{name:"total",label:"Total",field:"total",align:"right",sortable:!0},{name:"total_pagado",label:"A cuenta",field:"total_pagado",align:"right"},{name:"saldo_pendiente",label:"Saldo",field:"saldo_pendiente",align:"right"},{name:"usuario",label:"Usuario",field:t=>t.user?.name||"-",align:"left"},{name:"observacion",label:"Observacion",field:"observacion",align:"left"}],colsPagos:[{name:"id",label:"Id",field:"id",align:"left"},{name:"created_at",label:"Fecha/Hora",field:"created_at",align:"left",format:t=>t?String(t).slice(0,16).replace("T"," "):"-"},{name:"tipo_venta",label:"Tipo",field:"tipo_venta",align:"left"},{name:"cliente_nombre",label:"Cliente",field:"cliente_nombre",align:"left"},{name:"monto",label:"Monto",field:"monto",align:"right"},{name:"metodo",label:"Metodo",field:"metodo",align:"left"},{name:"observacion",label:"Observacion",field:"observacion",align:"left"},{name:"usuario",label:"Usuario",field:"usuario",align:"left"}],colsMovimientos:[{name:"id",label:"Id",field:"id",align:"left"},{name:"created_at",label:"Fecha/Hora",field:"created_at",align:"left"},{name:"monto_real",label:"Monto",field:"monto_real",align:"right"},{name:"observacion",label:"Glosa / Observacion",field:"observacion",align:"left"},{name:"usuario",label:"Usuario",field:"usuario",align:"left"}]}},mounted(){this.cargar()},methods:{money(n){return Number(n||0).toFixed(2)},cargar(){this.loading=!0;const n=this.filters.one_day?this.filters.date_from:this.filters.date_to;this.$axios.get("cajas/resumen-gastos",{params:{date_from:this.filters.date_from,date_to:n}}).then(t=>{this.ventas=t.data.ventas||[],this.movimientos=t.data.movimientos||[],this.pagos=t.data.pagos||[],this.totales=t.data.totales||{total_ventas:0,total_pagado:0,total_saldo:0,total_gastos:0}}).catch(t=>this.$alert.error(t.response?.data?.message||"No se pudo cargar el resumen")).finally(()=>{this.loading=!1})},imprimir(){const n=this.filters.one_day?`Fecha: ${this.filters.date_from}`:`Desde: ${this.filters.date_from} Hasta: ${this.filters.one_day?this.filters.date_from:this.filters.date_to}`,t=this.ventas.map(e=>`
        <tr>
          <td>${e.id}</td>
          <td>${e.fecha||""}</td>
          <td>${e.tipo_venta==="local"?"LOCAL":"DETALLE"}</td>
          <td>${e.cliente_nombre||"-"}</td>
          <td style="text-align:right">${this.money(e.total)}</td>
          <td style="text-align:right">${this.money(e.total_pagado)}</td>
          <td style="text-align:right">${this.money(e.saldo_pendiente)}</td>
          <td>${e.user?.name||"-"}</td>
        </tr>`).join(""),h=this.pagos.map(e=>`
        <tr>
          <td>${e.id}</td>
          <td>${e.created_at?String(e.created_at).slice(0,16).replace("T"," "):""}</td>
          <td>${e.tipo_venta==="local"?"LOCAL":"DETALLE"}</td>
          <td>${e.cliente_nombre||"-"}</td>
          <td style="text-align:right; color:green"><b>${this.money(e.monto)}</b></td>
          <td>${e.metodo||"-"}</td>
          <td>${e.observacion||"-"}</td>
          <td>${e.usuario||"-"}</td>
        </tr>`).join(""),b=this.movimientos.map(e=>`
        <tr>
          <td>${e.id}</td>
          <td>${e.created_at?String(e.created_at).slice(0,16).replace("T"," "):""}</td>
          <td style="text-align:right; color:red">${this.money(e.monto_real)}</td>
          <td>${e.observacion||"-"}</td>
          <td>${e.usuario||"-"}</td>
        </tr>`).join(""),o=`
        <div style="font-family:Arial,sans-serif; font-size:12px; padding:10px;">
          <h2 style="text-align:center; margin-bottom:4px;">RESUMEN DE GASTOS Y VENTAS</h2>
          <p style="text-align:center; margin:0;">${n}</p>
          <hr/>
          <h3>VENTAS DETALLE Y LOCAL</h3>
          <table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11px;">
            <thead style="background:#eee;">
              <tr>
                <th>Id</th><th>Fecha</th><th>Tipo</th><th>Cliente</th>
                <th>Total</th><th>A cuenta</th><th>Saldo</th><th>Usuario</th>
              </tr>
            </thead>
            <tbody>${t||'<tr><td colspan="8" style="text-align:center">Sin ventas</td></tr>'}</tbody>
          </table>
          <br/>
          <h3>PAGOS / COBROS DEL PERIODO</h3>
          <table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11px;">
            <thead style="background:#eee;">
              <tr><th>Id</th><th>Fecha/Hora</th><th>Tipo</th><th>Cliente</th><th>Monto</th><th>Metodo</th><th>Observacion</th><th>Usuario</th></tr>
            </thead>
            <tbody>${h||'<tr><td colspan="8" style="text-align:center">Sin pagos</td></tr>'}</tbody>
          </table>
          <br/>
          <h3>DETALLE DE GASTOS / EGRESOS DE CAJA</h3>
          <table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:11px;">
            <thead style="background:#eee;">
              <tr><th>Id</th><th>Fecha/Hora</th><th>Monto</th><th>Observacion</th><th>Usuario</th></tr>
            </thead>
            <tbody>${b||'<tr><td colspan="5" style="text-align:center">Sin gastos</td></tr>'}</tbody>
          </table>
          <br/>
          <table border="0" cellpadding="4" width="300" style="font-size:13px; float:right;">
            <tr><td><b>Total ventas:</b></td><td style="text-align:right"><b>${this.money(this.totales.total_ventas)} Bs</b></td></tr>
            <tr><td><b>Total ingresado:</b></td><td style="text-align:right"><b>${this.money(this.totales.total_pagado)} Bs</b></td></tr>
            <tr><td><b>Total gastos:</b></td><td style="text-align:right; color:red"><b>${this.money(this.totales.total_gastos)} Bs</b></td></tr>
            <tr style="border-top:2px solid #333;"><td><b>Efectivo neto:</b></td><td style="text-align:right; color:teal"><b>${this.money(this.totales.total_pagado-this.totales.total_gastos)} Bs</b></td></tr>
          </table>
        </div>`;document.getElementById("myElement").innerHTML=o,L.printTicketHtml(o)}}},V={class:"row q-col-gutter-md q-mb-md items-center"},O={class:"col-12 col-md-2"},P={key:0,class:"col-12 col-md-2"},B={class:"col-12 col-md-2"},Q={class:"row q-col-gutter-md q-mb-lg"},k={class:"col-6 col-md-3"},q={class:"text-h6 text-weight-bold text-primary"},D={class:"col-6 col-md-3"},I={class:"text-h6 text-weight-bold text-positive"},M={class:"col-6 col-md-3"},U={class:"text-h6 text-weight-bold text-negative"},G={class:"col-6 col-md-3"},F={class:"text-h6 text-weight-bold text-teal"};function N(n,t,h,b,o,e){return p(),x(C,{class:"q-pa-md"},{default:i(()=>[a(r,{flat:"",bordered:"",class:"q-mb-md"},{default:i(()=>[a(m,{class:"row items-center bg-orange-1"},{default:i(()=>[t[5]||(t[5]=l("div",null,[l("div",{class:"text-h6"},"Resumen de Gastos y Ventas"),l("div",{class:"text-caption text-grey-7"},"Reporte consolidado del periodo")],-1)),a(y),a(f,{flat:"","no-caps":"",icon:"print",color:"teal",label:"Imprimir",class:"q-mr-sm",onClick:e.imprimir,disable:o.loading},null,8,["onClick","disable"]),a(f,{flat:"","no-caps":"",icon:"search",color:"primary",label:"Buscar",onClick:e.cargar,loading:o.loading},null,8,["onClick","loading"])]),_:1})]),_:1}),l("div",V,[l("div",O,[a(_,{modelValue:o.filters.date_from,"onUpdate:modelValue":t[0]||(t[0]=s=>o.filters.date_from=s),type:"date",dense:"",outlined:"",label:o.filters.one_day?"Fecha":"Desde"},null,8,["modelValue","label"])]),o.filters.one_day?T("",!0):(p(),w("div",P,[a(_,{modelValue:o.filters.date_to,"onUpdate:modelValue":t[1]||(t[1]=s=>o.filters.date_to=s),type:"date",dense:"",outlined:"",label:"Hasta"},null,8,["modelValue"])])),l("div",B,[a(E,{modelValue:o.filters.one_day,"onUpdate:modelValue":t[2]||(t[2]=s=>o.filters.one_day=s),dense:"",label:"Un solo dia"},null,8,["modelValue"])])]),l("div",Q,[l("div",k,[a(r,{flat:"",bordered:""},{default:i(()=>[a(m,{class:"q-pa-sm"},{default:i(()=>[t[6]||(t[6]=l("div",{class:"text-caption text-grey-7"},"Total ventas",-1)),l("div",q,d(e.money(o.totales.total_ventas))+" Bs",1)]),_:1})]),_:1})]),l("div",D,[a(r,{flat:"",bordered:""},{default:i(()=>[a(m,{class:"q-pa-sm"},{default:i(()=>[t[7]||(t[7]=l("div",{class:"text-caption text-grey-7"},"Total ingresado",-1)),l("div",I,d(e.money(o.totales.total_pagado))+" Bs",1)]),_:1})]),_:1})]),l("div",M,[a(r,{flat:"",bordered:""},{default:i(()=>[a(m,{class:"q-pa-sm"},{default:i(()=>[t[8]||(t[8]=l("div",{class:"text-caption text-grey-7"},"Total gastos",-1)),l("div",U,d(e.money(o.totales.total_gastos))+" Bs",1)]),_:1})]),_:1})]),l("div",G,[a(r,{flat:"",bordered:""},{default:i(()=>[a(m,{class:"q-pa-sm"},{default:i(()=>[t[9]||(t[9]=l("div",{class:"text-caption text-grey-7"},"Efectivo neto",-1)),l("div",F,d(e.money(o.totales.total_pagado-o.totales.total_gastos))+" Bs",1)]),_:1})]),_:1})])]),t[10]||(t[10]=l("div",{class:"text-subtitle2 text-weight-bold q-mb-sm"},"Pagos / Cobros del Periodo",-1)),a(u,{dense:"",flat:"",bordered:"",rows:o.pagos,columns:o.colsPagos,"row-key":"id",loading:o.loading,pagination:o.paginationPagos,"onUpdate:pagination":t[3]||(t[3]=s=>o.paginationPagos=s),"rows-per-page-options":[10,25,50,0],class:"q-mb-lg"},{"body-cell-monto":i(s=>[a(c,{props:s,class:"text-right text-positive text-weight-bold"},{default:i(()=>[g(d(e.money(s.row.monto)),1)]),_:2},1032,["props"])]),"body-cell-tipo_venta":i(s=>[a(c,{props:s},{default:i(()=>[a(S,{dense:"",color:s.row.tipo_venta==="local"?"green-7":"blue-7","text-color":"white",size:"sm"},{default:i(()=>[g(d(s.row.tipo_venta==="local"?"LOCAL":"DETALLE"),1)]),_:2},1032,["color"])]),_:2},1032,["props"])]),_:1},8,["rows","columns","loading","pagination"]),t[11]||(t[11]=l("div",{class:"text-subtitle2 text-weight-bold q-mb-sm"},"Gastos / Egresos de Caja del Periodo",-1)),a(u,{dense:"",flat:"",bordered:"",rows:o.movimientos,columns:o.colsMovimientos,"row-key":"id",loading:o.loading,pagination:o.paginationMovimientos,"onUpdate:pagination":t[4]||(t[4]=s=>o.paginationMovimientos=s),"rows-per-page-options":[10,25,50,0]},{"body-cell-monto_real":i(s=>[a(c,{props:s,class:"text-right text-negative text-weight-bold"},{default:i(()=>[g(d(e.money(s.row.monto_real)),1)]),_:2},1032,["props"])]),_:1},8,["rows","columns","loading","pagination"]),t[12]||(t[12]=l("div",{id:"myElement",class:"hidden"},null,-1))]),_:1})}const W=v(A,[["render",N]]);export{W as default};
