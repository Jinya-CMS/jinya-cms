import App from './App.svelte';
import { addMessages, init } from 'svelte-i18n';
import de from './lang/de.json';
import en from './lang/en.json';

addMessages('de', de);
addMessages('en', en);

init({
  fallbackLocale: 'en',
});

const app = new App({
  target: document.getElementById('app'),
});

export default app;
