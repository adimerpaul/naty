<template>
  <q-page class="q-pa-xs">
    <br>
    <div class="">
      <q-badge class="full-width text-h5 flex flex-center" color="orange" text-color="black"
               v-for="(r,index) in cliente" :key="index"><b>FECHA: &nbsp;</b>{{ r.dia }}/{{ r.mes }} &nbsp;&nbsp;<b>LOCAL:
        &nbsp;</b>{{ r.local }} &nbsp;&nbsp;<b>TITULAR: &nbsp;</b>{{ r.titular }} &nbsp;&nbsp;<b>TELEFONO:
        &nbsp;</b>{{ r.telefono }}
      </q-badge>
    </div>
    <br>
    <div class="">
      <div>
        <q-badge class="full-width text-h6 flex flex-center" color="orange" text-color="black">
          <b>CLIENTES QUE CUMPLEN AÑOS EN LOS PRÓXIMOS 15 DÍAS</b>
        </q-badge>
      </div>
      <q-markup-table flat dense bordered>
        <thead>
        <tr>
          <th class="">CLIENTE</th>
          <th class="">FECHA NACIMIENTO</th>
          <th class="">DIAS PARA CUMPLE</th>
          <th class="">LOCAL</th>
        </tr>
        </thead>
        <tbody>
        <tr
          v-for="cliente in clienteCumple"
          :key="cliente.cliente"
          :class="cliente.dias_para_cumple === 0 ? 'bg-red text-white' : ''"
        >
          <td>{{ cliente.cliente }}</td>
          <td>
            {{ cliente.fechanac }}
          </td>
          <td class="text-right">
          <span :class="cliente.dias_para_cumple < 5 ? 'text-red' : 'text-green text-bold'">
            {{ cliente.dias_para_cumple }}
          </span>
          </td>
          <td>{{ cliente.local }}</td>
        </tr>
        </tbody>
      </q-markup-table>
      <!--      <pre>{{clienteCumple}}</pre>-->
      <!--      [-->
      <!--      {-->
      <!--      "cliente": "AMALIA CUBA MIER",-->
      <!--      "fechanac": "1965-05-17",-->
      <!--      "dias_para_cumple": 10,-->
      <!--      "ventas": [-->
      <!--      {-->
      <!--      "fecha": "2024-12-25",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2024-12-24",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2024-12-16",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2024-12-12",-->
      <!--      "total": 800-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2024-09-28",-->
      <!--      "total": 600-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2024-07-26",-->
      <!--      "total": 1200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2024-07-13",-->
      <!--      "total": 200-->
      <!--      }-->
      <!--      ]-->
      <!--      },-->
      <!--      {-->
      <!--      "cliente": "ANA GUTIERREZ",-->
      <!--      "fechanac": "1977-05-10",-->
      <!--      "dias_para_cumple": 3,-->
      <!--      "ventas": [-->
      <!--      {-->
      <!--      "fecha": "2025-05-05",-->
      <!--      "total": 400-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2025-05-03",-->
      <!--      "total": 800-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2025-05-01",-->
      <!--      "total": 800-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2025-05-01",-->
      <!--      "total": 800-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2025-04-28",-->
      <!--      "total": 0-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2025-04-28",-->
      <!--      "total": 400-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2025-04-27",-->
      <!--      "total": 1000-->
      <!--      }-->
      <!--      ]-->
      <!--      },-->
      <!--      {-->
      <!--      "cliente": "AURORA HUAYLLAS LOPEZ",-->
      <!--      "fechanac": "1994-05-11",-->
      <!--      "dias_para_cumple": 4,-->
      <!--      "ventas": [-->
      <!--      {-->
      <!--      "fecha": "2022-11-11",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2022-10-30",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2022-10-20",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2022-10-16",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2022-09-29",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2022-09-24",-->
      <!--      "total": 200-->
      <!--      },-->
      <!--      {-->
      <!--      "fecha": "2022-09-17",-->
      <!--      "total": 200-->
      <!--      }-->
      <!--      ]-->
      <!--      },-->
    </div>
  </q-page>
</template>

<script>


export default {
  data() {
    return {
      name: 'PageIndex',
      cliente: [],
      clienteCumple: [],
      loading: false,
    };
  },
  created() {
    this.$axios.get(process.env.API + "/aniver").then(res => {
      console.log(res.data)
      this.cliente = res.data;
    })
    this.cumple3Get();
  },
  methods: {
    cumple3Get() {
      this.loading = true;
      this.$axios.get(process.env.API + "/cumple3").then(res => {
        console.log(res.data)
        this.clienteCumple = res.data;
      }).finally(() => {
        this.loading = false;
      });
    }
  },
};
</script>
