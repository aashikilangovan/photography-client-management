import { createRouter, createWebHistory } from 'vue-router'
import ClientsView from '../views/ClientsView.vue'
import ProjectsView from '../views/ProjectsView.vue'
import ProjectDetailView from '../views/ProjectDetailView.vue'
import PublicGalleryView from '../views/PublicGalleryView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', redirect: '/clients' },
    { path: '/clients', name: 'clients', component: ClientsView },
    { path: '/projects', name: 'projects', component: ProjectsView },
    { path: '/projects/:id', name: 'project-detail', component: ProjectDetailView, props: true },
    // Public, unauthenticated share link — rendered with no admin nav
    // (see App.vue), same as how Pixieset delivers a gallery to a client.
    { path: '/g/:slug', name: 'public-gallery', component: PublicGalleryView, props: true, meta: { public: true } },
  ],
})

export default router
