# Spec 03 — Lyrics

## Overview

Every track may have a **write-up** of its lyrics. Lyrics come in two forms:
**plain text** and **time-synced** (lines carry timestamps so they can highlight
in time with the audio). Lyrics are the primary community-contributed content,
and they flow through an explicit **draft → moderator-publish** workflow with an
editing **lock** to prevent two people clashing on the same track.

This spec covers reading lyrics, printing them, and the full contribution and
moderation lifecycle for lyrics. The moderator review queue is summarized here
and also referenced from [Revision History & Audit](../09-revision-history-and-audit/spec.md).

## Personas

- **Visitor** — read and print lyrics.
- **Contributor** — create and edit lyrics drafts.
- **Moderator** — publish, delete, and override locks on drafts.

## User Stories

### Reading lyrics

#### Story: Read a track's lyrics
**As a** visitor, **I want** to read a nawha's lyrics, **so that** I can follow
along or study the text.

**Acceptance Criteria**
- The track page shows the published lyrics when they exist.
- When no lyrics exist, a friendly empty state is shown ("We don't have a
  write-up of this nawha yet").
- Repeated lines are rendered with a repeat indicator (e.g. "(x2)").
- Spacing/stanza breaks in the lyrics are preserved in the rendering.

#### Story: Know when lyrics are synced
**As a** visitor, **I want** to see whether lyrics are synced to the audio, **so
that** I know I can follow along during playback.

**Acceptance Criteria**
- When a track's lyrics are time-synced, a "synchronized with audio" badge is
  shown on the track.
- During playback, synced lyrics highlight/advance line-by-line in time with the
  audio (see [Audio Player](../02-audio-player-and-queue/spec.md)).

#### Story: Print lyrics
**As a** visitor, **I want** a printable version of the lyrics, **so that** I can
have a physical copy.

**Acceptance Criteria**
- A print action is available on the track page and from the player.
- The printable view is a clean, print-optimized layout: site/track heading and
  metadata, with the lyrics laid out for reading on paper (multi-column).
- The printable view contains no interactive UI.
- The printable view has its own shareable address.

### Contributing lyrics (drafts)

#### Story: Start or edit a lyrics draft
**As a** contributor, **I want** to write or improve a track's lyrics, **so
that** the catalog has accurate text.

**Acceptance Criteria**
- From a track, a contributor can open a lyrics editor and create a draft, or
  continue an existing draft.
- A track has **at most one** active draft at a time.
- The editor supports both **plain text** and **time-synced** modes; a
  contributor can enter plain lines or attach timestamps to lines for syncing.
- The editor supports marking a line as repeated (with a repeat count).
- Saving stores the draft without affecting the published lyrics.
- Anonymous visitors cannot create or edit drafts.

#### Story: See a pending draft before it's published
**As a** visitor, **I want** to see that a track already has a pending lyrics
draft, **so that** effort isn't duplicated.

**Acceptance Criteria**
- The existence and content of a track's pending draft is viewable.
- A draft can only be created against a track that exists.

### Editing locks

#### Story: Avoid clashing edits with a lock
**As a** contributor, **I want** the draft I'm editing to be locked to me, **so
that** someone else doesn't overwrite my work mid-edit.

**Acceptance Criteria**
- When a user begins editing a draft, it becomes **locked** to that user.
- A lock automatically expires after a period of inactivity (about an hour).
- The lock owner can release (unlock) their own lock.
- **Given** a draft is locked by another user, **When** a different contributor
  tries to edit it, **Then** the edit is blocked with a "locked by another user"
  indication.
- After a user saves their changes, the lock is released so others may take it.

#### Story: Moderators can break locks
**As a** moderator, **I want** to override another user's lock, **so that** I'm
never blocked from reviewing or correcting a draft.

**Acceptance Criteria**
- A moderator can unlock a draft locked by anyone (including other moderators).
- A contributor **cannot** override a lock held by a moderator (neither edit,
  unlock, nor re-lock it).
- A moderator can delete a draft even when another user holds the lock.

### Publishing & moderation

#### Story: Publish a draft as the official lyrics
**As a** moderator, **I want** to publish a draft, **so that** approved lyrics
become the track's official write-up.

**Acceptance Criteria**
- Only a moderator can publish a draft.
- **When** a draft is published, **Then** its content replaces the track's
  published lyrics (preserving plain-text vs. synced form) and the draft is
  cleared.
- A contributor attempting to publish is forbidden; their draft simply waits for
  a moderator.

#### Story: Delete an unwanted draft
**As a** moderator, **I want** to delete a draft, **so that** spam or incorrect
submissions don't linger.

**Acceptance Criteria**
- Only a moderator can delete a draft.
- A contributor cannot delete a draft (not even their own).

#### Story: Review pending drafts in one place
**As a** moderator, **I want** a queue of pending lyrics drafts, **so that** I
can review and publish them efficiently.

**Acceptance Criteria**
- A moderator-only "draft lyrics" review list shows pending drafts, paginated.
- Each entry shows the track, who last updated it and when, and a **before /
  after** comparison of current vs. drafted lyrics.
- Each entry offers a publish action.

## Permission summary

| Action | Visitor | Contributor | Moderator |
|---|:---:|:---:|:---:|
| Read published lyrics | ✓ | ✓ | ✓ |
| Print lyrics | ✓ | ✓ | ✓ |
| View a pending draft | ✓ | ✓ | ✓ |
| Create / edit a draft | ✗ | ✓ | ✓ |
| Lock / unlock own draft | ✗ | ✓ | ✓ |
| Override another user's lock | ✗ | ✗ | ✓ |
| Publish a draft | ✗ | ✗ | ✓ |
| Delete a draft | ✗ | ✗ | ✓ |

## Notes

- Lyrics are the one contribution type that, **as built**, already follows the
  intended "all contributions reviewed by a moderator" model. Album/track
  edits do not (see [Content Management](../08-content-management/spec.md)).
