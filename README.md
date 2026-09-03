# Photography Client Management API

A small full-stack app for photographers to manage clients, photography projects, and galleries — and to deliver a finished gallery to a client through a public, no-login link, the way Pixieset actually delivers photos.

Built as a focused portfolio project to get hands-on with PHP/Laravel and Vue, using a REST API backed by PostgreSQL and a Vue 3 SPA frontend.

## Tech stack

- **Backend:** PHP 8.3 + Laravel 11
- **Database:** PostgreSQL 16
- **Frontend:** Vue 3 (Composition API) + Vue Router + Axios
- **API:** REST, JSON
- **Local dev:** Docker Compose (Postgres, Laravel via `artisan serve`, Vue via Vite dev server)

There's no login/authentication in this app by design — see [Architecture](#architecture) for why, and [Future improvements](#future-improvements) for where auth would go if this became a real product.

## Main features

- **Clients** — create, view, edit, delete. Fields: name, email, phone, notes.
- **Projects** — create, view, edit, delete. Each project belongs to a client and has a title, description, date, and status (`pending` / `in_progress` / `completed`).
- **Galleries** — create and view, scoped to a project. Each gallery holds a name, description, and a list of image URLs (no real upload/storage — URLs or placeholder URLs only, kept simple on purpose).
- **Public gallery links** — every gallery gets a random, unguessable slug (e.g. `/g/xK9pQ2mNc4Rt`). Anyone with that link can view the gallery — no account, no login. This is the core idea the project is built around: a photographer manages everything privately, then hands a client one link to their photos.

## Architecture

```
┌────────────┐        REST/JSON        ┌──────────────┐        ┌────────────┐
│  Vue SPA   │ ──────────────────────► │  Laravel API │ ─────► │ PostgreSQL │
│ (Vite dev  │ ◄────────────────────── │ (routes/api  │        │            │
│  server)   │                         │   .php)      │        └────────────┘
└────────────┘                         └──────────────┘
```

- The Vue app and Laravel API are two separate applications, talking over REST — the frontend never touches the database directly.
- **No authentication anywhere.** Every admin endpoint (clients/projects/galleries) is open. This was a deliberate scope decision for a one-day project: it removes an entire layer (registration, login, token handling, per-user data scoping) that would otherwise dominate the build, without adding anything that demonstrates Laravel/Vue skill better than the core CRUD + relationships already do. In a real deployment, the admin routes would sit behind [Laravel Sanctum](https://laravel.com/docs/sanctum) — the public gallery route (`/api/public/galleries/{slug}`) would stay open either way, since it's meant to be shared.
- **Data model:** `Client 1—* Project 1—* Gallery`. A client has many projects; a project belongs to one client and has many galleries; a gallery belongs to one project.
- **Public vs. admin responses:** the same `Gallery` model is serialized two different ways — `GalleryResource` (full detail, for the admin UI) and `PublicGalleryResource` (name/description/images/project title only) for the public link, so a shared link can't leak client contact info or internal IDs.

## Running it locally

Requires [Docker](https://www.docker.com/) (Docker Desktop, or the Docker CLI + Compose plugin) — nothing else needs to be installed on your machine.

```bash
git clone <this-repo>
cd photography-client-management

# Backend env file
cp backend/.env.example backend/.env

# Build and start Postgres, the Laravel API, and the Vue dev server
docker compose up --build -d

# One-time setup: generate the app key and create the schema (with demo data)
docker compose exec backend php artisan key:generate
docker compose exec backend php artisan migrate --seed
```

- Frontend: http://localhost:5173
- API: http://localhost:8000/api
- Postgres: `localhost:5432` (user/pass/db: `photography` / `photography` / `photography`)

Run the backend's test suite (in-memory SQLite, no Postgres needed):

```bash
docker compose exec backend php artisan test
```

Stop everything with `docker compose down` (add `-v` to also drop the Postgres volume and start fresh).

## Example API endpoints

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/clients` | List clients |
| POST | `/api/clients` | Create a client |
| PUT | `/api/clients/{id}` | Update a client |
| DELETE | `/api/clients/{id}` | Delete a client (cascades to their projects/galleries) |
| GET | `/api/projects` | List projects, with client name and gallery count |
| POST | `/api/projects` | Create a project (`client_id`, `title`, `project_date`, ...) |
| GET | `/api/projects/{id}/galleries` | List galleries for a project |
| POST | `/api/projects/{id}/galleries` | Create a gallery under a project |
| GET | `/api/public/galleries/{slug}` | **Public**, unauthenticated — view a gallery by its share link |

All list/detail responses are wrapped in Laravel API Resources (`{"data": ...}`); validation errors return `422` with a Laravel-standard `{"errors": {...}}` body.

## What I learned

- How Laravel structures a request end-to-end: route → Form Request (validation) → controller → Eloquent model → API Resource (response shaping) — and why splitting those responsibilities up (rather than validating and building the response inline in the controller) keeps controllers thin and easy to read.
- Eloquent relationships (`hasMany`/`belongsTo`) and how `foreignId()->constrained()->cascadeOnDelete()` in a migration keeps referential integrity at the database level, not just in application code.
- Model events (`static::creating()` in the `Gallery` model) as a way to guarantee an invariant — every gallery gets a slug — no matter which code path creates the row, instead of relying on every caller to remember to set it.
- Designing two different API Resources for the same model (`GalleryResource` vs. `PublicGalleryResource`) as a deliberate way to control exactly what an unauthenticated audience can see, rather than filtering fields ad hoc in the controller.
- Vue's Composition API (`ref`, `onMounted`) for simple local component state, and `vue-router`'s route `meta` field for a small but real architectural decision: giving the public gallery page its own bare layout instead of the admin shell.
- Wiring Docker Compose for a decoupled frontend/backend/database setup, including the common pitfall of a bind-mounted source directory shadowing dependencies installed at image-build time (solved with named volumes for `vendor/` and `node_modules/`).

## Future improvements

- **Photographer authentication** via Laravel Sanctum, scoping clients/projects/galleries to the logged-in photographer — the natural next step if this stopped being single-tenant.
- **Real image upload/storage** (e.g. to S3 or local disk via Laravel's `Storage` facade) instead of URL-only galleries.
- **Client-side proofing** on the public gallery page — letting a client favorite/select images, mirroring how Pixieset's actual proofing feature works.
- **Pagination** on the clients/projects list endpoints once data volume grows past a demo-sized dataset.
- **Expiring or revocable share links**, rather than a slug that's valid forever once generated.
