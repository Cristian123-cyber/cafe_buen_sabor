// src/stores/analytics.js
import { defineStore } from "pinia";
import { ref } from "vue";
import { analyticsService } from "../services/analytics";

export const useAnalyticsStore = defineStore("analytics", () => {
  const summary = ref(null);
  const yearlyRevenue = ref(null);
  const productsTop = ref(null);
  const topWaiters = ref(null);

  const isLoading = ref(false);
  const error = ref(null);

  const loadingYearlyRevenue = ref(false);
  const errorYearlyRevenue = ref(null);
  const loadingTopWaiters = ref(false);
  const errorTopWaiters = ref(null);
  const loadingTopProducts = ref(false);
  const errorTopProducts = ref(null);

  const fetchDashboardData = async () => {
    isLoading.value = true;
    error.value = null;

    await new Promise((resolve) => setTimeout(resolve, 1000));
    try {
      const [summaryResult, yealyRevenueResult, topWaitersResult, topProductsResult ] = await Promise.all([
        analyticsService.getDashboardSummary(),
        analyticsService.getYearlyRevenueData(),
        analyticsService.getTopWaitersData(),
        analyticsService.getTopProductsData()
      ]);

      

      summary.value = summaryResult;
      yearlyRevenue.value = yealyRevenueResult;
      productsTop.value = topProductsResult;
      topWaiters.value = topWaitersResult.waiters;
    } catch (e) {
      error.value = "No se pudieron cargar las métricas.";
    } finally {
      isLoading.value = false;
    }
  };

  const fetchSummary = async () => {
    isLoading.value = true;
    error.value = null;
    try {
      const data = await analyticsService.getDashboardSummary();
      summary.value = data;
    } catch (error) {
      error.value = "Error al cargar el resumen del dashboard.";
      console.error(error);
    } finally {
      isLoading.value = false;
    }
  };

  const fetchYearlyRevenueData = async (options) => {
    loadingYearlyRevenue.value = true;
    await new Promise((resolve) => setTimeout(resolve, 1000));

    try {
      const data = await analyticsService.getYearlyRevenueData(options);

      

      yearlyRevenue.value = data;
      
    } catch (error) {
      yearlyRevenue.value = null;
      errorYearlyRevenue.value = error;
      console.log(error);
      
    } finally {
      loadingYearlyRevenue.value = false;
    }



  };
  const fetchTopProductsData = async (options) => {
    loadingTopProducts.value = true;
    await new Promise((resolve) => setTimeout(resolve, 1000));

    try {
      const data = await analyticsService.getTopProductsData(options);
     

      productsTop.value = data;
      
    } catch (error) {
      productsTop.value = null;
      errorTopProducts.value = error;
      console.log(error);
      
    } finally {
      loadingTopProducts.value = false;
    }



  };
  const fetchTopWaiters = async (options) => {
    loadingTopWaiters.value = true;
    await new Promise((resolve) => setTimeout(resolve, 1000));

    try {
      const data = await analyticsService.getTopWaitersData(options);
     

      topWaiters.value = data.waiters;
      
    } catch (error) {
      topWaiters.value = null;
      errorTopWaiters.value = error;
      console.log(error);
      
    } finally {
      loadingTopWaiters.value = false;
    }
  };

  return {
    summary,
    yearlyRevenue,
    productsTop,
    topWaiters,
    isLoading,
    loadingYearlyRevenue,
    loadingTopWaiters,
    loadingTopProducts,
    error,
    errorTopProducts,
    errorTopWaiters,
    errorYearlyRevenue,
    fetchDashboardData,
    fetchYearlyRevenueData,
    fetchTopProductsData,
    fetchTopWaiters,
    fetchSummary,


  };
});
