import * as React from 'react';
import { Link, usePage } from '@inertiajs/react';
import { NotificationCenter } from './notification-center';

interface AppHeaderProps {
    title?: string;
}

export const AppHeader: React.FC<AppHeaderProps> = ({ title }) => {
    const { auth } = usePage().props as any;

    return (
        <header className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
            <div className="flex items-center justify-between">
                <h1 className="text-xl font-semibold text-gray-900 dark:text-gray-100">
                    {title || 'AtomInovatec'}
                </h1>
                <div className="flex items-center gap-4">
                    <NotificationCenter />
                    <span className="text-sm text-gray-600 dark:text-gray-400">
                        {auth?.user?.name}
                    </span>
                </div>
            </div>
        </header>
    );
};
