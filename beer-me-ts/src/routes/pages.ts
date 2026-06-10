import type { FastifyInstance } from "fastify";
import { z } from "zod";
import { searchPatronByEmail } from "../services/patrons.js";
import { searchBarByName } from "../services/bars.js";
import { renderBarPage, renderPatronPage } from "../render.js";

const loginQuery = z.object({ username: z.string().min(1) });

export async function pageRoutes(app: FastifyInstance) {
  const index = (section: string, signupKind: string | null = null) => ({
    section,
    signupKind,
  });

  app.get("/", (_req, reply) => reply.view("index.njk", index("home")));
  app.get("/signin", (_req, reply) => reply.view("index.njk", index("signin")));
  app.get("/signup", (_req, reply) => reply.view("index.njk", index("signup")));
  app.get("/about", (_req, reply) => reply.view("index.njk", index("about")));
  app.get("/team", (_req, reply) => reply.view("index.njk", index("team")));
  app.get("/show_customer_signup", (_req, reply) =>
    reply.view("index.njk", index("signup", "customer")),
  );
  app.get("/show_business_signup", (_req, reply) =>
    reply.view("index.njk", index("signup", "business")),
  );

  // "Login": patrons sign in with their email, bars with their name.
  app.get("/login", async (req, reply) => {
    const { username } = loginQuery.parse(req.query);
    const [user, bar] = await Promise.all([
      searchPatronByEmail(username),
      searchBarByName(username),
    ]);
    if (bar) return renderBarPage(reply, bar, "none");
    if (user) return renderPatronPage(reply, user, "none");
    return reply.view("index.njk", index("signin"));
  });
}
