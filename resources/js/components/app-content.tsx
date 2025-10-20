import * as React from 'react';

interface AppContentProps {
    children: React.ReactNode;
    className?: string;
}

const AppContent: React.FC<AppContentProps> = ({ children, className = '' }) => {
    return (
        <main className={`flex-1 overflow-y-auto p-6 ${className}`}>
            {children}
        </main>
    );
};

export { AppContent };
export default AppContent;
