# 06 — Contributions & Moderation

How the catalog grows and stays accurate: contributors **propose** changes,
moderators **review** them, the catalog gets **edited**, and every change is
**recorded**. This is the engine of the community.

Prerequisites: [00 — Foundation](./00-foundation.md), especially §5 *Content
lifecycle* and §6.2 *Editing safety*.

---

## Principles

- **Contributors propose; moderators decide.** A contributor's catalog change does
  not go live until a moderator approves it.
- **Moderators may also act directly.** Moderators (and admins) can edit the live
  catalog without a queue step, taking effect immediately. They may still *choose*
  to submit a proposal instead (e.g. to invite a second opinion); the contributor
  stories below are available to every signed-in user, including moderators.
- **Everything that goes live is recorded** with who, what, when, and a before/after
  view.
- **Editing is safe under concurrency** via visible locks.

This applies uniformly to reciters, albums, tracks, and lyrics. Tags, topics, and
collections are curated by moderators directly.

---

## Stories — Contributors

### Proposing catalog changes

> **CON-1 — As a contributor, I want to propose a new reciter, album, or track, so that I can grow the library.**
>
> **Acceptance criteria**
> - **Given** I am signed in, **then** I can start a proposal to add a reciter (name,
>   biography, avatar), an album (title, artwork, reciter), or a track (title,
>   crediting reciter(s), release date, optional album, audio, optional video,
>   tags, topics).
> - **When** I submit, **then** the proposal enters the review queue and is **not**
>   visible in the live catalog.
> - **Then** I can see my own pending proposals and their status.
> - **Edge cases** — Required fields are validated before submission; a track may be
>   proposed with **no album** (a standalone single) and with **more than one**
>   reciter. Submitting a proposal requires a verified email (see
>   [08 — Accounts & Authentication](./08-accounts-and-authentication.md), AUTH-1b).

> **CON-2 — As a contributor, I want to propose edits to existing catalog items, so that I can correct or improve them.**
>
> **Acceptance criteria**
> - **Given** an existing reciter, album, or track, **when** I propose an edit,
>   **then** my proposed change is captured against the current version and queued
>   for review, without altering the live item.
> - **Then** I can see a before/after comparison of my own proposal.

> **CON-2b — As a contributor, I want to set or change a track's album membership and its order within an album, so that standalone singles and album tracks are organized correctly over time.**
>
> **Acceptance criteria**
> - **Given** a track, **then** I can propose placing it in an album, removing it from
>   its album (making it a standalone single), or moving it to a different album.
> - **Given** a track within an album, **then** I can propose its position in the
>   album's track order.
> - **When** I submit, **then** the change is queued for review like any other
>   proposal and does not alter the live catalog until approved.
> - **Edge cases** — Removing a track from its album never deletes the track; it
>   becomes a standalone single (see [01 — Catalog & Discovery](./01-catalog-and-discovery.md), CAT-7).

> **CON-2c — As a contributor, I want to change which reciters a track credits, so that collaborations and corrections are represented accurately.**
>
> **Acceptance criteria**
> - **Given** a track, **then** I can propose adding or removing crediting reciters
>   (a track may credit more than one — see CAT-6).
> - **When** I submit, **then** the change is queued for review and does not alter the
>   live track until approved.
> - **Edge cases** — A track must always credit **at least one** reciter; a proposal
>   that would leave it with none is rejected at submission.

### Contributing lyrics

> **CON-3 — As a contributor, I want to add or correct a track's lyrics, so that people can read along accurately.**
>
> **Acceptance criteria**
> - **Given** a track, **then** I can open a lyrics editor pre-filled with the
>   current lyrics (if any).
> - **Then** I can edit lines, group them into stanzas, and set **repeat counts**.
> - **When** I save, **then** my lyrics are submitted as a proposal for review; the
>   live lyrics are unchanged until approved.
> - **Edge cases** — I can save work-in-progress and return to it before submitting.

