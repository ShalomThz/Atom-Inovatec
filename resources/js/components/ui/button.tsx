import * as React from 'react';

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
    variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link' | 'gradient' | 'accent';
    size?: 'default' | 'sm' | 'lg' | 'icon';
}

const Button = React.forwardRef<HTMLButtonElement, ButtonProps>(
    ({ className = '', variant = 'default', size = 'default', ...props }, ref) => {
        const baseStyles = 'inline-flex items-center justify-center rounded-xl text-sm font-medium transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none active:scale-95';

        const variants = {
            default: 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-md hover:shadow-lg hover-scale-sm',
            destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90 shadow-md hover:shadow-lg hover-scale-sm',
            outline: 'border-2 border-input hover:bg-accent hover:text-accent-foreground hover:border-accent-foreground/50 hover-scale-sm',
            secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80 shadow-md hover:shadow-lg hover-scale-sm',
            ghost: 'hover:bg-accent hover:text-accent-foreground hover-scale-sm',
            link: 'underline-offset-4 hover:underline text-primary hover-scale-sm',
            gradient: 'bg-gradient-primary text-white shadow-lg hover:shadow-xl hover-lift glow-primary font-semibold',
            accent: 'bg-accent text-accent-foreground hover:bg-accent/90 shadow-md hover:shadow-lg hover-scale-sm',
        };

        const sizes = {
            default: 'h-10 py-2 px-5',
            sm: 'h-9 px-4 text-xs',
            lg: 'h-12 px-8 text-base',
            icon: 'h-10 w-10',
        };

        const classes = `${baseStyles} ${variants[variant]} ${sizes[size]} ${className}`;

        return <button className={classes} ref={ref} {...props} />;
    }
);

Button.displayName = 'Button';

export { Button };
