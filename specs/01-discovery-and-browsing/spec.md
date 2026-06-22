# Spec 01 — Discovery & Browsing

## Overview

The catalog is the heart of Nawhas.com. Anyone can browse it freely without an
account. Content is organized as **Reciter → Album → Track**, and visitors
discover content through a home page, browsable reciter listings, and
popularity-driven surfaces ("trending", "top reciters", "top nawhas").

Browsing is also where playback and contribution entry points live: every
track surface offers play / queue controls (see [Audio Player](../02-audio-player-and-queue/spec.md)),
and moderators/contributors see inline editing entry points (see
[Content Management](../08-content-management/spec.md)).

## Personas

Primarily **Visitor** (all browsing is public). Contributors additionally see a
"recently saved" surface; moderators/contributors additionally see editing
affordances, specified elsewhere.

## User Stories

### Home & discovery

#### Story: Land on a home page that orients me
**As a** visitor, **I want** a home page that surfaces popular and recent
content, **so that** I can start exploring without knowing what to search for.

**Acceptance Criteria**
- The home page presents a prominent hero area with a search entry point.
- It surfaces a **trending nawhas** section (a small set of currently popular
  tracks).
- It surfaces a **latest stories** section (most recent published stories).
- It surfaces a **top reciters** section (most popular reciters).
- It surfaces a **top nawhas** ranked list (an ordered list of the most popular
  tracks).
- **Given** I am signed in, a **recently saved nawhas** section appears showing
  tracks from my library; **Given** I am not signed in, that section is absent
  or shows an invitation to sign in.

#### Story: Trending and popularity reflect real engagement
**As a** visitor, **I want** "trending" and "top" rankings to reflect what
people are actually listening to and viewing, **so that** popularity surfaces
are meaningful.

**Acceptance Criteria**
- Popularity of reciters and tracks is derived from how often they are viewed /
  played over time.
- The system supports popularity over multiple time windows (e.g. day, week,
  month, year, and all-time); discovery surfaces choose an appropriate window
  (e.g. "trending this month" vs. an all-time "top" list).
- Viewing a reciter or a track contributes to its popularity.

### Browsing reciters

#### Story: Browse all reciters
**As a** visitor, **I want** to browse the full list of reciters, **so that** I
can find a performer I'm interested in.

**Acceptance Criteria**
- A reciters page lists reciters, with the most popular reciters featured first
  and the remainder shown in a paginated grid.
- Each reciter is represented by an avatar and name.
- I can page through all reciters.
- I can filter/search the reciters from this page.
- Selecting a reciter opens that reciter's profile.

#### Story: View a reciter profile
**As a** visitor, **I want** a reciter's profile page, **so that** I can see
who they are and browse their work.

**Acceptance Criteria**
- The profile shows the reciter's avatar, name, and (if present) description.
- It shows a **top nawhas** section of that reciter's most popular tracks.
- It shows the reciter's **albums**, paginated, each with title and year.
- Selecting an album opens the album page.
- A reciter with no albums shows a friendly empty state (e.g. "We don't have any
  albums for `<reciter>` yet").
- **Given** I am a moderator, edit and "add album" entry points are visible
  (see [Content Management](../08-content-management/spec.md)).

### Browsing albums

#### Story: View an album and its tracks
**As a** visitor, **I want** an album page listing its tracks, **so that** I can
choose what to listen to.

**Acceptance Criteria**
- The album page shows artwork, album title, reciter name, year, and track
  count.
- Tracks are shown as a numbered list; each entry links to the track page.
- Play-album and add-album-to-queue controls are available when the album has
  playable tracks (see [Audio Player](../02-audio-player-and-queue/spec.md)).
- Selecting a track opens the track page.
- **Given** I am a moderator or contributor, edit-album, add-track, and
  edit-track entry points are visible.

### Browsing tracks (nawhas)

#### Story: View a track page
**As a** visitor, **I want** a track page with everything about a nawha, **so
that** I can listen, read, and explore related content.

**Acceptance Criteria**
- The track page shows the track title, reciter name, album, and year.
- Playback controls are available: play / stop and add-to-queue.
- A **lyrics / write-up** area shows the track's lyrics, or a friendly empty
  state when none exist (e.g. "We don't have a write-up of this nawha yet")
  (see [Lyrics](../03-lyrics/spec.md)).
- When the track's lyrics are time-synced, a "synchronized with audio" badge is
  shown.
- When the track has a video, an embedded video player is shown.
- A "more from this album" area lists the album's other tracks.
- A **print** entry point is available for the lyrics.
- **Given** I am signed in, a **favorite** toggle lets me save/unsave the track
  (see [Personal Library](../04-personal-library/spec.md)).
- **Given** I am a moderator or contributor, edit-lyrics and edit-track entry
  points are visible.

### Stable, shareable locations

#### Story: Reach content via meaningful, shareable addresses
**As a** visitor, **I want** human-readable, stable addresses for reciters,
albums, tracks, and stories, **so that** I can share and bookmark them.

**Acceptance Criteria**
- Reciters, albums, tracks, and stories are addressable by readable identifiers
  derived from their names/titles (slugs), not just opaque IDs.
- Track and album addresses are nested under their reciter (and album), e.g. a
  track lives under its reciter and album.
- Stories are addressable by their display date and slug.
- Requesting an entity that does not exist, or a nested entity that does not
  belong to its parent (e.g. a track under the wrong album), results in a
  not-found response.

## Empty states

- Reciter with no albums → "We don't have any albums for `<reciter>` yet".
- Track with no lyrics → "We don't have a write-up of this nawha yet".
- Signed-out home page → recently-saved section invites sign-in or is hidden.

## Notes

- All read/browse behavior in this spec is fully public (no account required).
- Counts of plays/views drive the popularity surfaces but are not themselves
  shown to visitors as raw numbers.
