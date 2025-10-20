import * as React from 'react';

export interface InputOTPProps extends React.InputHTMLAttributes<HTMLInputElement> {
    length?: number;
}

const InputOTP = React.forwardRef<HTMLInputElement, InputOTPProps>(
    ({ className = '', length = 6, ...props }, ref) => {
        return (
            <input
                type="text"
                maxLength={length}
                className={`flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm text-center tracking-widest ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 ${className}`}
                ref={ref}
                {...props}
            />
        );
    }
);

InputOTP.displayName = 'InputOTP';

const InputOTPGroup = ({ children, className = '', ...props }: React.HTMLAttributes<HTMLDivElement>) => {
    return (
        <div className={`flex items-center gap-2 ${className}`} {...props}>
            {children}
        </div>
    );
};

const InputOTPSlot = ({ index, className = '', ...props }: React.HTMLAttributes<HTMLDivElement> & { index: number }) => {
    return (
        <div
            className={`relative flex h-10 w-10 items-center justify-center border border-input text-sm ${className}`}
            {...props}
        />
    );
};

export { InputOTP, InputOTPGroup, InputOTPSlot };
