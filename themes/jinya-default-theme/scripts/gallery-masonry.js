(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const items = document.querySelectorAll('[data-action=masonry-click]');
        items.forEach((item) => {
            const openModal = () => {
                const parent = item.parentElement;
                const clone = parent.cloneNode(true);
                clone.classList.add('is--modal');
                document.body.classList.add('is--open');
                clone.querySelector('img').removeEventListener('click', openModal);
                document.body.appendChild(clone);
                const closeModal = () => {
                    clone.classList.remove('is--open');
                    setTimeout(() => {
                        document.body.classList.remove('is--open');
                        document.body.removeChild(clone);
                    }, 501);
                };
                clone.addEventListener('click', closeModal);
                setTimeout(() => clone.classList.add('is--open'), 10);
            };

            item.addEventListener('click', openModal);
        });
    });
});
