# Knot Server

[![Tests](https://github.com/knotworks/knot-server/workflows/run-tests/badge.svg?branch=master)](https://github.com/knotworks/knot-server/actions)
[![License: BSD-3-Clause](https://img.shields.io/badge/license-BSD--3--Clause-blue.svg)](LICENSE)

### A quieter place for private social sharing

Private social networking for families and small groups, packaged as a self-hosted Laravel API. 🪢

Knot exists for people who want a calmer place to share photos, updates, reactions, and everyday moments without handing that experience to a public social platform. This repository is the server-side core: authentication, timelines, media processing, friendships, notifications, and the supporting services needed to run a private network on your own infrastructure.

> ✨ Backend only. If you want a starting point for a client application, take a look at the [sample Knot client](https://github.com/knotworks/knot-client).

---

## Social, But Smaller

Knot is a self-hosted social layer for close-knit communities. It is designed around private sharing, familiar social primitives, and an architecture that stays out of the way of whatever client experience you want to build on top of it.

This repository provides the API server for that experience:

- session-based authentication powered by Laravel Sanctum
- a timeline built from a user and their friendships
- posts with media, comments, reactions, and accompaniments
- notification delivery backed by Laravel notifications
- background processing for uploaded media and cleanup tasks

## Why People Reach for It

| Benefit | What it means |
| --- | --- |
| Private by design | Built for families and small groups rather than public discovery. |
| Self-hosted | Run it locally, in Docker, or on your own infrastructure. |
| Media-aware | Supports images, videos, avatars, queued processing, and remote storage. |
| Client-agnostic | Use the sample client or build your own app against the API. |
| Familiar Laravel stack | Easy to extend if you already work with Laravel, queues, Redis, and Eloquent. |

---

## Features

### Core social features

- account registration, login, logout, and password reset flows
- profiles with editable identity details and avatar uploads
- posts with text and up to 5 attached media files
- comments and threaded replies on posts
- reactions without requiring a comment
- friendship requests, acceptance, denial, and removal
- timeline aggregation for the authenticated user and their friends
- unread notification listing and mark-as-read support

### Media pipeline

- image and video uploads attached to posts
- asynchronous media processing through queued jobs
- avatar processing separated from request/response time
- support for pending, ready, and failed media states
- signed or temporary media URL generation via the configured storage disk
- scheduled cleanup for stale temporary uploads

### Privacy and authentication

- cookie-based SPA authentication with Laravel Sanctum
- authenticated API routes grouped behind `auth:sanctum`
- profile and post access controlled through Laravel policies
- session and stateful domain configuration for cross-subdomain clients

### Integrations and platform services

- S3-compatible media storage defaults with an R2-focused configuration
- Redis-backed queues and broadcasting support
- Laravel Reverb for realtime event delivery
- OpenCage integration for reverse geocoding/current location lookup
- Foursquare integration for nearby place lookup
- Telegram notification channel support
- FFmpeg and FFprobe support for video processing

### Operations

- Docker Compose setup for local development and production-style runs
- MySQL and SQLite compose variants
- dedicated queue, scheduler, and Reverb services
- Mailpit in the development stack for email inspection

---

## Quick Start

### Prerequisites

For the smoothest setup, use Docker and Docker Compose.

If you want to run the app natively instead, you will need:

- PHP 8.4+
- Composer
- MySQL or SQLite
- Redis
- FFmpeg and FFprobe for video support

### Option 1: Docker Compose

🐳 Recommended for the fastest local setup.

Start the default local stack with MySQL, Redis, Reverb, Mailpit, and a Vite container:

```bash
cp .env.example .env
make up
make key
make migrate
```

The app is served through the `web` container on `http://localhost:8080` by default.

Useful development commands:

```bash
make logs
make test
make pint
make artisan cmd="about"
```

If you prefer SQLite for local development:

```bash
cp .env.example .env
make up-sqlite
make key-sqlite
make migrate-sqlite
```

### Option 2: Native Laravel Setup

🛠️ Best if you prefer running Laravel services directly on your machine.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

You will also need to run the supporting processes your environment requires, such as:

```bash
php artisan queue:work
php artisan reverb:start
php artisan schedule:work
```

---

## Configuration Notes

The default `.env.example` is already tuned for a local Knot setup. The most important values to review are:

| Area | Keys |
| --- | --- |
| App and cookies | `APP_URL`, `SESSION_DOMAIN`, `SANCTUM_STATEFUL_DOMAINS` |
| Database | `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| Media storage | `MEDIA_DISK`, `MEDIA_TEMPORARY_DISK`, `MEDIA_URL_TTL_MINUTES`, `R2_*` |
| Video processing | `FFMPEG_BINARIES`, `FFPROBE_BINARIES`, `MEDIA_VIDEO_CRF` |
| Realtime | `BROADCAST_CONNECTION`, `REVERB_*`, `VITE_REVERB_*` |
| Queues and cache | `REDIS_HOST`, `REDIS_PORT`, `CACHE_DRIVER`, `QUEUE_DRIVER` |
| Email | `MAIL_*` |
| Optional services | `OPENCAGE_API_KEY`, `FOURSQUARE_CLIENT_ID`, `FOURSQUARE_CLIENT_SECRET`, `TELEGRAM_BOT_TOKEN` |

> 🍪 If you are building a separate frontend on another subdomain, the session and Sanctum domain settings matter as much as the API URL.

---

## Architecture at a Glance

Knot Server is a Laravel application that acts as the API boundary for a private social network.

- authentication uses Sanctum and cookie-based sessions for client apps
- API endpoints are split between public auth/reset routes and authenticated `/api` routes
- uploaded media lands in a temporary disk first, then background jobs transform and publish it to the configured media disk
- Redis supports queues, broadcasting, and the long-running workers used by the stack
- Reverb provides realtime transport for broadcast events
- the scheduler runs recurring maintenance, including stale media cleanup

The client is intentionally decoupled from this repository. You can use the sample client, build a custom SPA, or integrate the API into another product entirely.

---

## API Surface

Knot keeps the API compact and centered around the main social flows:

| Area | Responsibilities |
| --- | --- |
| Authentication | Login, logout, registration, password reset, current user lookup |
| Profile | View profiles, update profile info, upload avatars |
| Posts | Create, view, and delete posts with optional media |
| Comments and reactions | Comment on posts, update/delete comments, react to posts |
| Friendships | Send, accept, deny, and remove friendship relationships |
| Notifications | Fetch unread notifications and mark them as read |
| Support services | Nearby places, reverse geocoding, and link metadata lookup |

For the authoritative route list, inspect [`routes/api.php`](routes/api.php) and [`routes/web.php`](routes/web.php).

---

## Testing

Knot uses Pest for automated testing.

Run the focused suite locally with:

```bash
php artisan test --compact
```

Or, inside Docker:

```bash
make test
```

The test suite covers the core flows, including authentication, friendships, profiles, posts, comments, notifications, location services, and the media pipeline.

## Contributing

Contributions are welcome. A good contribution usually starts by:

1. running the test suite
2. making a focused change
3. adding or updating tests for the behavior you touched
4. formatting PHP changes with Laravel Pint

If you are introducing a larger change, keeping the API and deployment story clear for self-hosters will go a long way.

## License

Knot Server is open-sourced under the [BSD 3-Clause License](LICENSE).

## Acknowledgments

Knot was originally inspired by Path and the idea that small-group social sharing deserves a home that is private, portable, and owned by the people using it.
