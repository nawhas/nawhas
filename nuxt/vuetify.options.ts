import { Context } from '@nuxt/types';
import colors from 'vuetify/es5/util/colors';
import { UserVuetifyPreset } from 'vuetify';
import { parse as parseCookie } from 'cookie';
import { THEME_APPLIED_STORAGE_KEY } from '@/services/theme';

export default function options({ req }: Context): Partial<UserVuetifyPreset> {
  const cookie = process.client ? document.cookie : req.headers.cookie || '';
  const storage = parseCookie(cookie) || {};
  const dark = storage[THEME_APPLIED_STORAGE_KEY] === 'dark';

  return {
    icons: {
      iconfont: 'md',
    },
    theme: {
      dark,
      themes: {
        light: {
          primary: colors.red.base,
          secondary: colors.grey.darken2,
          accent: colors.orange.accent3,
        },
        dark: {
          primary: colors.red.base,
          secondary: colors.grey.darken2,
          accent: colors.orange.accent3,
        },
      },
    },
  };
};
