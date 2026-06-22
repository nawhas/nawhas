# Spec 10 — Feedback

## Overview

Anyone can send feedback — a bug report, a feature request, or a general
comment — from the site footer. Feedback is captured and routed to the team's
tracker; the user need not have an account.

## Personas

**Visitor** (available to everyone, signed in or not).

## User Stories

### Submitting feedback

#### Story: Report a bug or suggest a feature
**As a** visitor, **I want** to send feedback from anywhere on the site, **so
that** I can report problems or suggest improvements.

**Acceptance Criteria**
- A feedback entry point is available in the footer on every page.
- The form lets the user choose a **type**: bug, feature, or general.
- The form requires a short **summary** (a brief headline, length-limited).
- The form accepts optional **details** (longer description).
- The form accepts an optional **email** for follow-up.
- Submitting with a missing/invalid required field is rejected with validation
  feedback.
- On success, the submission is accepted and the user receives confirmation.

#### Story: My email isn't exposed publicly
**As a** visitor, **I want** any email I provide to be handled carefully, **so
that** it isn't scraped or exposed.

**Acceptance Criteria**
- When feedback is routed onward, a submitter's email is obfuscated rather than
  shown in plain form.

## Feedback fields

| Field | Required | Notes |
|---|:---:|---|
| Type | ✓ | One of: bug, feature, general |
| Summary | ✓ | Short headline; length-limited |
| Details | ✗ | Longer free text |
| Email | ✗ | For follow-up; obfuscated when routed onward |

## Notes

- The destination/tracker that feedback is routed to is an implementation
  concern and intentionally not specified here; the observable behavior is
  "submit feedback → acknowledged".
