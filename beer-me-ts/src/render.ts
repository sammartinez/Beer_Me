import type { FastifyReply } from "fastify";
import type { Bar, Patron } from "@prisma/client";
import { getPatronTokens, getPreferredBars } from "./services/patrons.js";
import { getAllBars, getBarItems, getBarTokens } from "./services/bars.js";

// These replace the original boolean-flag soup (send_token / token_form /
// edit_user / ... all passed on every render) with a single view selector.
export type PatronPageView = "none" | "send" | "tokens" | "edit" | "bars";
export type BarPageView = "none" | "tokens" | "menu" | "edit";

export async function renderPatronPage(reply: FastifyReply, user: Patron, view: PatronPageView) {
  const [tokens, allBars, preferredBars] = await Promise.all([
    getPatronTokens(user.id),
    getAllBars(),
    getPreferredBars(user.id),
  ]);
  return reply.view("patron.njk", { user, tokens, allBars, preferredBars, view });
}

export async function renderBarPage(reply: FastifyReply, bar: Bar, view: BarPageView) {
  const [tokens, items] = await Promise.all([getBarTokens(bar.id), getBarItems(bar.id)]);
  return reply.view("bar.njk", { bar, tokens, items, view });
}
