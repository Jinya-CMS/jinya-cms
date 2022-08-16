export async function navigateFrontstage({ layout, section, page }) {
  const { default: DisplayPage } = await import(`/jinya-designer/js/front/${section}/${page}.js`);
  const displayPage = new DisplayPage({ layout });
  displayPage.display();
}

// eslint-disable-next-line no-unused-vars
export function navigateBackstage({ layout }) {
}
