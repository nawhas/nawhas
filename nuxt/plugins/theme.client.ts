import { Plugin } from '@nuxt/types';
import {
  shouldUseDarkMode, applyTheme, saveAppliedTheme, Theme, THEME_PREFERENCE_STORAGE_KEY,
} from '@/services/theme';

function installThemeEventListener(callback: () => void) {
  if (!window.matchMedia) {
    return;
  }

  const query = window.matchMedia('(prefers-color-scheme: dark)');

  if (typeof query.addEventListener === 'function') {
    query.addEventListener('change', callback);
  } else if (typeof query.addListener === 'function') {
    query.addListener(callback);
  }
}

const PreferencesPlugin: Plugin = ({ store, $vuetify, $storage }) => {
  // 1. Set the preferences state using our universal storage.
  const preference: Theme = $storage.getUniversal(THEME_PREFERENCE_STORAGE_KEY);
  store.commit('preferences/SET_THEME', preference);

  // 2. Set up store watcher to update our universal storage when the preference changes.
  store.watch((state) => state.preferences.theme, (value: Theme) => {
    // Set vuetify theme.
    $vuetify.theme.dark = shouldUseDarkMode(value);

    // Set preference in storage.
    $storage.setUniversal(THEME_PREFERENCE_STORAGE_KEY, value);

    // Set last applied theme in storage.
    saveAppliedTheme($vuetify, $storage);
  });

  // 3. Set up listener to respond to when system dark mode setting changes.
  installThemeEventListener(() => {
    applyTheme(store.state.preferences.theme, $vuetify, $storage);
  });
};

export default PreferencesPlugin;
