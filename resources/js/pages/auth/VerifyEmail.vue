<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { logout } from '@/routes';
import { send } from '@/routes/verification';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    status?: string;
}>();

const processing = ref(false);

const onResend = (): void => {
    processing.value = true;

    router.post(send(), {}, {
        onFinish: () => {
            processing.value = false;
        },
    });
};
</script>

<template>
    <AuthLayout
        title="Verify email"
        description="Please verify your email address by clicking on the link we just emailed to you."
    >
        <Head title="Email verification" />

        <div
            v-if="props.status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <div class="space-y-6 text-center">
            <Button
                :disabled="processing"
                :loading="processing"
                severity="secondary"
                @click="onResend"
            >
                Resend verification email
            </Button>

            <TextLink
                :href="logout()"
                as="button"
                class="mx-auto block text-sm"
            >
                Log out
            </TextLink>
        </div>
    </AuthLayout>
</template>
