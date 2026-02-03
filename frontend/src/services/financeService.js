import apiClient from './api'

export default {
  // Accounts
  getAccounts(params = {}) {
    return apiClient.get('/finance/accounts', { params })
  },
  getAccount(id) {
    return apiClient.get(`/finance/accounts/${id}`)
  },
  createAccount(data) {
    return apiClient.post('/finance/accounts', data)
  },
  updateAccount(id, data) {
    return apiClient.put(`/finance/accounts/${id}`, data)
  },
  
  // Journal Entries
  getJournalEntries(params = {}) {
    return apiClient.get('/finance/journal-entries', { params })
  },
  getJournalEntry(id) {
    return apiClient.get(`/finance/journal-entries/${id}`)
  },
  createJournalEntry(data) {
    return apiClient.post('/finance/journal-entries', data)
  },
  
  // Reports
  getFinancialReports(params = {}) {
    return apiClient.get('/finance/reports', { params })
  },
  getBalanceSheet(params = {}) {
    return apiClient.get('/finance/reports/balance-sheet', { params })
  },
  getIncomeStatement(params = {}) {
    return apiClient.get('/finance/reports/income-statement', { params })
  }
}
