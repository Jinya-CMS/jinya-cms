export async function navigate({ layout, stage, section, page }) {
  const { default: DisplayPage } = await import(`/designer/js/${stage}/${section}/${page}.js`);
  const displayPage = new DisplayPage({ layout });
  displayPage.display();
}

// eslint-disable-next-line no-unused-vars
export function navigateBackstage({ layout }) {}
