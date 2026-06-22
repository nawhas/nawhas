# Nawhas.com — Product Specification

This directory contains the behavioral specification for Nawhas.com, derived
using a spec-driven approach. It describes the system **as built / as intended
to be built**, based on evidence in the codebase (routes, policies, tests, and
UI). It is the reference for upcoming rewrites and iterative improvements.

The spec is **behavioral only**. It describes *what* users and the system can
observably do — not *how* it is implemented. It deliberately says nothing about
the technology stack, the split between front end and back end, how data is
stored or sourced, caching, search engines, or deployment.

## What Nawhas.com is

Nawhas.com is a library and discovery platform for *nawhas* — recited
elegiac poetry. The catalog is organized in a three-level hierarchy:

> **Reciter → Album → Track (nawha)**

Each track has audio, optional video, and optional **lyrics** (plain or
time-synced to the audio). On top of the catalog sit a persistent audio
player, a personal library of saved tracks, editorial **stories** (articles),
global search, user accounts, and a community contribution + moderation
workflow.

## Personas

Roles are **cumulative**: each persona can do everything the persona above it
can, plus more.

### Visitor
Anyone using the site without signing in. A visitor can browse the catalog,
search, listen to audio, read lyrics and stories, print lyrics, submit
feedback, and change their theme preference. A visitor cannot save favorites,
contribute content, or access any moderation tools.

### Contributor
Any visitor who creates an account and signs in. **Every registered account is
a contributor** — there is no separate "plain member" role; registration grants
contribution rights automatically. A contributor inherits all visitor
abilities and additionally:

- maintains a **personal library** of saved nawhas (favorites);
- **contributes to the catalog** — adds and edits albums and tracks, and
  writes/edits **lyrics drafts**.

> ⚠️ **Intent vs. as-built — contribution approval.**
> The intended model is that *all contributions are reviewed and approved by a
> moderator before going live*. As built, this is only fully true for
> **lyrics**, which flow through an explicit draft → moderator-publish workflow.
> Contributor edits to **albums and tracks currently take effect immediately**
> and are captured in an auditable **revision history** (post-hoc review) rather
> than a pre-publish approval queue. Reciter records and stories can only be
> changed by moderators. Each spec notes where this divergence applies. Closing
> this gap (a unified pre-approval workflow for all contributions) is a likely
> goal of the rewrite, but is documented here as *intended*, not *as-built*.

### Moderator
A trusted contributor with elevated privileges. A moderator inherits all
contributor abilities and additionally:

- manages **reciter** records;
- **publishes** (or rejects/deletes) lyrics drafts submitted by contributors;
- creates and manages editorial **stories**;
- reviews the full **revision history** of catalog changes;
- resolves editing **locks** on lyrics drafts.

## Feature specs

| # | Spec | Summary |
|---|------|---------|
| 01 | [Discovery & Browsing](./01-discovery-and-browsing/spec.md) | Home, trending/popular, reciter/album/track browsing |
| 02 | [Audio Player & Queue](./02-audio-player-and-queue/spec.md) | Persistent playback, queue, controls |
| 03 | [Lyrics](./03-lyrics/spec.md) | Viewing synced/plain lyrics, printing, the draft → publish contribution workflow |
| 04 | [Personal Library](./04-personal-library/spec.md) | Saving favorite nawhas |
| 05 | [Search](./05-search/spec.md) | Global search across reciters, albums, tracks, and lyrics |
| 06 | [Stories](./06-stories/spec.md) | Reading and managing editorial articles |
| 07 | [Accounts & Authentication](./07-accounts-and-authentication/spec.md) | Register, sign in, password reset, identity |
| 08 | [Content Management](./08-content-management/spec.md) | Managing reciters, albums, tracks, and media |
| 09 | [Revision History & Audit](./09-revision-history-and-audit/spec.md) | The moderator audit trail and review queues |
| 10 | [Feedback](./10-feedback/spec.md) | Bug / feature / general feedback submission |
| 11 | [App Experience](./11-app-experience/spec.md) | About page, theming, app shell |

## Conventions

- User stories use the form **"As a `<persona>`, I want `<goal>`, so that
  `<benefit>`."**
- Acceptance criteria use **Given / When / Then** where a precise condition is
  useful, and bullet lists otherwise.
- **Cumulative personas:** a story attributed to "visitor" is also available to
  contributors and moderators; a "contributor" story is also available to
  moderators. Stories are filed under the *least-privileged* persona that can
  perform them.
- Callouts marked **⚠️ Intent vs. as-built** flag where the intended behavior
  diverges from what the code currently does.
- Callouts marked **🚩 Flagged / not yet active** flag behavior that exists in
  the product but is currently disabled or only partially wired up.

## Glossary

- **Nawha** — a single recited track; the core unit of content.
- **Reciter** — the artist who performs nawhas; the top of the catalog hierarchy.
- **Album** — a dated collection of a reciter's tracks (typically by year).
- **Lyrics / write-up** — the text of a nawha, optionally time-synced to audio.
- **Synced lyrics** — lyrics whose lines carry timestamps so they can highlight
  in time with playback.
- **Draft lyrics** — an in-progress lyrics submission awaiting a moderator to
  publish it as the track's official lyrics.
- **Story** — an editorial article (news, history, announcements).
- **Library** — a contributor's personal collection of saved (favorited) nawhas.
- **Revision** — a recorded change to a catalog entity, viewable by moderators.
