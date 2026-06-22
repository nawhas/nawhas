# Spec 09 — Revision History & Audit

## Overview

Moderators have an audit trail of changes to the catalog and a review queue for
pending lyrics. This is how the platform stays trustworthy while letting
contributors edit the catalog directly: every change is attributable and
reviewable after the fact.

## Personas

**Moderator** only. These tools are not visible to visitors or contributors.

## User Stories

### Revision history

#### Story: Review the history of catalog changes
**As a** moderator, **I want** a chronological history of changes to reciters,
albums, and tracks, **so that** I can monitor contributor activity and catch
mistakes or abuse.

**Acceptance Criteria**
- A moderator-only revision history lists changes, most recent first, paginated.
- Each entry shows: the kind of entity (reciter, album, or track), the entity's
  name, the **change type** (created, modified, or deleted), when it happened,
  and **who** made it.
- Change types are visually distinguished (e.g. created / modified / deleted).

#### Story: See exactly what changed
**As a** moderator, **I want** a before/after comparison for modifications, **so
that** I can understand the precise edit.

**Acceptance Criteria**
- For a modification, the entry shows a field-level before/after comparison.
- Only fields that actually changed are shown.
- For a creation, the entry reflects the initial values; for a deletion, the
  final values before removal.

#### Story: Empty state
**As a** moderator, **I want** a clear empty state, **so that** I know when
there's nothing to review.

**Acceptance Criteria**
- When there is no history to show, a friendly empty state is displayed.

### Lyrics review queue

#### Story: Review and publish pending lyrics
**As a** moderator, **I want** a queue of pending lyrics drafts with diffs, **so
that** I can approve contributions quickly.

**Acceptance Criteria**
- A moderator-only "draft lyrics" list shows pending drafts, paginated.
- Each entry shows the track, who last edited it and when, and a before/after
  comparison of the published lyrics vs. the draft.
- Each entry offers a publish action that promotes the draft to the track's
  official lyrics (see [Lyrics](../03-lyrics/spec.md)).

### Moderator workspace

#### Story: Access moderator tools in one place
**As a** moderator, **I want** a dedicated moderator area, **so that** I can
reach all my tools easily.

**Acceptance Criteria**
- A moderator workspace groups the moderation tools: revision history, draft
  lyrics review, and story management.
- Navigating to any moderator tool while **not** a moderator redirects away
  (e.g. to the home page); these areas are never exposed to non-moderators.

## What is auditable

| Entity | Tracked in revision history |
|---|:---:|
| Reciter | ✓ (created / modified / deleted) |
| Album | ✓ (created / modified / deleted) |
| Track | ✓ (created / modified / deleted) |
| Lyrics | Reviewed via the draft → publish queue (not the revision list) |
| Story | Managed directly by moderators (not in the revision list) |

## Notes

- The revision history is the **as-built** mechanism that compensates for
  contributors editing albums/tracks directly: review is *after* the change, not
  *before*. See the approval-model note in
  [Content Management](../08-content-management/spec.md).
- Each revision is attributed to its author; changes that originate from the
  system (rather than a specific user) may be unattributed.
