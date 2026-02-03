<template>
  <div class="product-list">
    <header class="page-header">
      <h1>Products</h1>
      <router-link to="/inventory/products/create" class="btn-primary">
        ➕ Add Product
      </router-link>
    </header>

    <div class="page-content">
      <div class="filters">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search products..."
          class="search-input"
          @input="debouncedSearch"
        />
      </div>

      <div v-if="loading" class="loading">
        Loading products...
      </div>

      <div v-else-if="error" class="error">
        {{ error }}
      </div>

      <div v-else-if="products.length === 0" class="empty-state">
        <p>No products found.</p>
        <router-link to="/inventory/products/create" class="btn-primary">
          Create your first product
        </router-link>
      </div>

      <div v-else class="product-grid">
        <div
          v-for="product in products"
          :key="product.id"
          class="product-card"
        >
          <h3>{{ product.name }}</h3>
          <p class="product-sku">SKU: {{ product.sku }}</p>
          <p class="product-type">{{ product.type }}</p>
          <div class="product-price">
            <span class="label">Price:</span>
            <span class="value">${{ product.selling_price }}</span>
          </div>
          <div class="product-actions">
            <router-link
              :to="`/inventory/products/${product.id}`"
              class="btn-view"
            >
              View
            </router-link>
            <router-link
              :to="`/inventory/products/${product.id}/edit`"
              class="btn-edit"
            >
              Edit
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useProductStore } from '../../../stores/productStore'

const productStore = useProductStore()

const searchQuery = ref('')
const loading = ref(false)
const error = ref(null)
const products = ref([])

// Mock data for demonstration
const mockProducts = [
  {
    id: 1,
    name: 'Sample Product 1',
    sku: 'PROD-001',
    type: 'inventory',
    selling_price: 99.99
  },
  {
    id: 2,
    name: 'Sample Product 2',
    sku: 'PROD-002',
    type: 'service',
    selling_price: 149.99
  }
]

onMounted(async () => {
  loading.value = true
  try {
    // For demo, using mock data
    // In production: await productStore.fetchProducts()
    // products.value = productStore.products
    
    products.value = mockProducts
  } catch (err) {
    error.value = 'Failed to load products'
  } finally {
    loading.value = false
  }
})

const debouncedSearch = () => {
  // Implement search functionality
  console.log('Searching for:', searchQuery.value)
}
</script>

<style scoped>
.product-list {
  flex: 1;
  background: #f5f7fa;
}

.page-header {
  background: white;
  padding: 1.5rem 2rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.page-header h1 {
  font-size: 1.75rem;
  color: #333;
}

.btn-primary {
  padding: 0.75rem 1.5rem;
  background: #667eea;
  color: white;
  text-decoration: none;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: #5568d3;
}

.page-content {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.filters {
  margin-bottom: 2rem;
}

.search-input {
  width: 100%;
  max-width: 400px;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.5rem;
  font-size: 1rem;
}

.search-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.loading,
.error,
.empty-state {
  text-align: center;
  padding: 3rem;
  background: white;
  border-radius: 0.5rem;
}

.error {
  color: #dc3545;
}

.empty-state p {
  margin-bottom: 1rem;
  color: #666;
}

.product-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.product-card {
  background: white;
  padding: 1.5rem;
  border-radius: 0.5rem;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s;
}

.product-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.product-card h3 {
  margin-bottom: 0.5rem;
  color: #333;
}

.product-sku,
.product-type {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 0.25rem;
}

.product-price {
  margin: 1rem 0;
  padding-top: 1rem;
  border-top: 1px solid #eee;
}

.product-price .label {
  color: #666;
  margin-right: 0.5rem;
}

.product-price .value {
  font-size: 1.25rem;
  font-weight: bold;
  color: #667eea;
}

.product-actions {
  display: flex;
  gap: 0.5rem;
  margin-top: 1rem;
}

.btn-view,
.btn-edit {
  flex: 1;
  padding: 0.5rem;
  text-align: center;
  text-decoration: none;
  border-radius: 0.25rem;
  font-size: 0.9rem;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-view {
  background: #f8f9fa;
  color: #667eea;
}

.btn-view:hover {
  background: #e9ecef;
}

.btn-edit {
  background: #667eea;
  color: white;
}

.btn-edit:hover {
  background: #5568d3;
}
</style>
