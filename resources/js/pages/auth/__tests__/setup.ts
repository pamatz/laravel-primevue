import { vi } from 'vitest';

vi.mock('@inertiajs/vue3', () => ({
    useForm: vi.fn((data) => ({
        ...data,
        processing: false,
        errors: {},
        submit: vi.fn(),
        reset: vi.fn()
    })),
    Head: { name: 'Head', template: '<div></div>' },
    Link: {
        name: 'Link',
        template: '<a><slot /></a>',
        props: ['href', 'preserveScroll', 'preserveState', 'as']
    },
    router: {
        post: vi.fn()
    }
}));

vi.mock('@/routes/login', () => ({
    store: () => '/login',
    request: () => ({ url: '/forgot-password' })
}));

vi.mock('@/routes/password', () => ({
    request: () => ({ url: '/forgot-password' }),
    email: () => '/forgot-password',
    update: () => '/reset-password'
}));

vi.mock('@/routes/password/confirm', () => ({
    store: () => '/password/confirm'
}));

vi.mock('@/routes/verification', () => ({
    send: () => '/email/verification-notification'
}));

vi.mock('@/routes', () => ({
    login: () => ({ url: '/login' })
}));

vi.mock('@/validation/login', () => ({
    loginResolver: vi.fn()
}));

vi.mock('@/validation/forgotPassword', () => ({
    forgotPasswordResolver: vi.fn()
}));

vi.mock('@/validation/resetPassword', () => ({
    resetPasswordResolver: vi.fn()
}));

vi.mock('@/validation/confirmPassword', () => ({
    confirmPasswordResolver: vi.fn()
}));
