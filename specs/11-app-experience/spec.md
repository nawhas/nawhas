# Spec 11 — App Experience

## Overview

Cross-cutting experience features that aren't tied to a single content type: the
About page, theme preference, global navigation, and the installable/offline app
shell with update and "what's new" notifications.

## Personas

**Visitor** (everyone). Theme preference applies to all personas.

## User Stories

### Navigation & shell

#### Story: Navigate the app consistently
**As a** visitor, **I want** consistent global navigation, **so that** I can move
between the main areas of the site easily.

**Acceptance Criteria**
- A persistent top bar provides: the logo (to home), primary navigation (home,
  browse reciters, library, about), global search, and the user menu.
- On small screens, primary navigation collapses into a drawer.
- A footer provides links to the About page and the feedback action, and the
  logo.
- When audio is playing, the player bar is present at the bottom (see
  [Audio Player](../02-audio-player-and-queue/spec.md)).

### About page

#### Story: Learn about the project
**As a** visitor, **I want** an About page, **so that** I understand what
Nawhas.com is, who made it, and how to contribute.

**Acceptance Criteria**
- The About page presents an introductory hero.
- It shows a **journey / timeline** of the project's history.
- It shows **credits** — contributors with avatar, name, role, contributions,
  and links.
- It includes a **contribute** call-to-action pointing to the project's source.

### Theme preference

#### Story: Choose a theme
**As a** visitor, **I want** to choose light, dark, or automatic theme, **so
that** the site is comfortable to read.

**Acceptance Criteria**
- A theme control (light / auto / dark) is available in the user menu.
- "Auto" follows the device/system preference.
- The chosen preference persists across visits.

### Installable / offline app shell

#### Story: 🚩 Install and use the app offline *(partially active)*
**As a** visitor, **I want** to install Nawhas.com like an app and have it work
when offline, **so that** it feels fast and reliable.

**Acceptance Criteria**
- The app is installable as a standalone app on supported devices.
- The app shell is available offline; previously loaded content remains
  accessible where possible.
- 🚩 The exact extent of offline support is **partial / evolving** and should be
  validated against current behavior during a rewrite.

#### Story: Be told when a new version is available
**As a** visitor, **I want** to know when the app has updated, **so that** I can
refresh to the latest version.

**Acceptance Criteria**
- When a newer version of the app is available, the user is prompted to update /
  refresh.

#### Story: See "what's new"
**As a** visitor, **I want** to see recent changes/announcements, **so that** I
know what's new in the product.

**Acceptance Criteria**
- A changelog / "what's new" notification surface presents recent product
  updates to the user.

## Notes

- The app-shell, install, offline, and update behaviors are surfaced here as
  **observable experience features**; their exact current extent is the area
  most worth re-verifying against live behavior during the rewrite.