> **CON-3b — As a contributor, I want to provide lyrics in multiple renderings, so that readers can choose the script or language that suits them.**
>
> **Acceptance criteria**
> - **Given** the lyrics editor, **then** I can supply or edit any of: the **source**
>   text in its native script (e.g. Urdu, right-to-left), a **transliteration** in
>   Latin script, and one or more **translations** into other languages (each
>   labeled with its language) — see [03 — Lyrics](./03-lyrics.md).
> - **Given** I set the track's **primary language**, **then** the source and
>   transliteration are understood to be that language and the translations are
>   other languages.
> - **Given** the source and transliteration share the recitation structure, **then**
>   the editor keeps their lines/stanzas/repeats aligned line-for-line.
> - **Given** I add a translation that doesn't align one-to-one, **then** I can align
>   it at the stanza level.
> - **When** I submit, **then** the renderings enter the review queue together as a
>   single proposal, with a before/after comparison per rendering.
> - **Edge cases** — I may contribute a single rendering (e.g. only a transliteration);
>   I need not provide all three.
> - **Edge cases** — When I change the shared recitation structure (add, remove, or
>   reorder lines/stanzas), any existing timestamps and translation alignments that
>   are affected are flagged for re-checking rather than silently breaking (see CON-4,
>   and the display fallback in [03 — Lyrics](./03-lyrics.md), LYR-3).

> **CON-3c — As a contributor, I want to organize lyrics into labelled sections, so that the refrain and verses are structured the way reciters expect.**
>
> **Acceptance criteria**
> - **Given** the lyrics editor, **then** I can group stanzas into **sections** and
>   assign each a role: **Asthayi** (refrain), **Sani**, **Antra** (auto-numbered),
>   **Dohra**, **Maqta**, or a **custom** label (see [03 — Lyrics](./03-lyrics.md)).
> - **Given** the refrain (Asthayi), **then** I enter its text **once** and mark where
>   it recurs between stanzas rather than retyping it.
> - **Given** I label sections, **then** the labels apply to the shared recitation
>   structure, so they hold across the source and transliteration renderings (see
>   CON-3b).
> - **When** I submit, **then** the sections enter the review queue as part of the
>   lyrics proposal like any other lyrics change.
> - **Edge cases** — Sectioning is **optional**: a nawha with no refrain or named
>   structure can be left as plain stanzas. Antra numbering follows section order
>   automatically, so reordering sections renumbers the Antras accordingly.

> **CON-4 — As a contributor, I want to add timing to lyrics, so that they can highlight in time with the audio.**
>
> **Acceptance criteria**
> - **Given** the lyrics editor, **then** I can set a **timestamp** for each line by
>   listening to the track.
> - **Then** I can preview the synced highlighting against playback before submitting.
> - **Given** timing is set against the shared recitation structure, **then** it
>   applies to the source and transliteration renderings alike (and, where aligned,
>   to translations) — I do not time each rendering separately.
> - **When** I submit, **then** the synced lyrics enter the review queue like any
>   other lyrics proposal.

### Safe concurrent editing

> **CON-5 — As a contributor, I want editing to be protected from collisions, so that I don't overwrite someone else's in-progress work (and vice-versa).**
>
> **Acceptance criteria**
> - **Given** I begin editing a track's lyrics, **then** the lyrics become **locked**
>   to me for editing, and others see that it is being edited and by whom.
> - **When** I finish or cancel, **then** the lock is released and others may edit.
> - **Given** a lock is held by someone else, **then** I cannot start a conflicting
>   edit until it is released.
> - **Given** a lock is left dangling, **then** a moderator can override/release it
>   (see CON-9).

### Tracking my contributions

> **CON-6 — As a contributor, I want to track the status of my proposals, so that I know whether my work was accepted.**
>
> **Acceptance criteria**
> - **Given** I have submitted proposals, **then** I can see each one's status:
>   pending, approved, rejected (with reason), or amended-and-approved.
> - **When** a proposal is approved, **then** I receive attribution for it on my
>   public profile (see [08 — Accounts & Authentication](./08-accounts-and-authentication.md)).
> - **When** a proposal is rejected, **then** I can read the moderator's reason.

---

## Stories — Moderators

### The review queue

> **CON-7 — As a moderator, I want a queue of pending proposals, so that I can review community contributions in one place.**
>
> **Acceptance criteria**
> - **Given** pending proposals exist, **then** I see them listed with: what is being
>   changed, who proposed it, when, and a clear **before/after comparison**.
> - **Then** the queue is paged and ordered so the oldest/most-pending work is easy
>   to find.
> - **Given** a proposal touches lyrics, **then** the comparison highlights exactly
>   what changed line by line.

> **CON-8 — As a moderator, I want to approve, reject, or amend a proposal, so that only good changes reach the live catalog.**
>
> **Acceptance criteria**
> - **Given** a pending proposal, **when** I **approve** it, **then** the change goes
>   live immediately and is recorded in history attributed to the original
>   contributor (with my approval noted).
> - **When** I **reject** it, **then** it does not go live and I can provide a reason
>   the contributor can read.
> - **When** I **amend** it, **then** I can adjust the content before approving, and
>   both the contributor and I are reflected in the record.

