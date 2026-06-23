# 03 — Lyrics

How the written text of a track is read, followed in time, presented across
scripts and languages, repeated, and printed. Contributing and editing lyrics —
including the draft/review workflow and edit locking — lives in
[06 — Contributions & Moderation](./06-contributions-and-moderation.md). This spec
owns the **reading and following** experience plus the data shape of lyrics as
users perceive them.

Prerequisites: [00 — Foundation](./00-foundation.md).

---

## How users perceive lyrics

The library is predominantly Urdu and other Indo-Pak languages (Pashto, Punjabi,
and others). A given track may present its lyrics in several forms, and a user can
choose the form that suits them.

**Recitation structure (shared).** Underneath all forms, the lyrics are organized
as **lines**, optionally grouped into **stanzas**. A line may carry a **repeat
count** ("×2", "×3"). This structure mirrors the recitation itself and is shared
across every form below.

**Renderings (the forms a user can read).** Each track has a **primary language**
(e.g. Urdu) and one or more **renderings** of its lyrics:

| Rendering | Same language? | Script | Typical direction | Purpose |
|---|---|---|---|---|
| **Source** | yes (the original) | native (e.g. Urdu/Nastaliq) | right-to-left | The authentic text in its own script. |
| **Transliteration** | yes | Latin | left-to-right | The same words written phonetically in Latin script (the form most of the library carries today). |
| **Translation** | **no** (another language) | that language's script | per language | The *meaning* rendered in a language the reader understands (e.g. English). A track may have several translations. |

Source and transliteration are the **same words** in different scripts, so they
share the recitation structure line-for-line. A translation conveys **meaning**
and aligns by line/stanza where it reasonably can.

**Grade (independent of rendering).**
- **Plain** — text only, with structure and repeats but no timing.
- **Synced** — lines carry **timestamps**, enabling highlight-in-time ("karaoke")
  following during playback. Timing is defined against the recitation structure, so
  it can drive highlighting in whichever rendering shares that structure.

---

## Stories

### Reading

> **LYR-1 — As a visitor, I want to read a track's lyrics on its page, so that I can understand and follow the recitation.**
>
> **Acceptance criteria**
> - **Given** a track with lyrics, **then** a rendering of the lyrics is displayed
>   with its line grouping preserved.
> - **Given** lines marked as repeated, **then** the repeat count is shown clearly
>   (e.g. "×2") in every rendering.
> - **Given** the track has more than one rendering, **then** a sensible default is
>   shown first (my saved preference if any — see LYR-8 — otherwise the
>   transliteration, otherwise the source).
> - **Given** a track without lyrics, **then** the page shows a neutral empty state
>   (and, for contributors, an invitation to add them).

### Following in time

> **LYR-2 — As a visitor, I want to know when lyrics are synced to the audio, so that I know I can follow along in time.**
>
> **Acceptance criteria**
> - **Given** a track whose lyrics are time-synced, **then** the page indicates the
>   lyrics are synced.
> - **Given** plain (un-synced) lyrics, **then** no sync indication is shown and the
>   lyrics still read normally.

> **LYR-3 — As a visitor, I want synced lyrics to highlight in time with the audio, so that I can follow the exact line being recited.**
>
> **Acceptance criteria**
> - **Given** a track with synced lyrics is playing, **then** the line currently
>   being recited is visually highlighted, advancing as the audio progresses.
> - **Given** I am viewing the source or transliteration rendering, **then**
>   highlighting tracks the recited line exactly (they share the recitation
>   structure).
> - **When** I scrub to a different point in the audio, **then** the highlighted
>   line updates to match the new position.
> - **Given** the lyrics view is open on a phone, **then** following-in-time works
>   the same as on larger screens.
> - **Edge cases** — When viewing a **translation**, highlighting follows the
>   aligned line/stanza on a best-effort basis; where it can't align, the
>   translation still reads normally without breaking playback. If timing data is
>   incomplete, the timed lines still highlight and the rest read as plain text.

### Scripts: source & transliteration

