<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { projectsApi, galleriesApi } from '../api/client'

const props = defineProps({ id: { type: [String, Number], required: true } })

const project = ref(null)
const galleries = ref([])
const loading = ref(true)
const error = ref('')

const emptyForm = { name: '', description: '', imageUrlsText: '' }
const form = ref({ ...emptyForm })
const formErrors = ref({})
const saving = ref(false)

async function loadAll() {
  loading.value = true
  error.value = ''
  try {
    const [projectData, galleryList] = await Promise.all([
      projectsApi.get(props.id),
      galleriesApi.listForProject(props.id),
    ])
    project.value = projectData
    galleries.value = galleryList
  } catch (e) {
    error.value = 'Could not load this project. Is the API running?'
  } finally {
    loading.value = false
  }
}

async function submit() {
  saving.value = true
  formErrors.value = {}
  try {
    const image_urls = form.value.imageUrlsText
      .split('\n')
      .map((line) => line.trim())
      .filter(Boolean)

    await galleriesApi.create(props.id, {
      name: form.value.name,
      description: form.value.description,
      image_urls,
    })
    form.value = { ...emptyForm }
    await loadAll()
  } catch (e) {
    if (e.response?.status === 422) {
      formErrors.value = e.response.data.errors
    } else {
      error.value = 'Something went wrong saving this gallery.'
    }
  } finally {
    saving.value = false
  }
}

function publicUrl(gallery) {
  return `${window.location.origin}/g/${gallery.slug}`
}

async function copyLink(gallery) {
  await navigator.clipboard.writeText(publicUrl(gallery))
  alert('Public gallery link copied to clipboard!')
}

onMounted(loadAll)
</script>

<template>
  <RouterLink to="/projects">&larr; Back to projects</RouterLink>

  <p v-if="error" class="error">{{ error }}</p>

  <template v-if="project">
    <h1>{{ project.title }}</h1>
    <p class="muted">
      Client: {{ project.client_name }} · {{ project.project_date }}
      <span class="status" :class="`status-${project.status}`">{{ project.status.replace('_', ' ') }}</span>
    </p>
    <p v-if="project.description">{{ project.description }}</p>

    <h2>Galleries</h2>

    <div class="card">
      <h3>Add a gallery</h3>
      <form @submit.prevent="submit">
        <label>
          Name
          <input v-model="form.name" required />
          <span v-if="formErrors.name" class="error">{{ formErrors.name[0] }}</span>
        </label>
        <label>
          Description
          <textarea v-model="form.description" rows="2"></textarea>
        </label>
        <label>
          Image URLs (one per line — real URLs or placeholders, no upload needed)
          <textarea
            v-model="form.imageUrlsText"
            rows="3"
            placeholder="https://placehold.co/800x600?text=1"
          ></textarea>
        </label>
        <div class="actions">
          <button type="submit" :disabled="saving">Add gallery</button>
        </div>
      </form>
    </div>

    <p v-if="loading">Loading galleries…</p>
    <p v-else-if="!galleries.length">No galleries yet — add one above.</p>

    <div v-for="gallery in galleries" :key="gallery.id" class="card">
      <div class="card-row">
        <div>
          <strong>{{ gallery.name }}</strong>
          <p v-if="gallery.description" class="muted">{{ gallery.description }}</p>
        </div>
        <div class="actions">
          <button class="secondary" @click="copyLink(gallery)">Copy public link</button>
        </div>
      </div>
      <p class="muted">Public link: <RouterLink :to="`/g/${gallery.slug}`">{{ publicUrl(gallery) }}</RouterLink></p>
      <div class="gallery-grid" v-if="gallery.image_urls.length">
        <img v-for="(url, i) in gallery.image_urls" :key="i" :src="url" :alt="`${gallery.name} photo ${i + 1}`" />
      </div>
    </div>
  </template>
</template>
