class classiex extends classie {

    public static add(element: NodeListOf<Element> | Element, clazz: string) {
        if (element instanceof Element) {
            classie.add(element, clazz);
        } else {
            for (let i = 0; i < element.length; i++) {
                classie.add(element.item(i), clazz);
            }
        }
    }

    public static remove(element: NodeListOf<Element> | Array<Element> | Element, clazz: string) {
        if (element instanceof Element) {
            classie.remove(element, clazz);
        } else {
            for (let i = 0; i < element.length; i++) {
                classie.remove(element[i], clazz);
            }
        }
    }
}