import { prisma } from "../db.js";

export const createBar = (data: { name: string; phone: string; address: string; website: string }) =>
  prisma.bar.create({ data });

export const findBar = (id: number) => prisma.bar.findUnique({ where: { id } });

export const searchBarByName = (name: string) =>
  prisma.bar.findFirst({ where: { name } });

export const getAllBars = () => prisma.bar.findMany({ orderBy: { name: "asc" } });

export const updateBar = (
  id: number,
  data: { name: string; phone: string; address: string; website: string },
) => prisma.bar.update({ where: { id }, data });

/** Unredeemed tokens for drinks at this bar, with the recipient patron. */
export const getBarTokens = (barId: number) =>
  prisma.token.findMany({
    where: { menu: { barId } },
    include: { patron: true, menu: { include: { item: true } } },
    orderBy: { id: "asc" },
  });

/** This bar's menu items, cheapest first (matches the original ORDER BY cost). */
export const getBarItems = (barId: number) =>
  prisma.item.findMany({
    where: { menus: { some: { barId } } },
    orderBy: { cost: "asc" },
  });
