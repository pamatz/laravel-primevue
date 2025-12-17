<script setup lang="ts">
import FormFieldError from '@/components/FormFieldError.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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

const isSubmitting = ref(false);

const page = usePage<any>();
const errors = computed<Record<string, string>>(
    () => (page.props?.errors as Record<string, string>) ?? {},
);

const onSubmit = (event: any): void => {
    const values = event?.values ?? event ?? {};

    isSubmitting.value = true;

    router.post(
        update(),
        {
            ...values,
            token: props.token,
            email: props.email,
        },
        {
            onFinish: () => {
                isSubmitting.value = false;
            },
        },
    );
};
</script>

<template>
    <AuthLayout
        title="Reset password"
        description="Please enter your new password below"
    >
        <Head title="Reset password" />

        <PvForm
            v-slot="$form"
            :initialValues="initialValues"
            :resolver="resetPasswordResolver"
            class="grid gap-6"
            @submit="onSubmit"
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
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        :value="props.email"
                        class="mt-1 block w-full"
                        readonly
                    />
                    <FormFieldError
                        field="email"
                        :form="$form"
                        :errors="errors"
                        extraClass="mt-2"
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
                        name="password"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        inputClass="w-full"
                        autofocus
                        placeholder="Password"
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
                        Confirm Password
                    </label>
                    <Password
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
                    <FormFieldError
                        field="password_confirmation"
                        :form="$form"
                        :errors="errors"
                    />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :loading="isSubmitting"
                    data-test="reset-password-button"
                >
                    Reset password
                </Button>
            </div>
        </PvForm>
    </AuthLayout>
</template>
