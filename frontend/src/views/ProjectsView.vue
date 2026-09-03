<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { clientsApi, projectsApi } from '../api/client'

const projects = ref([])
const clients = ref([])
const loading = ref(true)
const error = ref('')

const emptyForm = { client_id: '', title: '', description: '', project_date: '', status: 'pending' }
const form = ref({ ...emptyForm })
const editingId = ref(null)
const formErrors = ref({})
const saving = ref(false)

async function loadAll() {
  loading.value = true
  error.value = ''
  try {
    const [projectList, clientList] = await Promise.all([projectsApi.list(), clientsApi.list()])
    projects.value = projectList
    clients.value = clientList
  } catch (e) {
    error.value = 'Could not load projects. Is the API running?'
  } finally {
    loading.value = false
  }
}

function startEdit(project) {
  editingId.value = project.id
  form.value = {
    client_id: project.client_id,
    title: project.title,
    description: project.description || '',
    project_date: project.project_date,
    status: project.status,
  }
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
      await projectsApi.update(editingId.value, form.value)
    } else {
      await projectsApi.create(form.value)
    }
    cancelEdit()
    await loadAll()
  } catch (e) {
    if (e.response?.status === 422) {
      formErrors.value = e.response.data.errors
    } else {
      error.value = 'Something went wrong saving this project.'
    }
  } finally {
    saving.value = false
  }
}

async function remove(project) {
  if (!confirm(`Delete "${project.title}"? This also deletes its galleries.`)) return
  await projectsApi.remove(project.id)
  await loadAll()
}

onMounted(loadAll)
</script>

<template>
  <h1>Projects</h1>
  <p v-if="error" class="error">{{ error }}</p>
  <p v-if="!loading && !clients.length">
    You need a <RouterLink to="/clients">client</RouterLink> before you can create a project.
  </p>

  <div class="card" v-if="clients.length">
    <h3>{{ editingId ? 'Edit project' : 'Add a project' }}</h3>
    <form @submit.prevent="submit">
      <label>
        Client
        <select v-model="form.client_id" required>
          <option disabled value="">Select a client</option>
          <option v-for="c in clients" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <span v-if="formErrors.client_id" class="error">{{ formErrors.client_id[0] }}</span>
      </label>
      <label>
        Title
        <input v-model="form.title" required />
        <span v-if="formErrors.title" class="error">{{ formErrors.title[0] }}</span>
      </label>
      <label>
        Description
        <textarea v-model="form.description" rows="2"></textarea>
      </label>
      <label>
        Project date
        <input v-model="form.project_date" type="date" required />
        <span v-if="formErrors.project_date" class="error">{{ formErrors.project_date[0] }}</span>
      </label>
      <label>
        Status
        <select v-model="form.status">
          <option value="pending">Pending</option>
          <option value="in_progress">In progress</option>
          <option value="completed">Completed</option>
        </select>
      </label>
      <div class="actions">
        <button type="submit" :disabled="saving">{{ editingId ? 'Save changes' : 'Add project' }}</button>
        <button v-if="editingId" type="button" class="secondary" @click="cancelEdit">Cancel</button>
      </div>
    </form>
  </div>

  <p v-if="loading">Loading projects…</p>
  <p v-else-if="!projects.length && clients.length">No projects yet — add your first one above.</p>

  <div v-for="project in projects" :key="project.id" class="card card-row">
    <div>
      <RouterLink :to="`/projects/${project.id}`"><strong>{{ project.title }}</strong></RouterLink>
      <span class="status" :class="`status-${project.status}`">{{ project.status.replace('_', ' ') }}</span>
      <div class="muted">{{ project.client_name }} · {{ project.project_date }}</div>
      <p v-if="project.description">{{ project.description }}</p>
      <span class="muted">{{ project.galleries_count }} gallery(ies)</span>
    </div>
    <div class="actions">
      <button class="secondary" @click="startEdit(project)">Edit</button>
      <button class="danger" @click="remove(project)">Delete</button>
    </div>
  </div>
</template>
