import ForgotPassword from '../ForgotPassword.vue';
import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';

describe('ForgotPassword.vue', () => {
    const createWrapper = (props = {}) => {
        return mount(ForgotPassword, {
            props,
            global: {
                stubs: {
                    Head: true,
                    Form: true,
                    InputText: true,
                    Button: true,
                    Message: true,
                    FormFieldMessage: true,
                    AuthLayout: true
                }
            }
        });
    };

    describe('Contrato de Props', () => {
        it('debe renderizar sin props requeridas', () => {
            const wrapper = createWrapper();
            expect(wrapper.exists()).toBe(true);
        });

        it('debe aceptar prop status opcional', () => {
            const wrapper = createWrapper({ status: 'Email enviado' });
            expect(wrapper.props('status')).toBe('Email enviado');
        });
    });

    describe('Renderizado del Formulario', () => {
        it('debe mostrar campo de email', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Correo electrónico');
        });

        it('debe renderizar botón de envío', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Email password reset link');
        });

        it('debe tener data-test en el botón', () => {
            const btn = wrapper.find('[data-test=\"email-password-reset-link-button\"]');
            expect(btn.exists()).toBe(true);
        });
    });

    describe('Renderizado Condicional', () => {
        it('debe mostrar mensaje de status si existe', () => {
            const msg = 'Se ha enviado un link a tu email';
            const wrapper = createWrapper({ status: msg });
            expect(wrapper.text()).toContain(msg);
        });
    });

    describe('Links de Navegación', () => {
        it('debe tener link a login', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('log in');
        });

        it('debe mostrar texto de retorno a login', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Or, return to');
        });
    });

    describe('Layout', () => {
        it('debe usar AuthLayout como contenedor', () => {
            const wrapper = createWrapper();
            const layout = wrapper.findComponent({ name: 'AuthLayout' });
            expect(layout.exists()).toBe(true);
        });
    });

    describe('Estado Inicial', () => {
        it('debe inicializar email como cadena vacía', () => {
            const wrapper = createWrapper();
            const comp = wrapper.vm as any;
            expect(comp.form.email === '').toBe(true);
        });
    });
});
