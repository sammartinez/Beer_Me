import { prisma } from "../db.js";

export const createPatron = (name: string, email: string) =>
  prisma.patron.create({ data: { name, email } });

export const findPatron = (id: number) =>
  prisma.patron.findUnique({ where: { id } });

export const searchPatronByEmail = (email: string) =>
  prisma.patron.findUnique({ where: { email } });

export const updatePatron = (id: number, name: string, email: string) =>
  prisma.patron.update({ where: { id }, data: { name, email } });

/** Tokens this patron has received, with the bar/item they're good for. */
export const getPatronTokens = (patronId: number) =>
  prisma.token.findMany({
    where: { patronId },
    include: { menu: { include: { bar: true, item: true } } },
    orderBy: { id: "asc" },
  });

export const getPreferredBars = (patronId: number) =>
  prisma.bar.findMany({
    where: { preferredBy: { some: { patronId } } },
    orderBy: { name: "asc" },
  });

export const addPreferredBar = (patronId: number, barId: number) =>
  prisma.preferBar.upsert({
    where: { patronId_barId: { patronId, barId } },
    create: { patronId, barId },
    update: {},
  });

export const removePreferredBar = (patronId: number, barId: number) =>
  prisma.preferBar.deleteMany({ where: { patronId, barId } });
