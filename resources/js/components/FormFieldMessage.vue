<script setup lang="ts">
import { computed } from 'vue';

interface FieldError {
    message?: string;
}

interface FormFieldState {
    invalid?: boolean;
    error?: FieldError | null;
}

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
