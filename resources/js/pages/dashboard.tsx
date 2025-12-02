import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    Users,
    TrendingUp,
    Activity,
    Zap,
    ArrowUpRight,
    ArrowDownRight,
    Sparkles,
    Clock,
    CheckCircle2,
    Target
} from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { AnimatedOnScroll } from '@/components/ui/animated-wrapper';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface DashboardProps {
    stats: {
        users: number;
        projects: number;
        tasks: number;
        completed_tasks: number;
    };
    recent_projects: Array<{
        id: number;
        nombre: string;
        descripcion: string;
        created_at: string;
        estado: string;
    }>;
}

export default function Dashboard({ stats: serverStats, recent_projects }: DashboardProps) {
    const stats = [
        {
            title: 'Usuarios Totales',
            value: serverStats.users.toString(),
            change: '+12.5%', // Placeholder for now
            trend: 'up',
            icon: Users,
            color: 'from-purple-500 to-pink-500',
            bgColor: 'bg-purple-500/10',
        },
        {
            title: 'Proyectos Activos',
            value: serverStats.projects.toString(),
            change: '+2.4%',
            trend: 'up',
            icon: TrendingUp,
            color: 'from-blue-500 to-cyan-500',
            bgColor: 'bg-blue-500/10',
        },
        {
            title: 'Tareas Totales',
            value: serverStats.tasks.toString(),
            change: '-3.1%',
            trend: 'down',
            icon: Activity,
            color: 'from-green-500 to-emerald-500',
            bgColor: 'bg-green-500/10',
        },
        {
            title: 'Tareas Completadas',
            value: serverStats.completed_tasks.toString(),
            change: '+8.3%',
            trend: 'up',
            icon: CheckCircle2, // Changed icon to match context
            color: 'from-amber-500 to-orange-500',
            bgColor: 'bg-amber-500/10',
        },
    ];

    const recentActivities = recent_projects.map(project => ({
        title: project.nombre,
        description: project.descripcion || 'Sin descripción',
        time: new Date(project.created_at).toLocaleDateString(),
        icon: Target, // Using Target icon for projects
        iconBg: 'bg-blue-500/10',
        iconColor: 'text-blue-600 dark:text-blue-400',
    }));

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />

            <div className="flex flex-col gap-6 p-6">
                {/* Welcome Section */}
                <AnimatedOnScroll animation="slide-up">
                    <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-900 p-8 border border-border/50">
                        <div className="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl" />
                        <div className="absolute bottom-0 left-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl" />

                        <div className="relative z-10">
                            <div className="flex items-center gap-3 mb-3">
                                <Sparkles className="w-8 h-8 text-blue-600 dark:text-blue-400" />
                                <h1 className="text-3xl font-bold gradient-text">
                                    Bienvenido de vuelta!
                                </h1>
                            </div>
                            <p className="text-gray-700 dark:text-gray-300 text-lg">
                                Aquí tienes un resumen de tu actividad y el rendimiento del sistema.
                            </p>
                        </div>
                    </div>
                </AnimatedOnScroll>

                {/* Stats Grid */}
                <motion.div
                    variants={containerVariants}
                    initial="hidden"
                    animate="visible"
                    className="grid gap-4 md:grid-cols-2 lg:grid-cols-4"
                >
                    {stats.map((stat, index) => (
                        <motion.div key={index} variants={itemVariants}>
                            <Card className="overflow-hidden hover-lift">
                                <CardContent className="p-6">
                                    <div className="flex items-start justify-between">
                                        <div className="flex-1">
                                            <p className="text-sm font-medium text-muted-foreground mb-1">
                                                {stat.title}
                                            </p>
                                            <div className="flex items-baseline gap-2">
                                                <h3 className="text-3xl font-bold text-gray-900 dark:text-white">
                                                    {stat.value}
                                                </h3>
                                                <span
                                                    className={`text-sm font-semibold flex items-center gap-1 ${stat.trend === 'up'
                                                            ? 'text-green-600 dark:text-green-400'
                                                            : 'text-red-600 dark:text-red-400'
                                                        }`}
                                                >
                                                    {stat.trend === 'up' ? (
                                                        <ArrowUpRight className="w-4 h-4" />
                                                    ) : (
                                                        <ArrowDownRight className="w-4 h-4" />
                                                    )}
                                                    {stat.change}
                                                </span>
                                            </div>
                                        </div>
                                        <div
                                            className={`w-12 h-12 rounded-xl ${stat.bgColor} flex items-center justify-center`}
                                        >
                                            <stat.icon className="w-6 h-6 text-gray-700 dark:text-gray-300" />
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </motion.div>
                    ))}
                </motion.div>

                {/* Main Content Grid */}
                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Activity Chart Card */}
                    <AnimatedOnScroll animation="slide-up" className="lg:col-span-2">
                        <Card className="h-full">
                            <CardHeader>
                                <CardTitle className="text-gray-900 dark:text-white">Actividad Reciente</CardTitle>
                                <CardDescription>
                                    Métricas de rendimiento de los últimos 7 días
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="h-[300px] flex items-center justify-center relative overflow-hidden rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900">
                                    {/* Simulated Chart */}
                                    <div className="absolute inset-0 flex items-end justify-around p-6 gap-2">
                                        {[65, 45, 75, 55, 85, 70, 90].map((height, i) => (
                                            <motion.div
                                                key={i}
                                                initial={{ height: 0 }}
                                                animate={{ height: `${height}%` }}
                                                transition={{ duration: 0.5, delay: i * 0.1 }}
                                                className="flex-1 bg-gradient-primary rounded-t-lg hover:opacity-80 transition-opacity cursor-pointer"
                                            />
                                        ))}
                                    </div>
                                    <div className="relative z-10 text-center">
                                        <Activity className="w-16 h-16 text-blue-500/50 mb-2 mx-auto" />
                                        <p className="text-sm text-muted-foreground">
                                            Gráfico de actividad
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </AnimatedOnScroll>

                    {/* Recent Activities Card */}
                    <AnimatedOnScroll animation="slide-left" delay={0.2}>
                        <Card className="h-full">
                            <CardHeader>
                                <CardTitle className="text-gray-900 dark:text-white">Actividad Reciente</CardTitle>
                                <CardDescription>
                                    Últimas actualizaciones del sistema
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    {recentActivities.map((activity, index) => (
                                        <motion.div
                                            key={index}
                                            initial={{ opacity: 0, x: -20 }}
                                            animate={{ opacity: 1, x: 0 }}
                                            transition={{ duration: 0.5, delay: index * 0.1 }}
                                            className="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer hover-scale-sm"
                                        >
                                            <div
                                                className={`w-10 h-10 rounded-xl ${activity.iconBg} flex items-center justify-center flex-shrink-0`}
                                            >
                                                <activity.icon className={`w-5 h-5 ${activity.iconColor}`} />
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <p className="text-sm font-semibold truncate text-gray-900 dark:text-white">
                                                    {activity.title}
                                                </p>
                                                <p className="text-xs text-muted-foreground truncate">
                                                    {activity.description}
                                                </p>
                                                <div className="flex items-center gap-1 mt-1">
                                                    <Clock className="w-3 h-3 text-muted-foreground" />
                                                    <span className="text-xs text-muted-foreground">
                                                        {activity.time}
                                                    </span>
                                                </div>
                                            </div>
                                        </motion.div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    </AnimatedOnScroll>
                </div>

                {/* Additional Cards */}
                <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <AnimatedOnScroll animation="slide-up">
                        <Card variant="glass" className="h-full">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-gray-900 dark:text-white">
                                    <Zap className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    Rendimiento
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-4">
                                    <div>
                                        <div className="flex justify-between text-sm mb-2">
                                            <span className="text-muted-foreground">CPU</span>
                                            <span className="font-semibold text-gray-900 dark:text-white">45%</span>
                                        </div>
                                        <div className="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <motion.div
                                                initial={{ width: 0 }}
                                                animate={{ width: '45%' }}
                                                transition={{ duration: 1, delay: 0.5 }}
                                                className="h-full bg-gradient-primary"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <div className="flex justify-between text-sm mb-2">
                                            <span className="text-muted-foreground">Memoria</span>
                                            <span className="font-semibold text-gray-900 dark:text-white">67%</span>
                                        </div>
                                        <div className="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <motion.div
                                                initial={{ width: 0 }}
                                                animate={{ width: '67%' }}
                                                transition={{ duration: 1, delay: 0.6 }}
                                                className="h-full bg-gradient-secondary"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <div className="flex justify-between text-sm mb-2">
                                            <span className="text-muted-foreground">Almacenamiento</span>
                                            <span className="font-semibold text-gray-900 dark:text-white">32%</span>
                                        </div>
                                        <div className="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <motion.div
                                                initial={{ width: 0 }}
                                                animate={{ width: '32%' }}
                                                transition={{ duration: 1, delay: 0.7 }}
                                                className="h-full bg-gradient-accent"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </AnimatedOnScroll>

                    <AnimatedOnScroll animation="slide-up" delay={0.1}>
                        <Card variant="gradient" className="h-full">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Target className="w-5 h-5 text-white" />
                                    Objetivos
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-white/90">Usuarios mensuales</span>
                                        <span className="text-sm font-semibold text-white">85%</span>
                                    </div>
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-white/90">Ventas totales</span>
                                        <span className="text-sm font-semibold text-white">72%</span>
                                    </div>
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-white/90">Satisfacción</span>
                                        <span className="text-sm font-semibold text-white">94%</span>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </AnimatedOnScroll>

                    <AnimatedOnScroll animation="slide-up" delay={0.2}>
                        <Card className="h-full">
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2 text-gray-900 dark:text-white">
                                    <Sparkles className="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    Estado del Sistema
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="space-y-3">
                                    {[
                                        { name: 'API', status: 'Operacional', color: 'bg-green-500' },
                                        { name: 'Base de Datos', status: 'Operacional', color: 'bg-green-500' },
                                        { name: 'Cache', status: 'Operacional', color: 'bg-green-500' },
                                    ].map((service, i) => (
                                        <div key={i} className="flex items-center justify-between">
                                            <div className="flex items-center gap-2">
                                                <div className={`w-2 h-2 rounded-full ${service.color}`} />
                                                <span className="text-sm text-gray-900 dark:text-white">{service.name}</span>
                                            </div>
                                            <span className="text-xs text-muted-foreground">
                                                {service.status}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            </CardContent>
                        </Card>
                    </AnimatedOnScroll>
                </div>
            </div>
        </AppLayout>
    );
}
