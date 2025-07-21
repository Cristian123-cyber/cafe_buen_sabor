// src/services/analytics.js
import MonthlyRevenueChart from "../components/analytics/charts/MonthlyRevenueChart.vue";
import api from "./api.js";

export const analyticsService = {
  getDashboardSummary: async () => {
    try {
      //const response = await api.get('/analytics/dashboard-summary');

      const response = {
        data: {
          revenue: {
            title: "Ingresos (Hoy)",
            value: "$1,777",
            trend: {
              value: 15.1,
              text: "vs ayer",
            },
          },
          totalOrders: {
            title: "Pedidos Totales",
            value: "84",
            trend: {
              value: -5.2,
              text: "vs ayer",
            },
          },
          activeTables: {
            title: "Mesas Activas",
            value: "12 / 20",
            progress: 60,
            text: "60% de ocupación",
            trend: null, // La tendencia es nula para esta métrica de tipo snapshot
          },
          averageTicket: {
            title: "Ticket Promedio",
            value: "$14.89",
            trend: {
              value: 3.2,
              text: "vs ayer",
            },
          },
        },
      };
      return response.data;
    } catch (error) {
      console.error("Error fetching dashboard summary:", error);
      throw error;
    }
  },

  getYearlyRevenueData: async (options) => {
    try {

      /* const [yearlyRevenue, topWaiters] = await Promise.all([
        api.get(`/analytics/yearly-revenue?year=${options.yearlyRevenue.year}`),
        api.get(`/analytics/top-waiters?period=${options.topWaiters.period}`),
      ]); */
      //const response = await api.get('/analytics/dashboard-summary');


      

      const response = {
        data: {
          monthlyRevenue: {
            labels: [
              "Ene",
              "Feb",
              "Mar",
              "Abr",
              "May",
              "Jun",
              "Jul",
              "Ago",
              "Sep",
              "Oct",
              "Nov",
              "Dic",
            ],
            data: [
              21050, 18300, 24100, 23500, 25600, 28900, 27500, 31200, 29800,
              33400, 35100, 42500,
            ],
          },
          

          
        },
      };

      return response.data;
    } catch (error) {
      console.log(error);
      throw error;
    }
  },

  getTopProductsData: async (options) => {

    try {
      //const response = await api.get('/analytics/dashboard-summary');

      const response = {
        data: {
          productsTop: {
            labels: [
              "Cocacola",
              "Hamburguesa",
              "Latte Caramelo",
              "Cappuccino",
              "Pastel de Chocolate",
              "Espresso Doble",
              "Té Chai",
              
            ],
            data: [1000, 500, 280, 254, 210, 198, 150 ],
          }

        }
      }

      return response.data;

      
    } catch (error) {
      console.log(error);
      throw error;            
    }





  },

  getTopWaitersData: async (options) => {
    try {
      //const response = await api.get('/analytics/dashboard-summary');

      const response = {
        data: {
          topWaiters: {
            waiters: [
              {
                rank: 1,
                name: "Ana García",
                tables_served: 142,
              },
              {
                rank: 2,
                name: "Carlos Ruiz",
                tables_served: 128,
              },
              {
                rank: 3,
                name: "Sofía López",
                tables_served: 115,
              },
              {
                rank: 4,
                name: "David Moreno",
                tables_served: 98,
              },
              {
                rank: 5,
                name: "David Moreno",
                tables_served: 98,
              },
            ],
          }
          

        }
      }

      return response.data;
    } catch (error) {
      console.log(error);
      throw error;   
      
    }
  }
  // ... otros servicios de analytics
};
