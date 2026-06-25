# Future — Communities (exploration)

> **Status: exploratory / non-binding.** This document develops the *functionality*
> of communities. It is **not** part of the relaunch and contains no acceptance
> criteria yet. The next step is to **split this into user stories** under `specs/`
> when the feature is taken up; until then nothing here is a commitment. See
> [10 — Roadmap](../10-roadmap.md), *Community & social*.

Prerequisites for context: [00 — Foundation](../00-foundation.md).

---

## 1. Vision

A **community** is a local centre or organisation that recites and commemorates
together — for example a city's Shia community, an imambargah, an azadari
organization, or a reciting party. Today the platform knows about individuals
(visitors, contributors) and the catalog. Communities add a **group identity hub**:
a public home base for a group, from which its people, events, media, and posts all
hang.

The core job, stated plainly: a group like the *Rugby Shia community* can register,
publish **when and where its events are**, let people **take part** (recite before
the lecture or after via noha, or otherwise contribute), and **share** its nohas,
recordings, videos, and recitations so others can listen — all under one identity.

---

## 2. People & governance

- **Creation.** Any signed-in user can create a community **self-serve** and becomes
  its first **organiser**. Low friction, community-driven growth.
- **Verified badge.** An optional **verified** badge, granted by site administrators,
  marks established/authentic groups. Unverified communities still work and are
  clearly shown as unverified.
- **Roster vs followers.** Two distinct relationships:
  - **Roster** — the people who *are* the group: **organisers** (manage the page,
    events, roster, media) and **reciters** (recite with the group). Curated by
    organisers.
  - **Followers** — a wider audience who follow the community to receive its updates
    (new events, posts, media). Anyone can follow.
- **Moderation.** Communities are user-created public identities, so site moderators
  can step in: handle reports (see [08 — Accounts & Authentication](../08-accounts-and-authentication.md), ACC-REPORT), and
  rename, merge, or remove communities, and act on impersonation.

---

## 3. Events & timings

The biggest draw: people want to know **when and where** a community's majalis are.

- **Events** carry a date/time, a **location** (see *multiple locations* below), and
  what's on — typically a **lecturer** and **recitations**.
- **Multiple locations.** A community can have **more than one venue**; each event is
  held at one of the community's locations (one event here, another there).
- **Tagging reciters to an event.** An event can credit the **reciters who will
  recite** at it (e.g. before the lecture / after via noha). Where a reciter has a
  catalog profile, the tag links to it (see §4).
- **Livestreaming.** An event can be **live-streamed** — either to **YouTube** or
  **natively on the site** — and wherever it is streaming, the stream is surfaced on
  the community (and event) page so others can watch live.
- **Participation.** People can **volunteer to take part** in an event — e.g. sign up
  to recite before the lecture or after via noha, or otherwise contribute. (Exact
  signup/approval mechanics are an open question — see §8.)

---

## 4. Reciters & the catalog

Communities reuse the existing catalog rather than duplicating it:

- **A reciting party registers as a normal Reciter.** If a community has a reciting
  group, it simply registers as a catalog **Reciter** under the group's name and uses
  the existing flow (see [01 — Catalog & Discovery](../01-catalog-and-discovery.md), [06 — Contributions & Moderation](../06-contributions-and-moderation.md)). A community is
  **never** a special track credit — this keeps the catalog clean.
- **Roster links to catalog profiles.** A roster reciter who already has a catalog
  reciter profile links to it, so the community page can show "our reciters" pointing
  at their profiles and tracks.
- **Upcoming appearances ("reciting next").** Because events tag reciters (§3), a
  reciter's catalog profile can surface **where they are reciting next** — their
  upcoming appearances drawn from community events. This gives reciters a reason to
  engage and gives listeners a "where can I hear them next" path. *(This touches the
  reciter profile in [01 — Catalog & Discovery](../01-catalog-and-discovery.md) and would be specced there when graduated.)*

---

## 5. Media (hybrid model)

A community can share its own **recordings, videos, and recitations**. Two homes,
with a path between them:

