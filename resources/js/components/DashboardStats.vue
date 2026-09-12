<template>
    <div class="row g-4 mb-4">
        <!-- Vue component structure mirroring the blade layout -->
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted small fw-semibold">Cafe Orders (Today)</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2 fw-bold text-dark">{{ ordersCount }}</h3>
                            </div>
                            <p class="mb-0 small mt-1 text-primary">Live updates active</p>
                        </div>
                        <div class="avatar bg-label-primary rounded p-2" style="width: 45px; height: 45px;">
                            <i class="bx bx-shopping-bag bx-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="text-muted small fw-semibold">Revenue (Today)</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2 fw-bold text-success">Rs {{ revenue }}</h3>
                            </div>
                            <p class="mb-0 small mt-1 text-success">Realtime revenue</p>
                        </div>
                        <div class="avatar bg-label-success rounded p-2" style="width: 45px; height: 45px;">
                            <i class="bx bx-dollar bx-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const ordersCount = ref(0);
const revenue = ref(0);

onMounted(() => {
    // Simulate real-time fetch or use props passed from blade
    ordersCount.value = 42;
    revenue.value = 15400;
});
</script>

<style scoped>
.avatar {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
