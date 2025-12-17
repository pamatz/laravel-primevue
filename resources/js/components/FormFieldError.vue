<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    /**
     * Nombre del campo tal como lo expone PrimeVue Forms ($form.nombreCampo)
     * y las validaciones del backend (errors.nombreCampo).
     */
    field: string;
    /** Estado del formulario de PrimeVue Forms (el objeto $form del slot). */
    form?: Record<string, any>;
    /** Errores provenientes del backend (Inertia). */
    errors?: Record<string, string>;
    /** Clases extra para personalizar el estilo del mensaje. */
    extraClass?: string;
}

const props = defineProps<Props>();

const fieldState = computed(() => props.form?.[props.field]);

const message = computed<string | undefined>(() => {
    // Error de PrimeVue Forms (cliente)
    if (fieldState.value?.invalid) {
        const state = fieldState.value;
        const fromArray = Array.isArray(state.errors)
            ? state.errors[0]?.message
            : undefined;

        return (
            fromArray ?? state.error?.message ?? state.error ?? undefined
        );
    }

    // Error del servidor (Inertia)
    return props.errors?.[props.field];
});
</script>

<template>
    <small
        v-if="message"
        :class="[
            'text-xs text-red-600 dark:text-red-400',
            extraClass,
        ]"
    >
        {{ message }}
    </small>
</template>
