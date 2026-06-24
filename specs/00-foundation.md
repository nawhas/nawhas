# 00 — Foundation

This document defines the vocabulary, user types, permission model, and
cross-cutting rules that every other spec depends on. It contains few stories of
its own; instead it establishes the shared frame the feature specs build on.

---

## 1. Vision

Nawhas.com exists to **preserve, present, and grow** a high-quality, well-organized
library of nawhas, and to make them effortless to **discover, listen to, and read
along with** on any device.

Three forces keep the library healthy:

- **Visitors** consume freely, with no barrier to listening or reading.
- **Contributors** grow and correct the library through proposals.
- **Moderators and administrators** safeguard quality and keep the community and
  catalog in order.

The product should feel fast, generous, and trustworthy: nothing essential is
gated behind an account, contributions are easy to make and easy to review, and
the history of every change is transparent.

---

## 2. Glossary

| Term | Meaning |
|------|---------|
| **Nawha / Track** | A single recitation. The core unit of the library: it has a title, one or more reciters, a **release date**, audio, and optionally lyrics, a video, an album, tags, and topics. "Track" and "nawha" are used interchangeably. |
| **Reciter** | A performer who recites nawhas. Has a name, a description/biography, and an avatar image. A track may credit **more than one** reciter. |
| **Album** | An optional, named collection of tracks by a reciter — a **legacy organizational grouping**, not a required release unit. Has a title and artwork. A track may belong to **one album or none** (a standalone single). Time-based grouping is driven by track **release dates**, not by albums. |
| **Release date** | The date a track was released. The primary signal for ordering and grouping tracks chronologically (e.g. by year or period), independent of whether a track belongs to an album. |
| **Lyrics** | The written text of a track. May be plain text or **time-synced** so lines highlight in time with the audio. Lines may carry **repeat counts**. |
| **Tag** | A structured **key:value** label applied to tracks (e.g. `language:urdu`, `occasion:ashura`) for grouping and filtering. A track may carry many tags; multiple values for the same key are allowed. |
| **Topic** | A curated theme used to assemble editorial collections of tracks (e.g. an occasion or a commemorated personality). |
| **Story** | An editorial article or announcement (news, history, feature write-ups). |
| **Library** | A signed-in user's personal space: their favorited (saved) tracks, together with the playlists they create. Distinct from product-curated **collections**, which users can access and play but do not own. |
| **Playlist** | A user-created, ordered list of tracks. Can be private or public. |
| **Collection** | A curated grouping of tracks surfaced by the product (e.g. "by topic"), distinct from a user's personal playlist. |
| **Contribution / Proposal** | A change to the catalog submitted for review (a new track, an edit to a reciter, new lyrics, etc.). |
| **Review queue** | The set of pending proposals awaiting a moderator's decision. |
| **History / Revisions** | The transparent, time-ordered record of who changed what, and when. |
| **Feature toggle** | A switch, controlled by administrators, that turns a capability on or off for the whole site. |

---

## 3. User types

There are four user types. They are **cumulative**: each inherits everything the
type below it can do, and adds more.

### 3.1 Visitor (anonymous)
Anyone using the site without signing in. Visitors can fully consume the library:
browse, search, listen, read and print lyrics, read stories, and view public
profiles and public playlists. Visitors can submit feedback. Visitors cannot save
anything to an account or propose changes.

### 3.2 Contributor (default signed-in user)
**Every account is a contributor by default.** Contributors can do everything a
visitor can, plus:
- Maintain a personal **library** of saved tracks.
- Create and manage **playlists** (private or public).
- **Propose changes** to the catalog (add or edit reciters, albums, tracks, and
  lyrics), which enter the **review queue** for a moderator to approve.
- Maintain a **public contributor profile** and accrue attribution for accepted
  contributions.

Contributors **cannot** change the live catalog directly; their changes take
effect only once approved.

### 3.3 Moderator
A trusted, elevated user. Moderators can do everything a contributor can, plus:
- **Review the queue**: approve, reject, or amend proposals.
- **Edit the catalog directly** (reciters, albums, tracks, lyrics, media), with
  changes taking effect immediately and recorded in history.
- Manage **tags**, **topics**, and curated **collections**.
- Create and manage **stories**.
- View the full **history / revisions** of catalog changes.

### 3.4 Administrator
The highest tier. Administrators can do everything a moderator can, plus:
- Manage **users and roles** (grant or revoke contributor, moderator, and
  administrator standing; suspend accounts).
- Control **feature toggles** and site-wide configuration.
- Access operational/quality oversight surfaces (e.g. reported content, abuse).

> **Promotion path.** New accounts begin as contributors. Only administrators can
> elevate someone to moderator or administrator (see [09 — Administration](./09-administration.md)).

---

## 4. Permission model (at a glance)

This matrix summarizes the cross-cutting permission rules. Individual specs
restate the relevant rows as stories.

