<script setup>
import { ref, onMounted } from 'vue'
import { clientsApi } from '../api/client'

const clients = ref([])
const loading = ref(true)
const error = ref('')

const emptyForm = { name: '', email: '', phone: '', notes: '' }
const form = ref({ ...emptyForm })
const editingId = ref(null)
const formErrors = ref({})
const saving = ref(false)

async function loadClients() {
  loading.value = true
  error.value = ''
  try {
    clients.value = await clientsApi.list()
  } catch (e) {
    error.value = 'Could not load clients. Is the API running?'
  } finally {
    loading.value = false
  }
}

function startEdit(client) {
  editingId.value = client.id
  form.value = { name: client.name, email: client.email, phone: client.phone || '', notes: client.notes || '' }
  formErrors.value = {}
}

function cancelEdit() {
  editingId.value = null
  form.value = { ...emptyForm }
  formErrors.value = {}
}

async function submit() {
  saving.value = true
  formErrors.value = {}
  try {
    if (editingId.value) {
      await clientsApi.update(editingId.value, form.value)
    } else {
      await clientsApi.create(form.value)
    }
    cancelEdit()
    await loadClients()
  } catch (e) {
    if (e.response?.status === 422) {
      formErrors.value = e.response.data.errors
    } else {
      error.value = 'Something went wrong saving this client.'
    }
  } finally {
    saving.value = false
  }
}

async function remove(client) {
  if (!confirm(`Delete ${client.name}? This also deletes their projects and galleries.`)) return
  await clientsApi.remove(client.id)
  await loadClients()
}

onMounted(loadClients)
</script>

<template>
  <h1>Clients</h1>
  <p v-if="error" class="error">{{ error }}</p>

  <div class="card">
    <h3>{{ editingId ? 'Edit client' : 'Add a client' }}</h3>
    <form @submit.prevent="submit">
      <label>
        Name
        <input v-model="form.name" required />
        <span v-if="formErrors.name" class="error">{{ formErrors.name[0] }}</span>
      </label>
      <label>
        Email
        <input v-model="form.email" type="email" required />
        <span v-if="formErrors.email" class="error">{{ formErrors.email[0] }}</span>
      </label>
      <label>
        Phone
        <input v-model="form.phone" />
      </label>
      <label>
        Notes
        <textarea v-model="form.notes" rows="2"></textarea>
      </label>
      <div class="actions">
        <button type="submit" :disabled="saving">{{ editingId ? 'Save changes' : 'Add client' }}</button>
        <button v-if="editingId" type="button" class="secondary" @click="cancelEdit">Cancel</button>
      </div>
    </form>
  </div>

  <p v-if="loading">Loading clients…</p>
  <p v-else-if="!clients.length">No clients yet — add your first one above.</p>

  <div v-for="client in clients" :key="client.id" class="card card-row">
    <div>
      <strong>{{ client.name }}</strong>
      <div class="muted">{{ client.email }}<span v-if="client.phone"> · {{ client.phone }}</span></div>
      <p v-if="client.notes">{{ client.notes }}</p>
      <span class="muted">{{ client.projects_count }} project(s)</span>
    </div>
    <div class="actions">
      <button class="secondary" @click="startEdit(client)">Edit</button>
      <button class="danger" @click="remove(client)">Delete</button>
    </div>
  </div>
</template>
