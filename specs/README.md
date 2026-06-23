# Nawhas.com — Product Specification

This directory is the **single source of truth** for the *observable behavior* of
Nawhas.com. It describes **what** the product does for its users, not **how** it is
built. There is deliberately nothing here about technology choices, system
boundaries, data storage, or deployment — only behavior a user can see, do, or
experience.

These specs describe the **target state** of the product: the system we are
building toward in the overhaul, which folds in agreed improvements as the new
normal. Where the target meaningfully differs from how things work today, the
text simply describes the target; it does not document legacy behavior.

## What is Nawhas.com?

Nawhas.com is a library and listening platform for *nawhas* — devotional
elegiac recitations. It lets anyone discover, listen to, read along with, and
organize this material, and it lets a community of contributors and moderators
grow and curate the collection over time.

## How these specs are organized

The specification is split into a **foundation** document plus one document per
tentpole feature. Read the foundation first; every feature spec assumes its
glossary, user types, and cross-cutting rules.

| # | Spec | Covers |
|---|------|--------|
| 00 | [Foundation](./00-foundation.md) | Vision, glossary, user types, permission model, content lifecycle, cross-cutting rules |
| 01 | [Catalog & Discovery](./01-catalog-and-discovery.md) | Reciters, albums, tracks, artists, tags, topics, browsing |
| 02 | [Playback](./02-playback.md) | Audio player, queue, shuffle, continuous listening |
| 03 | [Lyrics](./03-lyrics.md) | Reading, synced/timestamped lyrics, repeats, printing |
| 04 | [Search](./04-search.md) | Finding reciters, albums, tracks, and more |
| 05 | [Library & Playlists](./05-library-and-playlists.md) | Favorites, playlists, collections, public/private sharing |
| 06 | [Contributions & Moderation](./06-contributions-and-moderation.md) | Proposing changes, review queue, publishing, history |
| 07 | [Stories](./07-stories.md) | Editorial articles / news |
| 08 | [Accounts & Authentication](./08-accounts-and-authentication.md) | Registration, login, social sign-in, password reset, profiles |
| 09 | [Administration](./09-administration.md) | User & role management, feature toggles, site configuration |
| 10 | [Roadmap (non-binding)](./10-roadmap.md) | Far-off capabilities not yet specced as committed behavior |

## How to read a story

Every requirement is expressed as a **user story** with **acceptance criteria**.

> **[ID] As a `<user type>`, I want `<capability>`, so that `<benefit>`.**
>
> **Acceptance criteria**
> - **Given** some context, **when** an action occurs, **then** an observable
>   outcome results.
>
> **Edge cases & notes** — clarifications, boundary conditions, and exclusions.

Story IDs are stable, prefixed per spec (e.g. `CAT-3`, `PLAY-7`). Reference them
in issues, commits, and tests so behavior is traceable to the spec. To keep IDs
stable, a story inserted between two existing ones takes a letter suffix
(e.g. `CAT-8b`) rather than forcing a renumber.

## Conventions

- **User types are cumulative.** A capability granted to a lower user type is
  also available to every higher one, unless a story explicitly says otherwise.
- **"The catalog"** means the body of reciters, albums, and tracks together.
- Anything labeled **Roadmap** is directional only and is not a commitment.
