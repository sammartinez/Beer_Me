// Ports the intent of the original PHPUnit suites (BarTest, ItemTest,
// PatronTest, TokenTest) against the new Prisma-backed services, with the
// database mocked.
import { beforeEach, describe, expect, it, vi } from "vitest";
import { mockDeep, mockReset, type DeepMockProxy } from "vitest-mock-extended";
import type { PrismaClient } from "@prisma/client";

vi.mock("../src/db.js", () => ({ prisma: mockDeep<PrismaClient>() }));

import { prisma } from "../src/db.js";
import * as patrons from "../src/services/patrons.js";
import * as bars from "../src/services/bars.js";
import * as items from "../src/services/items.js";
import * as tokens from "../src/services/tokens.js";

const prismaMock = prisma as unknown as DeepMockProxy<PrismaClient>;

beforeEach(() => {
  mockReset(prismaMock);
});

describe("patrons service", () => {
  const sam = { id: 1, name: "Sam", email: "sam@example.com" };

  it("creates a patron with name and email", async () => {
    prismaMock.patron.create.mockResolvedValue(sam);
    const result = await patrons.createPatron("Sam", "sam@example.com");
    expect(prismaMock.patron.create).toHaveBeenCalledWith({
      data: { name: "Sam", email: "sam@example.com" },
    });
    expect(result).toEqual(sam);
  });

  it("searches patrons by email", async () => {
    prismaMock.patron.findUnique.mockResolvedValue(sam);
    const result = await patrons.searchPatronByEmail("sam@example.com");
    expect(prismaMock.patron.findUnique).toHaveBeenCalledWith({
      where: { email: "sam@example.com" },
    });
    expect(result).toEqual(sam);
  });

  it("updates a patron's name and email", async () => {
    prismaMock.patron.update.mockResolvedValue({ ...sam, name: "Sammy" });
    await patrons.updatePatron(1, "Sammy", "sam@example.com");
    expect(prismaMock.patron.update).toHaveBeenCalledWith({
      where: { id: 1 },
      data: { name: "Sammy", email: "sam@example.com" },
    });
  });

  it("adds a preferred bar without duplicating it", async () => {
    await patrons.addPreferredBar(1, 2);
    expect(prismaMock.preferBar.upsert).toHaveBeenCalledWith({
      where: { patronId_barId: { patronId: 1, barId: 2 } },
      create: { patronId: 1, barId: 2 },
      update: {},
    });
  });

  it("removes a preferred bar", async () => {
    await patrons.removePreferredBar(1, 2);
    expect(prismaMock.preferBar.deleteMany).toHaveBeenCalledWith({
      where: { patronId: 1, barId: 2 },
    });
  });

  it("fetches a patron's tokens with the bar and item they're good for", async () => {
    prismaMock.token.findMany.mockResolvedValue([]);
    await patrons.getPatronTokens(1);
    expect(prismaMock.token.findMany).toHaveBeenCalledWith({
      where: { patronId: 1 },
      include: { menu: { include: { bar: true, item: true } } },
      orderBy: { id: "asc" },
    });
  });
});

describe("bars service", () => {
  const apex = { id: 1, name: "Apex", phone: "555", address: "1216 SE Division", website: "apexbar.com" };

  it("creates a bar", async () => {
    prismaMock.bar.create.mockResolvedValue(apex);
    const result = await bars.createBar({
      name: "Apex",
      phone: "555",
      address: "1216 SE Division",
      website: "apexbar.com",
    });
    expect(result).toEqual(apex);
  });

  it("searches bars by name", async () => {
    prismaMock.bar.findFirst.mockResolvedValue(apex);
    const result = await bars.searchBarByName("Apex");
    expect(prismaMock.bar.findFirst).toHaveBeenCalledWith({ where: { name: "Apex" } });
    expect(result).toEqual(apex);
  });

  it("lists a bar's menu items cheapest first", async () => {
    prismaMock.item.findMany.mockResolvedValue([]);
    await bars.getBarItems(1);
    expect(prismaMock.item.findMany).toHaveBeenCalledWith({
      where: { menus: { some: { barId: 1 } } },
      orderBy: { cost: "asc" },
    });
  });

  it("lists a bar's unredeemed tokens with their recipients", async () => {
    prismaMock.token.findMany.mockResolvedValue([]);
    await bars.getBarTokens(1);
    expect(prismaMock.token.findMany).toHaveBeenCalledWith({
      where: { menu: { barId: 1 } },
      include: { patron: true, menu: { include: { item: true } } },
      orderBy: { id: "asc" },
    });
  });
});

describe("items service", () => {
  it("creates an item directly onto the bar's menu", async () => {
    await items.addItemToBar(1, "IPA", 6.5);
    expect(prismaMock.item.create).toHaveBeenCalledWith({
      data: { description: "IPA", cost: 6.5, menus: { create: { barId: 1 } } },
    });
  });

  it("updates an item", async () => {
    await items.updateItem(3, "Stout", 7);
    expect(prismaMock.item.update).toHaveBeenCalledWith({
      where: { id: 3 },
      data: { description: "Stout", cost: 7 },
    });
  });

  it("deletes an item", async () => {
    await items.deleteItem(3);
    expect(prismaMock.item.delete).toHaveBeenCalledWith({ where: { id: 3 } });
  });
});

describe("tokens service", () => {
  it("creates a token for the menu row matching the bar and item", async () => {
    prismaMock.menu.findUnique.mockResolvedValue({ id: 9, barId: 1, itemId: 2 });
    const created = { id: 5, patronId: 3, menuId: 9, senderId: 4 };
    prismaMock.token.create.mockResolvedValue(created);

    const result = await tokens.createToken(3, 4, 1, 2);

    expect(prismaMock.menu.findUnique).toHaveBeenCalledWith({
      where: { barId_itemId: { barId: 1, itemId: 2 } },
    });
    expect(prismaMock.token.create).toHaveBeenCalledWith({
      data: { patronId: 3, senderId: 4, menuId: 9 },
    });
    expect(result).toEqual(created);
  });

  it("returns null when the bar doesn't serve the item", async () => {
    prismaMock.menu.findUnique.mockResolvedValue(null);
    const result = await tokens.createToken(3, 4, 1, 999);
    expect(result).toBeNull();
    expect(prismaMock.token.create).not.toHaveBeenCalled();
  });

  it("deletes a token when redeemed", async () => {
    await tokens.deleteToken(5);
    expect(prismaMock.token.delete).toHaveBeenCalledWith({ where: { id: 5 } });
  });
});
