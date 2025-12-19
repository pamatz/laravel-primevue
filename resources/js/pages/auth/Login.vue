<script lang="ts" setup>
import FormFieldMessage from '@/components/FormFieldMessage.vue';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { loginResolver } from '@/validation/login';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Form } from '@primevue/forms';
import { Button, Checkbox, InputText, Message, Password } from 'primevue';

const props = defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const initialValues = {
    email: '',
    password: '',
    remember: false
};

const form = useForm({ ...initialValues });

const onFormSubmit = ({ valid }: any) => {
    if (valid) {
        form.submit(store(), {
            onFinish: () => form.reset('password')
        });
    }
};
</script>

<template>
    <Head title="Iniciar Sesión" />
    <div class="px-6 py-20 md:px-20 lg:px-80 dark:bg-surface-950">
        <div
            class="mx-auto flex w-full max-w-lg flex-col gap-8 rounded-2xl bg-surface-0 p-8 shadow-sm md:p-12 dark:bg-surface-900"
        >
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-4">
                    <svg height="150" viewBox="20 30 160 120" width="180" xmlns="http://www.w3.org/2000/svg">
                        <rect class="fill-primary-500" height="120" rx="10" ry="10" width="90" x="20"
                              y="30" />
                        <rect class="fill-primary-800" height="80" rx="8" ry="8" width="60" x="120" y="70" />

                        <!-- Líneas en el documento -->
                        <rect fill="white" height="10" rx="2" width="60" x="35" y="45" />
                        <rect fill="white" height="10" rx="2" width="50" x="35" y="65" />
                        <rect fill="white" height="10" rx="2" width="40" x="35" y="85" />

                        <!-- Gráfico circular -->
                        <circle cx="60" cy="120" fill="white" r="20" />
                        <path class="fill-primary-800" d="M60 120 L60 100 A20 20 0 0 1 80 120 Z" />

                        <!-- Calculadora botones -->
                        <rect fill="white" height="15" rx="2" width="40" x="130" y="80" />
                        <rect fill="white" height="10" width="10" x="130" y="100" />
                        <rect fill="white" height="10" width="10" x="145" y="100" />
                        <rect fill="white" height="10" width="10" x="160" y="100" />
                        <rect fill="white" height="10" width="10" x="130" y="115" />
                        <rect fill="white" height="10" width="10" x="145" y="115" />
                        <rect fill="white" height="10" width="10" x="160" y="115" />
                        <rect fill="white" height="10" width="10" x="130" y="130" />
                        <rect fill="white" height="10" width="10" x="145" y="130" />
                        <rect fill="white" height="10" width="10" x="160" y="130" />
                    </svg>
                </div>
                <div class="flex w-full flex-col items-center gap-2">
                    <div
                        class="w-full text-center text-2xl leading-tight font-semibold text-surface-900 dark:text-surface-0"
                    >
                        {{ $page.props.name }}
                    </div>
                </div>
            </div>

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
                <div class="flex w-full flex-col gap-6">
                    <div class="flex w-full flex-col gap-2">
                        <label class="leading-normal font-medium text-surface-900 dark:text-surface-0"
                               for="email"
                            >Email Address</label
                        >
                        <InputText
                            id="email"
                            v-model="form.email"
                            autocomplete="email"
                            autofocus
                            class="w-full"
                            name="email"
                            type="email"
                        />
                        <FormFieldMessage :error="form.errors.email" :field="$form.email" />
                    </div>
                    <div class="flex w-full flex-col gap-2">
                        <label class="leading-normal font-medium text-surface-900 dark:text-surface-0"
                               for="password"
                            >Password</label
                        >
                        <Password
                            id="password"
                            v-model="form.password"
                            :feedback="false"
                            input-class="w-full!"
                            name="password"
                            toggleMask
                        />
                        <FormFieldMessage :error="form.errors.password" :field="$form.password" />
                    </div>
                    <div
                        class="flex w-full flex-col items-start justify-between gap-3 sm:flex-row sm:items-center sm:gap-0"
                    >
                        <div class="flex items-center gap-2">
                            <Checkbox id="rememberme1" v-model="form.remember" :binary="true" />
                            <label class="leading-normal text-surface-900 dark:text-surface-0"
                                   for="rememberme1"
                                >Remember me</label
                            >
                        </div>
                        <Link v-if="props.canResetPassword" :href="request().url" preserve-scroll
                              preserve-state>
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>
                </div>
                <Button
                    :disabled="form.processing"
                    :loading="form.processing"
                    class="mt-2 w-full"
                    label="Iniciar sesión"
                    type="submit"
                />
            </Form>
        </div>
    </div>
</template>
