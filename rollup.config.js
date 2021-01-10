import svelte from 'rollup-plugin-svelte';
import commonjs from '@rollup/plugin-commonjs';
import resolve from '@rollup/plugin-node-resolve';
import json from '@rollup/plugin-json';
import copy from 'rollup-plugin-copy';
import postcss from 'rollup-plugin-postcss';

const production = !process.env.ROLLUP_WATCH;

export default {
  input: 'src/main.js',
  output: {
    sourcemap: !production,
    name: 'app',
    file: 'public/build/bundle.js',
    inlineDynamicImports: true
  },
  plugins: [
    json(),
    svelte({
      compilerOptions: {
        // enable run-time checks when not in production
        dev: !production
      }
    }),
    postcss(),

    // If you have external dependencies installed from
    // npm, you'll most likely need these plugins. In
    // some cases you'll need additional configuration -
    // consult the documentation for details:
    // https://github.com/rollup/plugins/tree/master/packages/commonjs
    resolve({
      browser: true,
      dedupe: ['svelte']
    }),
    commonjs(),
    copy({
      targets: [
        {
          src: 'node_modules/tinymce/skins/content',
          dest: 'public/build/skins/',
        },
        {
          src: 'node_modules/tinymce/skins/ui',
          dest: 'public/build/skins/',
        },
      ],
      verbose: true,
      copyOnce: true,
    }),
  ],
  watch: {
    clearScreen: false
  }
};
