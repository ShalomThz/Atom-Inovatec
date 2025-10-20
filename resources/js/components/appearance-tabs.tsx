import * as React from 'react';
import { useState } from 'react';
import { router } from '@inertiajs/react';

interface AppearanceTabsProps {
    currentTheme?: 'light' | 'dark' | 'system';
}

const AppearanceTabs: React.FC<AppearanceTabsProps> = ({ currentTheme = 'system' }) => {
    const [theme, setTheme] = useState(currentTheme);

    const handleThemeChange = (newTheme: 'light' | 'dark' | 'system') => {
        setTheme(newTheme);
        router.post('/settings/appearance', { theme: newTheme }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const themes = [
        { value: 'light', label: 'Claro', icon: '☀️' },
        { value: 'dark', label: 'Oscuro', icon: '🌙' },
        { value: 'system', label: 'Sistema', icon: '💻' },
    ];

    return (
        <div className="flex gap-4">
            {themes.map((themeOption) => (
                <button
                    key={themeOption.value}
                    onClick={() => handleThemeChange(themeOption.value as any)}
                    className={`flex flex-col items-center justify-center p-4 border rounded-lg transition-all ${
                        theme === themeOption.value
                            ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20'
                            : 'border-gray-300 hover:border-gray-400 dark:border-gray-700'
                    }`}
                >
                    <span className="text-2xl mb-2">{themeOption.icon}</span>
                    <span className="text-sm font-medium">{themeOption.label}</span>
                </button>
            ))}
        </div>
    );
};

export default AppearanceTabs;
