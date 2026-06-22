# 03 — Lyrics

How the written text of a track is read, followed in time, repeated, and printed.
Contributing and editing lyrics — including the draft/review workflow and edit
locking — lives in [06 — Contributions & Moderation](./06-contributions-and-moderation.md).
This spec owns the **reading and following** experience plus the data shape of
lyrics as users perceive them.

Prerequisites: [00 — Foundation](./00-foundation.md).

---

## How users perceive lyrics

- Lyrics are organized as **lines**, optionally grouped (e.g. stanzas).
- A line may carry a **repeat count** ("×2", "×3") indicating it is sung
  repeatedly.
- Lyrics come in two grades:
  - **Plain** — text only, with structure and repeats but no timing.
  - **Synced** — each line additionally has a **timestamp**, enabling
    highlight-in-time ("karaoke") following during playback.

---

## Stories

### Reading

> **LYR-1 — As a visitor, I want to read a track's lyrics on its page, so that I can understand and follow the recitation.**
>
> **Acceptance criteria**
> - **Given** a track with lyrics, **then** the lyrics are displayed with their
>   line grouping preserved.
> - **Given** lines marked as repeated, **then** the repeat count is shown clearly
>   (e.g. "×2").
> - **Given** a track without lyrics, **then** the page shows a neutral empty state
>   (and, for contributors, an invitation to add them).

> **LYR-2 — As a visitor, I want to know when lyrics are synced to the audio, so that I know I can follow along in time.**
>
> **Acceptance criteria**
> - **Given** a track whose lyrics are time-synced, **then** the page indicates the
>   lyrics are synced.
> - **Given** plain (un-synced) lyrics, **then** no sync indication is shown and the
>   lyrics still read normally.

### Following in time

> **LYR-3 — As a visitor, I want synced lyrics to highlight in time with the audio, so that I can follow the exact line being recited.**
>
> **Acceptance criteria**
> - **Given** a track with synced lyrics is playing, **then** the line currently
>   being recited is visually highlighted, advancing as the audio progresses.
> - **When** I scrub to a different point in the audio, **then** the highlighted
>   line updates to match the new position.
> - **Given** the lyrics view is open on a phone, **then** following-in-time works
>   the same as on larger screens.
> - **Edge cases** — If timing data is incomplete, the available timed lines still
>   highlight and the rest read as plain text without breaking playback.

### Printing

> **LYR-4 — As a visitor, I want a printer-friendly version of a track's lyrics, so that I can print or save them to read offline.**
>
> **Acceptance criteria**
> - **Given** a track with lyrics, **then** I can open a print-optimized view
>   showing the track title and key metadata as a header and the full lyrics in a
>   layout suited to paper.
> - **Then** the print view omits playback chrome and navigation.
> - **Given** repeated lines, **then** repeat counts are preserved in print.

### Language & direction

> **LYR-5 — As a visitor, I want lyrics rendered legibly regardless of script or direction, so that the text is faithful and readable.**
>
> **Acceptance criteria**
> - **Given** lyrics in a right-to-left script, **then** they are displayed with
>   correct direction and alignment.
> - **Given** mixed-script content, **then** each segment renders in its correct
>   direction.

> **LYR-6 — As a visitor, I want translation and transliteration to be presentable alongside the original when available, so that I can read in a form I understand.**  *(Near-term)*
>
> **Acceptance criteria**
> - **Given** a track that has an original plus a transliteration and/or translation,
>   **then** I can view them together or switch between them.
> - **Given** only the original exists, **then** the lyrics read normally with no
>   empty alternate views.
> - **Edge cases** — Synchronization, when present, applies to the original; alternate
>   renderings need not be individually timed.
