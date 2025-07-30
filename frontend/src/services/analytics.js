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

      const response = await api.get(`/analytics/yearly-revenue?${options.year ? `year=${options.year}` : ''}`);
      console.log("Yearly revenue data fetched:", response.data);

      return response.data.data;
    } catch (error) {
      console.log(error);
      throw error;
    }
  },

  getTopProductsData: async (options = {}) => {

    try {
      const response = await api.get(`/analytics/top-products?limit=5&${options.period ? `period=${options.period}` : ''}`);

      console.log("Top products data fetched:", response.data);

      return response.data.data;

      
    } catch (error) {
      console.log(error);
      throw error;            
    }





  },

  getTopWaitersData: async (options = {}) => {
    try {
      const response = await api.get(`/analytics/top-waiters?${options.period ? `period=${options.period}` : ''}`);

      console.log("Top waiters data fetched:", response.data);
     

      return response.data.data;
    } catch (error) {
      console.log(error);
      throw error;   
      
    }
  }
  // ... otros servicios de analytics
};
