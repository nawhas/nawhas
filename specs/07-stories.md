# 07 — Stories

Editorial content: articles, announcements, and history pieces presented
alongside the library. Stories are authored by moderators and read by everyone.

Prerequisites: [00 — Foundation](./00-foundation.md).

---

## How users perceive a story

A story has a **title**, a **display date**, a **hero image**, a short **excerpt**,
and a **body** of formatted text. A story is either **published** (publicly visible)
or a **draft** (visible only to moderators).

---

## Stories — Readers

> **STORY-1 — As a visitor, I want to read published stories, so that I can learn the history and news around the library.**
>
> **Acceptance criteria**
> - **Given** a published story, **then** I can read its title, display date, hero
>   image, and full body.
> - **Given** a story has a durable, shareable address, **then** links to it keep
>   working.
> - **Given** a story is a draft (unpublished), **then** I cannot see it.

> **STORY-2 — As a visitor, I want recent and featured stories surfaced, so that I notice new editorial content.**
>
> **Acceptance criteria**
> - **Given** the home page, **then** featured/recent published stories are surfaced
>   with their title, excerpt, and hero image (see [01 — Catalog & Discovery](./01-catalog-and-discovery.md), CAT-1).
> - **When** I select a story, **then** I open its full page.

---

## Stories — Moderators

> **STORY-3 — As a moderator, I want to create a story, so that I can publish editorial content.**
>
> **Acceptance criteria**
> - **Given** I am a moderator, **when** I create a story, **then** I can set its
>   title, display date, hero image (uploaded or by address), excerpt, and body.
> - **Then** I can choose whether it is published or kept as a draft.
> - **When** I save, **then** I receive clear confirmation of success or a clear
>   error.

> **STORY-4 — As a moderator, I want to edit and unpublish a story, so that I can keep content accurate.**
>
> **Acceptance criteria**
> - **Given** an existing story, **then** I can edit any of its fields.
> - **When** I unpublish a published story, **then** it stops being publicly visible
>   but is retained as a draft.
> - **When** I publish a draft, **then** it becomes publicly visible.

> **STORY-5 — As a moderator, I want to delete a story, so that I can remove content that should no longer exist.**
>
> **Acceptance criteria**
> - **Given** a story, **when** I delete it, **then** I must confirm, and afterward it
>   is no longer accessible to anyone.

> **STORY-6 — As a moderator, I want to manage all stories in one place, so that I can see what's published and what's still a draft.**
>
> **Acceptance criteria**
> - **Given** the stories management view, **then** I see all stories with their
>   title, address, and publication status (published / draft), paged.
> - **Then** I can start a new story or open any existing one to edit.
