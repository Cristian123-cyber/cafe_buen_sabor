<template>
    <div>
        <button @click="authStore.logout()">LOGOUT</button>
    </div>
</template>

<script setup>

import { useAuthStore } from '../../stores/authS';
const authStore = useAuthStore();

</script>

<style lang="scss" scoped>

</style>