<script lang="ts" setup>
import { logout } from '@/routes';
import type { AppPageProps, BreadcrumbItemType } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import Avatar from 'primevue/avatar';
import Drawer from 'primevue/drawer';
import Menu from 'primevue/menu';
import type { MenuItem } from 'primevue/menuitem';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

interface Props {
    title?: string;
    breadcrumbs?: BreadcrumbItemType[];
}

const props = withDefaults(defineProps<Props>(), {
    title: '',
    breadcrumbs: () => [],
});

const page = usePage<AppPageProps>();

const navigation = computed(() => page.props.navigation ?? []);

const sidebarOpen = ref(page.props.sidebarOpen);
const isDesktop = ref(false);

if (typeof window !== 'undefined') {
    const updateIsDesktop = (): void => {
        isDesktop.value = window.matchMedia('(min-width: 768px)').matches;
    };

    onMounted(() => {
        updateIsDesktop();
        window.addEventListener('resize', updateIsDesktop);
    });

    onBeforeUnmount(() => {
        window.removeEventListener('resize', updateIsDesktop);
    });
}

const appName = computed(() => page.props.name ?? 'Dashboard');

const user = computed(() => page.props.auth.user);

const userInitials = computed(() => {
    if (!user.value?.name) {
        return '?';
    }

    const parts = user.value.name.trim().split(' ');

    if (parts.length === 1) {
        return parts[0][0]?.toUpperCase() ?? '?';
    }

    return `${parts[0][0] ?? ''}${parts[parts.length - 1][0] ?? ''}`.toUpperCase();
});

const breadcrumbItems = computed(() => props.breadcrumbs);

const currentUrl = computed(() => page.url);

const isActive = (path: string): boolean => {
    const url = currentUrl.value || '';

    return url === path || url.startsWith(`${path}/`);
};

const userMenu = ref();

const userMenuItems = computed<MenuItem[]>(() => [
    {
        label: 'Perfil',
        icon: 'pi pi-user',
        command: () => router.visit('/settings/profile'),
    },
    {
        label: 'Apariencia',
        icon: 'pi pi-palette',
        command: () => router.visit('/settings/appearance'),
    },
    {
        separator: true,
    },
    {
        label: 'Cerrar sesión',
        icon: 'pi pi-sign-out',
        command: () => router.post(logout().url),
    },
]);

const setSidebarCookie = (isOpen: boolean): void => {
    if (typeof document === 'undefined') {
        return;
    }

    const value = isOpen ? 'true' : 'false';
    const maxAge = 60 * 60 * 24 * 365; // 1 año

    document.cookie = `sidebar_state=${value};path=/;max-age=${maxAge}`;
};

const toggleSidebar = (): void => {
    sidebarOpen.value = !sidebarOpen.value;
};

watch(sidebarOpen, (value) => {
    setSidebarCookie(value);
});

const toggleUserMenu = (event: MouseEvent): void => {
    const menu = userMenu.value as any;

    if (menu && typeof menu.toggle === 'function') {
        menu.toggle(event);
    }
};
</script>

