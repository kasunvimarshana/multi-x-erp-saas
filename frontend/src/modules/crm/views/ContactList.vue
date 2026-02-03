<template>
  <div class="page-container">
    <div class="page-header">
      <h1 class="page-title">Contacts</h1>
      <button @click="showCreateModal = true" class="btn btn-primary">
        <PlusIcon class="icon" />
        Add Contact
      </button>
    </div>
    
    <DataTable
      :columns="columns"
      :data="contacts"
      :loading="loading"
      :current-page="currentPage"
      :total-pages="totalPages"
      @page-change="handlePageChange"
    >
      <template #actions="{ row }">
        <div class="action-buttons">
          <button @click="viewItem(row)" class="btn-icon" title="View">
            <EyeIcon class="icon" />
          </button>
          <button @click="editItem(row)" class="btn-icon" title="Edit">
            <PencilIcon class="icon" />
          </button>
          <button @click="deleteItemConfirm(row)" class="btn-icon" title="Delete">
            <TrashIcon class="icon" />
          </button>
        </div>
      </template>
    </DataTable>
    
    <Modal
      :show="showCreateModal"
      title="Add Contact"
      @close="showCreateModal = false"
      @confirm="handleCreate"
    >
      <p class="text-muted">Form fields for Contact will appear here</p>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { PlusIcon, PencilIcon, TrashIcon, EyeIcon } from '@heroicons/vue/24/outline'
import DataTable from '../../../components/common/DataTable.vue'
import Modal from '../../../components/common/Modal.vue'
import { useCRMStore } from '../../../stores/crmStore'

const store = useCRMStore()
const showCreateModal = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)

const columns = [
  { key: 'id', label: 'ID', width: '80px' },
  { key: 'name', label: 'Name', sortable: true },
  { key: 'slug', label: 'Slug', sortable: true }
]

const contacts = computed(() => store.contacts)
const loading = computed(() => store.loading)

onMounted(() => {
  fetchItems()
})

const fetchItems = async () => {
  await store.fetchContacts({ page: currentPage.value })
}

const handlePageChange = (page) => {
  currentPage.value = page
  fetchItems()
}

const handleCreate = async () => {
  showCreateModal.value = false
}

const viewItem = (item) => {
  console.log('View:', item)
}

const editItem = (item) => {
  console.log('Edit:', item)
}

const deleteItemConfirm = async (item) => {
  if (confirm('Are you sure you want to delete this item?')) {
    // await store.deleteContact(item.id)
  }
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
  transition: all 0.2s;
  border: none;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
}

.icon {
  width: 20px;
  height: 20px;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.btn-icon {
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px;
  color: #6b7280;
  transition: color 0.2s;
  border-radius: 4px;
}

.btn-icon:hover {
  color: #3b82f6;
  background: #eff6ff;
}

.text-muted {
  color: #9ca3af;
  font-size: 14px;
}
</style>
