import type { FastifyInstance } from "fastify";
import { z } from "zod";
import { Prisma } from "@prisma/client";
import {
  addPreferredBar,
  createPatron,
  findPatron,
  removePreferredBar,
  updatePatron,
} from "../services/patrons.js";
import { renderPatronPage, type PatronPageView } from "../render.js";

const idParam = z.object({ id: z.coerce.number().int().positive() });
const signupBody = z.object({ username: z.string().min(1), email: z.string().email() });
const editBody = z.object({ name: z.string().min(1), email: z.string().email() });
const addBarBody = z.object({ add_bar: z.coerce.number().int().positive() });
const deleteBarBody = z.object({ bar: z.coerce.number().int().positive() });

export async function patronRoutes(app: FastifyInstance) {
  app.post("/customer_signup", async (req, reply) => {
    const { username, email } = signupBody.parse(req.body);
    try {
      await createPatron(username, email);
    } catch (err) {
      // Email already registered: send them to sign in instead.
      if (err instanceof Prisma.PrismaClientKnownRequestError && err.code === "P2002") {
        return reply.redirect("/signin");
      }
      throw err;
    }
    return reply.view("signup_confirmation.njk");
  });

  // The patron page with one of its panels open.
  const panels: Record<string, PatronPageView> = {
    "/show_email_search/:id": "send",
    "/show_user_tokens/:id": "tokens",
    "/show_user_edit/:id": "edit",
    "/show_preferred_bars/:id": "bars",
    "/confirmation/:id": "tokens", // landing page from the notification email
  };
  for (const [route, view] of Object.entries(panels)) {
    app.get(route, async (req, reply) => {
      const { id } = idParam.parse(req.params);
      const user = await findPatron(id);
      if (!user) return reply.code(404).send("Patron not found");
      return renderPatronPage(reply, user, view);
    });
  }

  app.post("/edit_user/:id", async (req, reply) => {
    const { id } = idParam.parse(req.params);
    const { name, email } = editBody.parse(req.body);
    const user = await updatePatron(id, name, email);
    return renderPatronPage(reply, user, "none");
  });

  app.post("/add_preferred_bar/:id", async (req, reply) => {
    const { id } = idParam.parse(req.params);
    const { add_bar } = addBarBody.parse(req.body);
    const user = await findPatron(id);
    if (!user) return reply.code(404).send("Patron not found");
    await addPreferredBar(id, add_bar);
    return renderPatronPage(reply, user, "bars");
  });

  app.post("/delete_preferred_bar/:id", async (req, reply) => {
    const { id } = idParam.parse(req.params);
    const { bar } = deleteBarBody.parse(req.body);
    const user = await findPatron(id);
    if (!user) return reply.code(404).send("Patron not found");
    await removePreferredBar(id, bar);
    return renderPatronPage(reply, user, "bars");
  });
}
