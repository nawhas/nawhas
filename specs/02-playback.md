# 02 — Playback

Everything about listening: the player, the queue, continuous listening, and the
controls available while audio plays. Lyrics that move in time with audio are
specified in [03 — Lyrics](./03-lyrics.md); this spec references the synchronization
but owns the transport controls.

Prerequisites: [00 — Foundation](./00-foundation.md).

---

## Stories

### Core transport

> **PLAY-1 — As a visitor, I want to play and pause any track, so that I can listen on demand.**
>
> **Acceptance criteria**
> - **Given** any track, **when** I activate play, **then** audio begins and a
>   persistent player appears showing the current track's title, reciter(s),
>   release date, and artwork.
> - **When** I activate pause, **then** audio stops and my position is retained.
> - **When** audio cannot be loaded, **then** I see a clear error and the player
>   does not appear stuck in a loading state.

> **PLAY-2 — As a visitor, I want to scrub through a track, so that I can jump to a part I want to hear.**
>
> **Acceptance criteria**
> - **Given** a playing or paused track, **then** I see elapsed time, total
>   duration, and a draggable progress indicator.
> - **When** I move the progress indicator, **then** playback jumps to that point.

> **PLAY-3 — As a visitor, I want playback to continue while I browse, so that listening is never interrupted by navigation.**
>
> **Acceptance criteria**
> - **Given** a track is playing, **when** I navigate to other pages, **then**
>   audio continues without restarting.
> - **Given** a track is playing, **then** the player remains accessible from every
>   page.

### Queue & continuous listening

> **PLAY-4 — As a visitor, I want a play queue, so that tracks play one after another without my intervention.**
>
> **Acceptance criteria**
> - **Given** a queue with multiple tracks, **when** one track ends, **then** the
>   next begins automatically.
> - **Then** I can skip to the next track and return to the previous track.
> - **Then** I can view what's coming up ("up next").

> **PLAY-5 — As a visitor, I want to play or queue a whole album or collection at once, so that I can listen continuously.**
>
> **Acceptance criteria**
> - **Given** an album, topic collection, tag listing, or playlist, **when** I
>   choose "play", **then** its tracks load into the queue and the first plays.
> - **When** I choose "add to queue", **then** its tracks are appended without
>   interrupting what's currently playing.
> - **When** I start playback from a specific track within a list, **then** the
>   queue continues from that track through the rest of the list.

> **PLAY-6 — As a visitor, I want shuffle, so that I can listen to a varied order.**
>
> **Acceptance criteria**
> - **Given** a queue, **when** I enable shuffle, **then** upcoming tracks play in a
>   randomized order and the control shows it is active.
> - **When** I disable shuffle, **then** the original order resumes for what remains.

### While listening

> **PLAY-7 — As a signed-in user, I want to favorite the current track from the player, so that I can save it without leaving what I'm doing.**
>
> **Acceptance criteria**
> - **Given** I am signed in and a track is playing, **then** the player shows a
>   favorite control reflecting whether the track is already in my library.
> - **When** I toggle it, **then** the track is added to or removed from my library
>   immediately (see [05 — Library & Playlists](./05-library-and-playlists.md)).
> - **Given** I am not signed in, **then** the favorite control prompts me to sign
>   in rather than failing silently.

> **PLAY-8 — As a visitor, I want to read lyrics while listening, so that I can follow along.**
>
> **Acceptance criteria**
> - **Given** a playing track that has lyrics, **then** I can open a lyrics view
>   from the player.
> - **Given** the track's lyrics are time-synced, **then** the current line is
>   highlighted in time with the audio (see [03 — Lyrics](./03-lyrics.md)).

### Listening comfort

> **PLAY-11 — As a visitor, I want a sleep timer, so that playback stops on its own when I listen as I fall asleep.**
>
> **Acceptance criteria**
> - **Given** I am listening, **then** I can set a sleep timer for a chosen duration
>   (e.g. 15 / 30 / 60 minutes) or "until the end of the current track".
> - **Given** a timer is running, **then** I can see the time remaining and cancel or
>   change it.
> - **When** the timer elapses, **then** playback pauses (gently, per [00 — Foundation](./00-foundation.md), §6.10) and the timer clears.
> - **Edge cases** — Setting a new timer replaces any existing one; the timer is
>   available across screen sizes (compact and expanded player).

> **PLAY-12 — As a visitor, I want to adjust playback speed, so that I can follow the recitation at a comfortable pace.**
>
> **Acceptance criteria**
> - **Given** a playing track, **then** I can choose a playback speed (e.g. 0.75×–2×,
>   default 1×), and the control shows the current speed.
> - **When** I change speed, **then** audio adjusts without distorting pitch
>   unnaturally, and synced-lyrics highlighting stays aligned (see [03 — Lyrics](./03-lyrics.md), LYR-3).
> - **Given** I set a speed, **then** it persists for subsequent tracks: not signed
>   in, it persists on the device; signed in, it travels with my account (mirroring
>   the rendering preference — see [03 — Lyrics](./03-lyrics.md), LYR-8).
> - **Edge cases** — The speed control is available on both the compact and expanded
>   player (see PLAY-9).

> **PLAY-13 — As a visitor, I want playback to resume where I left off, so that I can pick up a long recitation later without hunting for my place.**
>
> **Acceptance criteria**
> - **Given** I stop partway through a track and return later, **then** I am offered
>   to resume from my last position rather than restarting.
> - **Given** I am signed in, **then** my last position travels with my account across
>   devices (mirroring recently played — see [05 — Library & Playlists](./05-library-and-playlists.md), LIB-3c).
> - **Given** I am not signed in, **then** my last position is retained on that device
>   where possible.
> - **Edge cases** — Finishing a track clears its resume point; this builds on the
>   within-session position retention of PLAY-1.

### Adaptive presentation

> **PLAY-9 — As a visitor on a phone, I want a player suited to a small screen, so that listening is comfortable on mobile.**
>
> **Acceptance criteria**
> - **Given** a small screen, **then** the player offers a compact bar that can
>   expand to a full-screen view with artwork, controls, queue, and lyrics.
> - **Given** a large screen, **then** the player presents as a persistent bar with
>   the option to expand.
> - Across screen sizes, the same transport, queue, favorite, and lyrics
>   capabilities are available.

### Listening counts toward popularity

> **PLAY-10 — As a visitor, I want the tracks I play to count toward popularity, so that the library's rankings reflect genuine listening.**
>
> **Acceptance criteria**
> - **Given** a track is played, **then** that listening contributes to the track's
>   and reciter's popularity signals used elsewhere (see [01 — Catalog & Discovery](./01-catalog-and-discovery.md), CAT-10).
> - **Given** I am a visitor or a signed-in user, **then** my listening counts the
>   same way and requires no action on my part.
