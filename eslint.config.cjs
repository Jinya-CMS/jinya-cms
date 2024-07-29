const js = require('@eslint/js');
const prettier = require('eslint-config-prettier');
const globals = require('globals');

module.exports = [
  {
    ...js.configs.recommended,
    ...prettier,
    files: [
      '/public/installer/**/*.js',
      '/public/designer/js/**/*.js',
      '/public/designer/lang/**/*.js',
    ],
    ignore: [
      '/vendor',
      '/themes'
    ],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        ...globals.browser,
        Jodit: 'readonly',
      },
    },
  },
];
