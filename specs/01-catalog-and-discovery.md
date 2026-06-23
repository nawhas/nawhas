# 01 — Catalog & Discovery

How users browse and understand the library: reciters, albums, tracks, the
relationships between them, and the curated layers (tags and topics) on top.
Editing the catalog is covered in [06 — Contributions & Moderation](./06-contributions-and-moderation.md);
this spec covers **viewing and navigating** it, plus the shape of each entity as
users experience it.

Prerequisites: [00 — Foundation](./00-foundation.md).

---

## Entity shapes (as users perceive them)

- **Reciter** — name, description/biography, avatar image, the albums and tracks
  attributed to them, and an indication of how prolific/popular they are.
- **Album** — title, artwork, owning reciter, and its ordered tracks. An optional,
  legacy grouping; it has no intrinsic year (chronology comes from the tracks'
  release dates).
- **Track (nawha)** — title; **one or more** crediting reciters; a **release
  date**; optional album; audio; optional video; optional lyrics; tags; topics;
  and a popularity signal.
- **Tag** — a structured **key:value** label (e.g. `language:urdu`,
  `occasion:ashura`) that groups and filters tracks across reciters and albums.
- **Topic** — a curated theme that assembles a collection of tracks.

---

## Stories

### Home & landing

> **CAT-1 — As a visitor, I want a welcoming home page that surfaces the best of the library, so that I can start listening or exploring immediately.**
>
> **Acceptance criteria**
> - **Given** I open the home page, **then** I see, at minimum: trending/popular
>   tracks, prominent/top reciters, featured stories, and a ranked list of the
>   most popular nawhas.
> - **When** I am signed in, **then** the home page additionally surfaces my
>   **recently saved** nawhas as a personalized shortcut.
> - **When** I select any surfaced reciter, album, track, or story, **then** I am
>   taken to its detail page.
> - **Edge cases** — With an empty or sparse library, each section degrades
>   gracefully (hides or shows an explanatory empty state) rather than appearing
>   broken.

### Browsing reciters

> **CAT-2 — As a visitor, I want to browse all reciters, so that I can explore the library by performer.**
>
> **Acceptance criteria**
> - **Given** the reciters listing, **then** I see reciters with their avatar,
>   name, and a sense of how many albums/tracks they have.
> - **Given** more reciters than fit on one screen, **then** I can page through
>   the full set, and a set of top/featured reciters is highlighted.
> - **When** I select a reciter, **then** I land on that reciter's profile.

> **CAT-3 — As a visitor, I want a reciter profile page, so that I can see who they are and everything they've recited.**
>
> **Acceptance criteria**
> - **Given** a reciter profile, **then** I see their name, biography, avatar, and
>   a banner.
> - **Then** I see their most popular/top tracks for a quick start.
> - **Then** I see their albums, paged if numerous, each showing its tracks.
> - **Then** I can also reach standalone tracks (tracks not in any album) credited
>   to this reciter.
> - **When** I select a track or album, **then** I navigate to its detail page.
> - **Edge cases** — A reciter with no albums still shows any standalone tracks; a
>   reciter with no content at all shows an explanatory empty state.

### Browsing albums

> **CAT-4 — As a visitor, I want an album page, so that I can see and play a grouped set of tracks.**
>
> **Acceptance criteria**
> - **Given** an album page, **then** I see its artwork, title, owning reciter, and
>   track count.
> - **Then** I see the album's tracks in order, each with its position, release
>   date, and key metadata.
> - **Then** I can **play the whole album** or **add the whole album to my queue**
>   in one action (see [02 — Playback](./02-playback.md)).
> - **When** I select a track, **then** I open its detail page.
> - **Notes** — An album is an optional, legacy grouping; it carries no year of its
>   own. Any chronology shown is derived from its tracks' release dates.

### Tracks

