import de from '../../lang/de.js';
import en from '../../lang/en.js';

/**
 * Gets the value by the given path
 *
 * @param obj {Object}
 * @param path {string}
 * @param def {string}
 * @return {string}
 */
function getByPath({ obj, path = '', def = '' }) {
  let current = obj;
  const splitPath = path.split('.');
  // eslint-disable-next-line no-plusplus
  for (let i = 0; i < splitPath.length; ++i) {
    if (!current[splitPath[i]]) return def;
    current = current[splitPath[i]];
  }

  return current;
}

/**
 * Localizes the given key and returns the matching string
 * @param key {string}
 * @return string
 */
export default function localize({ key }) {
  if (navigator.language.startsWith('de')) {
    return getByPath({ obj: de, path: key, def: key });
  }

  return getByPath({ obj: en, path: key, def: key });
}
