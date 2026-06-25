# 10 — Roadmap (non-binding)

This document captures **directional** capabilities that are wanted but **not yet
specced as committed behavior**. They are listed so the foundation and feature
specs can be designed without painting them into a corner. Nothing here is a
commitment; when one of these is taken up, it graduates into a full feature spec
with stories and acceptance criteria.

Items already **baked into** the committed specs (and therefore *not* repeated
here) include: multiple reciters per track, album-decoupled standalone tracks,
tags, topics & curated collections, public/private playlists, public contributor
profiles, social sign-in, and a powerful full search.

---

## Native mobile apps

Dedicated phone/tablet applications (beyond the responsive web experience), with
platform-native navigation, background audio, lock-screen controls, and push
notifications. Explored in [future/mobile-apps.md](./future/mobile-apps.md).

*Why it matters:* much of the audience listens on phones; native apps unlock
system-level audio integration and reliability.

## Offline playback

Letting signed-in users download tracks, albums, or playlists for listening
without a connection, including offline access to synced lyrics. Explored alongside
native apps in [future/mobile-apps.md](./future/mobile-apps.md).

*Dependencies:* native apps (above) are the natural home; rights/permission
considerations for downloadable audio must be settled first.

## Richer discovery

- Personalized recommendations based on listening and library.
- "More like this" from a track (by reciter, tag, topic, era).
- Continuous discovery: when the play queue ends, automatically continue with
  similar tracks drawn from the topics and reciters the listener has been playing.
- Short-form exploration: a swipeable feed that plays a snippet of each nawha, so
  listeners can sample and skip through the library quickly to discover new material.
- Faceted browse/filter (by release date, reciter, tag key/value, topic) on listings and
  search.

## Community & social

- Following reciters or contributors and being notified of new material.
- Reactions or comments on tracks and stories.
- Collaborative playlists co-owned by multiple users.
- Communities: groups (a majlis, an azadari organization, a reciting party) with
  their own page and membership, that publish the nawhas they will recite
  (programmes/setlists), keep recordings of their recitations, and share events and
  announcements. Explored in [future/communities.md](./future/communities.md).

## Lyrics depth

- Per-line annotations / footnotes for context.
- Community suggestions on individual lyric lines.
- Automatic transliteration or machine translation as a starting point for
  contributors (the *presentation* of source, transliteration, and translations is
  already committed in [03 — Lyrics](./03-lyrics.md)).

## Contributor experience

- Reputation and trust levels that unlock more autonomy (e.g. trusted contributors
  whose changes auto-publish).
- Bulk contribution tools — e.g. uploading a whole album at once, or importing an
  existing lyrics collection (such as a Google Drive folder) with LLM-assisted
  processing into structured, sectioned lyrics.
- Notifications to contributors when their proposals are reviewed.

## Internationalization

- Full UI localization into multiple languages.
- Locale-aware presentation of dates, numbers, and text direction beyond the
  lyrics-level support already specced.

## Sustainability

Ways to cover running costs (including AI-assisted features) without gating content,
consistent with the open-by-default principle:

- A single, clean "Sponsored by" card (e.g. on the home page) showcasing the
  businesses and supporters from the community who fund the project.
- Donations and optional supporter subscriptions (monthly or yearly) for individuals
  who want to help sustain the site.
- Transparency on how funds are used.

## Operational quality

- Public, automated detection of duplicate or low-quality uploads.
- Listener-facing playback quality options (e.g. bitrate) where source material
  allows.
