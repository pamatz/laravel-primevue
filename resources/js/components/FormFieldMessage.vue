<script setup lang="ts">
import { Message } from 'primevue';
import { computed } from 'vue';

interface FieldError {
    message?: string;
}

interface FormFieldState {
    invalid?: boolean;
    error?: FieldError | null;
}

// Contrato de este componente:
// - Prioriza siempre field.error.message sobre el prop error cuando ambos existen.
// - Soporta error como string o como arreglo de strings (usa el primer elemento del arreglo).
// - Muestra el mensaje si el campo es inválido (invalid) o si hay cualquier mensaje de error calculado.
// - No debe renderizar mensajes vacíos cuando no hay errores disponibles.
// Estos comportamientos están protegidos por tests en resources/js/components/__tests__/FormFieldMessage.spec.ts.
const props = defineProps<{
    field?: FormFieldState | null;
    error?: string | string[] | null;
}>();

const errorMessage = computed(() => {
    if (props.field?.error?.message) {
        return props.field.error.message;
    }

    if (Array.isArray(props.error)) {
        return props.error[0] ?? '';
    }

    return props.error ?? '';
});

const show = computed(() => {
    return Boolean(props.field?.invalid) || Boolean(errorMessage.value);
});
</script>

<template>
    <Message
        v-if="show && errorMessage"
        severity="error"
        size="small"
        variant="simple"
    >
        {{ errorMessage }}
    </Message>
</template>