| Capability | Visitor | Contributor | Moderator | Admin |
|---|:---:|:---:|:---:|:---:|
| Browse, search, listen, read & print lyrics | ✓ | ✓ | ✓ | ✓ |
| Read stories, view public profiles & public playlists | ✓ | ✓ | ✓ | ✓ |
| Submit feedback | ✓ | ✓ | ✓ | ✓ |
| Report content as problematic | ✓ | ✓ | ✓ | ✓ |
| Save tracks to a personal library | — | ✓ | ✓ | ✓ |
| Create & manage playlists | — | ✓ | ✓ | ✓ |
| Propose catalog changes (queued for review) | — | ✓ | ✓ | ✓ |
| Maintain a public contributor profile | — | ✓ | ✓ | ✓ |
| Review queue (approve / reject / amend) | — | — | ✓ | ✓ |
| Edit catalog directly (live) | — | — | ✓ | ✓ |
| Manage tags, topics, collections | — | — | ✓ | ✓ |
| Create & manage stories | — | — | ✓ | ✓ |
| View change history / revisions | — | — | ✓ | ✓ |
| Manage users & roles, suspend accounts | — | — | — | ✓ |
| Control feature toggles & site configuration | — | — | — | ✓ |

> A capability marked `—` for a user type means the option is **not offered** to
> that user. Attempting it anyway (e.g. by a contributor who has been signed out)
> results in the action being refused with a clear explanation, never a silent
> failure.

---

## 5. Content lifecycle

The catalog evolves through a consistent lifecycle, regardless of which entity is
changing:

1. **Proposal.** A contributor submits a change. It is held in the review queue
   and is **not** visible in the live catalog. The contributor can see and track
   their own pending proposals.
2. **Review.** A moderator examines the proposal alongside a clear before/after
   comparison, and either **approves** (it goes live), **rejects** (with a
   reason), or **amends and approves**.
3. **Direct change.** A moderator or administrator may instead change the catalog
   directly, taking effect immediately.
4. **Record.** Every change that goes live — whether from an approved proposal or
   a direct edit — is written to the transparent **history**, attributed to the
   person responsible, with a timestamp and a before/after view.

Stories may be kept as **drafts** and **published** independently of the catalog
lifecycle (see [07 — Stories](./07-stories.md)).

---

## 6. Cross-cutting rules

These rules apply everywhere and are not repeated in every story.

### 6.1 Open by default
Listening, reading lyrics, browsing, and searching never require an account. No
consumption feature is gated behind sign-in.

### 6.2 Editing safety (concurrency)
When two people could edit the same item at once (most importantly lyrics), the
item can be **locked** for editing so a person does not silently overwrite
another's work. A lock is visible to others, can be released by its owner, and
can be overridden by a moderator.

### 6.3 Attribution & transparency
Every published change is attributed to a person and timestamped. Contributors
accrue credit for accepted contributions on their public profile. Moderators can
always see who did what.

### 6.4 Reversibility
Because the full history is retained, moderators can understand and, where
supported by a feature spec, undo or correct prior changes.

### 6.5 Graceful states
Every list and detail surface has defined **empty**, **loading**, and **error**
states. Empty states explain what would appear and, where appropriate, how to add
it. Errors explain what went wrong and what to do next; they never fail silently.

### 6.6 Feedback to everyone
Any user can report a bug, request a feature, or send general feedback, with an
optional contact address (see [08 — Accounts & Authentication](./08-accounts-and-authentication.md), `ACC-FEEDBACK`). Any user can also
report specific content as problematic (see `ACC-REPORT`).

### 6.7 Feature toggles
Certain capabilities (e.g. public registration, social sign-in) can be switched
on or off site-wide by administrators. When a capability is off, its entry points
are hidden or clearly disabled, and attempts to use it are refused with an
explanation.

### 6.8 Accessibility & devices
The product is usable on phones, tablets, and desktops, and supports both light
and dark appearance. Core flows are operable by keyboard and with assistive
technology. Listening continues uninterrupted while navigating between pages.

### 6.9 Stable, shareable locations
Every reciter, album, track, story, public profile, and public playlist has a
durable, shareable address. When an item is renamed or reorganized, previously
shared links continue to resolve to the right place. Every such surface offers a
**share** action, and a shared link renders a **rich preview** (title, image, and a
short description) when posted to messaging and social apps and surfaced to search
engines. Previews are sober and respectful (§6.10) and never expose private data
(e.g. a private playlist or a user's library).

### 6.10 Respectful, sober presentation
Given the devotional subject matter, the presentation is sober and respectful by
default, free of intrusive or frivolous interruptions to listening and reading.

### 6.11 Keeping users informed
Users are told about events that affect them. Account and security events (e.g. a
password reset) and moderation actions affecting a user (e.g. an account
suspension — see [09 — Administration](./09-administration.md)) are communicated
clearly, in-app and, where important, by email. Contribution outcomes are always
visible to the contributor in-app (see [06 — Contributions & Moderation](./06-contributions-and-moderation.md), CON-6).
Notifications are sober and non-intrusive (§6.10). Richer, optional notifications —
proactive alerts when a proposal is reviewed, following reciters or contributors,
and digests — are directional (see [10 — Roadmap](./10-roadmap.md)).

---

## 7. Out of scope (this version)

The following are explicitly **not** specced as committed behavior here. Some are
captured directionally in [10 — Roadmap](./10-roadmap.md):

- Native mobile applications and offline playback.
- Paid content tiers or advertising. (Community **sponsorship and donations** to
  sustain the project are directional — see [10 — Roadmap](./10-roadmap.md) — but no
  content or feature is ever gated behind payment, consistent with §6.1 *Open by
  default*.)
- Social networking between users beyond public profiles and public playlists
  (e.g. following, direct messaging, comments on tracks).
- Automated (machine) transcription, transliteration, or translation of lyrics.
  Human-authored source text, transliterations, and translations *are* in scope
  (see [03 — Lyrics](./03-lyrics.md)).
