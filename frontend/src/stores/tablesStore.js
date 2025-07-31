import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { tablesService } from "../services/tables";
import { is } from "date-fns/locale";
export const useTablesStore = defineStore("tables", () => {
  //STATE
  const tables = ref([]);
  const isLoading = ref(false);
  const error = ref(null);

  // Estado de paginación
  const currentPage = ref(1);
  const perPage = ref(10);
  const total = ref(0);
  const totalPages = ref(0);

  // Estado de filtros
  const filterTerm = ref('');
  const filterState = ref(0);


  //estados

  const states = [
    { status_id: 1, status_label: 'Libre', status_name: 'FREE' },
    { status_id: 2, status_label: 'Ocupada', status_name: 'OCCUPIED' },
    { status_id: 3, status_label: 'Inactiva', status_name: 'INACTIVE' },
  ];

  const setFilters = ({ term, state }) => {
    console.log("seteando filtros mesaa", term, state);

    currentPage.value = 1; // Resetear a primera página

    if (term !== undefined) filterTerm.value = term;
    if (state !== undefined) filterState.value = state;

    fetchTables();
  };

  let isFetching = false;

  const addTable = async (data) => {
      
  
      try {
        const result = await tablesService.create(data);
  
        if (result.success) {
          const newTable = result.data;
  
          if (newTable !== null && newTable !== undefined) {
            tables.value.push(newTable); //  al  del array
          }
        } else {
          throw new Error(result.message || "Error al crear la mesa");
        }
      } catch (e) {
        error.value = e;
        throw new Error(e.response?.data?.message || "Error al crear la mesa"); // Re-lanzar el error para manejarlo en el componente
      }
    };


   const editTable = async (id, data) => {
       error.value = null;
   
       try {
         const result = await tablesService.update(id, data);
   
         if (result.success) {
           fetchTables(false);
         }
       } catch (e) {
         console.log(e);
         error.value = e;
         throw new Error(e.response?.data?.message || "Error al editar la mesa"); // Re-lanzar el error para manejarlo en el componente
       }
     };


  //ACTIONS

  const fetchTables = async (
    showLoading = true,
    filters = {
      term: filterTerm.value,
      state: filterState.value !== 0 && filterState.value !== '' ? filterState.value : null,
    }
  ) => {
    if (isFetching) return; // Evitar múltiples llamadas simultáneas
    isFetching = true;
    isLoading.value = showLoading;
    error.value = true;

    const params = {
      page: currentPage.value,
      perPage: perPage.value,
      orderBy: "table_number",
      ...filters,
    };

    try {
      const data = await tablesService.getAll(params);

      tables.value = data.data || [];
      total.value = data.data.total || 0;
      totalPages.value = data.data.last_page || 0;
      // Asegurar que currentPage esté en rango válido
      if (currentPage.value > totalPages.value && totalPages.value > 0) {
        currentPage.value = totalPages.value;
      }
    } catch (e) {
      console.log(e);
      tables.value = [];
      total.value = 0;
      totalPages.value = 0;
      error.value = e;
    } finally {
    
        isFetching = false;
    
      isLoading.value = false;
    }
  };

  // --- POLLING ---
  let pollingTimeout = null;
  const startPolling = () => {
    // Limpia cualquier polling previo
    clearTimeout(pollingTimeout);

    const poll = async () => {
      await fetchTables(true, {
      term: filterTerm.value,
      state: filterState.value !== 0 ? filterState.value : null,
    }); // Reutilizas tu función
      pollingTimeout = setTimeout(poll, (120000)); // 2 minutos
    };

    poll(); // Ejecutar inmediatamente
  };

  const stopPolling = () => {
    clearTimeout(pollingTimeout);
    pollingTimeout = null;
  };

  return {
    tables,
    states,
    isLoading,
    error,
    filterTerm,
    filterState,

    fetchTables,
    setFilters,

    startPolling,
    stopPolling,

    currentPage,
    perPage,
    total,
    totalPages,



    addTable,
    editTable,
  };
});
