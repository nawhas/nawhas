# Spec 07 — Accounts & Authentication

## Overview

Users create accounts to unlock the personal library and contribution features.
**Registration automatically grants the contributor role** — there is no
separate "plain member". The system supports registration, sign-in, sign-out,
and a self-service password reset. Social sign-in is present in the UI but
currently disabled.

## Personas

- **Visitor** — register and sign in.
- **Contributor / Moderator** — sign out; see their identity and role.

## User Stories

### Registration

#### Story: Create an account
**As a** visitor, **I want** to register, **so that** I can save nawhas and
contribute.

**Acceptance Criteria**
- Registration requires a name, an email, and a password.
- The email must be a valid email address and must not already be in use;
  violations are reported as validation errors.
- A missing name, email, or password is reported as a validation error.
- **When** registration succeeds, **Then** the user is signed in immediately.
- A newly registered account is a **contributor** (it can save tracks and
  contribute right away).

#### Story: Registration can be turned off
**As a** site operator, **I want** the ability to disable public registration,
**so that** sign-ups can be paused when needed.

**Acceptance Criteria**
- Public registration is governed by a toggle. When disabled, the register
  action is unavailable and the sign-up entry points are hidden.
- Registration is **currently enabled**.

### Signing in and out

#### Story: Sign in
**As a** visitor with an account, **I want** to sign in, **so that** I can
access my library and tools.

**Acceptance Criteria**
- Sign-in requires email and password; the submit action is unavailable until
  both are provided.
- **Given** valid credentials, **When** I sign in, **Then** I am signed in and my
  name and email appear in the user menu.
- **Given** invalid credentials, **When** I attempt sign-in, **Then** I see a
  generic failure message ("These credentials do not match our records.").
- A wrong email and a wrong password fail identically, so the form never reveals
  whether an email is registered.

#### Story: Sign out
**As a** contributor, **I want** to sign out, **so that** I can end my session.

**Acceptance Criteria**
- A sign-out action is available from the user menu and ends the session.

#### Story: 🚩 Sign in with a social account *(flagged / not yet active)*
**As a** visitor, **I want** to sign in with Google or Facebook, **so that** I
don't need a separate password.

**Acceptance Criteria**
- Social sign-in entry points (Google / Facebook) appear in the auth dialogs.
- 🚩 **Flagged / not yet active.** Social authentication is currently disabled
  by a feature toggle; the buttons are present in the UI but the flow is not
  active. When enabled, a social sign-in creates/sign-in a contributor account.

### Password reset

#### Story: Request a password reset
**As a** visitor, **I want** to request a reset link by email, **so that** I can
recover access if I forget my password.

**Acceptance Criteria**
- A user can request a reset by submitting their email.
- The response is always neutral — e.g. "If we have an account matching your
  email, we'll send an email." — so the form never reveals whether an email is
  registered.

#### Story: Reset my password
**As a** visitor, **I want** to set a new password from the emailed link, **so
that** I can regain access.

**Acceptance Criteria**
- The reset link carries a token that is validated before the reset form is
  shown.
- An invalid or expired token is rejected.
- With a valid token, the user can set a new password and then sign in with it.

### Identity & role

#### Story: See who I'm signed in as
**As a** contributor, **I want** to see my identity in the app, **so that** I
know which account is active.

**Acceptance Criteria**
- The user menu shows the signed-in user's name, email, and avatar.
- **Given** the user is a moderator, a moderator indicator/badge is shown.
- **Given** no user is signed in, the user menu shows a guest state with sign-in
  and (if registration is enabled) sign-up actions.

## Account fields

| Field | Notes |
|---|---|
| Name | Required |
| Email | Required, unique, valid format; used for sign-in and avatar |
| Password | Required; never exposed back to clients |
| Role | Contributor (default) or Moderator |
| Nickname | Optional |
| Avatar | Derived from the account's email |

## Notes & gaps

- **No self-service profile editing UI** is currently surfaced. The system can
  represent changes to a user's name, email, nickname, and password, but the
  only self-service account flow exposed today is the password reset. A
  profile/settings screen is a likely rewrite addition; documented here as a
  **gap**, not an existing feature.
- Roles are assigned operationally (a contributor is elevated to moderator
  outside the public UI); there is no in-app role-management screen.
