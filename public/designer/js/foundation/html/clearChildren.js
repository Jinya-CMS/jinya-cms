/**
 * Removes all children from an element
 * @param parent {Element}
 */
export default function clearChildren({ parent }) {
  while (parent.firstChild) {
    parent.firstChild.remove();
  }
}
