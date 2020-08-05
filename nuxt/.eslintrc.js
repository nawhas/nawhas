module.exports = {
  root: true,
  env: {
    browser: true,
    node: true,
  },
  extends: [
    '@nuxtjs/eslint-config-typescript',
    'plugin:nuxt/recommended',
  ],
  plugins: [
  ],
  // add your custom rules here
  rules: {
    'space-before-function-paren': 'off',
    'comma-dangle': ['warn', 'always-multiline'],
    'semi': ['error', 'always'],
    'no-console': 'warn',
    'quote-props': ['error', 'consistent-as-needed'],
    'no-useless-constructor': 'off',
    'arrow-parens': ['error', 'always'],
  },
};
