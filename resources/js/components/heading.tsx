import * as React from 'react';

interface HeadingProps {
    children: React.ReactNode;
    className?: string;
}

const Heading: React.FC<HeadingProps> = ({ children, className = '' }) => {
    return (
        <h1 className={`text-2xl font-bold text-gray-900 dark:text-gray-100 ${className}`}>
            {children}
        </h1>
    );
};

export default Heading;
