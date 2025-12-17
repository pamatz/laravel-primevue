<script setup lang="ts">
import FormFieldMessage from '@/components/FormFieldMessage.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Form } from '@primevue/forms';
import { registerResolver } from '@/validation/register';

const initialValues = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
};

const form = useForm({ ...initialValues });

const onFormSubmit = ({ valid }: { valid: boolean }) => {
    if (!valid) return;

    form.submit(store(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <Form
            v-slot="$form"
            :initialValues="initialValues"
            :resolver="registerResolver"
            class="flex flex-col gap-6"
            @submit="onFormSubmit"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <label
                        for="name"
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                    >
                        Name
                    </label>
                    <InputText
                        v-model="form.name"
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Full name"
                        class="w-full"
                    />
                    <FormFieldMessage
                        :field="$form.name"
                        :error="form.errors.name"
                    />
                </div>

                <div class="grid gap-2">
                    <label
                        for="email"
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                    >
                        Email address
                    </label>
                    <InputText
                        v-model="form.email"
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                        class="w-full"
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
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                        class="w-full"
                        inputClass="w-full"
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
                        Confirm password
                    </label>
                    <Password
                        v-model="form.password_confirmation"
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                        class="w-full"
                        inputClass="w-full"
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
                    class="mt-2 w-full"
                    tabindex="5"
                    :loading="form.processing"
                    :disabled="form.processing"
                    data-test="register-user-button"
                >
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <Link
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                >
                    Log in
                </Link>
            </div>
        </Form>
    </AuthBase>
</template>
