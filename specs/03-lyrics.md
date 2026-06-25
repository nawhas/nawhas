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

**Sections (optional).** Stanzas may additionally be grouped into named
**sections** that carry a **role**, reflecting how the recitation is built. The
common roles are:

| Role | Meaning |
|---|---|
| **Asthayi** | The recurring **refrain / chorus** (also known as *Mukhda* or *Tek*). It is written **once** and recurs between stanzas rather than being repeated in the text. |
| **Sani** | The first stanza, immediately following the refrain. |
| **Antra** | Each subsequent verse, **numbered** in order ("1st Antra", "2nd Antra", …). |
| **Dohra** | A couplet (or short set of mono-rhyme couplets) **recited** in a declamatory style rather than sung to the main tune, inserted between sung stanzas. A noha may contain more than one. |
| **Maqta** | An optional closing couplet. |

Sectioning is **optional and flexible**: many nawhas have no named refrain at all
(simple verse chains, regional and non-Urdu forms), in which case the lyrics are
just stanzas with no roles. A track may also use a **custom** role label where none
of the above fits. A **Dohra** is typically recited rather than sung, so it may stay
plain (un-timed) even when the rest of the track is synced. Like repeats and timing,
sections live on the **shared recitation structure**, so they apply across the source
and transliteration alike.
Because the role labels vary by community, each user may rename them for their own
view (see LYR-1d).

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

### Sections & structure

> **LYR-1b — As a visitor, I want a track's lyric sections labelled, so that I can tell the refrain from the verses and follow how the recitation is built.**
>
> **Acceptance criteria**
> - **Given** a track whose lyrics are organized into sections, **then** each section
>   is shown with its **role label** (e.g. "Asthayi", "Sani", "1st Antra", "Maqta").
> - **Given** Antra verses, **then** they are numbered in order ("1st Antra",
>   "2nd Antra", …).
> - **Given** I switch rendering (source ↔ transliteration), **then** the section
>   labels and grouping are preserved (they belong to the shared recitation
>   structure).
> - **Edge cases** — A track whose lyrics have no sections reads as plain stanzas
>   with no labels (per LYR-1). A section with a **custom** role shows that label as
>   given.

> **LYR-1c — As a visitor, I want to choose how the recurring refrain (Asthayi) is shown, so that I can read it the way that suits me.**
>
> **Acceptance criteria**
> - **Given** a track whose refrain (Asthayi) is written once and recurs between
>   stanzas, **then** by default I see where it recurs marked clearly, without its
>   full text repeated.
> - **When** I choose to **expand** the refrain, **then** its full text is shown
>   inline between each stanza.
> - **When** I choose to **collapse** it, **then** only a short marker indicates
>   where it recurs.
> - **When** I choose to **hide** it, **then** the recurrence markers are omitted and
>   only the first occurrence remains.
> - **Given** I set this choice, **then** it is remembered for future tracks
>   (mirroring the rendering preference — see LYR-8): not signed in, it persists on
>   the device; signed in, it travels with my account.
> - **Edge cases** — A track with no refrain offers no such control.

> **LYR-1d — As a visitor, I want to rename the section roles to the terms my community uses, so that I see names that are correct for me.**
>
> **Acceptance criteria**
> - **Given** the standard role labels (Asthayi, Sani, Antra, Maqta), **then** I can
>   override any of them with my own preferred term.
> - **Given** I set an override, **then** that term is shown in place of the default
>   wherever the role label appears, across all tracks.
> - **Given** I have set no override for a role, **then** its default label is shown.
> - **Given** I am not signed in, **then** my overrides persist on that device;
>   **given** I am signed in, **then** they travel with my account across devices
>   (mirroring LYR-8 and [08 — Accounts & Authentication](./08-accounts-and-authentication.md), AUTH-6).
> - **Edge cases** — Overrides change only how labels are displayed **to me**; they do
>   not change the underlying content, other users' view, or search.

> **LYR-1e — As a visitor, I want to jump between lyric sections while reading, so that I can move around quickly without scrolling — especially when reciting live.**
>
> **Acceptance criteria**
> - **Given** a track with labelled sections, **then** the lyrics view shows a
>   **section index** listing each section by its (possibly personalized — see LYR-1d)
>   label ("Asthayi", "Sani", "1st Antra", …, "Maqta").
> - **When** I select a section in the index, **then** the lyrics jump to it
>   immediately.
> - **Given** the refrain (Asthayi) recurs throughout, **then** I can return to it in
>   **one action** from anywhere in the lyrics, regardless of my refrain-display
>   choice (see LYR-1c).
> - **Given** the section index, **then** the section I am currently reading is
>   indicated; **given** synced lyrics are playing, **then** the indicated section
>   follows the highlighted line (see LYR-3).
> - **Given** a small screen, **then** the section index stays reachable (e.g. a
>   compact control), so navigation works the same while reciting on a phone.
> - **Edge cases** — A track with no labelled sections shows no section index and
>   reads and scrolls as normal.

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