> **CON-9 — As a moderator, I want to manage editing locks, so that abandoned edits don't block the queue.**
>
> **Acceptance criteria**
> - **Given** an item locked for editing, **then** I can see who holds the lock and
>   release/override it.
> - **When** I override a lock, **then** the previous holder is prevented from
>   silently overwriting newer content.

### Direct catalog management

> **CON-10 — As a moderator, I want to create and edit catalog items directly, so that I can curate efficiently without queuing my own work.**
>
> **Acceptance criteria**
> - **Given** I am a moderator, **when** I create or edit a reciter, album, or track,
>   **then** the change takes effect immediately in the live catalog.
> - **Then** I can upload a reciter **avatar**, album **artwork**, and track **audio**,
>   and set a track's **video**.
> - **Then** I can record **alternate names, spellings, and aliases** for an entity
>   to guarantee it is findable however people search (see [04 — Search](./04-search.md), SRCH-9).
> - **Then** every such change is recorded in history attributed to me.

> **CON-11 — As a moderator, I want to delete catalog items, so that I can remove erroneous or duplicate content.**
>
> **Acceptance criteria**
> - **Given** a reciter, album, or track, **when** I delete it, **then** I must
>   confirm, and the deletion is recorded in history.
> - **Given** deleting would orphan dependent content (e.g. deleting an album that
>   has tracks), **then** I am warned and the dependent content's fate is made
>   explicit (e.g. its tracks become standalone rather than silently disappearing).
> - **Given** I delete a **reciter** who credits tracks, **then** I am warned;
>   tracks crediting other reciters keep those credits, and any track that would be
>   left with **no** reciter is surfaced so I can reassign or delete it explicitly
>   rather than leaving it uncredited.

> **CON-12 — As a moderator, I want to publish and delete contributed lyrics, so that I control what readers see.**
>
> **Acceptance criteria**
> - **Given** proposed lyrics, **when** I publish them, **then** they replace the live
>   lyrics and the change is recorded.
> - **When** I delete a lyrics proposal, **then** it is removed from the queue.
> - **Given** live lyrics with multiple renderings, **then** I can delete or replace
>   an **individual** rendering (e.g. remove an inaccurate translation) while keeping
>   the others, and the change is recorded in history.
> - **Edge cases** — Deleting the source or transliteration does not remove the
>   shared recitation structure relied on by other renderings and by timing; if the
>   last remaining rendering is deleted, the track simply has no lyrics (per LYR-1).

### Tags, topics & collections

> **CON-13 — As a moderator, I want to manage tags, topics, and curated collections, so that thematic discovery stays meaningful.**
>
> **Acceptance criteria**
> - **Given** I am a moderator, **then** I can manage the set of tag **keys** and
>   their permitted **values**, and assign `key:value` tags to tracks.
> - **Then** I can create, rename, and remove **topics**, and curate each topic's
>   collection (which tracks it includes and their order).
> - **Given** a tag value or topic is removed, **then** affected tracks lose only
>   that association, not their other data.
> - **Edge cases** — Renaming a tag key or value updates it everywhere it is applied;
>   the same track may hold multiple values for one key.

---

## Stories — History & transparency

> **CON-14 — As a moderator, I want a complete history of catalog changes, so that I can audit what happened and by whom.**
>
> **Acceptance criteria**
> - **Given** the history view, **then** I see a time-ordered list of changes across
>   reciters, albums, and tracks.
> - **Then** each entry shows the **type** of change (created / modified / deleted),
>   the affected item, **who** made it, **when**, and a link to the item.
> - **Given** a modification, **then** the entry shows a **before/after** comparison of
>   what changed.
> - **Then** the history is paged and scannable, with change types visually
>   distinguished.

> **CON-15 — As a moderator, I want history to reflect attribution accurately for both proposals and direct edits, so that credit and accountability are correct.**
>
> **Acceptance criteria**
> - **Given** an approved proposal, **then** history attributes the content to the
>   contributor and notes the approving moderator.
> - **Given** a direct moderator edit, **then** history attributes it to that
>   moderator.

> **CON-16 — As a moderator, I want to correct or reverse a prior change using history, so that mistakes are recoverable.**
>
> **Acceptance criteria**
> - **Given** a past change, **then** I can see the prior state clearly enough to
>   restore it.
> - **When** I reverse a change, **then** the reversal itself is recorded as a new
>   history entry attributed to me.
