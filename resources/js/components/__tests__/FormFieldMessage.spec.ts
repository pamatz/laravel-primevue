import { mount } from '@vue/test-utils';
import { describe, it, expect } from 'vitest';
import FormFieldMessage from '../FormFieldMessage.vue';

describe('FormFieldMessage', () => {
    it('muestra field.error.message cuando el campo es inválido', () => {
        const wrapper = mount(FormFieldMessage, {
            props: {
                field: { invalid: true, error: { message: 'Campo requerido' } },
                error: null
            }
        });

        expect(wrapper.text()).toContain('Campo requerido');
    });

    it('usa el primer mensaje del array error cuando no hay field.error.message', () => {
        const wrapper = mount(FormFieldMessage, {
            props: {
                field: { invalid: true, error: {} },
                error: ['Email inválido', 'Otro error']
            }
        });

        expect(wrapper.text()).toContain('Email inválido');
    });

    it('muestra el mensaje cuando solo se recibe error string', () => {
        const wrapper = mount(FormFieldMessage, {
            props: {
                field: null,
                error: 'Error genérico'
            }
        });

        expect(wrapper.text()).toContain('Error genérico');
    });

    it('no muestra mensaje cuando no hay errores ni campo inválido', () => {
        const wrapper = mount(FormFieldMessage, {
            props: {
                field: { invalid: false, error: null },
                error: null
            }
        });

        expect(wrapper.text()).toBe('');
    });

    it('muestra el mensaje aunque field.invalid sea falso si field.error.message existe', () => {
        const wrapper = mount(FormFieldMessage, {
            props: {
                field: { invalid: false, error: { message: 'Error desde el campo' } },
                error: null,
            },
        });

        expect(wrapper.text()).toContain('Error desde el campo');
    });

    it('no muestra mensaje cuando el campo es inválido pero no hay ningún mensaje de error', () => {
        const wrapper = mount(FormFieldMessage, {
            props: {
                field: { invalid: true, error: {} },
                error: null,
            },
        });

        expect(wrapper.text()).toBe('');
    });

    it('prioriza field.error.message sobre el prop error cuando ambos tienen valor', () => {
        const wrapper = mount(FormFieldMessage, {
            props: {
                field: { invalid: true, error: { message: 'Mensaje de field' } },
                error: 'Mensaje de prop error',
            },
        });

        const text = wrapper.text();
        expect(text).toContain('Mensaje de field');
        expect(text).not.toContain('Mensaje de prop error');
    });
});
