import type { ReactNode } from 'react';
import clsx from 'clsx';
import Heading from '@theme/Heading';
import styles from './styles.module.css';

type FeatureItem = {
    title: string;
    description: ReactNode;
};

const FeatureList: FeatureItem[] = [
    {
        title: 'Full file management',
        description: (
            <>
                Manage your portfolio content with ease, including a file management with folders and tags.
            </>
        ),
    },
    {
        title: 'Get insights',
        description: (
            <>
                Get insights into what works and what doesn't. Check where your visitors come from and what they use.
            </>
        ),
    },
    {
        title: 'Make it yours',
        description: (
            <>
                Customize it as much as you want. Build your own theme and make Jinya CMS your own.
            </>
        ),
    },
];

function Feature({ title, description }: FeatureItem) {
    return (
        <div className={clsx('col col--4')}>
            <div className="text--center padding-horiz--md">
                <Heading as="h3">{title}</Heading>
                <p>{description}</p>
            </div>
        </div>
    );
}

export default function HomepageFeatures(): ReactNode {
    return (
        <section className={styles.features}>
            <div className="container">
                <div className="row">
                    {FeatureList.map((props, idx) => (
                        <Feature key={idx} {...props} />
                    ))}
                </div>
            </div>
        </section>
    );
}
