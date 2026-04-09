# Running this application locally

The Laravel app lives in `backend/`. Docker Compose at the **repository root** (`logging_system/`) runs MySQL, Redis, PHP-FPM, and Nginx together.

## Docker (recommended)

**Prerequisites:** [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or Docker Engine + Compose plugin).

From the **parent directory** of `backend` (the folder that contains `docker-compose.yml`):

```bash
cd /path/to/logging_system
docker compose up --build
```

- **Web app:** [http://localhost:8080](http://localhost:8080)
- **Health check:** [http://localhost:8080/up](http://localhost:8080/up)
- **Observability ingest (this API):** `POST` [http://localhost:8080/api/v1/ingest](http://localhost:8080/api/v1/ingest)

The `app` container runs `composer install` if `vendor/` is missing and applies migrations on startup. Database and Redis settings are injected by Compose (see `docker-compose.yml`); your `backend/.env` **APP_KEY** is still read from disk, so keep a valid key there.

**Ports exposed:** `8080` (HTTP), `3306` (MySQL), `6379` (Redis). Stop the stack with `Ctrl+C` or `docker compose down`.

### MySQL InnoDB write errors (e.g. `undo_002`, “only 0 were written”, OS error 5)

That usually means the database could not write its data files—often **Docker’s virtual disk is full**, the **MySQL volume is corrupted**, or **Docker Desktop’s filesystem** is struggling with InnoDB I/O.

1. **Free space:** Docker Desktop → **Settings → Resources** — increase **Disk image size** if it is maxed out; run `docker system df` and `docker system prune` to reclaim space.
2. **Reset the MySQL volume** (deletes local DB data): from the repo root, `docker compose down -v`, then `docker compose up --build`.
3. The Compose file sets **`shm_size`** and **`--innodb-use-native-aio=0`** to reduce common Docker/MySQL InnoDB issues; pull the latest `docker-compose.yml` if yours does not.

## Without Docker (PHP on the host)

Useful for quick edits without containers.

```bash
cd backend
composer install
cp .env.example .env   # if you do not already have .env
php artisan key:generate
```

Default `.env` uses SQLite. Create the database file and migrate:

```bash
touch database/database.sqlite
php artisan migrate
php artisan serve
```

Then open [http://127.0.0.1:8000](http://127.0.0.1:8000) and use `POST http://127.0.0.1:8000/api/v1/ingest` for the ingest route defined in `api.php`.

To use MySQL/Redis on the host instead, set `DB_*` and `REDIS_*` in `.env` to match your local services and run `php artisan migrate` again.

## API routes in this folder

| File        | Purpose |
| ----------- | ------- |
| `api.php`   | JSON API, including `POST /api/v1/ingest` |
| `web.php`   | Web routes (welcome page) |
| `console.php` | Artisan-only scheduling / closures |

`api.php` is loaded with the `api` middleware and prefixed with `/api` (see `bootstrap/app.php`).
