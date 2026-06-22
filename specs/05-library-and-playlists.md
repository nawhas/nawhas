# 05 — Library & Playlists

A signed-in user's personal space: their saved tracks (favorites) and the
playlists they build, keep private, or share publicly.

Prerequisites: [00 — Foundation](./00-foundation.md). Saving and playlists require
a signed-in account; see [08 — Accounts & Authentication](./08-accounts-and-authentication.md).

---

## Stories

### The library landing

> **LIB-1 — As a visitor, I want the library area to explain its value and invite me to sign in, so that I understand what an account gives me.**
>
> **Acceptance criteria**
> - **Given** I am not signed in and I open the library area, **then** I see an
>   explanation of what a library offers (favorites, playlists, collections) and a
>   clear call to get started.
> - **When** I choose to get started, **then** I am guided to register or sign in.

### Favorites

> **LIB-2 — As a signed-in user, I want to save tracks to my library, so that I can easily return to recitations I value.**
>
> **Acceptance criteria**
> - **Given** any track (on its page, in a list, or in the player), **then** I see a
>   favorite control reflecting whether it is already saved.
> - **When** I favorite a track, **then** it is added to my library immediately and
>   the control reflects the saved state everywhere it appears.
> - **When** I un-favorite a track, **then** it is removed from my library
>   immediately.
> - **Given** I favorite or un-favorite in one place, **then** the change is
>   reflected consistently across the site for my account.

> **LIB-3 — As a signed-in user, I want a home for my saved tracks, so that I can see and play what I've collected.**
>
> **Acceptance criteria**
> - **Given** my library home, **then** I see my most recently saved tracks with a
>   way to view all of them.
> - **Given** my full saved-tracks list, **then** I can page through every saved
>   track and see its key metadata.
> - **Then** I can **play all** my saved tracks as a queue.
> - **Given** I have saved nothing yet, **then** I see an empty state explaining how
>   to add favorites.

### Playlists

> **LIB-4 — As a signed-in user, I want to create playlists, so that I can group tracks for a purpose or mood.**
>
> **Acceptance criteria**
> - **Given** I am signed in, **when** I create a playlist, **then** I give it a
>   title (and optionally a description) and it appears in my playlists.
> - **When** I add a track to a playlist (from the track, a list, or the player),
>   **then** it is appended to that playlist.
> - **When** I remove a track or reorder tracks, **then** the playlist updates
>   accordingly.
> - **When** I delete a playlist, **then** it is removed after I confirm.

> **LIB-5 — As a signed-in user, I want to play a playlist, so that I can listen to my grouping continuously.**
>
> **Acceptance criteria**
> - **Given** a playlist with tracks, **when** I play it, **then** its tracks load
>   into the queue in order (see [02 — Playback](./02-playback.md)).
> - **Then** I can also add the playlist to the existing queue.
> - **Given** an empty playlist, **then** play controls are disabled with an
>   explanation.

> **LIB-6 — As a signed-in user, I want to choose whether a playlist is private or public, so that I control who can see it.**
>
> **Acceptance criteria**
> - **Given** a playlist, **then** it is **private by default**, visible only to me.
> - **When** I make it public, **then** it gains a durable, shareable address and can
>   be viewed and played by anyone (including visitors) and can appear in search.
> - **When** I make it private again, **then** it stops being accessible to others
>   and disappears from search.
> - **Given** a public playlist, **then** viewers can play it but cannot modify it.

> **LIB-7 — As a visitor, I want to view and play someone's public playlist, so that I can benefit from community curation.**
>
> **Acceptance criteria**
> - **Given** a public playlist link, **then** I can view its title, description,
>   owner, and tracks, and play it — without signing in.
> - **Given** I am signed in, **then** I can save any track from it to my own library
>   or copy it into a playlist of my own.
> - **Given** the playlist was made private or deleted, **then** I see a clear
>   "not available" state.

### Collections vs. playlists

> **LIB-8 — As a visitor, I want product-curated collections to be distinct from personal playlists, so that I can tell editorial curation from user lists.**
>
> **Acceptance criteria**
> - **Given** a topic collection (see [01 — Catalog & Discovery](./01-catalog-and-discovery.md), CAT-9), **then** it is
>   presented as curated content, not as a user's personal playlist.
> - **Given** I am signed in, **then** I can play a collection and save its tracks,
>   but I do not own or edit it.
