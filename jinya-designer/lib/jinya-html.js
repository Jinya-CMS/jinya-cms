/**
 * Renders the given template string and parses the given function, string or array directly into the HTML
 *
 * @param {String[]} strings The static parts of the template
 * @param {Function|String|Array} args The dynamic types of the template
 * @returns {string} The rendered HTML
 */
export default function html(strings, ...args) {
  let str = strings[0];
  for (let i = 0; i < args.length; i++) {
    if (args[i] instanceof Function) {
      str += args[i]();
    } else if (Array.isArray(args[i])) {
      str += args[i].join('');
    } else {
      str += args[i];
    }
    str += strings[i + 1];
  }

  return str.trimEnd().trimStart();
}
