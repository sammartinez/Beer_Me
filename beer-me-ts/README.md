# Beer Me

It's about time you buy your friend a beer.

This is the TypeScript rewrite of the original PHP app
([sammartinez/Beer_Me](https://github.com/sammartinez/Beer_Me)). Same idea,
same look, new stack: sign up with your email, add your favorite bars, and
buy drinks for friends remotely — they get an email and redeem the token at
the bar.

## Stack

| | |
|---|---|
| Runtime | Node.js 20+ / TypeScript |
| Web framework | [Fastify](https://fastify.dev) |
| Templates | [Nunjucks](https://mozilla.github.io/nunjucks/) (server-rendered, converted from the original Twig views) |
| Database | MySQL via [Prisma](https://www.prisma.io) (schema ported from `prisma/legacy-beer.sql`) |
| Email | [Nodemailer](https://nodemailer.com) |
| Validation | [Zod](https://zod.dev) |
| Tests | [Vitest](https://vitest.dev) |

## Setup

1. Install dependencies:

   ```sh
   npm install
   ```

2. Configure the environment:

   ```sh
   cp .env.example .env
   # edit DATABASE_URL to point at your MySQL server
   ```

3. Create the database and tables, and seed the tutorial bars
   (Apex, Side Street, Binks, Target, Test Bar):

   ```sh
   mysql -uroot -p -e "CREATE DATABASE IF NOT EXISTS beer"
   npm run db:push
   npm run db:seed
   ```

4. Run it:

   ```sh
   npm run dev
   ```

   Then open <http://localhost:8000>. The tutorial in the original README
   applies unchanged: sign up two users, favorite a bar, and send a token.

Email is optional in development — leave `SMTP_HOST` unset and outgoing
notification emails are logged to the console instead of sent.

## Tests

```sh
npm test
```

The test suite runs against a mocked database, so no MySQL server is needed.

## What changed from the PHP version

- **Silex → Fastify**: same route URLs, but state-changing actions
  (redeeming tokens, deleting items/preferred bars, edits) are POSTs now —
  the original did some of these on GET or via the `_method` override hack.
- **Twig → Nunjucks**: near-identical syntax; the per-page boolean flags
  (`send_token`, `token_form`, `edit_user`, ...) were collapsed into a single
  `view` variable, and the shared header/nav/scripts moved into `base.njk`.
- **Raw PDO SQL → Prisma**: every query in the original interpolated user
  input into SQL strings; Prisma parameterizes everything. The schema gained
  real foreign keys, cascading deletes, and a unique constraint on patron
  email.
- **Secrets → environment**: SMTP and database credentials were hardcoded in
  `app/app.php`; they now live in `.env`.
- Fixed the original's undefined-`$bar` bug in the redeem-token handler.

Known limitation carried over from the original: there are no passwords —
"signing in" is entering an email (or bar name). A real session +
magic-link login is the natural next step since email sending is already
wired up.

### Legal

Copyright (c) 2015 Kelli Sommerdyke, Casey Heitz, Jason Bethel, Kyle Pratuch,
Jordan Johansen, and Sam Martinez

This software is licensed under the MIT license.
