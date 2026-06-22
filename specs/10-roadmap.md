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
notifications.

*Why it matters:* much of the audience listens on phones; native apps unlock
system-level audio integration and reliability.

## Offline playback

Letting signed-in users download tracks, albums, or playlists for listening
without a connection, including offline access to synced lyrics.

*Dependencies:* native apps (above) are the natural home; rights/permission
considerations for downloadable audio must be settled first.

## Richer discovery

- Personalized recommendations based on listening and library.
- "More like this" from a track (by reciter, tag, topic, era).
- Faceted browse/filter (by year, reciter, tag, topic, language) on listings and
  search.

## Community & social

- Following reciters or contributors and being notified of new material.
- Reactions or comments on tracks and stories.
- Collaborative playlists co-owned by multiple users.

## Lyrics depth

- First-class translation and transliteration as parallel renderings (a near-term
  version is sketched in [03 — Lyrics](./03-lyrics.md), LYR-6).
- Per-line annotations / footnotes for context.
- Community suggestions on individual lyric lines.

## Contributor experience

- Reputation and trust levels that unlock more autonomy (e.g. trusted contributors
  whose changes auto-publish).
- Bulk contribution tools (e.g. uploading a whole album at once).
- Notifications to contributors when their proposals are reviewed.

## Internationalization

- Full UI localization into multiple languages.
- Locale-aware presentation of dates, numbers, and text direction beyond the
  lyrics-level support already specced.

## Operational quality

- Public, automated detection of duplicate or low-quality uploads.
- Listener-facing playback quality options (e.g. bitrate) where source material
  allows.
