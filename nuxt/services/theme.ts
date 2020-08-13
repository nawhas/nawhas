import { Framework } from 'vuetify';
import { NuxtStorage } from '@nuxtjs/universal-storage';

export type Theme = 'light'|'dark'|'auto';

export const THEME_PREFERENCE_STORAGE_KEY = 'theme:preference';
export const THEME_APPLIED_STORAGE_KEY = 'theme:applied';

export function shouldUseDarkMode(preference: Theme): boolean {
  if (preference === 'dark') {
    return true;
  }

  if (preference === 'auto' && process.browser && window.matchMedia) {
    // This will check to see if the user has Dark Mode on their OS
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
  }

  return false;
}

export function saveAppliedTheme($vuetify: Framework, $storage: NuxtStorage) {
  const applied: Theme = $vuetify.theme.dark ? 'dark' : 'light';
  $storage.setUniversal(THEME_APPLIED_STORAGE_KEY, applied);
}

export function applyTheme(theme: Theme, $vuetify: Framework, $storage: NuxtStorage) {
  // Set vuetify theme.
  $vuetify.theme.dark = shouldUseDarkMode(theme);

  // Set last applied theme in storage.
  saveAppliedTheme($vuetify, $storage);
}
