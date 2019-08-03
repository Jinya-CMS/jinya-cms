export default {
    async asyncForeach(array, callback) {
        const promises = array.map((item, index) => callback(item, index));
        await Promise.all(promises);
    },
};
