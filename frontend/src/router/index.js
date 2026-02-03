import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/authStore'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('../views/auth/Login.vue'),
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    component: () => import('../layouts/DashboardLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: () => import('../views/Dashboard.vue')
      },
      // IAM Routes
      {
        path: 'iam/users',
        name: 'Users',
        component: () => import('../modules/iam/views/UserList.vue')
      },
      {
        path: 'iam/roles',
        name: 'Roles',
        component: () => import('../modules/iam/views/RoleList.vue')
      },
      {
        path: 'iam/permissions',
        name: 'Permissions',
        component: () => import('../modules/iam/views/PermissionList.vue')
      },
      // Inventory Routes
      {
        path: 'inventory/products',
        name: 'Products',
        component: () => import('../modules/inventory/views/ProductList.vue')
      },
      {
        path: 'inventory/stock-ledgers',
        name: 'StockLedgers',
        component: () => import('../modules/inventory/views/StockLedgerList.vue')
      },
      {
        path: 'inventory/stock-movements',
        name: 'StockMovements',
        component: () => import('../modules/inventory/views/StockMovementList.vue')
      },
      {
        path: 'inventory/warehouses',
        name: 'Warehouses',
        component: () => import('../modules/inventory/views/WarehouseList.vue')
      },
      // CRM Routes
      {
        path: 'crm/customers',
        name: 'Customers',
        component: () => import('../modules/crm/views/CustomerList.vue')
      },
      {
        path: 'crm/contacts',
        name: 'Contacts',
        component: () => import('../modules/crm/views/ContactList.vue')
      },
      // POS Routes
      {
        path: 'pos/quotations',
        name: 'Quotations',
        component: () => import('../modules/pos/views/QuotationList.vue')
      },
      {
        path: 'pos/sales-orders',
        name: 'SalesOrders',
        component: () => import('../modules/pos/views/SalesOrderList.vue')
      },
      {
        path: 'pos/invoices',
        name: 'Invoices',
        component: () => import('../modules/pos/views/InvoiceList.vue')
      },
      {
        path: 'pos/payments',
        name: 'Payments',
        component: () => import('../modules/pos/views/PaymentList.vue')
      },
      // Procurement Routes
      {
        path: 'procurement/suppliers',
        name: 'Suppliers',
        component: () => import('../modules/procurement/views/SupplierList.vue')
      },
      {
        path: 'procurement/purchase-orders',
        name: 'PurchaseOrders',
        component: () => import('../modules/procurement/views/PurchaseOrderList.vue')
      },
      {
        path: 'procurement/grns',
        name: 'GRNs',
        component: () => import('../modules/procurement/views/GRNList.vue')
      },
      // Manufacturing Routes
      {
        path: 'manufacturing/boms',
        name: 'BOMs',
        component: () => import('../modules/manufacturing/views/BOMList.vue')
      },
      {
        path: 'manufacturing/production-orders',
        name: 'ProductionOrders',
        component: () => import('../modules/manufacturing/views/ProductionOrderList.vue')
      },
      {
        path: 'manufacturing/work-orders',
        name: 'WorkOrders',
        component: () => import('../modules/manufacturing/views/WorkOrderList.vue')
      },
      // Finance Routes
      {
        path: 'finance/accounts',
        name: 'Accounts',
        component: () => import('../modules/finance/views/AccountList.vue')
      },
      {
        path: 'finance/journal-entries',
        name: 'JournalEntries',
        component: () => import('../modules/finance/views/JournalEntryList.vue')
      },
      {
        path: 'finance/reports',
        name: 'FinanceReports',
        component: () => import('../modules/finance/views/ReportList.vue')
      },
      // Reporting Routes
      {
        path: 'reporting/dashboards',
        name: 'ReportingDashboards',
        component: () => import('../modules/reporting/views/DashboardList.vue')
      },
      {
        path: 'reporting/analytics',
        name: 'Analytics',
        component: () => import('../modules/reporting/views/AnalyticsList.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
  } else if (to.name === 'Login' && authStore.isAuthenticated) {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router
