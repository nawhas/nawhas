# Spec 04 — Personal Library

## Overview

Signed-in users keep a personal **library** of saved (favorited) nawhas. The
library is private to the user and accessible from anywhere in the app. Saving
is a single-tap toggle available on track surfaces and within the player.

## Personas

**Contributor** (any signed-in user). Visitors cannot save tracks and are
prompted to sign in.

## User Stories

### Saving and unsaving

#### Story: Save a nawha to my library
**As a** contributor, **I want** to favorite a nawha, **so that** I can find and
replay it later.

**Acceptance Criteria**
- A favorite toggle is available on the track page, in track listings, and in
  the player.
- **Given** a track is not saved, **When** I tap favorite, **Then** it is added
  to my library and the control reflects the saved state.
- **Given** a track is saved, **When** I tap favorite again, **Then** it is
  removed from my library and the control reverts.
- The saved/unsaved state is reflected consistently across every place the track
  appears.

#### Story: Be prompted to sign in to save
**As a** visitor, **I want** to be invited to sign in when I try to save, **so
that** I understand saving requires an account.

**Acceptance Criteria**
- Favoriting is unavailable (or prompts sign-in) for signed-out visitors.
- The library landing page, when viewed signed-out, presents a welcome / "get
  started" prompt that leads into sign-up.

### Viewing the library

#### Story: See my recently saved nawhas
**As a** contributor, **I want** a quick view of what I recently saved, **so
that** I can jump back in.

**Acceptance Criteria**
- A library home shows a small set of recently saved nawhas with a way to view
  all.
- The signed-in home page also surfaces a "recently saved" section (see
  [Discovery & Browsing](../01-discovery-and-browsing/spec.md)).
- Visiting the library while signed in goes to the library; while signed out it
  shows the sign-in prompt.

#### Story: Browse all my saved nawhas
**As a** contributor, **I want** a full, paginated list of my saved nawhas, **so
that** I can manage my whole collection.

**Acceptance Criteria**
- A "saved nawhas" page lists all saved tracks, paginated, each with metadata
  and reciter.
- The page offers a "play all" control that queues and plays the saved tracks.
- Track titles link to their track pages.
- I can unsave a track directly from this list, and it updates immediately.
- **Given** I have saved nothing, a friendly empty state is shown (e.g. "Keep
  track of nawhas you love.").

## Notes

- The library reflects changes immediately after saving/unsaving anywhere in the
  app.
- Saved order is preserved when listing the library.
