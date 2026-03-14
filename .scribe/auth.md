# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_BEARER_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Generate a bearer token with <code>POST /api/tokens/create</code>. Session-based clients can use <code>POST /login</code> and <code>POST /logout</code> instead.
