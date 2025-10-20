import * as React from 'react';

interface SeparatorProps {
    orientation?: 'horizontal' | 'vertical';
    className?: string;
}

const Separator: React.FC<SeparatorProps> = ({ orientation = 'horizontal', className = '' }) => {
    return (
        <div
            className={`bg-gray-200 dark:bg-gray-700 ${
                orientation === 'horizontal' ? 'h-px w-full' : 'w-px h-full'
            } ${className}`}
        />
    );
};

export { Separator };
