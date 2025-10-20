import * as React from 'react';
import { Link } from '@inertiajs/react';

interface TextLinkProps {
    href: string;
    children: React.ReactNode;
    className?: string;
}

const TextLink: React.FC<TextLinkProps> = ({ href, children, className = '' }) => {
    return (
        <Link
            href={href}
            className={`text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors ${className}`}
        >
            {children}
        </Link>
    );
};

export default TextLink;