<template>
    <div
        class="flex min-h-screen bg-neutral-50 text-neutral-900 dark:bg-neutral-900 dark:text-neutral-50"
    >
        <!-- Drawer de navegación (solo móvil / tablet) -->
        <Drawer
            v-if="!isDesktop"
            v-model:visible="sidebarOpen"
            :modal="true"
            :showCloseIcon="true"
            position="left"
        >
            <div
                class="flex items-center gap-2 border-b border-neutral-200 px-4 py-4 dark:border-neutral-800"
            >
                <span
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-primary-600 text-lg font-semibold text-white uppercase"
                >
                    {{ appName.charAt(0) }}
                </span>
                <span
                    class="truncate text-sm font-semibold text-neutral-900 dark:text-neutral-50"
                >
                    {{ appName }}
                </span>
            </div>

            <nav class="mt-4 space-y-4 px-2 text-sm font-medium">
                <div v-for="section in navigation" :key="section.label">
                    <p
                        class="mb-2 px-3 text-[11px] font-semibold tracking-wide text-neutral-500 uppercase dark:text-neutral-400"
                    >
                        {{ section.label }}
                    </p>
                    <Link
                        v-for="item in section.items"
                        :key="item.href"
                        :href="item.href"
                        :class="[
                            'flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-neutral-100 dark:hover:bg-neutral-800',
                            isActive(item.href)
                                ? 'bg-neutral-100 text-neutral-900 dark:bg-neutral-800 dark:text-neutral-50'
                                : 'text-neutral-700 dark:text-neutral-200',
                        ]"
                    >
                        <i class="text-base" :class="item.icon"></i>
                        <span>{{ item.label }}</span>
                    </Link>
                </div>
            </nav>
        </Drawer>

        <!-- Sidebar de navegación principal, de color y colapsable (solo desktop) -->
        <aside
            v-else
            :class="[
                'flex flex-col bg-primary-600 text-white shadow-lg transition-[width] duration-200',
                sidebarOpen ? 'w-64' : 'w-20',
            ]"
        >
            <div class="flex items-center gap-2 px-4 py-4">
                <span
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-lg font-semibold uppercase"
                >
                    {{ appName.charAt(0) }}
                </span>
                <span v-if="sidebarOpen" class="truncate text-sm font-semibold">
                    {{ appName }}
                </span>
            </div>

            <nav class="mt-4 flex-1 space-y-4 px-2 text-xs font-medium">
                <div v-for="section in navigation" :key="section.label">
                    <p
                        v-if="sidebarOpen"
                        class="mb-2 px-3 text-[11px] font-semibold tracking-wide text-primary-100/80 uppercase"
                    >
                        {{ section.label }}
                    </p>
                    <Link
                        v-for="item in section.items"
                        :key="item.href"
                        :href="item.href"
                        :title="item.label"
                        :class="[
                            'flex items-center rounded-lg px-3 py-2 hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-white/60 focus-visible:outline-none',
                            sidebarOpen ? 'justify-start gap-3' : 'justify-center',
                            isActive(item.href)
                                ? 'bg-white/10 text-white'
                                : 'text-primary-50/80',
                        ]"
                    >
                        <i class="text-base" :class="item.icon"></i>
                        <span v-if="sidebarOpen">{{ item.label }}</span>
                    </Link>
                </div>
            </nav>
        </aside>

        <!-- Contenido principal -->
        <div class="flex min-h-screen flex-1 flex-col">
            <!-- Navbar -->
            <header
                class="sticky top-0 z-20 flex items-center justify-between gap-4 border-b border-neutral-200 bg-white/80 px-4 py-3 backdrop-blur-md dark:border-neutral-800 dark:bg-neutral-900/80"
            >
                <div class="flex flex-1 items-center gap-3">
                    <Button
                        class="!h-9 !w-9"
                        icon="pi pi-bars"
                        rounded
                        text
                        type="button"
                        @click="toggleSidebar"
                    />
                    <div class="flex flex-col">
                        <p class="text-sm font-semibold">
                            {{ props.title || 'Dashboard' }}
                        </p>
                        <nav
                            v-if="breadcrumbItems.length"
                            aria-label="Breadcrumb"
                            class="mt-0.5 hidden items-center gap-1 text-xs text-neutral-500 md:flex dark:text-neutral-400"
                        >
                            <Link
                                class="flex items-center gap-1 hover:text-neutral-800 dark:hover:text-neutral-100"
                                href="/dashboard"
                            >
                                <i class="pi pi-home text-xs" />
                                <span>Inicio</span>
                            </Link>
                            <span>/</span>
                            <template
                                v-for="(item, index) in breadcrumbItems"
                                :key="item.title + index"
                            >
                                <Link
                                    v-if="index < breadcrumbItems.length - 1"
                                    :href="item.href"
                                    class="hover:text-neutral-800 dark:hover:text-neutral-100"
                                >
                                    {{ item.title }}
                                </Link>
                                <span
                                    v-else
                                    class="font-medium text-neutral-700 dark:text-neutral-100"
                                >
                                    {{ item.title }}
                                </span>
                                <span
                                    v-if="index < breadcrumbItems.length - 1"
                                    class="mx-1"
                                    >/</span
                                >
                            </template>
                        </nav>
                    </div>
                </div>

                <!-- Menú de usuario con avatar -->
                <div class="flex items-center gap-2">
                    <Menu ref="userMenu" :model="userMenuItems" popup />
                    <Button
                        class="flex items-center gap-2 !px-2"
                        rounded
                        text
                        type="button"
                        @click="toggleUserMenu"
                    >
                        <Avatar
                            :label="userInitials"
                            class="!bg-neutral-200 !text-xs dark:!bg-neutral-700"
                            shape="circle"
                        />
                        <span
                            class="hidden text-sm font-medium md:inline-block"
                        >
                            {{ user?.name }}
                        </span>
                        <i
                            class="pi pi-chevron-down text-xs text-neutral-500"
                        />
                    </Button>
                </div>
            </header>

            <!-- Breadcrumb para móvil -->
            <div
                v-if="breadcrumbItems.length"
                class="border-b border-neutral-200 bg-white px-4 py-2 text-xs text-neutral-500 md:hidden dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400"
            >
                <nav aria-label="Breadcrumb" class="flex items-center gap-1">
                    <Link
                        class="flex items-center gap-1 hover:text-neutral-800 dark:hover:text-neutral-100"
                        href="/dashboard"
                    >
                        <i class="pi pi-home text-xs" />
                        <span>Inicio</span>
                    </Link>
                    <span>/</span>
                    <template
                        v-for="(item, index) in breadcrumbItems"
                        :key="item.title + index"
                    >
                        <span v-if="index < breadcrumbItems.length - 1">
                            {{ item.title }}
                            <span
                                v-if="index < breadcrumbItems.length - 1"
                                class="mx-1"
                                >/</span
                            >
                        </span>
                        <span
                            v-else
                            class="font-medium text-neutral-700 dark:text-neutral-100"
                        >
                            {{ item.title }}
                        </span>
                    </template>
                </nav>
            </div>

            <main class="flex-1 bg-neutral-50 px-4 py-6 dark:bg-neutral-950">
                <slot />
            </main>

            <footer
                class="border-t border-neutral-200 bg-white px-4 py-3 text-xs text-neutral-500 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-400"
            >
                <div
                    class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"
                >
                    <span
                        >&copy; {{ new Date().getFullYear() }} {{ appName }}.
                        Todos los derechos reservados.</span
                    >
                    <span class="text-[11px]"
                        >Dashboard construido con PrimeVue, Inertia y Tailwind
                        CSS.</span
                    >
                </div>
            </footer>
        </div>
    </div>
</template>
