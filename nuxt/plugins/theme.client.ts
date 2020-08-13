/* eslint-disable no-console */
import { Plugin } from '@nuxt/types';
import {
  shouldUseDarkMode, applyTheme, saveAppliedTheme, Theme, THEME_PREFERENCE_STORAGE_KEY,
} from '@/services/theme';

function installThemeEventListener(callback: () => void) {
  if (!window.matchMedia) {
    return;
  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    console.log('System theme setting changed: ', e.matches ? 'dark' : 'light');
    callback();
  });
}

const PreferencesPlugin: Plugin = ({ store, $vuetify, $storage }) => {
  // 1. Set the preferences state using our universal storage.
  const preference: Theme = $storage.getUniversal(THEME_PREFERENCE_STORAGE_KEY);
  console.log('Found theme preference from storage:', preference);
  store.commit('preferences/SET_THEME', preference);

  // 2. Set up store watcher to update our universal storage when the preference changes.
  store.watch((state) => state.preferences.theme, (value: Theme) => {
    console.log('Theme preference in Vuex changed:', value);
    // Set vuetify theme.
    $vuetify.theme.dark = shouldUseDarkMode(value);
    console.log('Determined new theme:', $vuetify.theme.dark ? 'dark' : 'light');

    // Set preference in storage.
    $storage.setUniversal(THEME_PREFERENCE_STORAGE_KEY, value);
    console.log('Saved theme preference in storage:', value);

    // Set last applied theme in storage.
    saveAppliedTheme($vuetify, $storage);
  });

  // 3. Set up listener to respond to when system dark mode setting changes.
  installThemeEventListener(() => {
    console.log('Responding to system theme change listener');
    applyTheme(store.state.preferences.theme, $vuetify, $storage);
  });
};

export default PreferencesPlugin;
