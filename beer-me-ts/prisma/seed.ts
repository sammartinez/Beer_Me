// Seeds the test bars from the original README tutorial, each with a small menu.
import { PrismaClient } from "@prisma/client";

const prisma = new PrismaClient();

const bars = [
  { name: "Apex", phone: "503-555-0101", address: "1216 SE Division St", website: "www.apexbar.com" },
  { name: "Side Street", phone: "503-555-0102", address: "140 SE 28th Ave", website: "www.sidestreetbar.com" },
  { name: "Binks", phone: "503-555-0103", address: "2715 NE Alberta St", website: "www.binkspdx.com" },
  { name: "Target", phone: "503-555-0104", address: "939 SW Morrison St", website: "www.target.com" },
  { name: "Test Bar", phone: "503-555-0105", address: "123 Test St", website: "www.testbar.com" },
];

const items = [
  { description: "Domestic Beer", cost: 4.0 },
  { description: "Import Beer", cost: 6.0 },
  { description: "Beer Flight", cost: 12.0 },
  { description: "House Special", cost: 8.5 },
];

async function main() {
  for (const item of items) {
    await prisma.item.create({ data: item });
  }
  const allItems = await prisma.item.findMany();

  for (const bar of bars) {
    await prisma.bar.create({
      data: {
        ...bar,
        menus: { create: allItems.map((item) => ({ itemId: item.id })) },
      },
    });
  }
  console.log(`Seeded ${bars.length} bars and ${items.length} items.`);
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(() => prisma.$disconnect());
