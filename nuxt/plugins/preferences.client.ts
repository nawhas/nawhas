import { Plugin } from '@nuxt/types';
import { Theme } from '@/store/preferences';

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

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    callback();
  });
}

const PreferencesPlugin: Plugin = async ({ store, $vuetify }) => {
  await store.dispatch('preferences/initialize');

  const setTheme = () => {
    $vuetify.theme.dark = shouldUseDarkMode(store.state.preferences.theme);
  };

  store.watch((state) => state.preferences.theme, setTheme);

  setTheme();
  installThemeEventListener(setTheme);
};

export default PreferencesPlugin;
