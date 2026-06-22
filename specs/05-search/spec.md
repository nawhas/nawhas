# Spec 05 — Search

## Overview

A global search is available from the top of the app on every page. It returns
results across reciters, albums, and tracks, and — importantly — matches against
**lyrics text**, so users can find a nawha by a remembered line.

## Personas

**Visitor** (search is fully public).

## User Stories

### Searching

#### Story: Search from anywhere
**As a** visitor, **I want** a search box always within reach, **so that** I can
look something up at any time.

**Acceptance Criteria**
- A global search entry point is present in the top bar on every page.
- The home hero also offers a prominent search entry point.
- On small screens, a search icon expands into a search field overlay, with a
  way to dismiss it.

#### Story: Get results as I type
**As a** visitor, **I want** results to appear as I type, **so that** I can find
things quickly.

**Acceptance Criteria**
- Results update interactively as the query changes.
- The results area indicates the current query (e.g. "Showing results for
  `<query>`").
- Selecting a result navigates to that reciter, album, or track.

#### Story: Search across all catalog types
**As a** visitor, **I want** search to cover reciters, albums, and tracks, **so
that** one box finds anything in the catalog.

**Acceptance Criteria**
- Results include matching **reciters** (by name and description).
- Results include matching **albums** (by title, year, and reciter name).
- Results include matching **tracks** (by title, reciter, album, year, **and the
  lyrics text**).
- Each result shows enough context to disambiguate (e.g. reciter/album for a
  track).

#### Story: Find a nawha by a line of its lyrics
**As a** visitor, **I want** to search by a phrase from the lyrics, **so that** I
can find a nawha when I only remember a line.

**Acceptance Criteria**
- A query that matches lyrics text returns the corresponding track(s).
- Matching results may show a highlighted snippet indicating where the match
  occurred.

## Notes

- Newly added or edited catalog content becomes findable through search.
- Search results respect the same public visibility as browsing (e.g. only
  published stories would appear in any story search; unpublished editorial
  content is never surfaced to visitors).
