import de from '../../../lang/de.js';
import en from '../../../lang/en.js';

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
  let splitPath = [];
  if (path.includes('.')) {
    splitPath = path.split('.');
  } else {
    splitPath = path.split('-');
  }

  // eslint-disable-next-line no-plusplus
  for (let i = 0; i < splitPath.length; ++i) {
    if (!current[splitPath[i]]) return def;
    current = current[splitPath[i]];
  }

  return current;
}

export function getLanguage() {
  if (navigator.language.startsWith('de')) {
    return 'de';
  }

  return 'en';
}

/**
 * Localizes the given key and returns the matching string
 * @param key {string}
 * @param values {Object}
 * @return string
 */
export default function localize({ key, values = {} }) {
  let localized = key;
  if (getLanguage() === 'de') {
    localized = getByPath({
      obj: de,
      path: key,
      def: key,
    });
  } else {
    localized = getByPath({
      obj: en,
      path: key,
      def: key,
    });
  }

  let transformed = localized;
  for (const valueKey of Object.keys(values)) {
    transformed = transformed.replaceAll(`{${valueKey}}`, values[valueKey]);
  }

  return transformed;
}