> **CAT-5 — As a visitor, I want a track page, so that I can listen, read along, and understand the recitation in context.**
>
> **Acceptance criteria**
> - **Given** a track page, **then** I see its title, crediting reciter(s),
>   album (if any), release date, and artwork.
> - **Then** I have full playback controls for the track (see [02 — Playback](./02-playback.md)).
> - **Then**, if lyrics exist, I see them, with an indication when they are
>   time-synced (see [03 — Lyrics](./03-lyrics.md)).
> - **Then**, if a video is provided, I can watch it.
> - **Then** I see related tracks — at minimum, the rest of the album (if any) and
>   more from the crediting reciter(s) — with the current track indicated.
> - **Edge cases** — A track with no album still presents fully, substituting
>   "more from this reciter" / related-by-tag/topic for the album list. A track
>   with no lyrics shows an invitation to contribute them (to contributors) or a
>   neutral empty state (to visitors).

> **CAT-6 — As a visitor, I want tracks to credit multiple reciters when applicable, so that collaborations are represented accurately.**
>
> **Acceptance criteria**
> - **Given** a track recited by more than one reciter, **then** all crediting
>   reciters are shown and each links to their profile.
> - **Given** such a track, **then** it appears under **each** crediting reciter's
>   profile.

> **CAT-7 — As a visitor, I want to find and play standalone tracks that are not part of any album, so that singles are first-class.**
>
> **Acceptance criteria**
> - **Given** a track that belongs to no album, **then** it is browsable, playable,
>   searchable, and linkable exactly like an album track.
> - **Given** a reciter's profile, **then** standalone tracks are reachable
>   alongside albums.

> **CAT-7b — As a visitor, I want to browse a reciter's tracks by release date, so that chronology works regardless of albums.**
>
> **Acceptance criteria**
> - **Given** a reciter's profile, **then** I can view their tracks grouped or
>   ordered by **release date** (e.g. by year or period), spanning both album tracks
>   and standalone tracks.
> - **Given** tracks share a release period, **then** they group together regardless
>   of which album (if any) they belong to.
> - **Edge cases** — A track with no known release date is grouped under an
>   "undated" bucket rather than omitted.

### Tags & topics

> **CAT-8 — As a visitor, I want tracks to carry structured key:value tags, so that I can discover and filter related material precisely.**
>
> **Acceptance criteria**
> - **Given** a track with tags, **then** its tags are visible on the track page as
>   **key:value** pairs (e.g. `language:urdu`, `occasion:ashura`).
> - **When** I select a tag value, **then** I see a list of all tracks carrying that
>   exact key:value, which I can play and page through.
> - **When** I select a tag **key**, **then** I can see the values available for that
>   key and narrow to one.
> - **Given** a track carries several values for the same key, **then** it appears
>   under each of those values.

> **CAT-9 — As a visitor, I want curated collections by topic, so that I can find material appropriate to a theme or occasion.**
>
> **Acceptance criteria**
> - **Given** topics exist, **then** I can browse the available topics.
> - **When** I open a topic, **then** I see its curated collection of tracks with a
>   title and description, which I can play and page through.
> - **Edge cases** — A topic with no tracks is not surfaced to visitors.

### Popularity & ranking

> **CAT-10 — As a visitor, I want to see what's most listened to, so that I can find the most valued recitations.**
>
> **Acceptance criteria**
> - **Given** the home page or a reciter profile, **then** popularity-ranked tracks
>   and reciters are presented.
> - **Given** popularity is derived from real listening over time, **then** rankings
>   reflect aggregate visits/plays, not manual ordering.
> - **When** I filter popular tracks by a specific reciter, **then** I see that
>   reciter's most popular tracks.

### Resilient links

> **CAT-11 — As a visitor, I want previously shared links to keep working after content is renamed or reorganized, so that bookmarks and shared URLs don't rot.**
>
> **Acceptance criteria**
> - **Given** a reciter, album, or track has been renamed or moved, **when** I
>   follow an older link to it, **then** I arrive at the correct current page.
> - **Given** a link points to something that no longer exists, **then** I see a
>   clear "not found" state with a path back into the library.
