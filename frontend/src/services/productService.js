import api from './api.js'; // Tu instancia de Axios configurada

/**
 * product-service.js
 * * Este servicio encapsula todas las llamadas a la API para la gestión de productos.
 * Su única responsabilidad es realizar las peticiones HTTP y devolver los datos o lanzar errores.
 */
export const productService = {
  /**
   * Obtiene la lista completa de productos activos.
   * Corresponde a: GET /api/products
   * @returns {Promise<Array>} Una promesa que resuelve a un array de productos.
   */
  fetchProducts: async (params) => {
    try {
      const response = await api.get('/productos', { params: params});
      return response.data;
    } catch (error) {
      console.error('Error al obtener los productos:', error.response?.data || error.message);
      throw error; // Lanza el error para que el store lo maneje
    }
  },

  /**
   * Obtiene la lista de todas las categorías de productos.
   * Corresponde a: GET /api/categories
   * @returns {Promise<Array>} Una promesa que resuelve a un array de categorías.
   */
  fetchCategories: async () => {
    try {
      const response = await api.get('/categorias-productos');
      return response.data;
    } catch (error) {
      console.error('Error al obtener las categorías:', error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * (Admin) Crea un nuevo producto en la base de datos.
   * Corresponde a: POST /api/products
   * @param {Object} productData - Los datos del nuevo producto.
   * @returns {Promise<Object>} Una promesa que resuelve al objeto del producto creado.
   */
  createProduct: async (productData) => {
    try {
      // Usualmente para archivos se usa 'multipart/form-data', pero si solo es data, JSON está bien.
      const response = await api.post('/products', productData);
      return response.data;
    } catch (error) {
      console.error('Error al crear el producto:', error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * (Admin) Actualiza un producto existente.
   * Corresponde a: PUT /api/products/:id
   * @param {string|number} id - El ID del producto a actualizar.
   * @param {Object} productData - Los nuevos datos del producto.
   * @returns {Promise<Object>} Una promesa que resuelve al objeto del producto actualizado.
   */
  updateProduct: async (id, productData) => {
    try {
      const response = await api.put(`/products/${id}`, productData);
      return response.data;
    } catch (error) {
      console.error(`Error al actualizar el producto ${id}:`, error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * (Admin) Elimina un producto.
   * Corresponde a: DELETE /api/products/:id
   * @param {string|number} id - El ID del producto a eliminar.
   * @returns {Promise<void>}
   */
  deleteProduct: async (id) => {
    try {
      const response = await api.delete(`/products/${id}`);
      return response.data; // El backend podría devolver un mensaje de éxito
    } catch (error) {
      console.error(`Error al eliminar el producto ${id}:`, error.response?.data || error.message);
      throw error;
    }
  },



  getProductById: async (id) => {

    try {
      const response = await api.get(`/productos/${id}`);
      console.warn('data individual: ', response.data);
      return response.data; // El backend podría devolver un mensaje de éxito
    } catch (error) {
      console.error(`Error al obtener el producto ${id}:`, error.response?.data || error.message);
      throw error;
    }



  }
};