import axios from 'axios'

// Base URL points at the Laravel API. This runs in the browser, so it always
// talks to the backend's host-mapped port (localhost:8000) — Docker service
// names only resolve container-to-container, not from the browser. Override
// with VITE_API_URL if you expose the backend on a different port.
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: { 'Content-Type': 'application/json' },
})

export const clientsApi = {
  list: () => api.get('/clients').then((r) => r.data.data),
  create: (payload) => api.post('/clients', payload).then((r) => r.data.data),
  update: (id, payload) => api.put(`/clients/${id}`, payload).then((r) => r.data.data),
  remove: (id) => api.delete(`/clients/${id}`),
}

export const projectsApi = {
  list: () => api.get('/projects').then((r) => r.data.data),
  get: (id) => api.get(`/projects/${id}`).then((r) => r.data.data),
  create: (payload) => api.post('/projects', payload).then((r) => r.data.data),
  update: (id, payload) => api.put(`/projects/${id}`, payload).then((r) => r.data.data),
  remove: (id) => api.delete(`/projects/${id}`),
}

export const galleriesApi = {
  listForProject: (projectId) =>
    api.get(`/projects/${projectId}/galleries`).then((r) => r.data.data),
  create: (projectId, payload) =>
    api.post(`/projects/${projectId}/galleries`, payload).then((r) => r.data.data),
  getPublic: (slug) => api.get(`/public/galleries/${slug}`).then((r) => r.data.data),
}

export default api
