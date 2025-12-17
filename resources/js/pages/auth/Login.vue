<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { login } from '@/routes';
import FormFieldError from '@/components/FormFieldError.vue';
import { computed, ref } from 'vue';
import { loginResolver } from '@/validation/login';

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

const isSubmitting = ref(false);

const page = usePage<any>();
const errors = computed<Record<string, string>>(
    () => (page.props?.errors as Record<string, string>) ?? {},
);

const onSubmit = (event: any): void => {
    const values = event?.values ?? event ?? {};

    isSubmitting.value = true;

    router.post(login(), values, {
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <AuthLayout
        title="Inicia sesión"
        description="Ingresa tus credenciales para acceder a tu cuenta."
    >
        <Head title="Login" />

        <div v-if="props.status" class="mb-4">
            <Message severity="success" :life="5000" class="w-full">
                {{ props.status }}
            </Message>
        </div>

        <PvForm
            v-slot="$form"
            :initialValues="initialValues"
            :resolver="loginResolver"
            class="flex flex-col gap-4"
            @submit="onSubmit"
        >
            <div class="flex flex-col gap-2">
                <label
                    for="email"
                    class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                >
                    Correo electrónico
                </label>
                <InputText
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    autofocus
                    class="w-full"
                />
                <FormFieldError
                    field="email"
                    :form="$form"
                    :errors="errors"
                />
            </div>

            <div class="flex flex-col gap-2">
                <label
                    for="password"
                    class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                >
                    Contraseña
                </label>
                <Password
                    id="password"
                    name="password"
                    toggleMask
                    :feedback="false"
                    autocomplete="current-password"
                    class="w-full"
                    inputClass="w-full"
                />
                <FormFieldError
                    field="password"
                    :form="$form"
                    :errors="errors"
                />
            </div>

            <div
                class="flex items-center justify-between gap-4 text-sm text-neutral-700 dark:text-neutral-300"
            >
                <div class="flex items-center gap-2">
                    <Checkbox
                        id="remember"
                        name="remember"
                        binary
                        inputId="remember"
                    />
                    <label for="remember">Recordarme</label>
                </div>

                <a
                    v-if="props.canResetPassword"
                    href="/forgot-password"
                    class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-400"
                >
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <Button
                type="submit"
                label="Iniciar sesión"
                class="w-full mt-2"
                :loading="isSubmitting"
            />

            <p
                v-if="props.canRegister"
                class="mt-4 text-center text-sm text-neutral-700 dark:text-neutral-300"
            >
                ¿No tienes una cuenta?
                <a
                    href="/register"
                    class="font-medium text-primary-600 hover:underline dark:text-primary-400"
                >
                    Crear una cuenta
                </a>
            </p>
        </PvForm>
    </AuthLayout>
</template>
