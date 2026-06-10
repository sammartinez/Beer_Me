import { prisma } from "../db.js";

/**
 * Creates a token for the (bar, item) pair: sender buys patron that item at
 * that bar. Replaces the original Bar::getMenuId + Token::save two-step.
 */
export const createToken = async (
  patronId: number,
  senderId: number,
  barId: number,
  itemId: number,
) => {
  const menu = await prisma.menu.findUnique({
    where: { barId_itemId: { barId, itemId } },
  });
  if (!menu) return null;
  return prisma.token.create({ data: { patronId, senderId, menuId: menu.id } });
};

/** A token with everything its pages need: the bar, the item, and the patron. */
export const findToken = (id: number) =>
  prisma.token.findUnique({
    where: { id },
    include: { patron: true, menu: { include: { bar: true, item: true } } },
  });

export const deleteToken = (id: number) => prisma.token.delete({ where: { id } });
