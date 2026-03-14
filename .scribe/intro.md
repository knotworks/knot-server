# Introduction

HTTP API documentation for Knot, a self-hosted social network for families and other small groups.

<aside>
    <strong>Base URL</strong>: <code>https://knot.test</code>
</aside>

Knot supports two authentication flows:

- Personal access tokens for API clients via `POST /api/tokens/create`
- Session login/logout for first-party clients via `POST /login` and `POST /logout`

Most `/api/*` endpoints require a Sanctum bearer token. Media download endpoints also require a valid signed URL, so keep the original query string intact when requesting avatar or post media files.

