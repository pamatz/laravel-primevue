<script setup lang="ts">
import { Card } from 'primevue';
interface Props {
    title: string;
    description?: string;
}

const props = defineProps<Props>();
</script>

<template>
    <div
        class="min-h-screen flex items-center justify-center bg-neutral-50 px-4 py-8 dark:bg-neutral-900"
    >
        <div class="w-full max-w-md">
            <Card class="shadow-md">
                <template #title>
                    <h1 class="text-2xl font-semibold text-neutral-900 dark:text-neutral-50">
                        {{ props.title }}
                    </h1>
                </template>

                <template v-if="props.description" #subtitle>
                    <p
                        class="mt-1 text-sm text-neutral-600 dark:text-neutral-300"
                    >
                        {{ props.description }}
                    </p>
                </template>

                <template #content>
                    <slot />
                </template>
            </Card>
        </div>
    </div>
</template>
