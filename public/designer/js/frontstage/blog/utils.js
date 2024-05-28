export function prepareCategories(cats, nesting = 0) {
  return cats
    .map((cat) => {
      const res = {
        ...cat,
        nesting,
      };

      if (cat.children) {
        return [res, ...prepareCategories(cat.children, nesting + 1)];
      }

      return [res];
    })
    .flat(Infinity);
}

export function categoriesToTree(categories) {
  return categories.reduce((tree, category) => {
    const parent = categories.find((c) => c.id === category.parent?.id);
    if (parent) {
      if (!parent.children) {
        parent.children = [];
      }
      parent.children.push(category);
    } else {
      tree.push(category);
    }

    return tree;
  }, []);
}
