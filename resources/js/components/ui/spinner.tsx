import * as React from 'react';

export interface SpinnerProps extends React.HTMLAttributes<HTMLDivElement> {
    size?: 'sm' | 'md' | 'lg';
}

function Spinner({ className = '', size = 'md', ...props }: SpinnerProps) {
    const sizes = {
        sm: 'h-4 w-4',
        md: 'h-8 w-8',
        lg: 'h-12 w-12',
    };

    return (
        <div className={`inline-block ${className}`} {...props}>
            <div
                className={`${sizes[size]} animate-spin rounded-full border-2 border-solid border-current border-r-transparent motion-reduce:animate-[spin_1.5s_linear_infinite]`}
                role="status"
            >
                <span className="sr-only">Loading...</span>
            </div>
        </div>
    );
}

export { Spinner };
