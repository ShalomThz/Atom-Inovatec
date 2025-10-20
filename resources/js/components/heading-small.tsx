import * as React from 'react';

interface HeadingSmallProps {
    children: React.ReactNode;
    className?: string;
}

const HeadingSmall: React.FC<HeadingSmallProps> = ({ children, className = '' }) => {
    return (
        <h3 className={`text-lg font-semibold text-gray-900 dark:text-gray-100 ${className}`}>
            {children}
        </h3>
    );
};

export default HeadingSmall;
