<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import FormFieldMessage from '@/components/FormFieldMessage.vue';
import { update } from '@/routes/password';
import { Head, useForm } from '@inertiajs/vue3';
import { Form } from '@primevue/forms';
import { resetPasswordResolver } from '@/validation/resetPassword';

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
        title="Reset password"
        description="Please enter your new password below"
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
                        for="email"
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                    >
                        Email
                    </label>
                    <InputText
                        v-model="form.email"
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        class="mt-1 block w-full"
                        readonly
                    />
                    <FormFieldMessage
                        :field="$form.email"
                        :error="form.errors.email"
                    />
                </div>

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
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        inputClass="w-full"
                        autofocus
                        placeholder="Password"
                        toggleMask
                        :feedback="false"
                    />
                    <FormFieldMessage
                        :field="$form.password"
                        :error="form.errors.password"
                    />
                </div>

                <div class="grid gap-2">
                    <label
                        for="password_confirmation"
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                    >
                        Confirm Password
                    </label>
                    <Password
                        v-model="form.password_confirmation"
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        inputClass="w-full"
                        placeholder="Confirm password"
                        toggleMask
                        :feedback="false"
                    />
                    <FormFieldMessage
                        :field="$form.password_confirmation"
                        :error="form.errors.password_confirmation"
                    />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :loading="form.processing"
                    :disabled="form.processing"
                    data-test="reset-password-button"
                >
                    Reset password
                </Button>
            </div>
        </Form>
    </AuthLayout>
</template>
