(() => {
  const scrollItem = document.querySelector('.jinya-horizontal-gallery');
  if (scrollItem) {
    scrollItem.addEventListener('wheel', (e) => {
      if (!e.deltaX) {
        scrollItem.scrollBy({
          behavior: 'auto',
          left: e.deltaY > 0 ? 100 : -100,
        });
      }
    });
  }
})();
