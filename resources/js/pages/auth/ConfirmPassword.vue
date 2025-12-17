<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import FormFieldError from '@/components/FormFieldError.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { store } from '@/routes/password/confirm';
import { computed, ref } from 'vue';
import { confirmPasswordResolver } from '@/validation/confirmPassword';

const initialValues = {
    password: '',
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
    <AuthLayout
        title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <Head title="Confirm password" />

        <PvForm
            v-slot="$form"
            :initialValues="initialValues"
            :resolver="confirmPasswordResolver"
            reset-on-success
            class="space-y-6"
            @submit="onSubmit"
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
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        class="mt-1 block w-full"
                        inputClass="w-full"
                        toggleMask
                        :feedback="false"
                        autofocus
                    />

                    <FormFieldError
                        field="password"
                        :form="$form"
                        :errors="errors"
                    />
                </div>

                <div class="flex items-center">
                    <Button
                        class="w-full"
                        :loading="isSubmitting"
                        data-test="confirm-password-button"
                    >
                        Confirm Password
                    </Button>
                </div>
            </div>
        </PvForm>
    </AuthLayout>
</template>
