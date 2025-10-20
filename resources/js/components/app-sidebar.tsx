import * as React from 'react';
import { Link } from '@inertiajs/react';

interface SidebarItem {
    name: string;
    href: string;
    icon?: React.ReactNode;
    current?: boolean;
}

interface AppSidebarProps {
    items: SidebarItem[];
}

const AppSidebar: React.FC<AppSidebarProps> = ({ items }) => {
    return (
        <aside className="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">
            <nav className="flex flex-col p-4 space-y-2">
                {items.map((item) => (
                    <Link
                        key={item.name}
                        href={item.href}
                        className={`flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors ${
                            item.current
                                ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-400'
                                : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700'
                        }`}
                    >
                        {item.icon && <span className="mr-3">{item.icon}</span>}
                        {item.name}
                    </Link>
                ))}
            </nav>
        </aside>
    );
};

export { AppSidebar };
export default AppSidebar;
