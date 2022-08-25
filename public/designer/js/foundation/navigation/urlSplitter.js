/**
 * Splits the current hash to match parse the section and page
 * @return {{section: string, page: string, stage: string}}
 */
export default function urlSplitter() {
  const url = window.location.hash;
  if (url === '') {
    return {
      stage: 'front',
      section: 'statistics',
      page: 'matomo-stats',
    };
  }
  const split = url.split('/');
  return {
    stage: split.length === 0 ? 'front' : split[0].replace('#', ''),
    section: split.length === 0 ? 'statistics' : split[1],
    page: split.length === 0 ? 'index' : split[2],
  };
}
