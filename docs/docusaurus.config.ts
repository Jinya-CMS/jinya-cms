import { themes as prismThemes } from 'prism-react-renderer';
import type { Config } from '@docusaurus/types';
import type * as Preset from '@docusaurus/preset-classic';
import type * as OpenApiPlugin from 'docusaurus-plugin-openapi-docs';

// This runs in Node.js - Don't use client-side code here (browser APIs, JSX...)

const config: Config = {
    title: 'Jinya CMS',
    tagline: 'Manage your portfolio with ease',
    favicon: 'img/logo.svg',

    // Future flags, see https://docusaurus.io/docs/api/docusaurus-config#future
    future: {
        v4: true, // Improve compatibility with the upcoming Docusaurus v4
    },

    // Set the production url of your site here
    url: 'https://cms.jinya.dev',
    // Set the /<baseUrl>/ pathname under which your site is served
    // For GitHub pages deployment, it is often '/<projectName>/'
    baseUrl: '/',

    // GitHub pages deployment config.
    // If you aren't using GitHub pages, you don't need these.
    organizationName: 'jinya-cms', // Usually your GitHub org/user name.
    projectName: 'jinya-cms', // Usually your repo name.

    onBrokenLinks: 'throw',

    // Even if you don't use internationalization, you can use this field to set
    // useful metadata like html lang. For example, if your site is Chinese, you
    // may want to replace "en" with "zh-Hans".
    i18n: {
        defaultLocale: 'en',
        locales: ['en'],
    },

    presets: [
        [
            'classic',
            {
                docs: {
                    sidebarPath: './sidebars.ts',
                    docItemComponent: '@theme/ApiItem',
                },
                theme: {
                    customCss: './src/css/custom.css',
                },
            } satisfies Preset.Options,
        ],
    ],

    themeConfig: {
        // Replace with your project's social card
        image: 'img/social-card.jpg',
        colorMode: {
            respectPrefersColorScheme: true,
        },
        navbar: {
            title: 'Jinya CMS',
            logo: {
                alt: 'Jinya CMS Logo',
                src: 'img/logo.svg',
            },
            items: [
                {
                    type: 'docSidebar',
                    sidebarId: 'users',
                    position: 'left',
                    label: 'User Guide',
                },
                {
                    type: 'docSidebar',
                    sidebarId: 'developers',
                    position: 'left',
                    label: 'Developer Docs',
                },
                {
                    href: 'https://gitlab.imanuel.dev/jinya-cms/jinya-cms/',
                    label: 'GitLab',
                    position: 'right',
                },
                {
                    href: 'https://github.com/jinya-cms/jinya-cms',
                    label: 'GitHub',
                    position: 'right',
                },
            ],
        },
        footer: {
            style: 'dark',
            links: [
                {
                    title: 'Documentation',
                    items: [
                        {
                            label: 'User Guides',
                            to: '/docs/getting-started',
                        },
                        {
                            label: 'Developer Docs',
                            to: '/docs/theme/create',
                        },
                        {
                            label: 'API Documentation',
                            to: '/docs/api-docs',
                        },
                    ],
                },
                {
                    title: 'Community',
                    items: [
                        {
                            label: 'Stack Overflow',
                            href: 'https://stackoverflow.com/questions/tagged/jinya-cms',
                        },
                        {
                            label: 'Website',
                            href: 'https://jinya.de',
                        },
                    ],
                },
                {
                    title: 'More',
                    items: [
                        {
                            href: 'https://gitlab.imanuel.dev/jinya-cms/jinya-cms/',
                            label: 'GitLab',
                        },
                        {
                            label: 'GitHub',
                            href: 'https://github.com/jinya-cms/jinya-cms',
                        },
                    ],
                },
            ],
            copyright: `Copyright © ${new Date().getFullYear()} Jinya Developers. Built with Docusaurus.`,
        },
        prism: {
            additionalLanguages: ['php', 'php-extras', 'yaml'],
            theme: prismThemes.github,
            darkTheme: prismThemes.dracula,
        },
    } satisfies Preset.ThemeConfig,
    plugins: [
        [
            'docusaurus-plugin-openapi-docs',
            {
                id: 'api', // plugin id
                docsPluginId: 'classic', // configured for preset-classic
                config: {
                    petstore: {
                        specPath: 'apidocs.yml',
                        outputDir: 'docs/api',
                        maskCredentials: false, // Disable credential masking in code snippets
                        sidebarOptions: {
                            groupPathsBy: 'tag',
                        },
                    } satisfies OpenApiPlugin.Options,
                },
            },
        ],
    ],
    themes: ['docusaurus-theme-openapi-docs'],
};

export default config;
