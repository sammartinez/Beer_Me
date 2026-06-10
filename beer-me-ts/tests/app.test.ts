// Route/rendering tests: boot the real Fastify app with the database mocked
// and check that pages render.
import { beforeEach, describe, expect, it, vi } from "vitest";
import { mockDeep, mockReset, type DeepMockProxy } from "vitest-mock-extended";
import type { PrismaClient } from "@prisma/client";

vi.mock("../src/db.js", () => ({ prisma: mockDeep<PrismaClient>() }));

import { prisma } from "../src/db.js";
import { buildApp } from "../src/app.js";

const prismaMock = prisma as unknown as DeepMockProxy<PrismaClient>;
const sam = { id: 1, name: "Sam", email: "sam@example.com" };
const apex = { id: 2, name: "Apex", phone: "555", address: "1216 SE Division", website: "apexbar.com" };

function app() {
  return buildApp();
}

beforeEach(() => {
  mockReset(prismaMock);
});

describe("static pages", () => {
  it.each(["/", "/signin", "/signup", "/about", "/team"])("renders %s", async (url) => {
    const res = await app().inject({ method: "GET", url });
    expect(res.statusCode).toBe(200);
    expect(res.body).toContain("Beer Me");
  });

  it("shows the customer signup form", async () => {
    const res = await app().inject({ method: "GET", url: "/show_customer_signup" });
    expect(res.statusCode).toBe(200);
    expect(res.body).toContain('action="/customer_signup"');
  });
});

describe("login", () => {
  it("renders the patron page for a patron email", async () => {
    prismaMock.patron.findUnique.mockResolvedValue(sam);
    prismaMock.bar.findFirst.mockResolvedValue(null);
    prismaMock.token.findMany.mockResolvedValue([]);
    prismaMock.bar.findMany.mockResolvedValue([]);

    const res = await app().inject({ method: "GET", url: "/login?username=sam%40example.com" });
    expect(res.statusCode).toBe(200);
    expect(res.body).toContain("Howdy, Sam.");
  });

  it("renders the bar page for a bar name", async () => {
    prismaMock.patron.findUnique.mockResolvedValue(null);
    prismaMock.bar.findFirst.mockResolvedValue(apex);
    prismaMock.token.findMany.mockResolvedValue([]);
    prismaMock.item.findMany.mockResolvedValue([]);

    const res = await app().inject({ method: "GET", url: "/login?username=Apex" });
    expect(res.statusCode).toBe(200);
    expect(res.body).toContain("Unredeemed Tokens");
  });

  it("falls back to the sign-in page for unknown usernames", async () => {
    prismaMock.patron.findUnique.mockResolvedValue(null);
    prismaMock.bar.findFirst.mockResolvedValue(null);

    const res = await app().inject({ method: "GET", url: "/login?username=nobody" });
    expect(res.statusCode).toBe(200);
    expect(res.body).toContain("Log in to your account.");
  });
});

describe("patron pages", () => {
  it("shows the empty-tokens message", async () => {
    prismaMock.patron.findUnique.mockResolvedValue(sam);
    prismaMock.token.findMany.mockResolvedValue([]);
    prismaMock.bar.findMany.mockResolvedValue([]);

    const res = await app().inject({ method: "GET", url: "/show_user_tokens/1" });
    expect(res.statusCode).toBe(200);
    expect(res.body).toContain("You don't have any tokens yet!");
  });

  it("404s for a missing patron", async () => {
    prismaMock.patron.findUnique.mockResolvedValue(null);
    const res = await app().inject({ method: "GET", url: "/show_user_tokens/42" });
    expect(res.statusCode).toBe(404);
  });

  it("400s on a non-numeric id", async () => {
    const res = await app().inject({ method: "GET", url: "/show_user_tokens/abc" });
    expect(res.statusCode).toBe(400);
  });
});

describe("signup", () => {
  it("creates a patron and confirms", async () => {
    prismaMock.patron.create.mockResolvedValue(sam);
    const res = await app().inject({
      method: "POST",
      url: "/customer_signup",
      payload: "username=Sam&email=sam%40example.com",
      headers: { "content-type": "application/x-www-form-urlencoded" },
    });
    expect(res.statusCode).toBe(200);
    expect(res.body).toContain("Thanks for signing up!");
    expect(prismaMock.patron.create).toHaveBeenCalledWith({
      data: { name: "Sam", email: "sam@example.com" },
    });
  });

  it("rejects an invalid email", async () => {
    const res = await app().inject({
      method: "POST",
      url: "/customer_signup",
      payload: "username=Sam&email=not-an-email",
      headers: { "content-type": "application/x-www-form-urlencoded" },
    });
    expect(res.statusCode).toBe(400);
  });
});
