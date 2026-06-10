import type { FastifyInstance } from "fastify";
import { z } from "zod";
import { createBar, findBar, updateBar } from "../services/bars.js";
import { addItemToBar, deleteItem, updateItem } from "../services/items.js";
import { renderBarPage, type BarPageView } from "../render.js";

const idParam = z.object({ id: z.coerce.number().int().positive() });
const barItemParams = z.object({
  barId: z.coerce.number().int().positive(),
  itemId: z.coerce.number().int().positive(),
});
const barBody = z.object({
  name: z.string().min(1),
  phone: z.string().min(1),
  address: z.string().min(1),
  website: z.string().min(1),
});
const itemBody = z.object({
  description: z.string().min(1),
  cost: z.coerce.number().nonnegative(),
});

export async function barRoutes(app: FastifyInstance) {
  app.post("/business_signup", async (req, reply) => {
    await createBar(barBody.parse(req.body));
    return reply.view("signup_confirmation.njk");
  });

  // The bar page with one of its panels open.
  const panels: Record<string, BarPageView> = {
    "/show_bar_tokens/:id": "tokens",
    "/show_menu_items/:id": "menu",
    "/show_bar_edit/:id": "edit",
  };
  for (const [route, view] of Object.entries(panels)) {
    app.get(route, async (req, reply) => {
      const { id } = idParam.parse(req.params);
      const bar = await findBar(id);
      if (!bar) return reply.code(404).send("Bar not found");
      return renderBarPage(reply, bar, view);
    });
  }

  app.post("/edit_bar/:id", async (req, reply) => {
    const { id } = idParam.parse(req.params);
    const bar = await updateBar(id, barBody.parse(req.body));
    return renderBarPage(reply, bar, "edit");
  });

  app.post("/add_item/:barId", async (req, reply) => {
    const { barId } = barItemParams.pick({ barId: true }).parse(req.params);
    const { description, cost } = itemBody.parse(req.body);
    const bar = await findBar(barId);
    if (!bar) return reply.code(404).send("Bar not found");
    await addItemToBar(barId, description, cost);
    return renderBarPage(reply, bar, "menu");
  });

  app.post("/edit_item/:barId/:itemId", async (req, reply) => {
    const { barId, itemId } = barItemParams.parse(req.params);
    const { description, cost } = itemBody.parse(req.body);
    const bar = await findBar(barId);
    if (!bar) return reply.code(404).send("Bar not found");
    await updateItem(itemId, description, cost);
    return renderBarPage(reply, bar, "menu");
  });

  // POST, not GET like the original: deleting on GET lets crawlers and
  // link prefetchers destroy data.
  app.post("/delete_item/:barId/:itemId", async (req, reply) => {
    const { barId, itemId } = barItemParams.parse(req.params);
    const bar = await findBar(barId);
    if (!bar) return reply.code(404).send("Bar not found");
    await deleteItem(itemId);
    return renderBarPage(reply, bar, "menu");
  });
}
