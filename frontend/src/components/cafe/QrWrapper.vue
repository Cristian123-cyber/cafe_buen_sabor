<template>
    <QrMinimal 
    :qr_token="qrToken ?? ''" 
    :tableId="tableId" 
    :timeLeft="timeLeft"
    :totalDuration="totalDuration"
    :loading="isLoading"
  />
    
</template>

<script setup>
import { onMounted } from 'vue';
import { useTableDisplay } from '../../composables/useTableQR';


const props = defineProps({
    tableId: {
        type: [String, Number],
        required: true
    }
});
const { qrToken, isLoading, error, timeLeft, totalDuration } = useTableDisplay(props.tableId);


</script>

<style lang="scss" scoped></style>