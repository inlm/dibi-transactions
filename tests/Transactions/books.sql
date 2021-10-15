DROP TABLE IF EXISTS "book";
CREATE TABLE "book" (
	"id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
	"name" text NOT NULL
);

INSERT INTO "book" ("name") VALUES ('Lord of the Rings 1');
INSERT INTO "book" ("name") VALUES ('Lord of the Rings 2');
INSERT INTO "book" ("name") VALUES ('Lord of the Rings 3');
