<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
</script>

<template>
  <!-- The public gallery route (/g/:slug) renders bare, with no admin nav —
       it's the page a client opens from a shared link, not the photographer's
       tool. Everything else gets the admin layout. -->
  <template v-if="route.meta.public">
    <RouterView />
  </template>
  <template v-else>
    <div class="app-shell">
      <nav class="nav">
        <span class="brand">📸 Photography Studio</span>
        <RouterLink to="/clients">Clients</RouterLink>
        <RouterLink to="/projects">Projects</RouterLink>
      </nav>
      <main class="content">
        <RouterView />
      </main>
    </div>
  </template>
</template>
