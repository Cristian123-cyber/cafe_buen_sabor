// src/services/analytics.js
import MonthlyRevenueChart from "../components/analytics/charts/MonthlyRevenueChart.vue";
import api from "./api.js";

export const analyticsService = {
  getDashboardSummary: async () => {
    try {
      const response = await api.get('/analytics/dashboard-summary');
     
      return response.data.data;
    } catch (error) {
      console.error("Error fetching dashboard summary:", error);
      throw error;
    }
  },

  getYearlyRevenueData: async (options = {}) => {
    try {
/* 
      const response = await api.get(`/analytics/yearly-revenue?${options.year ? `year=${options.year}` : ''}`);
      console.log("Yearly revenue data fetched:", response.data);
 */
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

  getTopProductsData: async (options = {}) => {

    try {
      /* const response = await api.get(`/analytics/top-products?limit=5&${options.period ? `period=${options.period}` : ''}`);

      console.log("Top products data fetched:", response.data); */

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

  getTopWaitersData: async (options = {}) => {
    try {
      /* const response = await api.get(`/analytics/top-waiters?${options.period ? `period=${options.period}` : ''}`);

      console.log("Top waiters data fetched:", response.data); */
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
