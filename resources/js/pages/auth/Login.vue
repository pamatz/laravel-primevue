<script lang="ts" setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import FormFieldMessage from '@/components/FormFieldMessage.vue';
import { store } from '@/routes/login';
import { loginResolver } from '@/validation/login';
import { Head, useForm } from '@inertiajs/vue3';
import { Form } from '@primevue/forms';

const props = defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const initialValues = {
    email: '',
    password: '',
    remember: false,
};

// Inertia.js useForm para manejar envío y errores del backend
const form = useForm({ ...initialValues });

const onFormSubmit = ({ valid }) => {
    if (valid) {
        form.submit(store(), {
            onFinish: () => form.reset('password'),
        });
    }
};
</script>

<template>
    <AuthLayout
        description="Ingresa tus credenciales para acceder a tu cuenta."
        title="Inicia sesión"
    >
        <Head title="Login" />

        <div v-if="props.status" class="mb-4">
            <Message :life="5000" class="w-full" severity="success">
                {{ props.status }}
            </Message>
        </div>

        <Form
            v-slot="$form"
            :initialValues="initialValues"
            :resolver="loginResolver"
            class="flex flex-col gap-4"
            @submit="onFormSubmit"
        >
            <div class="flex flex-col gap-2">
                <label
                    class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                    for="email"
                >
                    Correo electrónico
                </label>
                <InputText
                    id="email"
                    v-model="form.email"
                    autocomplete="email"
                    autofocus
                    class="w-full"
                    name="email"
                    type="email"
                />
                <FormFieldMessage
                    :field="$form.email"
                    :error="form.errors.email"
                />
            </div>

            <div class="flex flex-col gap-2">
                <label
                    class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                    for="password"
                >
                    Contraseña
                </label>
                <Password
                    id="password"
                    v-model="form.password"
                    :feedback="false"
                    autocomplete="current-password"
                    class="w-full"
                    inputClass="w-full"
                    name="password"
                    toggleMask
                />
                <FormFieldMessage
                    :field="$form.password"
                    :error="form.errors.password"
                />
            </div>

            <div
                class="flex items-center justify-between gap-4 text-sm text-neutral-700 dark:text-neutral-300"
            >
                <div class="flex items-center gap-2">
                    <Checkbox
                        id="remember"
                        v-model="form.remember"
                        binary
                        inputId="remember"
                        name="remember"
                    />
                    <label for="remember">Recordarme</label>
                </div>

                <a
                    v-if="props.canResetPassword"
                    class="text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline"
                    href="/forgot-password"
                >
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <Button
                :disabled="form.processing"
                :loading="form.processing"
                class="mt-2 w-full"
                label="Iniciar sesión"
                type="submit"
            />

            <p
                v-if="props.canRegister"
                class="mt-4 text-center text-sm text-neutral-700 dark:text-neutral-300"
            >
                ¿No tienes una cuenta?
                <a
                    class="text-primary-600 dark:text-primary-400 font-medium hover:underline"
                    href="/register"
                >
                    Crear una cuenta
                </a>
            </p>
        </Form>
    </AuthLayout>
</template>
