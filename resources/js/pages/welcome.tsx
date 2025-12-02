import { dashboard, login, register } from '@/routes';
import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    ArrowRight,
    Shield,
    Zap,
    Layers,
    Sparkles,
    CheckCircle2,
    Layout,
    Palette,
    Users
} from 'lucide-react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    const features = [
        {
            icon: Layers,
            title: 'Tablero Kanban',
            description: 'Organiza tus tareas con un sistema de arrastrar y soltar intuitivo y visual.'
        },
        {
            icon: Users,
            title: 'Gestión de Equipos',
            description: 'Asigna tareas, colabora con tu equipo y mantén el control total del proyecto.'
        },
        {
            icon: Layout,
            title: 'Dashboard en Tiempo Real',
            description: 'Visualiza el progreso de tus proyectos con métricas y estadísticas actualizadas.'
        },
        {
            icon: Shield,
            title: 'Seguridad Avanzada',
            description: 'Protección con autenticación 2FA y gestión completa de permisos por rol.'
        },
        {
            icon: Palette,
            title: 'Interfaz Personalizable',
            description: 'Temas claro/oscuro con diseño moderno y adaptable a cualquier dispositivo.'
        },
        {
            icon: Zap,
            title: 'Rápido y Eficiente',
            description: 'Tecnología moderna que garantiza velocidad y fluidez en cada interacción.'
        }
    ];

    const techStack = [
        { name: 'Laravel 12', color: 'from-red-500 to-orange-500' },
        { name: 'React 19', color: 'from-blue-500 to-cyan-500' },
        { name: 'TypeScript', color: 'from-blue-600 to-blue-400' },
        { name: 'Tailwind CSS', color: 'from-teal-500 to-emerald-500' },
        { name: 'Inertia.js', color: 'from-purple-500 to-pink-500' },
        { name: 'Filament', color: 'from-amber-500 to-yellow-500' }
    ];

    const containerVariants = {
        hidden: { opacity: 0 },
        visible: {
            opacity: 1,
            transition: {
                staggerChildren: 0.1
            }
        }
    };

    const itemVariants = {
        hidden: { opacity: 0, y: 20 },
        visible: {
            opacity: 1,
            y: 0,
            transition: {
                duration: 0.5
            }
        }
    };

    return (
        <>
            <Head title="Welcome to Atom-Inovatec" />

            <div className="relative min-h-screen overflow-hidden bg-white dark:bg-black selection:bg-red-500 selection:text-white">
                {/* Main Gradient Background */}
                <div className="absolute inset-0 bg-gradient-to-br from-blue-50 via-indigo-50/50 to-white dark:from-gray-900 dark:via-[#0f172a] dark:to-black" />

                {/* Vibrant Mesh Gradient - Increased Opacity for Dark Mode */}
                <div className="absolute inset-0 bg-mesh-gradient opacity-40 dark:opacity-40 mix-blend-multiply dark:mix-blend-screen" />

                {/* Floating Orbs - Enhanced Visibility */}
                <motion.div
                    className="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-500/30 rounded-full blur-[100px] dark:bg-blue-600/20"
                    animate={{
                        scale: [1, 1.2, 1],
                        opacity: [0.3, 0.6, 0.3],
                        x: [0, 50, 0],
                        y: [0, 30, 0],
                    }}
                    transition={{
                        duration: 15,
                        repeat: Infinity,
                        ease: "easeInOut"
                    }}
                />
                <motion.div
                    className="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-purple-500/30 rounded-full blur-[100px] dark:bg-purple-600/20"
                    animate={{
                        scale: [1.2, 1, 1.2],
                        opacity: [0.3, 0.6, 0.3],
                        x: [0, -50, 0],
                        y: [0, -30, 0],
                    }}
                    transition={{
                        duration: 18,
                        repeat: Infinity,
                        ease: "easeInOut"
                    }}
                />
                <motion.div
                    className="absolute top-[40%] left-[50%] transform -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-500/10 rounded-full blur-[120px] dark:bg-indigo-500/10"
                    animate={{
                        scale: [1, 1.1, 1],
                        opacity: [0.1, 0.3, 0.1],
                    }}
                    transition={{
                        duration: 20,
                        repeat: Infinity,
                        ease: "easeInOut"
                    }}
                />

                {/* Navigation */}
                <nav className="relative z-10 container mx-auto px-6 py-6">
                    <div className="flex items-center justify-between">
                        <motion.div
                            initial={{ opacity: 0, x: -20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.5 }}
                            className="flex items-center gap-3"
                        >
                            <div className="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-primary">
                                <Sparkles className="w-6 h-6 text-white" />
                            </div>
                            <span className="text-2xl font-bold gradient-text">
                                Atom-Inovatec
                            </span>
                        </motion.div>

                        <motion.div
                            initial={{ opacity: 0, x: 20 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.5 }}
                            className="flex items-center gap-4"
                        >
                            {auth.user ? (
                                <Link
                                    href={dashboard().url}
                                    className="px-6 py-2.5 rounded-xl bg-gradient-primary text-white font-medium hover-scale-sm transition-smooth flex items-center gap-2 shadow-lg hover:shadow-xl"
                                >
                                    Dashboard
                                    <ArrowRight className="w-4 h-4" />
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={login().url}
                                        className="px-6 py-2.5 rounded-xl glass-light font-medium hover-scale-sm transition-smooth text-gray-800 dark:text-gray-200"
                                    >
                                        Iniciar Sesión
                                    </Link>
                                    <Link
                                        href={register().url}
                                        className="px-6 py-2.5 rounded-xl bg-gradient-primary text-white font-medium hover-scale-sm transition-smooth flex items-center gap-2 shadow-lg hover:shadow-xl glow-primary"
                                    >
                                        Comenzar
                                        <ArrowRight className="w-4 h-4" />
                                    </Link>
                                </>
                            )}
                        </motion.div>
                    </div>
                </nav>

                {/* Hero Section */}
                <section className="relative z-10 container mx-auto px-6 py-20 md:py-32">
                    <div className="max-w-5xl mx-auto text-center">
                        <motion.div
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.7 }}
                            className="mb-6"
                        >
                            <span className="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-light text-sm font-medium mb-6 text-gray-700 dark:text-gray-300">
                                <Sparkles className="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                Gestor de Proyectos con Kanban
                            </span>
                        </motion.div>

                        <motion.h1
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.7, delay: 0.1 }}
                            className="text-5xl md:text-7xl font-bold mb-6 leading-tight text-gray-900 dark:text-white"
                        >
                            Gestiona tus Proyectos{' '}
                            <span className="gradient-text">con Eficiencia</span>
                            <br />
                            y Visualiza tu Progreso
                        </motion.h1>

                        <motion.p
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.7, delay: 0.2 }}
                            className="text-xl md:text-2xl text-gray-600 dark:text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed"
                        >
                            Sistema completo de gestión de proyectos con tablero Kanban interactivo.
                            Organiza tareas, asigna responsables, visualiza el progreso en tiempo real
                            y lleva tus proyectos al siguiente nivel.
                        </motion.p>

                        <motion.div
                            initial={{ opacity: 0, y: 30 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.7, delay: 0.3 }}
                            className="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16"
                        >
                            <Link
                                href={auth.user ? dashboard().url : register().url}
                                className="px-8 py-4 rounded-xl bg-gradient-primary text-white font-semibold text-lg hover-lift transition-smooth flex items-center gap-2 shadow-2xl glow-primary"
                            >
                                {auth.user ? 'Ir al Dashboard' : 'Comenzar Gratis'}
                                <ArrowRight className="w-5 h-5" />
                            </Link>
                            <Link
                                href="#features"
                                className="px-8 py-4 rounded-xl glass-light font-semibold text-lg hover-scale-sm transition-smooth text-gray-800 dark:text-gray-200"
                            >
                                Ver Características
                            </Link>
                        </motion.div>

                        {/* Tech Stack Pills */}
                        <motion.div
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            transition={{ duration: 0.7, delay: 0.4 }}
                            className="flex flex-wrap items-center justify-center gap-3"
                        >
                            {techStack.map((tech, index) => (
                                <motion.span
                                    key={index}
                                    initial={{ opacity: 0, scale: 0.8 }}
                                    animate={{ opacity: 1, scale: 1 }}
                                    transition={{ duration: 0.3, delay: 0.5 + index * 0.1 }}
                                    className="px-4 py-2 rounded-full glass-light text-sm font-medium text-gray-700 dark:text-gray-300 hover-scale-sm transition-smooth cursor-default"
                                >
                                    {tech.name}
                                </motion.span>
                            ))}
                        </motion.div>
                    </div>
                </section>

                {/* Features Section */}
                <section id="features" className="relative z-10 container mx-auto px-6 py-20">
                    <div className="max-w-6xl mx-auto">
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true }}
                            transition={{ duration: 0.5 }}
                            className="text-center mb-16"
                        >
                            <h2 className="text-4xl md:text-5xl font-bold mb-4 text-gray-900 dark:text-white">
                                Características <span className="gradient-text">Principales</span>
                            </h2>
                            <p className="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                                Todo lo que necesitas para gestionar tus proyectos de manera efectiva
                            </p>
                        </motion.div>

                        <motion.div
                            variants={containerVariants}
                            initial="hidden"
                            whileInView="visible"
                            viewport={{ once: true }}
                            className="grid gap-8 md:grid-cols-2 lg:grid-cols-3"
                        >
                            {features.map((feature, index) => (
                                <motion.div
                                    key={index}
                                    variants={itemVariants}
                                    className="group p-8 rounded-2xl glass-light hover-lift transition-smooth border border-gray-200/50 dark:border-gray-700/50"
                                >
                                    <div className="flex items-center justify-center w-14 h-14 rounded-xl bg-gradient-primary mb-6 group-hover:scale-110 transition-transform">
                                        <feature.icon className="w-7 h-7 text-white" />
                                    </div>
                                    <h3 className="text-xl font-bold mb-3 text-gray-900 dark:text-white">
                                        {feature.title}
                                    </h3>
                                    <p className="text-gray-600 dark:text-gray-300 leading-relaxed">
                                        {feature.description}
                                    </p>
                                </motion.div>
                            ))}
                        </motion.div>
                    </div>
                </section>

                {/* CTA Section */}
                <section className="relative z-10 container mx-auto px-6 py-20 mb-20">
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95 }}
                        whileInView={{ opacity: 1, scale: 1 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.5 }}
                        className="max-w-4xl mx-auto relative overflow-hidden rounded-3xl bg-gradient-primary p-12 md:p-16 text-center"
                    >
                        <div className="absolute inset-0 bg-mesh-gradient opacity-30" />

                        <div className="relative z-10">
                            <div className="flex justify-center mb-6">
                                <CheckCircle2 className="w-16 h-16 text-white" />
                            </div>
                            <h2 className="text-4xl md:text-5xl font-bold mb-6 text-white">
                                ¿Listo para Comenzar?
                            </h2>
                            <p className="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                                Únete a miles de equipos que ya están gestionando sus proyectos de manera más eficiente
                            </p>
                            <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                                <Link
                                    href={register().url}
                                    className="px-8 py-4 rounded-xl bg-white text-blue-600 font-semibold text-lg hover-lift transition-smooth shadow-2xl"
                                >
                                    Crear Cuenta Gratis
                                </Link>
                                <Link
                                    href={login().url}
                                    className="px-8 py-4 rounded-xl bg-white/10 backdrop-blur-lg text-white font-semibold text-lg hover-scale-sm transition-smooth border-2 border-white/30"
                                >
                                    Iniciar Sesión
                                </Link>
                            </div>
                        </div>
                    </motion.div>
                </section>

                {/* Footer */}
                <footer className="relative z-10 border-t border-gray-200 dark:border-gray-800 py-8">
                    <div className="container mx-auto px-6">
                        <div className="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div className="flex items-center gap-2">
                                <Sparkles className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                <span className="font-bold gradient-text">Atom-Inovatec</span>
                            </div>
                            <p className="text-sm text-gray-600 dark:text-gray-400">
                                © 2024 Atom-Inovatec. Todos los derechos reservados.
                            </p>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