- **Catalog** — polished nohas belong in the shared catalog, contributed the normal
  way (the group registers as a Reciter and submits tracks for review).
- **Community media wall** — informal media (live majlis audio, event video clips,
  raw recitations) lives on the **community's own page**, at a lower bar than the
  catalog.
- **Promote to catalog** — a good piece of community media can be **promoted into the
  catalog** later via the normal review (see [06 — Contributions & Moderation](../06-contributions-and-moderation.md)).

---

## 6. Posts & discussion

- **Posts / announcements** — communities publish updates (news, event notes,
  general announcements) to their followers.
- **Comments / discussion** — users can comment on and discuss posts (and likely
  events). This is the first place the platform allows open discussion, so it will
  need the usual safeguards (reporting, moderation by community organisers and/or
  site moderators).

---

## 7. Donations to a community

- Users can **donate directly to a community/centre** — the funds are for that
  community, to support its activities.
- **This is separate from platform sustainability.** Donations and sponsorship that
  sustain *nawhas.com itself* are a different concern (see [10 — Roadmap](../10-roadmap.md),
  *Sustainability*); community donations are a feature *of* communities and the money
  is the community's, not the platform's.
- Open questions: how funds are handled/transferred, fees, and what transparency a
  community must provide to its donors.

---

## 8. How it relates to existing specs

Communities is largely a **group-ownership layer** over concepts the platform
already has, plus a few genuinely new pieces (events, livestream, community media,
discussion, community donations):

| Community building block | Closest existing concept | New? |
|---|---|---|
| Community page / identity | Contributor public profile (AUTH-7) | layer |
| Roster reciters → profiles | Catalog Reciter (01, 06) | reuse |
| Followers | Roadmap *following* (10) | new-ish |
| Posts / announcements | Stories (07) | layer |
| Media wall | Catalog + contributions (06) + promotion | new (informal store) |
| Events, timings, locations | — | **new** |
| Livestreaming | — | **new** |
| Comments / discussion | — | **new** (first discussion surface) |
| Community donations | — | **new** (distinct from platform sustainability) |
| Upcoming appearances on reciter page | Reciter profile (01) | extension |

---

## 9. Settled vs open

**Settled in brainstorming:**
- Purpose = group identity hub for local centres/organisations.
- Creation = self-serve + optional admin-granted verified badge; moderators can
  rename/merge/remove and handle impersonation.
- Membership = curated roster (organisers + reciters) + open followers.
- Catalog stance = a reciting party registers as a normal Reciter; community is never
  a track credit; roster links to catalog profiles.
- Media = hybrid (catalog for polished nohas, community wall for informal, promote
  path between).
- Events = multiple locations, reciter tagging, participation, livestream (YouTube or
  native) surfaced on the community.
- Discussion = posts + comments allowed.
- Donations = to communities, **separate** from platform sustainability.
- Reciter profiles show upcoming appearances from tagged events.

**Open questions to resolve before / during story-writing:**
- **Participation signups** — open signup per event slot, or request → organiser
  approval? Who can volunteer (followers, anyone, roster only)?
- **Discussion moderation** — community organisers, site moderators, or both? How
  far do ACC-REPORT and oversight (ADM-6) extend to community content?
- **Native livestreaming** — is hosting streams ourselves in scope, or start with
  YouTube embeds only?
- **Community donations handling** — payment flow, fees, transparency, and any
  rights/consent or legal considerations.
- **Recordings rights & consent** — posting recordings of named reciters.
- **Privacy** — can a community, event, or post be private/unlisted, or are
  communities inherently public?
- **Discovery** — how much of communities (pages, events, programmes) appears in
  global search ([04 — Search](../04-search.md)) and on the home page.

---

## 10. Next step

When this is taken up, **break it into user stories** across the appropriate specs
(a new `specs/NN-communities.md`, plus extensions to the reciter profile in
[01 — Catalog & Discovery](../01-catalog-and-discovery.md) for upcoming appearances, and to oversight in
[09 — Administration](../09-administration.md) for verification and moderation). Resolve the §9 open questions
as part of that work. The committed specs are designed so as not to preclude any of
the above.
