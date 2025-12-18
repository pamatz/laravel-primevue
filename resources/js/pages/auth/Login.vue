<script lang="ts" setup>
import FormFieldMessage from '@/components/FormFieldMessage.vue';
import { store } from '@/routes/login';
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
    remember: false,
};

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
    <Head title="Iniciar Sesión" />
    <!--    <div class="topbar-actions">-->
    <!--        <Button-->
    <!--            type="button"-->
    <!--            class="topbar-theme-button"-->
    <!--            @click="toggleDarkMode"-->
    <!--            text-->
    <!--            rounded-->
    <!--        >-->
    <!--            <i-->
    <!--                :class="[-->
    <!--                    'pi',-->
    <!--                    'pi',-->
    <!--                    { 'pi-moon': isDarkMode, 'pi-sun': !isDarkMode },-->
    <!--                ]"-->
    <!--            />-->
    <!--        </Button>-->
    <!--        <div class="relative">-->
    <!--            <Button-->
    <!--                v-styleclass="{-->
    <!--                    selector: '@next',-->
    <!--                    enterFromClass: 'hidden',-->
    <!--                    enterActiveClass: 'animate-scalein',-->
    <!--                    leaveToClass: 'hidden',-->
    <!--                    leaveActiveClass: 'animate-fadeout',-->
    <!--                    hideOnOutsideClick: true,-->
    <!--                }"-->
    <!--                icon="pi pi-cog"-->
    <!--                text-->
    <!--                rounded-->
    <!--                aria-label="Settings"-->
    <!--            />-->
    <!--            <AppConfig />-->
    <!--        </div>-->
    <!--    </div>-->
    <div class="px-6 py-20 md:px-20 lg:px-80 dark:bg-surface-950">
        <div
            class="mx-auto flex w-full max-w-lg flex-col gap-8 rounded-2xl bg-surface-0 p-8 shadow-sm md:p-12 dark:bg-surface-900"
        >
            <div class="flex flex-col items-center gap-4">
                <div class="flex items-center gap-4">
                    <svg
                        width="180"
                        height="150"
                        viewBox="20 30 160 120"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <rect
                            x="20"
                            y="30"
                            width="90"
                            height="120"
                            rx="10"
                            ry="10"
                            class="fill-primary-500"
                        />
                        <rect
                            x="120"
                            y="70"
                            width="60"
                            height="80"
                            rx="8"
                            ry="8"
                            class="fill-primary-900"
                        />

                        <!-- Líneas en el documento -->
                        <rect
                            x="35"
                            y="45"
                            width="60"
                            height="10"
                            rx="2"
                            fill="white"
                        />
                        <rect
                            x="35"
                            y="65"
                            width="50"
                            height="10"
                            rx="2"
                            fill="white"
                        />
                        <rect
                            x="35"
                            y="85"
                            width="40"
                            height="10"
                            rx="2"
                            fill="white"
                        />

                        <!-- Gráfico circular -->
                        <circle cx="60" cy="120" r="20" fill="white" />
                        <path
                            d="M60 120 L60 100 A20 20 0 0 1 80 120 Z"
                            class="fill-primary-900"
                        />

                        <!-- Calculadora botones -->
                        <rect
                            x="130"
                            y="80"
                            width="40"
                            height="15"
                            rx="2"
                            fill="white"
                        />
                        <rect
                            x="130"
                            y="100"
                            width="10"
                            height="10"
                            fill="white"
                        />
                        <rect
                            x="145"
                            y="100"
                            width="10"
                            height="10"
                            fill="white"
                        />
                        <rect
                            x="160"
                            y="100"
                            width="10"
                            height="10"
                            fill="white"
                        />
                        <rect
                            x="130"
                            y="115"
                            width="10"
                            height="10"
                            fill="white"
                        />
                        <rect
                            x="145"
                            y="115"
                            width="10"
                            height="10"
                            fill="white"
                        />
                        <rect
                            x="160"
                            y="115"
                            width="10"
                            height="10"
                            fill="white"
                        />
                        <rect
                            x="130"
                            y="130"
                            width="10"
                            height="10"
                            fill="white"
                        />
                        <rect
                            x="145"
                            y="130"
                            width="10"
                            height="10"
                            fill="white"
                        />
                        <rect
                            x="160"
                            y="130"
                            width="10"
                            height="10"
                            fill="white"
                        />
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
                        <label
                            for="email"
                            class="leading-normal font-medium text-surface-900 dark:text-surface-0"
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
                        <FormFieldMessage
                            :field="$form.email"
                            :error="form.errors.email"
                        />
                    </div>
                    <div class="flex w-full flex-col gap-2">
                        <label
                            for="password"
                            class="leading-normal font-medium text-surface-900 dark:text-surface-0"
                            >Password</label
                        >
                        <Password
                            id="password"
                            v-model="form.password"
                            toggleMask
                            name="password"
                            :feedback="false"
                            input-class="w-full!"
                        />
                        <FormFieldMessage
                            :field="$form.password"
                            :error="form.errors.password"
                        />
                    </div>
                    <div
                        class="flex w-full flex-col items-start justify-between gap-3 sm:flex-row sm:items-center sm:gap-0"
                    >
                        <div class="flex items-center gap-2">
                            <Checkbox
                                id="rememberme1"
                                v-model="form.remember"
                                :binary="true"
                            />
                            <label
                                for="rememberme1"
                                class="leading-normal text-surface-900 dark:text-surface-0"
                                >Remember me</label
                            >
                        </div>
                        <Link
                            v-if="props.canResetPassword"
                            href="/forgot-password"
                            preserve-scroll
                            preserve-state
                        >
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
