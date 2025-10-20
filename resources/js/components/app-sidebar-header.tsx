import * as React from 'react';
import { Link } from '@inertiajs/react';
import AppLogoIcon from './app-logo-icon';

const AppSidebarHeader: React.FC = () => {
    return (
        <div className="flex items-center gap-3 px-4 py-4 border-b border-gray-200 dark:border-gray-700">
            <AppLogoIcon className="w-8 h-8" />
            <span className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                AtomInovatec
            </span>
        </div>
    );
};

export { AppSidebarHeader };
export default AppSidebarHeader;
