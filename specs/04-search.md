# 04 — Search

Finding anything in the library quickly. Search is a first-class, powerful surface
available to everyone, and it is held to a **world-class** bar: a user should find
what they mean even when they don't know how it's spelled.

Prerequisites: [00 — Foundation](./00-foundation.md).

---

## The transliteration challenge

Most of the library is Urdu and other Indo-Pak languages (Pashto, Punjabi, …)
written as **Latin transliteration**, for which there is **no canonical spelling**.
The same name is written many legitimate ways, all of which a user might type:

- *Hussain · Husain · Husayn · Hussein · Hossain*
- *Muhammad · Mohammad · Mohammed · Muhammed*
- *Muharram · Moharram · Muharam*
- *nawha · noha · nauha · nohay*
- *Zainab · Zaynab · Zenab*

A search that only matches exact (or nearly exact) spelling is unusable here.
Search must treat these spelling families as **the same word** and still return
the right result first. This is a defining quality of the product, not a nicety.

The strategy below rests on six observable pillars. None of them prescribes *how*
matching works internally — only what the user must experience:

1. **Forgiving matching** — spelling variation, accents, and punctuation never
   block a match (SRCH-3, SRCH-4).
2. **Cross-script** — typing in Latin finds native-script content and vice-versa
   (SRCH-5).
3. **Best-first ranking** — the closest, most likely result leads, despite loose
   matching (SRCH-6).
4. **Type-ahead that sidesteps spelling** — likely entities surface as the user
   types, so they can pick rather than spell (SRCH-7).
5. **Graceful recovery** — when a query is sparse or empty, the product suggests
   the right thing (SRCH-8).
6. **Curated aliases** — moderators can guarantee recall for important entities by
   recording their alternate names and spellings (SRCH-9).

> **Required equivalences.** This spec defines the *families of variation* search
> must tolerate by example (below). The list is illustrative of the required
> behavior, not exhaustive, and deliberately says nothing about the matching
> technique used to achieve it.

---

## Stories

### Finding & scope

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
> - **Then** tracks match on title, reciter name, album, release date, and lyrics
>   content; reciters match on name; albums match on title and reciter.
> - **Given** a multi-word query (e.g. `nadeem sarwar karbala`), **then** all terms
>   narrow the results together, regardless of the order I type them.

### Forgiving, transliteration-aware matching

