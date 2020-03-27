module.exports = {
  root: true,
  env: {
    node: true,
  },
  extends: [
    'plugin:vue/essential',
    '@vue/airbnb',
    '@vue/typescript/recommended',
  ],
  parserOptions: {
    ecmaVersion: 2020,
  },
  rules: {
    'no-plusplus': 0,
    'class-methods-use-this': 0,
    'no-return-assign': 0,
    "no-useless-constructor": "off",
    'lines-between-class-members': 0,
    'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    // don't require .vue extension when importing
    'import/extensions': ['error', 'always', {
      js: 'never',
      ts: 'never',
      vue: 'always',
    }],
    'import/prefer-default-export': 0,
    'max-len': ['error', { code: 120, ignoreUrls: true }],
    'no-restricted-syntax': 0,
    'linebreak-style': 0,
    '@typescript-eslint/no-explicit-any': 0,
    '@typescript-eslint/camelcase': ['error', {
      properties: 'always',
      allow: ['per_page'],
    }],
    'no-shadow': 0,
    'arrow-parens': ['error', 'always'],
    'no-param-reassign': ['error', {
      props: true,
      ignorePropertyModificationsFor: [
        'acc', // for reduce accumulators
        'e', // for e.returnvalue
        'ctx', // for Koa routing
        'req', // for Express requests
        'request', // for Express requests
        'res', // for Express responses
        'response', // for Express responses
        '$scope', // for Angular 1 scopes
        'state', // for Vuex mutations
      ],
    }],
  },
  overrides: [
    {
      files: [
        '**/__tests__/*.{j,t}s?(x)',
        '**/tests/unit/**/*.spec.{j,t}s?(x)',
      ],
      env: {
        jest: true,
      },
    },
  ],
};
