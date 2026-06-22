# Spec 06 — Stories

## Overview

**Stories** are editorial articles — news, history, announcements, and features.
Visitors read published stories; moderators author and manage them through a
draft/publish workflow with an optional hero image and a display date.

## Personas

- **Visitor** — read published stories.
- **Moderator** — create, edit, publish/unpublish, and delete stories.
  (Contributors have no story privileges beyond reading.)

## User Stories

### Reading stories

#### Story: Discover the latest stories
**As a** visitor, **I want** to see the latest stories, **so that** I can keep
up with news and editorial content.

**Acceptance Criteria**
- The home page surfaces a "latest stories" section showing recent **published**
  stories as cards.
- Each card links to the full story.

#### Story: Read a story
**As a** visitor, **I want** to read a full story, **so that** I can engage with
the content.

**Acceptance Criteria**
- A story page shows the title, display date, hero image (when present), excerpt,
  and body.
- A story is addressable by its display date and slug.
- **Given** a story is not published, **When** a visitor requests it, **Then**
  they receive a not-found response.

### Visibility rules

#### Story: Only see published stories as a visitor or contributor
**As a** visitor or contributor, **I want** to see only published stories, **so
that** unfinished drafts aren't exposed.

**Acceptance Criteria**
- Visitors and contributors see only published stories in any story listing.
- Visitors and contributors cannot retrieve an unpublished story.
- Moderators see both published and unpublished stories in listings, with
  published stories ordered ahead of drafts.

### Managing stories (moderator)

#### Story: Create a story
**As a** moderator, **I want** to create a story, **so that** I can publish
editorial content.

**Acceptance Criteria**
- A moderator can create a story with: title (required), slug, display date,
  hero image, excerpt, body, and a published toggle.
- A new story defaults to **unpublished** (draft).
- The slug is auto-generated from the title when not supplied.
- Slugs must be unique; attempting a duplicate slug is rejected with a
  validation error.
- The display date must be a valid date.

#### Story: Edit a story
**As a** moderator, **I want** to edit an existing story, **so that** I can
correct or update it.

**Acceptance Criteria**
- A moderator can edit any field of a story.
- Changing a slug to one already used by another story is rejected.

#### Story: Publish and unpublish
**As a** moderator, **I want** to control whether a story is publicly visible,
**so that** I can stage and retract content.

**Acceptance Criteria**
- A moderator can publish a draft (making it public and recording when it was
  published) and can unpublish a published story (removing it from public view).

#### Story: Set or upload a hero image
**As a** moderator, **I want** a hero image for a story, **so that** it looks
compelling in listings and at the top of the article.

**Acceptance Criteria**
- A moderator can provide a hero image either by uploading a file or by
  supplying an external image address.
- Uploading with no file is rejected with a validation error.
- An external image address provided for a story is preserved as-is.
- Only moderators can set/upload a story hero image (contributors are
  forbidden; signed-out users are unauthorized).

#### Story: Delete a story
**As a** moderator, **I want** to delete a story, **so that** obsolete content
can be removed.

**Acceptance Criteria**
- A moderator can delete a story (with a confirmation step in the UI).

#### Story: Manage stories from a dashboard
**As a** moderator, **I want** a stories management list, **so that** I can see
and manage all stories in one place.

**Acceptance Criteria**
- A moderator-only stories list shows each story's title, slug, and status
  (published/draft), paginated, with edit access and a "new story" action.
- After create/save/delete, the moderator receives confirmation feedback.

## Story fields

| Field | Notes |
|---|---|
| Title | Required |
| Slug | Unique; auto-generated from title if omitted |
| Display date | Optional; must be a valid date; used in the story's address |
| Hero image | Optional; uploaded file or external address |
| Excerpt | Optional short summary |
| Body | Optional main content |
| Published | Boolean; drives public visibility; records when published |

## Notes

- Story management is moderator-only in both intent and as-built — there is no
  contributor-level story drafting.
