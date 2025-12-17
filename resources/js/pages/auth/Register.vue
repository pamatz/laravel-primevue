<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import FormFieldError from '@/components/FormFieldError.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { registerResolver } from '@/validation/register';

const initialValues = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
};

const isSubmitting = ref(false);

const page = usePage<any>();
const errors = computed<Record<string, string>>(
    () => (page.props?.errors as Record<string, string>) ?? {},
);

const onSubmit = (event: any): void => {
    const values = event?.values ?? event ?? {};

    isSubmitting.value = true;

    router.post(store(), values, {
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <PvForm
            v-slot="$form"
            :initialValues="initialValues"
            :resolver="registerResolver"
            class="flex flex-col gap-6"
            @submit="onSubmit"
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
                    <FormFieldError
                        field="name"
                        :form="$form"
                        :errors="errors"
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
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                        class="w-full"
                    />
                    <FormFieldError
                        field="email"
                        :form="$form"
                        :errors="errors"
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
                    <FormFieldError
                        field="password"
                        :form="$form"
                        :errors="errors"
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
                    <FormFieldError
                        field="password_confirmation"
                        :form="$form"
                        :errors="errors"
                    />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="5"
                    :loading="isSubmitting"
                    data-test="register-user-button"
                >
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline underline-offset-4"
                    :tabindex="6"
                    >Log in</TextLink
                >
            </div>
        </PvForm>
    </AuthBase>
</template>
