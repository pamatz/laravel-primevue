<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import FormFieldError from '@/components/FormFieldError.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { login } from '@/routes';
import { email as passwordEmail } from '@/routes/password';
import { computed, ref } from 'vue';
import { forgotPasswordResolver } from '@/validation/forgotPassword';

const props = defineProps<{
    status?: string;
}>();

const initialValues = {
    email: '',
};

const isSubmitting = ref(false);

const page = usePage<any>();
const errors = computed<Record<string, string>>(
    () => (page.props?.errors as Record<string, string>) ?? {},
);

const onSubmit = (event: any): void => {
    const values = event?.values ?? event ?? {};

    isSubmitting.value = true;

    router.post(passwordEmail(), values, {
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};
</script>

<template>
    <AuthLayout
        title="Forgot password"
        description="Enter your email to receive a password reset link"
    >
        <Head title="Forgot password" />

        <div
            v-if="props.status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ props.status }}
        </div>

        <div class="space-y-6">
            <PvForm
                v-slot="$form"
                :initialValues="initialValues"
                :resolver="forgotPasswordResolver"
                class="space-y-6"
                @submit="onSubmit"
            >
                <div class="grid gap-2">
                    <label
                        for="email"
                        class="text-sm font-medium text-neutral-800 dark:text-neutral-100"
                    >
                        Email address
                    </label>
                    <InputText
                        id="email"
                        name="email"
                        type="email"
                        autocomplete="off"
                        autofocus
                        placeholder="email@example.com"
                        class="w-full"
                    />
                    <FormFieldError
                        field="email"
                        :form="$form"
                        :errors="errors"
                    />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button
                        class="w-full"
                        :loading="isSubmitting"
                        data-test="email-password-reset-link-button"
                    >
                        Email password reset link
                    </Button>
                </div>
            </PvForm>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Or, return to</span>
                <TextLink :href="login()">log in</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
