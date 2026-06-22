# Spec 08 — Content Management

## Overview

The catalog (reciters, albums, tracks, and their media) is maintained by
contributors and moderators directly within the browsing experience: editing
entry points appear inline on the same pages visitors use to browse. This spec
covers creating, editing, and deleting catalog entities and uploading media.

Lyrics are managed through their own draft/publish workflow — see
[Lyrics](../03-lyrics/spec.md).

> ⚠️ **Intent vs. as-built — approval.** The intended model is that all
> contributions are reviewed by a moderator before going live. **As built,
> album and track changes by contributors take effect immediately** and are
> recorded in the [revision history](../09-revision-history-and-audit/spec.md)
> for post-hoc moderator review — there is no pre-publish approval queue for
> them. Reciter records can only be changed by moderators. This spec documents
> the as-built permission model and flags where it diverges from intent.

## Personas

- **Contributor** — create/edit/delete albums and tracks; upload album artwork
  and track audio.
- **Moderator** — everything a contributor can do, **plus** create/edit/delete
  reciters and upload reciter avatars.

## User Stories

### Reciters (moderator only)

#### Story: Add a reciter
**As a** moderator, **I want** to add a reciter, **so that** their work can be
cataloged.

**Acceptance Criteria**
- Only a moderator can create a reciter.
- A reciter requires a name; the name must be unique (a duplicate is rejected as
  "taken").
- A readable identifier (slug) is generated from the name.
- A description and avatar are optional.

#### Story: Edit a reciter
**As a** moderator, **I want** to edit a reciter's details, **so that** I can
keep them accurate.

**Acceptance Criteria**
- A moderator can change a reciter's name, description, and avatar.
- A changed name must remain unique.
- An "edit reciter" entry point appears on the reciter profile for moderators.

#### Story: Upload a reciter avatar
**As a** moderator, **I want** to upload an avatar image, **so that** the
reciter is visually identifiable.

**Acceptance Criteria**
- A moderator can upload an avatar image for a reciter.

#### Story: Delete a reciter
**As a** moderator, **I want** to delete a reciter, **so that** invalid entries
can be removed.

**Acceptance Criteria**
- Only a moderator can delete a reciter.

### Albums (contributor + moderator)

#### Story: Add an album
**As a** contributor, **I want** to add an album to a reciter, **so that** their
tracks can be organized by release.

**Acceptance Criteria**
- A contributor or moderator can create an album under a reciter; signed-out
  visitors cannot.
- An album requires a title and a year.
- Artwork is optional and may be uploaded (drag-and-drop supported).
- An "add album" entry point appears on the reciter profile for
  contributors/moderators.

#### Story: Edit or delete an album
**As a** contributor, **I want** to edit or delete an album, **so that** I can
correct mistakes.

**Acceptance Criteria**
- A contributor or moderator can change an album's title, year, and artwork, and
  can delete the album.
- An "edit album" entry point appears on the album page for
  contributors/moderators.

### Tracks (contributor + moderator)

#### Story: Add a track
**As a** contributor, **I want** to add a track to an album, **so that** a nawha
is available to listen to.

**Acceptance Criteria**
- A contributor or moderator can create a track under an album; signed-out
  visitors cannot.
- A track requires a title; a readable identifier (slug) is generated from the
  title and is unique within its album.
- A track may have audio (uploaded), a video address, and a language.
- An "add track" entry point appears on the album page for
  contributors/moderators.

#### Story: Edit or delete a track
**As a** contributor, **I want** to edit or delete a track, **so that** I can fix
its details or media.

**Acceptance Criteria**
- A contributor or moderator can change a track's title, audio, video, and
  language, and can delete the track.
- An "edit track" entry point appears on the track page for
  contributors/moderators.

#### Story: Upload track audio
**As a** contributor, **I want** to upload an audio file for a track, **so that**
people can listen to it.

**Acceptance Criteria**
- A contributor or moderator can upload an audio file for a track
  (drag-and-drop supported), with upload progress/status shown.
- A track without audio is not playable (see [Audio Player](../02-audio-player-and-queue/spec.md)).

### Accountability

#### Story: Every catalog change is recorded
**As a** moderator, **I want** every change to reciters, albums, and tracks
recorded, **so that** I can review and trust contributor activity.

**Acceptance Criteria**
- Creating, modifying, or deleting a reciter, album, or track is captured in the
  revision history with the change, who made it, and when (see
  [Revision History & Audit](../09-revision-history-and-audit/spec.md)).

## Permission summary

| Action | Visitor | Contributor | Moderator |
|---|:---:|:---:|:---:|
| Create / edit / delete reciter | ✗ | ✗ | ✓ |
| Upload reciter avatar | ✗ | ✗ | ✓ |
| Create / edit / delete album | ✗ | ✓ | ✓ |
| Upload album artwork | ✗ | ✓ | ✓ |
| Create / edit / delete track | ✗ | ✓ | ✓ |
| Upload track audio | ✗ | ✓ | ✓ |

## Catalog fields

- **Reciter:** name (required, unique), description, avatar.
- **Album:** title (required), year (required), artwork; belongs to a reciter.
- **Track:** title (required), audio, video address, language, lyrics; belongs
  to an album (and reciter).

## Notes

- ⚠️ The contributor-direct-edit behavior for albums and tracks is the primary
  divergence from the intended "all contributions approved" model and is the
  most likely target for change in a rewrite.
