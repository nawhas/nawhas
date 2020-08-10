/* eslint-disable no-console */
import { Plugin } from '@nuxt/types';
import { Theme, THEME_APPLIED_STORAGE_KEY, THEME_PREFERENCE_STORAGE_KEY } from '@/entities/theme';

function shouldUseDarkMode(preference: Theme): boolean {
  if (preference === 'dark') {
    return true;
  }

  if (preference === 'auto' && window.matchMedia) {
    // This will check to see if the user has Dark Mode on their OS
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
  }

  return false;
}

function installThemeEventListener(callback) {
  if (!window.matchMedia) {
    return;
  }

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
    console.log('System theme setting changed: ', e.matches ? 'dark' : 'light');
    callback();
  });
}

const PreferencesPlugin: Plugin = ({ store, $vuetify, $storage }) => {
  // Set up closures.
  const saveAppliedTheme = () => {
    const applied: Theme = $vuetify.theme.dark ? 'dark' : 'light';
    console.log('Saving applied theme:', applied);
    $storage.setUniversal(THEME_APPLIED_STORAGE_KEY, applied);
  };

  const applyTheme = () => {
    // Set vuetify theme.
    $vuetify.theme.dark = shouldUseDarkMode(store.state.preferences.theme);
    console.log('Determined new theme:', $vuetify.theme.dark ? 'dark' : 'light');

    // Set last applied theme in storage.
    saveAppliedTheme();
  };

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
    saveAppliedTheme();
  });

  // 3. Set up listener to respond to when system dark mode setting changes.
  installThemeEventListener(() => {
    console.log('Responding to system theme change listener');
    applyTheme();
  });

  // 4. Initialize theme:
  applyTheme();
};

export default PreferencesPlugin;
