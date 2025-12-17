<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';

const breadcrumbs: BreadcrumbItemType[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <AppLayout title="Dashboard" :breadcrumbs="breadcrumbs">
        <Head title="Dashboard" />

        <section class="space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <template #title>Resumen de ventas</template>
                    <template #content>
                        <p class="text-sm text-neutral-600 dark:text-neutral-300">
                            Total de ventas del mes actual.
                        </p>
                        <p class="mt-3 text-3xl font-semibold">$24,500</p>
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                            +12% respecto al mes anterior
                        </p>
                    </template>
                </Card>

                <Card>
                    <template #title>Nuevos clientes</template>
                    <template #content>
                        <p class="text-sm text-neutral-600 dark:text-neutral-300">
                            Clientes que se registraron esta semana.
                        </p>
                        <p class="mt-3 text-3xl font-semibold">128</p>
                        <p class="mt-1 text-xs text-emerald-600 dark:text-emerald-400">
                            +8 desde ayer
                        </p>
                    </template>
                </Card>

                <Card>
                    <template #title>Tickets abiertos</template>
                    <template #content>
                        <p class="text-sm text-neutral-600 dark:text-neutral-300">
                            Soportes pendientes de resolución.
                        </p>
                        <p class="mt-3 text-3xl font-semibold">32</p>
                        <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                            5 con prioridad alta
                        </p>
                    </template>
                </Card>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <Card class="lg:col-span-2">
                    <template #title>Actividad reciente</template>
                    <template #content>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium">Pago recibido</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                        Factura #INV-2025-001 de Juan Pérez
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300"
                                >
                                    Completado
                                </span>
                            </li>
                            <li class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium">Nuevo usuario registrado</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                        ana.garcia@example.com
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"
                                >
                                    Nuevo
                                </span>
                            </li>
                            <li class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-medium">Ticket actualizado</p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                        Soporte para acceso al portal de proveedores
                                    </p>
                                </div>
                                <span
                                    class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"
                                >
                                    En progreso
                                </span>
                            </li>
                        </ul>
                    </template>
                </Card>

                <Card>
                    <template #title>Acciones rápidas</template>
                    <template #content>
                        <div class="flex flex-col gap-2">
                            <Button
                                label="Crear reporte"
                                icon="pi pi-chart-line"
                                class="w-full justify-start"
                            />
                            <Button
                                label="Agregar usuario"
                                icon="pi pi-user-plus"
                                class="w-full justify-start"
                                severity="secondary"
                            />
                            <Button
                                label="Ver tickets"
                                icon="pi pi-inbox"
                                class="w-full justify-start"
                                severity="secondary"
                                outlined
                            />
                        </div>
                    </template>
                </Card>
            </div>
        </section>
    </AppLayout>
</template>
