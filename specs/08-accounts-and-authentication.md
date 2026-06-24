# 08 — Accounts & Authentication

Becoming a user, signing in and out, recovering access, managing one's profile and
preferences, and the public contributor profile. Role elevation is in
[09 — Administration](./09-administration.md).

Prerequisites: [00 — Foundation](./00-foundation.md).

---

## Stories — Getting an account

> **AUTH-1 — As a visitor, I want to register an account, so that I can save tracks, build playlists, and contribute.**
>
> **Acceptance criteria**
> - **Given** public registration is enabled, **when** I register with a name, email,
>   and password (and an optional display name/nickname), **then** an account is
>   created and I am signed in.
> - **Given** I successfully register, **then** my account starts as a **contributor**.
> - **Given** the email is already in use or the input is invalid, **then** I see a
>   clear, specific error and no account is created.
> - **Given** public registration is disabled by an administrator, **then** the
>   registration entry point is unavailable and direct attempts are refused with an
>   explanation (see [09 — Administration](./09-administration.md)).

> **AUTH-1b — As a registered user, I want to verify my email address, so that my account is trusted and I can contribute.**
>
> **Acceptance criteria**
> - **Given** I have registered, **then** I am sent a verification email with a
>   confirmation link, and my account is immediately usable for consuming, saving,
>   and building playlists.
> - **Given** I have not yet verified, **when** I try to submit a catalog proposal
>   (see [06 — Contributions & Moderation](./06-contributions-and-moderation.md), CON-1), **then** I am prompted to verify first and the
>   proposal is not submitted until I do — refused with a clear explanation, never a
>   silent failure.
> - **When** I open a valid verification link, **then** my email is marked verified
>   and I can contribute.
> - **Given** the verification link is invalid or expired, **then** I see a clear
>   message and can request a new one.
> - **Edge cases** — Social sign-in (AUTH-2) supplies a provider-verified email, which
>   counts as verified; such users need no separate verification step.

> **AUTH-2 — As a visitor, I want to sign in with a social account, so that I can get started without creating a new password.**  *(Feature-toggled)*
>
> **Acceptance criteria**
> - **Given** social sign-in is enabled, **then** I can choose to continue with a
>   supported provider and, on success, I am signed in.
> - **Given** it's my first time, **then** an account is created for me as a
>   **contributor**, using my provider profile (name, email, avatar).
> - **Given** an account already exists for my email, **then** social sign-in signs
>   me into that existing account rather than creating a duplicate.
> - **Given** social sign-in is disabled, **then** the option is not shown.

---

## Stories — Access

> **AUTH-3 — As a registered user, I want to sign in and out, so that I can access my account and protect it on shared devices.**
>
> **Acceptance criteria**
> - **Given** valid credentials, **when** I sign in, **then** I gain access to my
>   library, playlists, contribution tools, and (if elevated) moderator/admin tools.
> - **Given** invalid credentials, **then** I see a clear error and remain signed out.
> - **When** I sign out, **then** my session ends and account-only surfaces become
>   unavailable until I sign in again.
> - **Given** I am signed in, **then** my listening can continue across navigation
>   regardless of auth state.

> **AUTH-4 — As a registered user, I want to recover access if I forget my password, so that I'm never permanently locked out.**
>
> **Acceptance criteria**
> - **Given** I request a password reset for my email, **then** I am sent a reset
>   link, and the interface confirms the request without revealing whether the email
>   is registered.
> - **Given** I open a valid reset link, **then** I can set a new password, after
>   which I can sign in with it.
> - **Given** I open an invalid or expired reset link, **then** I see a clear message
>   and can request a new one.

---

## Stories — Profile & preferences

> **AUTH-5 — As a signed-in user, I want to see and manage my account, so that my identity and preferences are correct.**
>
> **Acceptance criteria**
> - **Given** I am signed in, **then** I can see my name, display name, email,
>   avatar, and my role indicated (e.g. a badge for moderators/admins).
> - **Then** I can update my profile details and avatar.
> - **Then** I can change my password (with current-password confirmation).

> **AUTH-6 — As a visitor, I want my appearance preference remembered, so that the site looks the way I like every visit.**
>
> **Acceptance criteria**
> - **Given** I choose light or dark appearance, **then** the choice persists across
>   visits on that device.
> - **Given** I am signed in, **then** the choice travels with my account across
>   devices.
> - **Given** I have set no preference, **then** a sensible default applies.

> **AUTH-7 — As a contributor, I want a public profile, so that my accepted contributions are attributed and discoverable.**
>
> **Acceptance criteria**
> - **Given** I have accepted contributions, **then** I have a public profile showing
>   my display name, avatar, and a record of my contributions.
> - **Given** a visitor opens my public profile, **then** they can see my
>   contributions and any public playlists I own — but never my private data
>   (email, private playlists, library).
> - **Given** I prefer not to be public, **then** I can limit what my profile shows.

---

## Stories — Feedback (everyone)

> **ACC-FEEDBACK — As any user, I want to report a bug, request a feature, or send general feedback, so that I can help improve the product.**
>
> **Acceptance criteria**
> - **Given** the feedback entry point, **then** I can choose a type (bug, feature,
>   or general), give a short summary, and optionally add details and a contact
>   email.
> - **When** I submit, **then** I receive confirmation that it was received.
> - **Given** I provide no email, **then** I can still submit anonymously.

> **ACC-REPORT — As any user, I want to report a specific piece of content as problematic, so that moderators and administrators can act on it.**
>
> **Acceptance criteria**
> - **Given** a track, reciter, album, story, or public playlist, **then** I can
>   report it, choose a reason, and optionally add details.
> - **When** I submit a report, **then** I receive confirmation, and the report —
>   with a reference to the exact content — reaches the oversight surface (see
>   [09 — Administration](./09-administration.md), ADM-6).
> - **Given** I am not signed in, **then** I can still submit a report.
> - **Edge cases** — Reporting does not change the content's visibility; only a
>   moderator's or administrator's action does.
