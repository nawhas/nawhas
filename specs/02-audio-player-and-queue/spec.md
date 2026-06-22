# Spec 02 — Audio Player & Queue

## Overview

A single, persistent audio player runs at the bottom of the application and
survives navigation between pages. Any track surface can start playback or add
to a play queue. The player offers standard transport controls, a seek bar,
shuffle, a visible queue, and a synced-lyrics overlay.

## Personas

**Visitor** for all playback and queue behavior. Contributors additionally get
favorite controls within the player (see [Personal Library](../04-personal-library/spec.md)).

## User Stories

### Playing audio

#### Story: Play a single track
**As a** visitor, **I want** to play a nawha, **so that** I can listen to it.

**Acceptance Criteria**
- A play control is available on the track page (and on track listings).
- **Given** a track is stopped, **When** I press play, **Then** audio begins and
  the control changes to a stop/pause affordance.
- **Given** a track is playing, **When** I press stop/pause, **Then** audio
  halts and the control reverts to play.
- Only tracks that have audio are playable; tracks without audio do not offer a
  play action.

#### Story: Play an entire album
**As a** visitor, **I want** to play a whole album, **so that** I can listen
straight through.

**Acceptance Criteria**
- The album page offers a "play album" control when the album has playable
  tracks.
- Playing an album loads its tracks into the queue and begins playback.

#### Story: Play my whole library
**As a** contributor, **I want** to play all my saved nawhas, **so that** I can
listen to my collection.

**Acceptance Criteria**
- The library offers a "play all" control that queues and plays the saved
  tracks (see [Personal Library](../04-personal-library/spec.md)).

### The queue

#### Story: Add tracks and albums to a queue
**As a** visitor, **I want** to queue tracks and albums, **so that** I can line
up what plays next without interrupting the current track.

**Acceptance Criteria**
- Track surfaces offer "add to queue"; album surfaces offer "add album to
  queue".
- **When** I add to the queue, **Then** a confirmation (e.g. "Added to Queue")
  is shown.
- The confirmation offers an **undo** action that removes what was just added.
- The added items appear at the end of the queue.

#### Story: View and manage the queue
**As a** visitor, **I want** to see what's queued, **so that** I know what plays
next.

**Acceptance Criteria**
- A queue view lists upcoming tracks in order.
- On small screens, the queue is available as a full-screen overlay.

### Transport controls

#### Story: Control playback
**As a** visitor, **I want** standard playback controls, **so that** I can move
through the queue and within a track.

**Acceptance Criteria**
- The player offers: play/pause, previous, next, and shuffle.
- A seek bar shows elapsed and total time and lets me scrub within the current
  track.
- The player shows the now-playing track's title, reciter, and year, plus
  artwork.
- The artwork/now-playing area can be expanded or minimized.
- Toggling shuffle randomizes the order in which queued tracks play.

### Now-playing extras

#### Story: Follow synced lyrics while listening
**As a** visitor, **I want** the lyrics to follow along with the audio, **so
that** I can read in time with the recitation.

**Acceptance Criteria**
- When the playing track has time-synced lyrics, a lyrics overlay is available
  from the player.
- The overlay highlights/advances the current line in time with playback (see
  [Lyrics](../03-lyrics/spec.md) for the synced-lyrics model).

#### Story: Favorite and print from the player
**As a** contributor, **I want** to favorite the now-playing track from the
player, **so that** I don't have to navigate away.

**Acceptance Criteria**
- The player exposes a favorite toggle for the now-playing track (account
  required).
- The player exposes a print-lyrics affordance for the now-playing track.

### Persistence

#### Story: Keep playing as I browse
**As a** visitor, **I want** the player and queue to persist as I move around
the site, **so that** listening isn't interrupted by navigation.

**Acceptance Criteria**
- The player remains present and continues playing while I navigate between
  pages.
- The queue and current playback position are retained across navigation.

## Notes

- Playback that is started/observed contributes to track popularity (see
  [Discovery & Browsing](../01-discovery-and-browsing/spec.md)).
