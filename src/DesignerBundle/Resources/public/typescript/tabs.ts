class Tabs {
    private static init = (() => {
        let tabs = document.querySelectorAll<HTMLAnchorElement>('[data-toggle=tab]');
        let parents = [];
        for (let i = 0; i < tabs.length; i++) {
            let tab = tabs[i];
            parents.push(tab.parentElement);

            tab.addEventListener('click', evt => {
                let target = tab.getAttribute('href');

                classiex.remove(document.querySelectorAll('.page.active'), 'active');
                classiex.remove(parents, 'active');

                classie.add(document.querySelector(target), 'active');
                classie.add(tab.parentElement, 'active');
            });
        }

        let currentHash = document.location.hash;
        let tab: HTMLAnchorElement;
        if (currentHash) {
            tab = document.querySelector<HTMLAnchorElement>(`[href='${currentHash}']`);
        } else {
            if (tabs.length > 0) {
                tab = tabs[0];
            }
        }
        if (tab) {
            tab.click();
        }
    })();
}