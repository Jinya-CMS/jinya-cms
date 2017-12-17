class Util {
    /**
     * Converts the given html string into a node
     * @param {string} html
     * @returns {Node}
     */
    static htmlToElement = (html: string) => {
        let range = document.createRange();
        range.selectNode(document.querySelector('body'));

        return range.createContextualFragment(html).firstElementChild;
    };

    static getText = (identifier: string): string => {
        return texts[identifier];
    }
}

declare var texts: any;