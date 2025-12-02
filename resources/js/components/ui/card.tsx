import * as React from 'react';

interface CardProps {
    children: React.ReactNode;
    className?: string;
    variant?: 'default' | 'glass' | 'gradient';
}

const Card: React.FC<CardProps> = ({ children, className = '', variant = 'default' }) => {
    const variants = {
        default: 'bg-card text-card-foreground border border-border/50 shadow-md hover:shadow-lg',
        glass: 'glass-light border border-border/30 shadow-xl',
        gradient: 'bg-gradient-primary text-white border-0 shadow-2xl',
    };

    return (
        <div className={`rounded-2xl transition-smooth ${variants[variant]} ${className}`}>
            {children}
        </div>
    );
};

const CardHeader: React.FC<CardProps> = ({ children, className = '' }) => {
    return (
        <div className={`px-6 py-5 ${className}`}>
            {children}
        </div>
    );
};

const CardTitle: React.FC<CardProps> = ({ children, className = '' }) => {
    return (
        <h3 className={`text-2xl font-bold tracking-tight ${className}`}>
            {children}
        </h3>
    );
};

const CardDescription: React.FC<CardProps> = ({ children, className = '' }) => {
    return (
        <p className={`text-sm text-muted-foreground mt-1.5 ${className}`}>
            {children}
        </p>
    );
};

const CardContent: React.FC<CardProps> = ({ children, className = '' }) => {
    return <div className={`px-6 py-4 ${className}`}>{children}</div>;
};

const CardFooter: React.FC<CardProps> = ({ children, className = '' }) => {
    return (
        <div className={`px-6 py-4 border-t border-border/50 ${className}`}>
            {children}
        </div>
    );
};

export { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter };
