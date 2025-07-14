import { defineStore } from "pinia";
import { ref, computed, watch } from "vue";

/**
 * cart-store.js
 * Gestiona el estado global del carrito en la aplicación y localStorage.
 */

export const useCartStore = defineStore("cartStore", () => {
  const carrito = ref([]);

  // Inicializar desde localStorage
  try {
    const cartSaved = localStorage.getItem('cart');
    if (cartSaved) {
      carrito.value = JSON.parse(cartSaved);
    }
  } catch (error) {
    console.log('Error al cargar el carrito:', error);
    carrito.value = [];
  }

  // Computadas corregidas - usar carrito.value en lugar de items.value
  const isEmpty = computed(() => {
    return carrito.value.length === 0;
  });

  const subtotal = computed(() => {
    return carrito.value.reduce((total, item) => total + (item.price * item.quantity), 0);
  });

  const totalItems = computed(() => {
    return carrito.value.reduce((total, item) => total + item.quantity, 0);
  });

  // Total incluyendo envío
  const total = computed(() => {
    return subtotal.value;
  });

  // Acciones
  const addToCart = (item) => {
    // Buscar si el producto ya está en el carrito
    const existing = carrito.value.find(p => p.product_id === item.product_id);

    if (existing) {
      // Si ya está, le sumamos la cantidad
      existing.quantity += item.quantity;
    } else {
      // Si no está, lo agregamos con la cantidad que trae
      carrito.value.push({ ...item });
    }

    saveLocalStorage(); // Guardar el estado actualizado
  };

  const removeFromCart = (productId) => {
    carrito.value = carrito.value.filter(item => item.product_id !== productId);
    saveLocalStorage();
  };

  const updateQuantity = (productId, newQuantity) => {
    const item = carrito.value.find(p => p.product_id === productId);
    if (item) {
      if (newQuantity > 0) {
        item.quantity = newQuantity;
      }
      saveLocalStorage();
    }
  };

  const clearCart = () => {
    carrito.value = [];
    localStorage.removeItem('cart');
  };

  const saveLocalStorage = () => {
    localStorage.setItem('cart', JSON.stringify(carrito.value));
  };

 

  return {
    // Estado
    carrito,
    
    // Computadas
    isEmpty,
    subtotal,
    totalItems,
    total,

    // Acciones
    addToCart,
    removeFromCart,
    clearCart,
    updateQuantity
  };
});