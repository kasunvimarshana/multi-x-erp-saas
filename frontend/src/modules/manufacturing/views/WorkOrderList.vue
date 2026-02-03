<template>
  <div class="page-container">
    <div class="page-header">
      <h1 class="page-title">WorkOrders</h1>
      <button @click="createNew" class="btn btn-primary">
        <PlusIcon class="icon" />
        Add WorkOrder
      </button>
    </div>
    
    <div class="content-card">
      <p class="text-muted">WorkOrders management interface</p>
    </div>
  </div>
</template>

<script setup>
import { PlusIcon } from '@heroicons/vue/24/outline'

const createNew = () => {
  console.log('Create WorkOrder')
}
</script>

<style scoped>
.page-container {
  max-width: 1400px;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}

.page-title {
  font-size: 28px;
  font-weight: 700;
  color: #1f2937;
}

.btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  background: #3b82f6;
  color: white;
}

.btn:hover {
  background: #2563eb;
}

.icon {
  width: 20px;
  height: 20px;
}

.content-card {
  background: white;
  padding: 40px;
  border-radius: 12px;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  text-align: center;
}

.text-muted {
  color: #9ca3af;
  font-size: 14px;
}
</style>
