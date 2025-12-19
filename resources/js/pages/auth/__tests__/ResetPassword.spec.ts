import ResetPassword from '../ResetPassword.vue';
import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';

describe('ResetPassword.vue', () => {
    const defaultProps = {
        token: 'valid-token-123',
        email: 'user@example.com'
    };

    const createWrapper = (props = {}) => {
        return mount(ResetPassword, {
            props: {
                ...defaultProps,
                ...props
            },
            global: {
                stubs: {
                    Head: true,
                    Form: true,
                    InputText: true,
                    Password: true,
                    Button: true,
                    FormFieldMessage: true,
                    AuthLayout: true
                }
            }
        });
    };

    describe('Contrato de Props', () => {
        it('debe renderizar con props requeridas', () => {
            const wrapper = createWrapper();
            expect(wrapper.exists()).toBe(true);
        });

        it('debe requerir token prop', () => {
            const wrapper = createWrapper({ token: 'mytoken' });
            expect(wrapper.props('token')).toBe('mytoken');
        });

        it('debe requerir email prop', () => {
            const wrapper = createWrapper({ email: 'test@test.com' });
            expect(wrapper.props('email')).toBe('test@test.com');
        });
    });

    describe('Renderizado del Formulario', () => {
        it('debe mostrar campo de email', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Email');
        });

        it('debe mostrar campo de password', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Password');
        });

        it('debe mostrar campo de password confirmation', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Confirm Password');
        });

        it('debe renderizar botón de reset', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Reset password');
        });

        it('debe tener data-test en el botón', () => {
            const btn = wrapper.find('[data-test=\"reset-password-button\"]');
            expect(btn.exists()).toBe(true);
        });
    });

    describe('Campo de Email', () => {
        it('debe mostrar el email de la prop', () => {
            const wrapper = createWrapper({ email: 'john@example.com' });
            const comp = wrapper.vm as any;
            expect(comp.form.email).toBe('john@example.com');
        });

        it('el email debe ser readonly', () => {
            const wrapper = createWrapper();
            const readonly = wrapper.findAll('input[readonly]');
            expect(readonly.length).toBeGreaterThanOrEqual(1);
        });
    });

    describe('Estado Inicial', () => {
        it('debe inicializar password como cadena vacía', () => {
            const wrapper = createWrapper();
            const comp = wrapper.vm as any;
            expect(comp.form.password === '').toBe(true);
        });

        it('debe inicializar password_confirmation como cadena vacía', () => {
            const wrapper = createWrapper();
            const comp = wrapper.vm as any;
            expect(comp.form.password_confirmation === '').toBe(true);
        });

        it('debe incluir el token en el formulario', () => {
            const wrapper = createWrapper({ token: 'tokenxyz' });
            const comp = wrapper.vm as any;
            expect(comp.form.token).toBe('tokenxyz');
        });
    });

    describe('Layout', () => {
        it('debe usar AuthLayout como contenedor', () => {
            const wrapper = createWrapper();
            const layout = wrapper.findComponent({ name: 'AuthLayout' });
            expect(layout.exists()).toBe(true);
        });
    });
});
