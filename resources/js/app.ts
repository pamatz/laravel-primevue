import '../css/app.css';

import { useLayout } from '@/composables/useLayout';
import { createInertiaApp } from '@inertiajs/vue3';
import Material from '@primeuix/themes/material';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import PrimeVue from 'primevue/config';
import StyleClass from 'primevue/styleclass';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';

const { updateColors } = useLayout();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.directive('styleclass', StyleClass);
        app.use(PrimeVue, {
            theme: {
                preset: Material,
                options: {
                    darkModeSelector: '.p-dark',
                },
            },
        });
        updateColors('primary','sky');
        app.mount(el);
    },
});
