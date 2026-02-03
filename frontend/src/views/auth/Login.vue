<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-header">
        <h1>Multi-X ERP</h1>
        <p>Sign in to your account</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label for="email">Email</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            placeholder="admin@demo.com"
          />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="password"
            type="password"
            required
            placeholder="Enter your password"
          />
        </div>

        <button type="submit" class="btn-login" :disabled="loading">
          {{ loading ? 'Signing in...' : 'Sign In' }}
        </button>

        <div v-if="error" class="error-message">
          {{ error }}
        </div>
      </form>

      <div class="demo-credentials">
        <p><strong>Demo Credentials:</strong></p>
        <p>Email: admin@demo.com</p>
        <p>Password: password</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('admin@demo.com')
const password = ref('password')
const tenantSlug = ref('demo-company')
const loading = ref(false)
const error = ref(null)

const handleLogin = async () => {
  loading.value = true
  error.value = null

  try {
    const result = await authStore.login(email.value, password.value, tenantSlug.value)
    
    if (result.success) {
      // Redirect to dashboard
      const redirect = router.currentRoute.value.query.redirect || '/dashboard'
      router.push(redirect)
    } else {
      error.value = result.message || 'Login failed. Please try again.'
    }
  } catch (err) {
    error.value = err.message || 'Login failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
}

.login-container {
  background: white;
  padding: 3rem;
  border-radius: 1rem;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 400px;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-header h1 {
  font-size: 2rem;
  color: #667eea;
  margin-bottom: 0.5rem;
}

.login-header p {
  color: #666;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: #333;
}

.form-group input {
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 0.5rem;
  font-size: 1rem;
}

.form-group input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-login {
  padding: 0.75rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-login:hover:not(:disabled) {
  background: #5568d3;
}

.btn-login:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  padding: 0.75rem;
  background: #fee;
  border: 1px solid #fcc;
  border-radius: 0.5rem;
  color: #c33;
  text-align: center;
}

.demo-credentials {
  margin-top: 2rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 0.5rem;
  font-size: 0.9rem;
  text-align: center;
}

.demo-credentials p {
  margin: 0.25rem 0;
  color: #666;
}

.demo-credentials strong {
  color: #333;
}
</style>
