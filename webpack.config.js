const Encore = require('@symfony/webpack-encore');
const path = require('path');
const MonacoEditorPlugin = require('monaco-editor-webpack-plugin')

Encore.configureRuntimeEnvironment('dev');
Encore
// directory where compiled assets will be stored
  .setOutputPath('public/designer-app')
  // public path used by the web server to access the output path
  .setPublicPath('/designer-app')
  // only needed for CDN's or sub-directory deploy
  .setManifestKeyPrefix('designer-app/')

  /*
   * ENTRY CONFIG
   *
   * Add 1 entry for each "page" of your app
   * (including one that's included on every page - e.g. "app")
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
   */
  .addEntry('designer', './assets/designer/main.js')
  .enableVueLoader()

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .enableSingleRuntimeChunk()

  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())

  // uncomment if you use Sass/SCSS files
  .enableSassLoader(() => ({
    data: '@import "designer";',
    includePaths: [
      path.join(__dirname, 'assets', 'designer', 'scss'),
    ],
  }))

  .addAliases({ '@': path.join(__dirname, 'assets', 'designer') })
  .addRule({
    test: /worker\/VideoUploader\.js$/,
    include: path.join(__dirname, 'assets', 'designer'),
    use: [
      { loader: 'worker-loader' },
      { loader: 'babel-loader' },
    ],
  })
  .copyFiles({
    from: path.join(__dirname, 'assets', 'designer', 'img'),
  })
  .addPlugin(new MonacoEditorPlugin({
    languages: ['javascript', 'css', 'html'],
  }))
  .enablePostCssLoader();

const webpackConfigEncore = Encore.getWebpackConfig();
webpackConfigEncore.resolve.alias = {
  ...webpackConfigEncore.resolve.alias,
  '@': path.join(__dirname, 'assets', 'designer'),
};
module.exports = webpackConfigEncore;