> **SRCH-3 — As a visitor, I want search to treat the many spellings of a word as the same, so that I find what I mean without knowing the "right" spelling.**
>
> **Acceptance criteria**
> - **Given** any word that has multiple common transliterations, **when** I type any
>   one of them, **then** I find entries recorded under any of the others.
> - **Then** at minimum, the following families are treated as the same word:
>   - **Vowel variation** — missing, added, lengthened, or swapped vowels:
>     `Husain` = `Husayn` = `Hussain` = `Hussein` = `Hossain`; `Mohammed` =
>     `Muhammad`; `salam` = `salaam`.
>   - **Doubled vs single consonants** — `Abbas` = `Abas`; `Sajjad` = `Sajad`;
>     `Allah` = `Alah`.
>   - **Interchangeable consonants/digraphs** — `k`↔`q` (`Qasim` = `Kasim`),
>     `w`↔`v` (`Wilayat` = `Vilayat`), `s`↔`th` (`Usman` = `Uthman`),
>     `z`↔`s` where applicable (`Zehra` = `Zahra`), and clusters like `kh`, `gh`,
>     `ch`, `sh` treated as units.
>   - **Elided glottals/diacritical marks** — apostrophes for *ayn*/*hamza* are
>     optional: `Ma'soom` = `Masoom`; `Du'a` = `Dua`.
>   - **Word-ending variation** — `Fatima` = `Fatimah`; `nawha` = `noha` = `nauha`;
>     `Karbalai` = `Karbalaee`.
>   - **Word splitting/joining** — `Abdullah` = `Abd Allah`; `Karbala` = `Kerbala`.
> - **Given** a matched result, **then** the matching portion is highlighted so I can
>   see why it matched.
> - **Edge cases** — Forgiveness must not flood me with noise: clearly unrelated
>   words do not match, and the closest spellings rank first (see SRCH-6).

> **SRCH-4 — As a visitor, I want case, accents, and punctuation ignored, so that small surface differences never hide a result.**
>
> **Acceptance criteria**
> - **Given** a query that differs only by letter case, **then** it matches (search
>   is case-insensitive).
> - **Given** a query or content with diacritics/accents (e.g. `Ḥusayn`, `Karbalāʾ`),
>   **then** matching ignores those marks in both directions.
> - **Given** punctuation, apostrophes, or hyphens in the query or content, **then**
>   they do not prevent a match (`Ali-Akbar` = `Ali Akbar` = `AliAkbar`).

> **SRCH-5 — As a visitor, I want to search in either the Latin or the native script, so that script choice never limits what I can find.**
>
> **Acceptance criteria**
> - **Given** an entity that exists in a native script (e.g. an Urdu source title or
>   name), **when** I search using its Latin transliteration, **then** I find it.
> - **Given** I type in the native script, **then** I find entities recorded in Latin
>   transliteration for the same word.
> - **Given** both script forms exist for an entity, **then** it appears once, not as
>   duplicates.

> **SRCH-6 — As a visitor, I want the most likely result first, so that forgiving matching never buries the obvious answer.**
>
> **Acceptance criteria**
> - **Given** results spanning exact, close-variant, and loose matches, **then** they
>   are ordered best-first: exact and near-exact spellings rank above looser
>   sound-alike matches.
> - **Given** several results match comparably well, **then** more popular/more
>   listened-to entities rank higher (see [01 — Catalog & Discovery](./01-catalog-and-discovery.md), CAT-10).
> - **Given** a query that exactly names one entity, **then** that entity is the top
>   result even if many looser matches exist.

> **SRCH-7 — As a visitor, I want suggestions as I type, so that I can pick the right entity without spelling it out in full or correctly.**
>
> **Acceptance criteria**
> - **Given** I have typed only a few characters, **then** the most likely reciters,
>   tracks, and albums are suggested, ranked by likelihood and popularity.
> - **When** I select a suggestion, **then** I go straight to that entity, bypassing
>   spelling entirely.
> - **Given** my partial input is itself a variant spelling, **then** suggestions
>   still surface the intended entities (the variant tolerance of SRCH-3 applies to
>   type-ahead too).

> **SRCH-8 — As a visitor, I want to be guided when my query finds little or nothing, so that I'm never left at a dead end.**
>
> **Acceptance criteria**
> - **Given** a query that matches nothing, **then** I see a clear "no results" state
>   with the closest suggestions ("did you mean …?") that I can select directly.
> - **Given** a query that matches very little, **then** likely-intended alternatives
>   are offered alongside the matches.
> - **Given** I select a suggested correction, **then** the search re-runs for it.

### Curated recall

> **SRCH-9 — As a moderator, I want to record alternate names and spellings for an entity, so that important content is always findable however people search for it.**
>
> **Acceptance criteria**
> - **Given** a reciter, album, or track, **then** I can record alternate names,
>   known spellings, and aliases for it (e.g. a reciter known by several names, or a
>   track widely known by its first line).
> - **Given** a user searches using any recorded alias, **then** the entity is found
>   and ranks as a strong match.
> - **Given** recorded aliases exist, **then** they also inform type-ahead (SRCH-7)
>   and "did you mean" suggestions (SRCH-8).
> - **Then** alias management follows the standard change history (see [06 — Contributions & Moderation](./06-contributions-and-moderation.md)).

### Breadth

> **SRCH-10 — As a visitor, I want search to also reach tags and topics, so that thematic discovery is searchable too.**
>
> **Acceptance criteria**
> - **Given** a query that matches a tag (by key, value, or `key:value`) or a topic,
>   **then** that tag/topic appears in results and leads to its collection of tracks
>   (see [01 — Catalog & Discovery](./01-catalog-and-discovery.md)).

> **SRCH-11 — As a visitor, I want a full results page for a query, so that I can review and page through many matches, not just a quick preview.**
>
> **Acceptance criteria**
> - **Given** a query with many matches, **then** I can open a dedicated results
>   view that lists all matches, grouped or filterable by type, and paged.
> - **Then** I can play a track directly from results without first opening its page.

> **SRCH-12 — As a visitor, I want public playlists to be discoverable, so that community-curated lists surface in search.**
>
> **Acceptance criteria**
> - **Given** a public playlist whose title or contents match my query, **then** it
>   appears in results and links to the playlist (see [05 — Library & Playlists](./05-library-and-playlists.md)).
> - **Given** a private playlist, **then** it never appears in anyone else's results.
