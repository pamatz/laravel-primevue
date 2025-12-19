import ConfirmPassword from '../ConfirmPassword.vue';
import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';

describe('ConfirmPassword.vue', () => {
    const createWrapper = (props = {}) => {
        return mount(ConfirmPassword, {
            props,
            global: {
                stubs: {
                    Head: true,
                    Form: true,
                    Password: true,
                    Button: true,
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
    });

    describe('Renderizado del Layout', () => {
        it('debe usar AuthLayout como contenedor', () => {
            const wrapper = createWrapper();
            const layout = wrapper.findComponent({ name: 'AuthLayout' });
            expect(layout.exists()).toBe(true);
        });

        it('debe mostrar título correcto', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Confirm your password');
        });

        it('debe mostrar descripción', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('This is a secure area of the application');
        });
    });

    describe('Renderizado del Formulario', () => {
        it('debe mostrar campo de password', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Password');
        });

        it('debe renderizar botón de confirmación', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Confirm Password');
        });

        it('debe tener data-test en el botón', () => {
            const btn = wrapper.find('[data-test=\"confirm-password-button\"]');
            expect(btn.exists()).toBe(true);
        });
    });

    describe('Campo de Password', () => {
        it('debe tener tipo password', () => {
            const wrapper = createWrapper();
            const inputs = wrapper.findAll('input[type=\"password\"]');
            expect(inputs.length).toBeGreaterThanOrEqual(1);
        });

        it('debe tener autocomplete current-password', () => {
            const wrapper = createWrapper();
            const inputs = wrapper.findAll('input[autocomplete=\"current-password\"]');
            expect(inputs.length).toBeGreaterThanOrEqual(1);
        });
    });

    describe('Estado Inicial', () => {
        it('debe inicializar password como cadena vacía', () => {
            const wrapper = createWrapper();
            const comp = wrapper.vm as any;
            expect(comp.form.password === '').toBe(true);
        });
    });

    describe('Label y Accesibilidad', () => {
        it('debe tener etiqueta para el campo de password', () => {
            const wrapper = createWrapper();
            const label = wrapper.find('label[for=\"password\"]');
            expect(label.exists()).toBe(true);
        });

        it('debe mostrar texto de la etiqueta', () => {
            const wrapper = createWrapper();
            expect(wrapper.text()).toContain('Password');
        });
    });
});
