import VerifyEmail from '../VerifyEmail.vue';
import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';

describe('VerifyEmail.vue', () => {
    const createWrapper = (props = {}) => {
        return mount(VerifyEmail, {
            props,
            global: {
                stubs: {
                    Head: true,
                    Button: true,
                    Link: true,
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
            const wrapper = createWrapper({ status: 'verification-link-sent' });
            expect(wrapper.props('status')).toBe('verification-link-sent');
        });
    });

    describe('Renderizado del Layout', () => {
        it('debe usar AuthLayout como contenedor', () => {
            const wrapper = createWrapper();
            const layout = wrapper.findComponent({ name: 'AuthLayout' });
            expect(layout.exists()).toBe(true);
        });

        it('debe mostrar título correcto', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Verify email');
        });

        it('debe mostrar descripción', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Please verify your email address');
        });
    });

    describe('Botón de Reenvío', () => {
        it('debe renderizar botón para reenviar email', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Resend verification email');
        });
    });

    describe('Mensaje de Status', () => {
        it('debe mostrar mensaje cuando status es verification-link-sent', () => {
            const wrapper = createWrapper({ status: 'verification-link-sent' });
            expect(wrapper.text()).toContain('A new verification link has been sent');
        });

        it('no debe mostrar mensaje si status es distinto', () => {
            const wrapper = createWrapper({ status: 'other' });
            expect(wrapper.text()).not.toContain('A new verification link has been sent');
        });
    });

    describe('Link de Logout', () => {
        it('debe tener link para logout', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Log out');
        });

        it('el link debe ser un Link de Inertia', () => {
            const wrapper = createWrapper();
            const link = wrapper.findComponent({ name: 'Link' });
            expect(link.exists()).toBe(true);
        });
    });

    describe('Estado Inicial', () => {
        it('debe inicializar processing como false', () => {
            const wrapper = createWrapper();
            const comp = wrapper.vm as any;
            expect(comp.processing).toBe(false);
        });
    });
});
