# Beer Me — JavaScript/TypeScript Conversion Analysis

This document analyzes what it would take to convert Beer Me from its current
PHP stack to a JavaScript/TypeScript application, and recommends an approach.

## 1. What the app is today

| Layer | Current technology | Notes |
|---|---|---|
| Web framework | Silex 1.x (PHP micro-framework) | **End-of-life since June 2018** — no security patches |
| Templating | Twig 1.x (server-rendered) | 9 templates in `views/` |
| Database | MySQL via PDO, raw SQL strings | 6 tables: `bars`, `items`, `menus`, `patrons`, `preferbars`, `tokens` |
| Models | 4 hand-rolled Active Record classes in `src/` | `Patron`, `Bar`, `Item`, `Token`, all using `$GLOBALS['DB']` |
| Email | PHPMailer 5.2 over Gmail SMTP | Credentials hardcoded in `app/app.php` |
| Front-end | Bootstrap 3 "Agency" theme, jQuery | Static assets in `web/` |
| Tests | PHPUnit 4.5 model tests | ~950 lines in `tests/` |
| Routing | ~30 routes in a single `app/app.php` (~620 lines) | All logic inline in route closures |

### Domain model

```
Patron (name, email)
Bar (name, phone, address, website)
Item (description, cost)
Menu  = join table (bar_id, item_id)        — "this bar serves this item"
PreferBar = join table (patron_id, bar_id)  — patron's favorite bars
Token (patron_id, menu_id, sender_id)       — "sender bought patron this item at this bar"
```

The core flow: a patron signs in (by email only), looks up a friend by email,
picks one of the friend's favorite bars, picks a menu item, and sends a
"token". The friend gets an email, and the bar redeems (deletes) the token
when the drink is claimed.

## 2. Recommendation: rebuild as a new TypeScript app, don't port line-by-line

A mechanical port is the wrong move here, for three reasons:

1. **The framework is dead.** Silex was discontinued in 2018; there is no
   "equivalent modern Silex" to map onto. Any conversion is a rewrite of the
   routing layer regardless.
2. **The current patterns shouldn't survive the trip.** Every query
   interpolates user input directly into SQL strings (SQL injection
   throughout), `find()` loads *every* row and loops in PHP, deletes happen
   on GET requests, there is no real authentication (knowing an email address
   *is* the login), and SMTP credentials are committed to source. Porting
   these faithfully would reproduce the problems in TypeScript.
3. **The app is small.** 4 entities, 6 tables, ~25 distinct routes, 9
   templates. A clean rebuild is days, not months — cheaper than a careful
   incremental port.

So: keep the **database schema** (`beer.sql`), the **static assets**
(`web/img`, CSS, theme), and the **behavioral spec** (the README tutorial +
existing routes), and rebuild the application code in a fresh project —
either a new repo (e.g. `beer-me-ts`) or a subdirectory of this one.

## 3. Proposed stack

Two viable paths, depending on how much you want to modernize:

### Option A — Faithful server-rendered port (lowest effort, ~2–4 days)

| Current | Replacement | Why |
|---|---|---|
| Silex routes | **Fastify** (or Express) + TypeScript | Same micro-framework feel; route closures map ~1:1 |
| Twig templates | **Nunjucks** | Nunjucks is a JS implementation of Jinja2/Twig-style syntax — `{{ }}`, `{% if %}`, `{% for %}` carry over nearly unchanged, so the 9 templates are mostly copy-and-fix |
| PDO + raw SQL | **Prisma** (or Drizzle) + MySQL | Typed, parameterized queries; schema generated from existing `beer.sql` |
| PHPMailer | **Nodemailer** (or Resend/SendGrid SDK) | Gmail no longer accepts plain-password SMTP; use an app password or a transactional email API, configured via env vars |
| PHPUnit | **Vitest** | Port the 4 model test suites |
| Composer | **npm/pnpm** + `package.json` | — |

### Option B — Modern full-stack rewrite (~1–2 weeks)

**Next.js (React) + TypeScript + Prisma + Postgres/MySQL**, with:
- Server components / API routes replacing the Silex routes
- React components replacing Twig templates (the Bootstrap theme would be
  rebuilt or swapped for Tailwind)
