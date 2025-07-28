import { defineStore } from "pinia";
import { computed, ref } from "vue";
import { employeService } from "../services/employees";

export const useEmployeStore = defineStore("employees", () => {
  //STATE
  const employees = ref([]);
  const roles = ref([]);
  const states = ref([]);
  const filterTerm = ref(null);
  const filterRol = ref(null);
  const filterState = ref(null);

  const isLoading = ref(false);
  const error = ref(null);

  // Estado de paginación
  const currentPage = ref(1);
  const perPage = ref(10);
  const total = ref(0);
  const totalPages = ref(0);

  const setFilters = ({ term, role, state }) => {
    console.log("seteando filtros", term, role, state);

    currentPage.value = 1; // Resetear a primera página

    if (term !== undefined) filterTerm.value = term;
    if (role !== undefined) filterRol.value = role;
    if (state !== undefined) filterState.value = state;

    fetchEmployees();
  };

  //ACTIONS

  const fetchEmployees = async (
    filters = {
      term: filterTerm.value,
      role: filterRol.value !== 0 ? filterRol.value : null,
      state: filterState.value !== 0 ? filterState.value : null,
    }
  ) => {
    isLoading.value = true;
    error.value = null;

    const params = {
      page: currentPage.value,
      perPage: perPage.value,
      orderBy: "created_date",
      ...filters,
    };
    try {
      const data = await employeService.getAll(params);
      employees.value = data.data.employees || [];

      total.value = data.data.total || 0;
      totalPages.value = data.data.last_page || 0;
      // Asegurar que currentPage esté en rango válido
      if (currentPage.value > totalPages.value && totalPages.value > 0) {
        currentPage.value = totalPages.value;
      }
    } catch (e) {
      console.log(e);
      employees.value = [];
      total.value = 0;
      totalPages.value = 0;
      error.value = e;
    } finally {
      isLoading.value = false;
    }
  };

  // Cambiar página
  const setPage = async (page) => {
    if (page !== currentPage.value) {
      currentPage.value = page;
      await fetchEmployees();
    }
  };

  // Cambiar elementos por página
  const setPerPage = async (newPerPage) => {
    if (newPerPage !== perPage.value) {
      perPage.value = newPerPage;
      currentPage.value = 1; // Resetear a primera página
      await fetchEmployees();
    }
  };

  // Resetear paginación
  const resetPagination = async () => {
    currentPage.value = 1;
    perPage.value = 10;
    total.value = 0;
    totalPages.value = 0;
    await fetchEmployees();
  };

  const fetchRoles = async () => {
    try {
      const data = await employeService.getRoles();
      roles.value = data.data;
    } catch (e) {
      console.log(e);
      error.value = e;
    }
  };
  const fetchStates = async () => {
    try {
      const data = await employeService.getStates();
      states.value = data.data;
    } catch (e) {
      console.log(e);
      error.value = e;
    }
  };

  const addEmploye = async (data) => {
    isLoading.value = true;
    error.value = null;

    try {
      const result = await employeService.create(data);

      if (result.success) {
        const newEmployee = result.data;

        if (newEmployee !== null && newEmployee !== undefined) {
          employees.value.unshift(newEmployee); // Agregar al inicio del array
        }
      } else {
        throw new Error(result.message || "Error al crear empleado");
      }
    } catch (e) {
      error.value = e;
      throw new Error(e.response?.data?.message || "Error al crear empleado"); // Re-lanzar el error para manejarlo en el componente
    } finally {
      isLoading.value = false;
    }
  };

  const editEmploye = async (id, data) => {
    isLoading.value = true;
    error.value = null;

    try {
      const result = await employeService.update(id, data);

      if (result.success) {
        //employees.value.push(data);
        fetchEmployees();
      }
    } catch (e) {
      console.log(e);
      error.value = e;
    } finally {
      isLoading.value = false;
    }
  };

  const deleteEmploye = async (id) => {
    isLoading.value = true;
    error.value = null;

    try {
      const result = await employeService.delete(id);

      if (result.success) {
        //employees.value.push(data);
        fetchEmployees();
      }
    } catch (e) {
      console.log(e);
      throw new Error(e.response?.data?.message || "Error al eliminar empleado");

    } finally {
      isLoading.value = false;
    }
  };

  return {
    filterTerm,
    filterRol,
    employees,
    isLoading,
    error,
    roles,
    states,

    setFilters,

    addEmploye,
    editEmploye,
    fetchEmployees,
    deleteEmploye,
    fetchRoles,
    fetchStates,

    currentPage,
    perPage,
    total,
    totalPages,

    setPage,
    setPerPage,
    resetPagination,
  };
});
