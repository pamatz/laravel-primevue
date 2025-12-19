import { mount } from '@vue/test-utils';
import { describe, it, expect, beforeEach, vi } from 'vitest';
import { ref } from 'vue';
import AppConfig from '../AppConfig.vue';

const updateColorsMock = vi.fn();

vi.mock('@/composables/useLayout', () => {
    const primaryColors = [
        { name: 'emerald', palette: { 500: '#00ff00' } },
        { name: 'indigo', palette: { 500: '#0000ff' } }
    ];

    const surfaces = [
        { name: 'slate', palette: { 500: '#111111' } },
        { name: 'zinc', palette: { 500: '#222222' } }
    ];

    return {
        useLayout: () => ({
            primaryColors,
            surfaces,
            preset: ref('default'),
            primary: ref('emerald'),
            surface: ref(null),
            isDarkMode: ref(false),
            updateColors: updateColorsMock
        })
    };
});

describe('AppConfig', () => {
    beforeEach(() => {
        updateColorsMock.mockClear();
    });

    it('llama a updateColors con el color primario seleccionado', async () => {
        const wrapper = mount(AppConfig);

        const buttons = wrapper.findAll('button[title]');
        const indigoButton = buttons.find((btn) => btn.attributes('title') === 'indigo');

        expect(indigoButton).toBeTruthy();

        await indigoButton!.trigger('click');

        expect(updateColorsMock).toHaveBeenCalledWith('primary', 'indigo');
    });

    it('llama a updateColors con el color de surface seleccionado', async () => {
        const wrapper = mount(AppConfig);

        const buttons = wrapper.findAll('button[title]');
        const surfaceButton = buttons.find((btn) => btn.attributes('title') === 'zinc');

        expect(surfaceButton).toBeTruthy();

        await surfaceButton!.trigger('click');

        expect(updateColorsMock).toHaveBeenCalledWith('surface', 'zinc');
    });
});
