import type { FastifyInstance } from "fastify";
import { z } from "zod";
import { findPatron, getPreferredBars, searchPatronByEmail } from "../services/patrons.js";
import { findBar, getBarItems } from "../services/bars.js";
import { createToken, deleteToken, findToken } from "../services/tokens.js";
import { sendTokenNotification } from "../mail.js";
import { renderBarPage, renderPatronPage } from "../render.js";

const idParam = z.object({ id: z.coerce.number().int().positive() });
const tokenParam = z.object({ tokenId: z.coerce.number().int().positive() });
const friendParams = z.object({
  id: z.coerce.number().int().positive(),
  friendId: z.coerce.number().int().positive(),
});
const addTokenParams = friendParams.extend({ barId: z.coerce.number().int().positive() });
const searchQuery = z.object({ search_email: z.string().min(1) });
const selectBarBody = z.object({ select_bar: z.coerce.number().int().positive() });
const addTokenBody = z.object({ item_id: z.coerce.number().int().positive() });

export async function tokenRoutes(app: FastifyInstance) {
  // Look up the friend to buy a beer for; falls back to the search panel
  // when no account matches that email.
  app.get("/find_friend/:id", async (req, reply) => {
    const { id } = idParam.parse(req.params);
    const { search_email } = searchQuery.parse(req.query);
    const user = await findPatron(id);
    if (!user) return reply.code(404).send("Patron not found");

    const friend = await searchPatronByEmail(search_email);
    if (!friend) return renderPatronPage(reply, user, "send");

    return reply.view("send_token.njk", {
      user,
      friend,
      friendBars: await getPreferredBars(friend.id),
      selectedBar: null,
      selectedBarItems: [],
    });
  });

  app.get("/select_bar/:id/:friendId", async (req, reply) => {
    const { id, friendId } = friendParams.parse(req.params);
    const [user, friend] = await Promise.all([findPatron(id), findPatron(friendId)]);
    if (!user || !friend) return reply.code(404).send("Patron not found");

    return reply.view("send_token.njk", {
      user,
      friend,
      friendBars: await getPreferredBars(friend.id),
      selectedBar: null,
      selectedBarItems: [],
    });
  });

  app.post("/select_bar/:id/:friendId", async (req, reply) => {
    const { id, friendId } = friendParams.parse(req.params);
    const { select_bar } = selectBarBody.parse(req.body);
    const [user, friend, selectedBar] = await Promise.all([
      findPatron(id),
      findPatron(friendId),
      findBar(select_bar),
    ]);
    if (!user || !friend) return reply.code(404).send("Patron not found");

    return reply.view("send_token.njk", {
      user,
      friend,
      friendBars: await getPreferredBars(friend.id),
      selectedBar,
      selectedBarItems: selectedBar ? await getBarItems(selectedBar.id) : [],
    });
  });

  app.post("/add_token/:id/:friendId/:barId", async (req, reply) => {
    const { id, friendId, barId } = addTokenParams.parse(req.params);
    const { item_id } = addTokenBody.parse(req.body);
    const [user, friend] = await Promise.all([findPatron(id), findPatron(friendId)]);
    if (!user || !friend) return reply.code(404).send("Patron not found");

    const token = await createToken(friendId, id, barId, item_id);
    if (!token) return reply.code(404).send("That bar doesn't serve that item");

    try {
      await sendTokenNotification(friend.email, friend.name, friend.id);
    } catch (err) {
      // The token is saved either way; don't fail the purchase over email.
      req.log.error({ err }, "failed to send token notification email");
    }

    return reply.view("token_confirmation.njk", { user, friend });
  });

  // A bar's view of a token, with the redeem button.
  app.get("/token/:tokenId", async (req, reply) => {
    const { tokenId } = tokenParam.parse(req.params);
    const token = await findToken(tokenId);
    if (!token) return reply.code(404).send("Token not found");
    return reply.view("redeem_token.njk", {
      token,
      item: token.menu.item,
      bar: token.menu.bar,
    });
  });

  // A patron's view of a token.
  app.get("/view_token/:tokenId", async (req, reply) => {
    const { tokenId } = tokenParam.parse(req.params);
    const token = await findToken(tokenId);
    if (!token) return reply.code(404).send("Token not found");
    return reply.view("view_token.njk", {
      token,
      item: token.menu.item,
      bar: token.menu.bar,
    });
  });

  // POST, not GET/DELETE-override like the original. This also fixes the
  // original DELETE handler's undefined $bar bug.
  app.post("/redeem_token/:tokenId", async (req, reply) => {
    const { tokenId } = tokenParam.parse(req.params);
    const token = await findToken(tokenId);
    if (!token) return reply.code(404).send("Token not found");
    await deleteToken(tokenId);
    return renderBarPage(reply, token.menu.bar, "tokens");
  });
}
