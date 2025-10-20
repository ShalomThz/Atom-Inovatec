import * as React from 'react';

interface AppShellProps {
    children: React.ReactNode;
}

const AppShell: React.FC<AppShellProps> = ({ children }) => {
    return (
        <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
            {children}
        </div>
    );
};

export { AppShell };
export default AppShell;
