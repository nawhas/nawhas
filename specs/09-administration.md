# 09 — Administration

The administrator tier: managing people and roles, controlling site-wide
capabilities, and overseeing quality. Administrators inherit everything a
moderator can do (see [06 — Contributions & Moderation](./06-contributions-and-moderation.md)
and [07 — Stories](./07-stories.md)); this spec covers what is **unique** to admins.

Prerequisites: [00 — Foundation](./00-foundation.md), especially §3 *User types*.

---

## Stories — Users & roles

> **ADM-1 — As an administrator, I want to view and search the user base, so that I can find and manage accounts.**
>
> **Acceptance criteria**
> - **Given** I am an administrator, **then** I can browse and search users by name
>   or email, paged.
> - **Then** for each user I can see their display name, email, role, and join date.

> **ADM-2 — As an administrator, I want to grant and revoke roles, so that I can build a trusted moderation team.**
>
> **Acceptance criteria**
> - **Given** a user, **then** I can promote them to **moderator** or **administrator**,
>   or demote them back to **contributor**.
> - **When** I change a user's role, **then** their available capabilities change
>   accordingly the next time they act, and the change is recorded.
> - **Given** I attempt to remove my own administrator access, **then** I am
>   protected from leaving the site with no administrators (e.g. the action is
>   refused or requires another admin).

> **ADM-3 — As an administrator, I want to suspend or restore accounts, so that I can stop abuse without destroying data.**
>
> **Acceptance criteria**
> - **Given** an account, **when** I suspend it, **then** that user can no longer sign
>   in or act, and the reason is recorded.
> - **When** I restore it, **then** the user regains their prior access.
> - **Given** an account is suspended, **then** its public attribution and any
>   already-published contributions remain intact unless separately removed.
> - **Given** the suspended account holds editing locks or has pending proposals,
>   **then** its locks are released so others are not blocked (see [06 — Contributions & Moderation](./06-contributions-and-moderation.md), CON-5/CON-9),
>   and its pending proposals remain reviewable in the queue.

---

## Stories — Site capabilities

> **ADM-4 — As an administrator, I want to turn site-wide capabilities on or off, so that I can control what's available without a release.**
>
> **Acceptance criteria**
> - **Given** the set of feature toggles (e.g. public registration, social sign-in),
>   **then** I can see each one's current state and change it.
> - **When** I disable a capability, **then** its entry points across the site are
>   hidden or disabled and attempts to use it are refused with an explanation.
> - **When** I enable a capability, **then** it becomes available site-wide.

> **ADM-5 — As an administrator, I want to manage site-wide presentation and announcements, so that I can communicate changes to everyone.**
>
> **Acceptance criteria**
> - **Given** an announcement / "what's new" capability, **then** I can publish a
>   dismissible notice that users see and can dismiss.
> - **Then** I control its content, validity window, and whether it is currently
>   shown.

> **ADM-5b — As an administrator, I want to manage informational and legal pages, so that the site can present about, privacy, terms, and contact information.**
>
> **Acceptance criteria**
> - **Given** the informational-pages capability, **then** I can create and edit
>   standalone pages (e.g. **About**, **Privacy Policy**, **Terms**, **Contact**) with a
>   title, body, and durable address (see [00 — Foundation](./00-foundation.md), §6.9).
> - **Given** a page is published, **then** it is publicly readable by everyone at its
>   durable address and reachable from a consistent place (e.g. the site footer).
> - **When** I unpublish a page, **then** it stops being publicly visible; **when** I
>   delete one, **then** I must confirm, after which it is no longer accessible.
> - **Edge cases** — These pages are informational only and never gate consumption
>   (§6.1 *Open by default*).

---

## Stories — Oversight

> **ADM-6 — As an administrator, I want to review feedback and reports, so that I can act on problems users raise.**
>
> **Acceptance criteria**
> - **Given** feedback (see [08 — Accounts & Authentication](./08-accounts-and-authentication.md), ACC-FEEDBACK) and content
>   reports (ACC-REPORT) submitted by users, **then** I can review them in one place.
> - **Then** each content report links to the exact content and its reporter (where
>   not anonymous), so I can reach the relevant content or user.
> - **Then** I can resolve or dismiss a report, and the outcome is recorded.

> **ADM-7 — As an administrator, I want full visibility into change history, so that I can audit the catalog and the team.**
>
> **Acceptance criteria**
> - **Given** the change history (see [06 — Contributions & Moderation](./06-contributions-and-moderation.md), CON-14),
>   **then** I have at least the same visibility as moderators across all changes
>   and all contributors.
