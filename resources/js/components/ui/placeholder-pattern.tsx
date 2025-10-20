import * as React from 'react';

interface PlaceholderPatternProps {
    className?: string;
}

const PlaceholderPattern: React.FC<PlaceholderPatternProps> = ({ className = '' }) => {
    return (
        <div className={`relative ${className}`}>
            <svg
                className="absolute inset-0 w-full h-full"
                xmlns="http://www.w3.org/2000/svg"
            >
                <defs>
                    <pattern
                        id="grid"
                        width="40"
                        height="40"
                        patternUnits="userSpaceOnUse"
                    >
                        <path
                            d="M 40 0 L 0 0 0 40"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="1"
                            className="text-gray-200 dark:text-gray-700"
                        />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
    );
};

export { PlaceholderPattern };
