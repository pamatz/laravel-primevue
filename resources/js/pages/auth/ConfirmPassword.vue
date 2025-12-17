<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import FormFieldMessage from '@/components/FormFieldMessage.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { store } from '@/routes/password/confirm';
import { Form } from '@primevue/forms';
import { confirmPasswordResolver } from '@/validation/confirmPassword';

const initialValues = {
    password: '',
};

const form = useForm({ ...initialValues });

const onFormSubmit = ({ valid }: { valid: boolean }) => {
    if (!valid) return;

    form.submit(store(), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthLayout
        title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <Head title="Confirm password" />

        <Form
            v-slot="$form"
            :initialValues="initialValues"
            :resolver="confirmPasswordResolver"
            reset-on-success
            class="space-y-6"
            @submit="onFormSubmit"
        >
            <div class="space-y-6">
                <div class="grid gap-2">
                    <label
                        for="password"
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                    >
                        Password
                    </label>
                    <Password
                        v-model="form.password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        class="mt-1 block w-full"
                        inputClass="w-full"
                        toggleMask
                        :feedback="false"
                        autofocus
                    />

                    <FormFieldMessage
                        :field="$form.password"
                        :error="form.errors.password"
                    />
                </div>

                <div class="flex items-center">
                    <Button
                        class="w-full"
                        :loading="form.processing"
                        :disabled="form.processing"
                        data-test="confirm-password-button"
                    >
                        Confirm Password
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>
