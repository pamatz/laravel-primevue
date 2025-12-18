<script lang="ts" setup>
import FormFieldMessage from '@/components/FormFieldMessage.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { resetPasswordResolver } from '@/validation/resetPassword';
import { Head, useForm } from '@inertiajs/vue3';
import { Form } from '@primevue/forms';
import { Button, InputText, Password } from 'primevue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const initialValues = {
    email: props.email,
    password: '',
    password_confirmation: '',
};

const form = useForm({
    ...initialValues,
    token: props.token,
});

const onFormSubmit = ({ valid }: { valid: boolean }) => {
    if (!valid) return;

    form.submit(update(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout
        description="Please enter your new password below"
        title="Reset password"
    >
        <Head title="Reset password" />

        <Form
            v-slot="$form"
            :initialValues="initialValues"
            :resolver="resetPasswordResolver"
            class="grid gap-6"
            @submit="onFormSubmit"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <label
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                        for="email"
                    >
                        Email
                    </label>
                    <InputText
                        id="email"
                        v-model="form.email"
                        autocomplete="email"
                        class="mt-1 block w-full"
                        name="email"
                        readonly
                        type="email"
                    />
                    <FormFieldMessage
                        :error="form.errors.email"
                        :field="$form.email"
                    />
                </div>

                <div class="grid gap-2">
                    <label
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                        for="password"
                    >
                        Password
                    </label>
                    <Password
                        id="password"
                        v-model="form.password"
                        :feedback="false"
                        autocomplete="new-password"
                        autofocus
                        class="mt-1 block w-full"
                        inputClass="w-full"
                        name="password"
                        placeholder="Password"
                        toggleMask
                        type="password"
                    />
                    <FormFieldMessage
                        :error="form.errors.password"
                        :field="$form.password"
                    />
                </div>

                <div class="grid gap-2">
                    <label
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                        for="password_confirmation"
                    >
                        Confirm Password
                    </label>
                    <Password
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        :feedback="false"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        inputClass="w-full"
                        name="password_confirmation"
                        placeholder="Confirm password"
                        toggleMask
                        type="password"
                    />
                    <FormFieldMessage
                        :error="form.errors.password_confirmation"
                        :field="$form.password_confirmation"
                    />
                </div>

                <Button
                    :disabled="form.processing"
                    :loading="form.processing"
                    class="mt-4 w-full"
                    data-test="reset-password-button"
                    type="submit"
                >
                    Reset password
                </Button>
            </div>
        </Form>
    </AuthLayout>
</template>
