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
    };

    /**
     * Code from https://stackoverflow.com/a/24075430
     */
    static jsonStringifyWithoutCycle = (obj: any, replacer?: (key: string, value: any) => any, space?: string | number) => {
        let cache = [];
        let json = JSON.stringify(obj, function (key, value) {
            if (typeof value === 'object' && value !== null) {
                if (cache.indexOf(value) !== -1) {
                    // circular reference found, discard key
                    return;
                }
                // store value in our collection
                cache.push(value);
            }
            return replacer ? replacer(key, value) : value;
        }, space);
        cache = null;

        return json;
    };
}

declare var texts: any;