> **LYR-4 — As a visitor, I want to read a nawha in its native script, so that I can experience the authentic source text.**
>
> **Acceptance criteria**
> - **Given** a track that has a source rendering (e.g. Urdu), **then** I can view
>   the lyrics in their native script.
> - **Given** a right-to-left script, **then** the text is displayed with correct
>   direction, alignment, and legible typography.
> - **Given** mixed-script content within a rendering, **then** each segment renders
>   in its correct direction.

> **LYR-5 — As a visitor, I want to switch between the transliteration and the native script, so that I can read whichever form I'm comfortable with.**
>
> **Acceptance criteria**
> - **Given** a track that has both a transliteration and a source rendering, **then**
>   I can switch between them without leaving the track.
> - **When** I switch rendering, **then** the recitation structure, repeat counts,
>   and (if playing) the synced highlight position are preserved across the switch.
> - **Given** a track that has only one of the two, **then** only that one is offered
>   and no empty alternate is shown.

### Translations

> **LYR-6 — As a visitor, I want to read a translation of a nawha in a language I understand, so that I can grasp its meaning even if I don't know the original language.**
>
> **Acceptance criteria**
> - **Given** a track that has one or more translations, **then** I can choose a
>   translation language and read the lyrics in it.
> - **Given** more than one translation exists, **then** I can see which languages
>   are available and pick among them.
> - **Given** a translation is selected, **then** its language and that it is a
>   translation (not the source) are clearly indicated, so it's never mistaken for
>   the original.
> - **Given** no translation exists, **then** no translation option is offered.

> **LYR-7 — As a visitor, I want to view two renderings together, so that I can read the original (or transliteration) and its meaning at the same time.**
>
> **Acceptance criteria**
> - **Given** a track with at least two renderings, **then** I can view two of them
>   side by side (e.g. transliteration + translation, or source + transliteration),
>   aligned by line/stanza.
> - **Given** lines that don't align one-to-one (common with translations), **then**
>   the pairing degrades gracefully to stanza-level alignment rather than appearing
>   broken.
> - **Given** a small screen, **then** a parallel view remains usable (e.g. stacked
>   per line/stanza) or I am offered single-rendering switching instead.

### Preferences

> **LYR-8 — As a visitor, I want my preferred rendering remembered, so that I don't re-select it on every track.**
>
> **Acceptance criteria**
> - **Given** I choose a rendering preference (e.g. "prefer transliteration", or
>   "prefer English translation when available"), **then** tracks default to that
>   rendering when they have it.
> - **Given** a track lacks my preferred rendering, **then** it falls back to a
>   sensible available rendering (per LYR-1) without error.
> - **Given** I have set no preference, **then** the default ordering in LYR-1 applies.
> - **Given** I am not signed in, **then** the preference persists on that device
>   (mirroring appearance preference — see [08 — Accounts & Authentication](./08-accounts-and-authentication.md), AUTH-6).
> - **Given** I am signed in, **then** the preference travels with my account across
>   devices.

### Availability

> **LYR-9 — As a visitor, I want to see which renderings a track offers, so that I know what's available before choosing.**
>
> **Acceptance criteria**
> - **Given** a track's lyrics, **then** the available renderings (source,
>   transliteration, and any translation languages) are clearly presented as
>   choices.
> - **Given** a rendering is missing, **then** it is simply absent from the choices
>   (and, for contributors, presented as something they could contribute — see
>   [06 — Contributions & Moderation](./06-contributions-and-moderation.md)).

### Printing

> **LYR-10 — As a visitor, I want a printer-friendly version of a track's lyrics, so that I can print or save them to read offline.**
>
> **Acceptance criteria**
> - **Given** a track with lyrics, **then** I can open a print-optimized view
>   showing the track title and key metadata as a header and the full lyrics in a
>   layout suited to paper.
> - **Given** the track has multiple renderings, **then** I can choose which
>   rendering to print, or print two together in parallel (e.g. transliteration +
>   translation).
> - **Then** the print view omits playback chrome and navigation, and preserves
>   repeat counts and correct script direction.
