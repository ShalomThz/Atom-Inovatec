import { motion, useInView } from 'framer-motion';
import { useRef, type ReactNode } from 'react';

interface AnimatedOnScrollProps {
    children: ReactNode;
    animation?: 'fade' | 'slide-up' | 'slide-left' | 'slide-right' | 'zoom';
    delay?: number;
    className?: string;
}

export function AnimatedOnScroll({
    children,
    animation = 'fade',
    delay = 0,
    className = ''
}: AnimatedOnScrollProps) {
    const ref = useRef(null);
    const isInView = useInView(ref, { once: true, margin: "-100px" });

    const variants = {
        fade: {
            hidden: { opacity: 0 },
            visible: { opacity: 1 }
        },
        'slide-up': {
            hidden: { opacity: 0, y: 50 },
            visible: { opacity: 1, y: 0 }
        },
        'slide-left': {
            hidden: { opacity: 0, x: 50 },
            visible: { opacity: 1, x: 0 }
        },
        'slide-right': {
            hidden: { opacity: 0, x: -50 },
            visible: { opacity: 1, x: 0 }
        },
        zoom: {
            hidden: { opacity: 0, scale: 0.8 },
            visible: { opacity: 1, scale: 1 }
        }
    };

    return (
        <motion.div
            ref={ref}
            initial="hidden"
            animate={isInView ? "visible" : "hidden"}
            variants={variants[animation]}
            transition={{
                duration: 0.5,
                delay,
                ease: "easeOut"
            }}
            className={className}
        >
            {children}
        </motion.div>
    );
}

interface AnimatedListProps {
    children: ReactNode;
    className?: string;
}

export function AnimatedList({ children, className = '' }: AnimatedListProps) {
    const ref = useRef(null);
    const isInView = useInView(ref, { once: true });

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                staggerChildren: 0.1
            }
        }
    };

    return (
        <motion.div
            ref={ref}
            initial="hidden"
            animate={isInView ? "visible" : "hidden"}
            variants={containerVariants}
            className={className}
        >
            {children}
        </motion.div>
    );
}
