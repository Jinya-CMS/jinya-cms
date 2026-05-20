import type { SidebarsConfig } from '@docusaurus/plugin-content-docs';
import apiSidebar from './docs/api/sidebar';

const sidebars: SidebarsConfig = {
    developers: [
        {
            type: 'category',
            label: 'Themes',
            items: [
                'theme/create',
                'theme/configure',
                'theme/theme-extensions',
                'theme/access-configuration',
                'theme/access-links',
            ],
        },
        {
            type: 'category',
            label: 'API Documentation',
            items: apiSidebar,
            collapsed: true
        },
    ],
    users: [
        {
            type: 'category',
            label: 'Getting Started',
            link: {
                type: 'doc',
                id: 'getting-started',
            },
            items: [
                'getting-started/apache',
                'getting-started/docker',
                'getting-started/frankenphp',
            ],
        },
    ],
};

export default sidebars;
