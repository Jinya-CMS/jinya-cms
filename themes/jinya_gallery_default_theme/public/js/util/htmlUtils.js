/**
 * Utility class for DOM manipulation
 */
class HtmlUtils {
    /**
     * Converts the given html string into a node
     * @param {string} html
     * @returns {Node}
     */
    htmlToElement = (html) => {
        const range = document.createRange();
        range.selectNode(document.querySelector('body'));
        return range.createContextualFragment(html).firstElementChild;
    };
}