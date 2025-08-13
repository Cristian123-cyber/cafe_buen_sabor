import { defineStore } from "pinia";
import { ref } from "vue";
import { productService } from "../services/productService"; // Importamos el servicio

/**
 * product-store.js
 * * Gestiona el estado global de los productos y categorías en la aplicación.
 * Utiliza el `productService` para interactuar con la API y actualiza su estado interno.
 */
export const useProductStore = defineStore("products", () => {
  // --- STATE ---
  const products = ref([]);
  const categories = ref([]);
  const productTypes = ref(null);
  const ingredients = ref(null);
  const isLoading = ref(false);
  const error = ref(null);

  const filterTerm = ref("");
  const filterCategory = ref(0);

  // ✅ Getter: Función que filtra según categoría
  const getProductsByCategory = (category) => {
    if (category === "All") return products.value;

    return products.value.filter((p) => p.category_name === category);
  };

  // --- ACTIONS ---

  /**
   * Acción para cargar todos los productos desde la API.
   * Gestiona los estados de carga y error.
   */
  const fetchAllProducts = async () => {
    isLoading.value = true;
    error.value = null;
    try {
      // Llama al servicio para obtener los datos
      const data = await productService.fetchProducts();
      products.value = [
        ...data.data[0].productos_no_preparados,
        ...data.data[0].productos_preparados,
      ];
    } catch (e) {
      error.value = "No se pudieron cargar los productos.";
      console.error(e);
    } finally {
      isLoading.value = false;
    }
  };

  const fetchProducts = async (
    showLoading = true,
    filters = {
      term: filterTerm.value,
      category: filterCategory.value !== 0 ? filterCategory.value : null,
    }
  ) => {
    isLoading.value = showLoading;
    error.value = null;

    const params = {
      ...filters,
    };
    try {
      const data = await productService.fetchProducts(params);
      products.value = [
        ...data.data[0].productos_no_preparados,
        ...data.data[0].productos_preparados,
      ];
    } catch (e) {
      console.log(e);
      products.value = [];

      error.value = e;
    } finally {
      isLoading.value = false;
    }
  };

  const setFilters = ({ term, category }) => {
    if (term !== undefined) filterTerm.value = term;
    if (category !== undefined) filterCategory.value = category;

    fetchProducts();
  };

  /**
   * Acción para cargar todas las categorías desde la API.
   */
  const fetchAllCategories = async () => {
    // Podríamos tener un loading/error separado si quisiéramos
    try {
      const data = await productService.fetchCategories();
      categories.value = data.data;
      console.log(categories.value);
      categories.value.unshift({
        cantidad_productos: 0,
        category_name: "Todas",
        id_category: 0,
      });
    } catch (e) {
      console.error("No se pudieron cargar las categorías.", e);
    }
  };

  const fetchAllIngredients = async () => {
    // Podríamos tener un loading/error separado si quisiéramos
    try {
      const data = await productService.fetchIngredients();
      ingredients.value = data;
    } catch (e) {
      console.error("No se pudieron cargar las ingridents.", e);
    }
  };
  const fetchTypes = async () => {
    // Podríamos tener un loading/error separado si quisiéramos
    try {
      const data = await productService.fetchTypes();
      productTypes.value = data;
    } catch (e) {
      console.error("No se pudieron cargar las tipos.", e);
    }
  };

  /**
   * (Admin) Añade un nuevo producto.
   * @param {Object} productData - Datos del producto a crear.
   */
  const addProduct = async (productData, productImg) => {
    try {
      const newProduct = await productService.createProduct(productData);

      if (newProduct?.data?.id_product && productImg) {
        // Subo la imagen SOLO si hay producto y archivo
        const imageResult = await productService.uploadImage(
          productImg,
          newProduct.data.id_product
        );

        newProduct.data.product_image_url = imageResult?.data?.image_url;

        products.value.push(newProduct.data);
      }else{

        throw new Error("Error al crear producto");
        
        
      }
    } catch (e) {
      // El error se puede propagar al componente para mostrar una notificación
      throw new Error("Error al añadir el producto.");
    }
  };

  /**
   * (Admin) Elimina un producto.
   * @param {string|number} productId - ID del producto a eliminar.
   */
  const removeProduct = async (productId) => {
    try {
      await productService.deleteProduct(productId);
      // Filtra el producto eliminado del estado local
      products.value = products.value.filter((p) => p.id !== productId);
    } catch (e) {
      throw new Error("Error al eliminar el producto.");
    }
  };

  const getProductById = async (id) => {
    isLoading.value = true;

    try {
      const data = await productService.getProductById(id);
      console.log(data.data, "data obtenida");
      return data.data;
    } catch (error) {
      error.value = "No se pudo cargar el producto.";

      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  // Se retornan el estado, getters y acciones para que el composable los pueda usar.
  return {
    // State
    products,
    categories,
    ingredients,
    productTypes,
    isLoading,
    error,
    // Getters
    getProductsByCategory,
    // Actions
    fetchAllProducts,
    fetchProducts,
    fetchTypes,
    fetchAllCategories,
    fetchAllIngredients,
    addProduct,
    removeProduct,
    getProductById,
    filterTerm,
    filterCategory,

    setFilters,
  };
});
