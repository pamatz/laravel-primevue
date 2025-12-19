<script lang="ts" setup>
import FormFieldMessage from '@/components/FormFieldMessage.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { email as passwordEmail } from '@/routes/password';
import { forgotPasswordResolver } from '@/validation/forgotPassword';
import { Head, useForm } from '@inertiajs/vue3';
import { Form } from '@primevue/forms';
import { Button, InputText, Message } from 'primevue';

const props = defineProps<{
    status?: string;
}>();

const initialValues = {
    email: ''
};

const form = useForm({ ...initialValues });

const onFormSubmit = ({ valid }: { valid: boolean }) => {
    if (!valid) return;

    form.submit(passwordEmail(), {
        onFinish: () => form.reset('email')
    });
};
</script>

<template>
    <AuthLayout description="Enter your email to receive a password reset link" title="Forgot password">
        <Head title="Forgot password" />

        <div v-if="props.status" class="mb-4">
            <Message :life="5000" class="w-full" severity="success">
                {{ props.status }}
            </Message>
        </div>

        <div class="space-y-6">
            <Form
                v-slot="$form"
                :initialValues="initialValues"
                :resolver="forgotPasswordResolver"
                class="space-y-6"
                @submit="onFormSubmit"
            >
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium text-neutral-800 dark:text-neutral-100" for="email">
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
                    <FormFieldMessage :error="form.errors.email" :field="$form.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button
                        :disabled="form.processing"
                        :loading="form.processing"
                        class="w-full"
                        data-test="email-password-reset-link-button"
                        type="submit"
                    >
                        Email password reset link
                    </Button>
                </div>
            </Form>

            <div class="text-muted-foreground space-x-1 text-center text-sm">
                <span>Or, return to</span>
                <Button :href="login().url" as="a" label="log in" variant="link"></Button>
            </div>
        </div>
    </AuthLayout>
</template>
