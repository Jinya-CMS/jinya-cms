import type { SidebarsConfig } from '@docusaurus/plugin-content-docs';
import apiSidebar from './docs/api/sidebar';

// This runs in Node.js - Don't use client-side code here (browser APIs, JSX...)

/**
 * Creating a sidebar enables you to:
 - create an ordered group of docs
 - render a sidebar for each doc of that group
 - provide next/previous navigation

 The sidebars can be generated from the filesystem, or explicitly defined here.

 Create as many sidebars as you want.
 */
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
        'getting-started',
    ],
    // By default, Docusaurus generates a sidebar from the docs folder structure
    // But you can create a sidebar manually
    /*
    tutorialSidebar: [
      'intro',
      'hello',
      {
        type: 'category',
        label: 'Tutorial',
        items: ['tutorial-basics/create-a-document'],
      },
    ],
     */
};

export default sidebars;
