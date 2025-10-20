import * as React from 'react';

interface AppLogoIconProps {
    className?: string;
}

const AppLogoIcon: React.FC<AppLogoIconProps> = ({ className = 'w-8 h-8' }) => {
    return (
        <svg
            className={className}
            viewBox="0 0 100 100"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <circle cx="50" cy="50" r="45" fill="currentColor" className="text-indigo-600" />
            <text
                x="50"
                y="50"
                textAnchor="middle"
                dominantBaseline="middle"
                fill="white"
                fontSize="40"
                fontWeight="bold"
            >
                A
            </text>
        </svg>
    );
};

export default AppLogoIcon;
