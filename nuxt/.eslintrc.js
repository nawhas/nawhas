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
    'no-use-before-define': ['off'],
    '@typescript-eslint/no-use-before-define': ['error', { typedefs: false }],
    'arrow-parens': ['error', 'always'],
    'vue/no-v-html': ['off'],
    'vue/no-v-text-v-html-on-component': ['off'],
    'vue/order-in-components': ['off'],
    '@typescript-eslint/no-var-requires': ['off'],
  },
};
