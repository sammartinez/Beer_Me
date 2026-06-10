import { prisma } from "../db.js";

/** Creates an item and puts it on the bar's menu in one step. */
export const addItemToBar = (barId: number, description: string, cost: number) =>
  prisma.item.create({
    data: { description, cost, menus: { create: { barId } } },
  });

export const findItem = (id: number) => prisma.item.findUnique({ where: { id } });

export const updateItem = (id: number, description: string, cost: number) =>
  prisma.item.update({ where: { id }, data: { description, cost } });

export const deleteItem = (id: number) => prisma.item.delete({ where: { id } });
