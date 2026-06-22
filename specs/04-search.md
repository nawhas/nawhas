# 04 — Search

Finding anything in the library quickly. Search is a first-class, powerful surface
available to everyone.

Prerequisites: [00 — Foundation](./00-foundation.md).

---

## Stories

> **SRCH-1 — As a visitor, I want to search the whole library from anywhere, so that I can jump straight to what I want.**
>
> **Acceptance criteria**
> - **Given** any page, **then** a search entry point is available (a bar on large
>   screens, an accessible control on small screens).
> - **When** I type a query, **then** results appear quickly and update as I refine
>   the query.
> - **Then** I can dismiss search and return to where I was.

> **SRCH-2 — As a visitor, I want search to cover reciters, albums, and tracks together, so that I find the right kind of result regardless of what I'm looking for.**
>
> **Acceptance criteria**
> - **Given** a query, **then** matching **reciters**, **albums**, and **tracks** are
>   returned, visually distinguished by type.
> - **Given** a result, **when** I select it, **then** I navigate to its detail page.
> - **Then** tracks match on title, reciter name, album, year, and lyrics content;
>   reciters match on name; albums match on title, year, and reciter.

> **SRCH-3 — As a visitor, I want results that are tolerant and relevant, so that I find things despite typos, partial words, or alternate spellings.**
>
> **Acceptance criteria**
> - **Given** a misspelled or partial query, **then** the most relevant matches are
>   still returned and ranked sensibly.
> - **Given** a matched result, **then** the matching portion is highlighted so I can
>   see why it matched.
> - **Given** a query that matches nothing, **then** a clear "no results" state is
>   shown with suggestions to broaden the search.

> **SRCH-4 — As a visitor, I want search to also reach tags and topics, so that thematic discovery is searchable too.**  *(Near-term)*
>
> **Acceptance criteria**
> - **Given** a query that matches a tag or topic, **then** that tag/topic appears in
>   results and leads to its collection of tracks (see [01 — Catalog & Discovery](./01-catalog-and-discovery.md)).

> **SRCH-5 — As a visitor, I want a full results page for a query, so that I can review and page through many matches, not just a quick preview.**  *(Near-term)*
>
> **Acceptance criteria**
> - **Given** a query with many matches, **then** I can open a dedicated results
>   view that lists all matches, grouped or filterable by type, and paged.
> - **Then** I can play a track directly from results without first opening its page.

> **SRCH-6 — As a visitor, I want public playlists to be discoverable, so that community-curated lists surface in search.**  *(Near-term)*
>
> **Acceptance criteria**
> - **Given** a public playlist whose title or contents match my query, **then** it
>   appears in results and links to the playlist (see [05 — Library & Playlists](./05-library-and-playlists.md)).
> - **Given** a private playlist, **then** it never appears in anyone else's results.
