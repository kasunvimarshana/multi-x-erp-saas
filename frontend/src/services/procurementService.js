import apiClient from './api'

export default {
  // Suppliers
  getSuppliers(params = {}) {
    return apiClient.get('/procurement/suppliers', { params })
  },
  getSupplier(id) {
    return apiClient.get(`/procurement/suppliers/${id}`)
  },
  createSupplier(data) {
    return apiClient.post('/procurement/suppliers', data)
  },
  updateSupplier(id, data) {
    return apiClient.put(`/procurement/suppliers/${id}`, data)
  },
  deleteSupplier(id) {
    return apiClient.delete(`/procurement/suppliers/${id}`)
  },
  
  // Purchase Orders
  getPurchaseOrders(params = {}) {
    return apiClient.get('/procurement/purchase-orders', { params })
  },
  getPurchaseOrder(id) {
    return apiClient.get(`/procurement/purchase-orders/${id}`)
  },
  createPurchaseOrder(data) {
    return apiClient.post('/procurement/purchase-orders', data)
  },
  updatePurchaseOrder(id, data) {
    return apiClient.put(`/procurement/purchase-orders/${id}`, data)
  },
  
  // GRNs
  getGRNs(params = {}) {
    return apiClient.get('/procurement/grns', { params })
  },
  getGRN(id) {
    return apiClient.get(`/procurement/grns/${id}`)
  },
  createGRN(data) {
    return apiClient.post('/procurement/grns', data)
  }
}
