class JSONTools {
    /**
     * Code from https://stackoverflow.com/a/24075430
     */
    jsonStringifyWithoutCycle = (obj, replacer, space) => {
        let cache = [];
        const json = JSON.stringify(obj, function (key, value) {
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