<script setup>
import { ref, onMounted } from 'vue'
import { galleriesApi } from '../api/client'

const props = defineProps({ slug: { type: String, required: true } })

const gallery = ref(null)
const loading = ref(true)
const notFound = ref(false)

onMounted(async () => {
  try {
    gallery.value = await galleriesApi.getPublic(props.slug)
  } catch (e) {
    if (e.response?.status === 404) {
      notFound.value = true
    }
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="public-gallery">
    <p v-if="loading">Loading gallery…</p>
    <p v-else-if="notFound">This gallery link doesn't exist or may have been removed.</p>
    <template v-else-if="gallery">
      <p class="muted">{{ gallery.project_title }}</p>
      <h1>{{ gallery.name }}</h1>
      <p v-if="gallery.description">{{ gallery.description }}</p>
      <div class="gallery-grid" v-if="gallery.image_urls.length">
        <img v-for="(url, i) in gallery.image_urls" :key="i" :src="url" :alt="`${gallery.name} photo ${i + 1}`" />
      </div>
      <p v-else class="muted">No photos have been added to this gallery yet.</p>
    </template>
  </div>
</template>
