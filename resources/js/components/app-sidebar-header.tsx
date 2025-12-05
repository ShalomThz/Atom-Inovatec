import * as React from 'react';
import { Link, usePage } from '@inertiajs/react';
import AppLogoIcon from './app-logo-icon';
import { NotificationCenter } from './notification-center';

const AppSidebarHeader: React.FC = () => {
    const { auth } = usePage().props as any;

    return (
        <div className="flex items-center justify-between gap-3 px-4 py-4 border-b border-gray-200 dark:border-gray-700">
            <div className="flex items-center gap-3">
                <AppLogoIcon className="w-8 h-8" />
                <span className="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    AtomInov
                </span>
            </div>
            <div className="flex items-center gap-4">
                <NotificationCenter />
                <span className="text-sm text-gray-600 dark:text-gray-400">
                    {auth?.user?.name}
                </span>
            </div>
        </div>
    );
};

export { AppSidebarHeader };
export default AppSidebarHeader;