- Real auth (NextAuth/Auth.js — magic-link email login actually fits this
  app's existing "login by email" concept perfectly)

Option A gets you a working TS app fastest and preserves the look and flow
exactly. Option B is the better destination if this is a portfolio
modernization. A reasonable middle path: do Option A first (it forces the
domain logic into clean TypeScript services), then layer a React front-end on
later — the service layer carries over.

## 4. Conversion map (Option A)

### Project skeleton

```
beer-me-ts/
├── package.json, tsconfig.json, .env.example
├── prisma/schema.prisma          # generated from beer.sql via `prisma db pull`
├── src/
│   ├── server.ts                 # Fastify bootstrap (replaces web/index.php + app/app.php setup)
│   ├── routes/                   # patron.ts, bar.ts, token.ts, auth.ts, pages.ts
│   ├── services/                 # patron.ts, bar.ts, item.ts, token.ts  (replaces src/*.php)
│   └── mail.ts                   # Nodemailer (replaces PHPMailer block)
├── views/                        # *.njk — converted from views/*.html.twig
├── public/                       # copied from web/ (css, img, fonts, js)
└── tests/                        # Vitest suites ported from tests/*.php
```

### Models → services (`src/*.php` → `src/services/*.ts`)

The four classes become Prisma-backed service modules. Example of the shape
change:

```php
// PHP today — loads ALL patrons, loops to find one, string-interpolated SQL
static function find($search_id) {
    $patrons = Patron::getAll();
    foreach($patrons as $patron) { ... }
}
```

```ts
// TypeScript — typed, parameterized, single query
export const findPatron = (id: number) =>
  prisma.patron.findUnique({ where: { id } });

export const getPreferredBars = (patronId: number) =>
  prisma.bar.findMany({ where: { preferbars: { some: { patronId } } } });
```

This eliminates the N+1 / table-scan pattern and the SQL injection in one
move, because Prisma parameterizes everything.

### Routes (`app/app.php` → `src/routes/*.ts`)

~25 distinct routes. They group naturally:

- **Static pages** (`/`, `/about`, `/team`, `/signup`, `/signin`, signup-mode
  toggles): trivial template renders.
- **Patron flows** (`/login`, `/show_user_tokens`, `/show_user_edit`,
  `/show_preferred_bars`, `/edit_user`, `/add_preferred_bar`,
  `/delete_preferred_bar`): one render per route with a flags object — in the
  rewrite, collapse the boolean-flag soup (`send_token`, `token_form`,
  `edit_user`, …) into a single `view` enum passed to the template.
- **Bar flows** (`/show_bar_tokens`, `/show_menu_items`, `/show_bar_edit`,
  `/edit_bar`, `/add_item`, `/edit_item`, `/delete_item`): same pattern.
- **Token flows** (`/find_friend`, `/select_bar`, `/add_token`, `/token/:id`,
  `/view_token/:id`, `/redeem_token/:id`, `/confirmation/:id`): the heart of
  the app, including the email send.

Silex's `Request::enableHttpMethodParameterOverride()` hack (HTML forms
faking PATCH/DELETE via `_method`) is replaced either by
`fastify-method-override` for fidelity, or by just using POST routes.

### Templates (`views/*.html.twig` → `views/*.njk`)

Nunjucks syntax is close enough to Twig that this is mostly a rename plus
small fixes (filter names, `{% raw %}` blocks if any). ~1,200 lines of
template across 9 files. The Bootstrap/jQuery assets in `web/` copy over
untouched as static files.

### Database

Reuse `beer.sql` as-is: run it into MySQL, then `npx prisma db pull` to
generate the schema, then add the missing foreign-key relations (the current
schema has no FK constraints — the join tables use bare ints). SQLite is also
an option for local dev since nothing here is MySQL-specific.

### Email

The `add_token` route's PHPMailer block becomes ~10 lines of Nodemailer.
Two required changes regardless of language:
- **Credentials must move to environment variables.** The Gmail password is
  currently hardcoded in `app/app.php` (and is in git history — rotate that
  account).
- Gmail has disabled basic-auth SMTP; use an app-specific password or a
  transactional provider (Resend, SendGrid, SES).

## 5. Things to fix during conversion (not faithfully port)

1. **SQL injection** — every query in `src/*.php` interpolates user input.
   Fixed automatically by Prisma/Drizzle.
2. **No authentication** — "sign in" is just typing an email. Minimum viable
   fix: signed session cookie after a magic-link email (reuses the email
   infrastructure the app already needs).
3. **State-changing GETs** — `GET /redeem_token/:id` and
   `GET /delete_item/...` delete rows; crawlers/prefetchers can destroy data.
   Make them POST/DELETE.
4. **Existing bug** — the `DELETE /redeem_token/{token_id}` handler
   (`app/app.php:218`) references `$bar` without ever defining it; the GET
   variant was added as a workaround. The rewrite should keep only the
   correct version.
5. **Input validation** — none exists. Add `zod` schemas on form bodies.
6. **Secrets in repo** — beyond the Gmail password, DB credentials
   (`root`/`root`) are also hardcoded; move all of it to `.env`.

## 6. Effort estimate (Option A)

| Phase | Work | Estimate |
|---|---|---|
| 0 | Scaffold TS project, tooling, copy static assets | 0.5 day |
| 1 | Prisma schema from `beer.sql`, DB connection, seed data | 0.5 day |
| 2 | Port 4 model classes to typed services + Vitest tests | 1 day |
| 3 | Port ~25 routes + 9 Twig→Nunjucks templates | 1–1.5 days |
| 4 | Nodemailer + env config + auth/session fix | 0.5–1 day |
| **Total** | | **~3.5–4.5 days** |

Option B (Next.js) roughly doubles this because every template becomes a
React component and auth/routing are rebuilt on different primitives.
