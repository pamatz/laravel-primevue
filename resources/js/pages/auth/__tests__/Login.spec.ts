import Login from '../Login.vue';
import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';

describe('Login.vue', () => {
    const defaultProps = {
        canResetPassword: true,
        canRegister: true
    };

    const createWrapper = (props = {}) => {
        return mount(Login, {
            props: {
                ...defaultProps,
                ...props
            },
            global: {
                stubs: {
                    Head: true,
                    Link: true,
                    Form: true,
                    InputText: true,
                    Password: true,
                    Button: true,
                    Checkbox: true,
                    Message: true,
                    FormFieldMessage: true
                },
                mocks: {
                    $page: {
                        props: {
                            name: 'Mi Aplicación'
                        }
                    }
                }
            }
        });
    };

    describe('Contrato de Props', () => {
        it('debe renderizar con props requeridas', () => {
            const wrapper = createWrapper();
            expect(wrapper.exists()).toMatchSnapshot();
        });

        it('debe aceptar canResetPassword prop', () => {
            const wrapper = createWrapper({ canResetPassword: true });
            expect(wrapper.props('canResetPassword')).toBe(true);
        });

        it('debe aceptar canRegister prop', () => {
            const wrapper = createWrapper({ canRegister: false });
            expect(wrapper.props('canRegister')).toBe(false);
        });

        it('debe aceptar status prop opcional', () => {
            const wrapper = createWrapper({ status: 'Verificación exitosa' });
            expect(wrapper.props('status')).toBe('Verificación exitosa');
        });
    });

    describe('Renderizado del Formulario', () => {
        it('debe renderizar campo de email', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Email Address');
        });

        it('debe renderizar campo de password', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Password');
        });

        it('debe renderizar checkbox Remember me', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Remember me');
        });

        it('debe renderizar botón de envío', () => {
            const wrapper = createWrapper();
            const submitButton = wrapper.find('button[type=\"submit\"]');
            expect(submitButton.exists()).toBe(true);
            expect(submitButton.text()).toContain('Iniciar sesión');
        });
    });

    describe('Renderizado Condicional', () => {
        it('debe mostrar link a forgot password si está habilitado', () => {
            const wrapper = createWrapper({ canResetPassword: true });
            expect(wrapper.text()).toContain('¿Olvidaste tu contraseña?');
        });

        it('no debe mostrar link si canResetPassword es false', () => {
            const wrapper = createWrapper({ canResetPassword: false });
            expect(wrapper.text()).not.toContain('¿Olvidaste tu contraseña?');
        });

        it('debe mostrar mensaje de status si está presente', () => {
            const msg = 'Registro exitoso';
            const wrapper = createWrapper({ status: msg });
            expect(wrapper.text()).toContain(msg);
        });
    });

    describe('Estado Inicial', () => {
        it('debe inicializar email como cadena vacía', () => {
            const wrapper = createWrapper();
            const comp = wrapper.vm as any;
            expect(comp.form.email === '').toBe(true);
        });

        it('debe inicializar password como cadena vacía', () => {
            const wrapper = createWrapper();
            const comp = wrapper.vm as any;
            expect(comp.form.password === '').toBe(true);
        });

        it('debe inicializar remember como false', () => {
            const wrapper = createWrapper();
            const comp = wrapper.vm as any;
            expect(comp.form.remember === false).toBe(true);
        });
    });
});
