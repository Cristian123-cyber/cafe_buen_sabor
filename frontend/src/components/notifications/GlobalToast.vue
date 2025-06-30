<script setup>
import { useNotificationStore } from '../../stores/notificationsS';
import { computed } from 'vue';

const notificationStore = useNotificationStore();

const notificationClasses = (type) => {
  return {
    'bg-green-500': type === 'success',
    'bg-red-500': type === 'error',
    'bg-blue-500': type === 'info',
    'bg-yellow-500': type === 'warning',
  };
};
</script>

<template>
  <div class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-2">
    <transition-group name="list">
      <div 
        v-for="notification in notificationStore.notifications"
        :key="notification.id"
        class="text-white px-4 py-3 rounded-md shadow-lg max-w-sm"
        :class="notificationClasses(notification.type)"
        role="alert"
      >
        <p>{{ notification.message }}</p>
        <i-ic-baseline-backspace 
          class="cursor-pointer text-white hover:text-gray-200 transition-colors"
          @click="notificationStore.removeNotification(notification.id)"
        ></i-ic-baseline-backspace>
      </div>
    </transition-group>
  </div>
</template>

<style scoped>
.list-enter-active, .list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from, .list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>