# Future — Native Mobile Apps (exploration)

> **Status: exploratory / non-binding.** This document develops the *functionality*
> of native mobile applications and the offline listening they unlock. It is **not**
> part of the relaunch and contains no acceptance criteria yet. The next step is to
> **split this into user stories** (a new `specs/NN-mobile.md`, plus targeted
> extensions to existing specs) when the feature is taken up; until then nothing here
> is a commitment. See [10 — Roadmap](../10-roadmap.md), *Native mobile apps* and
> *Offline playback*.

Prerequisites for context: [00 — Foundation](../00-foundation.md).

---

## 1. Vision

Much of the audience listens on phones. The responsive web experience already
covers listening, reading, browsing, and contributing on mobile devices (see
[02 — Playback](../02-playback.md), `PLAY-9`). Native apps are not a re-imagining of
the product — they are a **delivery channel** that unlocks the system-level
capabilities a browser cannot reach: reliable background audio, lock-screen and
in-car controls, offline downloads, and push notifications.

The guiding principle: **behavior parity with the web by default; native apps add
reach and reliability, not a divergent product.** Anything a user can do on the web
they can do in the app, and the spec that owns a behavior on the web continues to
own it on mobile. This document focuses on the **genuinely new surface** the apps
introduce, not on restating the catalog, search, library, or lyrics behavior that
already carries over unchanged.

---

## 2. Scope & platforms

- **Platforms.** iOS and Android phones and tablets. (A tablet-optimized layout,
  not merely a stretched phone view.)
- **Parity baseline.** The committed web specs (01–09) define the behavior; the apps
  present that same behavior with platform-native navigation and chrome.
- **Out of this doc.** Desktop apps and the open-by-default consumption stance
  (§6.1 of the foundation) are unchanged — apps never gate consumption behind an
  account beyond what the web already does.

---

## 3. Net-new capabilities

These are the reasons native apps exist. Each is a candidate story cluster.

### 3.1 Offline downloads
The biggest draw beyond the web.

- Signed-in users can **download** a track, album, collection, or playlist for
  listening **without a connection**, including the **synced lyrics** so
  highlight-in-time still works offline (see [03 — Lyrics](../03-lyrics.md)).
- Downloads are managed: see what's downloaded, how much space it uses, and remove
  downloads; optionally auto-remove stale ones.
- **Rights gate (the dependency).** Downloadable audio raises rights/permission
  questions that must be settled **first** — which audio may be made available
  offline, and under what consent. This is the gating open question for the whole
  offline feature (see §5).

### 3.2 Push notifications
- Opt-in push for the events the foundation already promises to communicate
  (§6.11): account/security events, moderation actions affecting the user, and
  contribution outcomes (`CON-6`).
- Push respects the same **communication preferences** the user sets on the web
  (the in-app notifications inbox and preferences — see [08 — Accounts &
  Authentication](../08-accounts-and-authentication.md)); push is one delivery
  channel for those same notifications, never a separate, louder one.
- Notifications stay **sober and non-intrusive** (§6.10).

### 3.3 System & hardware integration
- **Background audio** that continues when the app is backgrounded or the screen is
  locked.
- **Lock-screen / notification-shade controls** and **CarPlay / Android Auto** for
  hands-free, in-car listening, with the current track's metadata and artwork.
- Home-screen **widgets** / quick actions (e.g. resume last, recently played) are a
  possible extension.

### 3.4 Native authentication
- Native **social sign-in** flows (Google, Apple — see `AUTH-2`), including
  **Sign in with Apple** where platform policy requires it, honoring the same
  feature toggles administrators control (`ADM-4`).

### 3.5 Deep links & web↔app continuity
- The durable, shareable addresses every entity already has (§6.9) resolve to the
  right **in-app** screen when opened on a device with the app installed, and fall
  back to the web otherwise.
- Listening and key state carry across web and app for a signed-in user, building
  on the cross-device preferences already specced (rendering — `LYR-8`; appearance —
  `AUTH-6`; recently played — `LIB-3c`).

---

## 4. How it relates to existing specs

Mostly a **delivery layer** over committed web behavior, plus a few genuinely new
pieces (offline, push, system integration).

| Mobile building block | Closest existing concept | New? |
|---|---|---|
| Listening, queue, shuffle | Playback (02), incl. mobile player `PLAY-9` | reuse |
| Search, library, lyrics, contributing | 03, 04, 05, 06 | reuse |
| Social sign-in (native flows) | `AUTH-2`, toggles `ADM-4` | extension |
| Deep links to in-app screens | Durable addresses §6.9 | extension |
| Cross-device state | `LYR-8`, `AUTH-6`, `LIB-3c` | extension |
| Push notifications | Notifications §6.11 + inbox/preferences (08) | **new (channel)** |
| Background / lock-screen / CarPlay audio | — | **new** |
| Offline downloads (+ lyrics) | — | **new** |
| Audio rights for offline | Roadmap *Offline playback* dependency | **open** |

---

## 5. Direction vs. open questions

**Assumed direction (low-risk):**
- Behavior parity with the web; native apps add reach, not a divergent product.
- iOS + Android, phone and tablet.
- Push is a delivery channel for the notifications already defined, not a new class.
- Deep links honor the existing durable addresses (§6.9).

**Open questions to resolve before / during story-writing:**
- **Audio rights for offline** — which audio may be downloaded, under what consent,
  and how (if at all) downloads are protected. *This gates the entire offline
  feature.*
- **Build approach's effect on behavior** — only where it changes observable
  behavior (e.g. offline availability of synced lyrics, background-audio limits per
  platform). The spec stays implementation-free, but platform constraints may shape
  what is promised.
- **Notification granularity** — which event types are push-eligible by default, and
  how per-type opt-in maps across in-app, email, and push.
- **Offline scope limits** — caps on how much can be downloaded, and behavior when a
  downloaded item is later edited or removed from the catalog.
- **Tablet layout** — how far the tablet experience diverges from phone (e.g.
  parallel lyrics view, `LYR-7`).
- **Account-gating** — confirm offline download requires sign-in while all online
  consumption stays open by default (§6.1).

---

## 6. Next step

When this is taken up, **break it into user stories** — a new `specs/NN-mobile.md`
for the net-new capabilities (offline, push, system integration, deep links), plus
targeted extensions where mobile changes observable behavior (e.g. offline lyrics in
[03 — Lyrics](../03-lyrics.md), native sign-in in [08 — Accounts &
Authentication](../08-accounts-and-authentication.md)). Settle the §5 open questions
— **audio rights for offline first** — as part of that work. The committed specs are
designed so as not to preclude any of the above.
