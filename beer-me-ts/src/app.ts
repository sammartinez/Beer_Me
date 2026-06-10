import Fastify from "fastify";
import formbody from "@fastify/formbody";
import fastifyStatic from "@fastify/static";
import fastifyView from "@fastify/view";
import nunjucks from "nunjucks";
import path from "node:path";
import { fileURLToPath } from "node:url";
import { ZodError } from "zod";
import { pageRoutes } from "./routes/pages.js";
import { patronRoutes } from "./routes/patrons.js";
import { barRoutes } from "./routes/bars.js";
import { tokenRoutes } from "./routes/tokens.js";

// One level above src/ in dev and dist/ in production: the project root.
const rootDir = path.dirname(path.dirname(fileURLToPath(import.meta.url)));

export function buildApp() {
  const app = Fastify({ logger: true });

  app.register(formbody);
  app.register(fastifyStatic, { root: path.join(rootDir, "public") });
  app.register(fastifyView, {
    engine: { nunjucks },
    root: path.join(rootDir, "views"),
  });

  app.setErrorHandler((err, req, reply) => {
    if (err instanceof ZodError) {
      return reply.code(400).send({ error: "Invalid input", issues: err.issues });
    }
    req.log.error(err);
    return reply.code(500).send({ error: "Internal server error" });
  });

  app.register(pageRoutes);
  app.register(patronRoutes);
  app.register(barRoutes);
  app.register(tokenRoutes);

  return app;
}
