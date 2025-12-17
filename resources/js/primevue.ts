import type { App } from 'vue';
import Aura from '@primevue/themes/aura';
import PrimeVue from 'primevue/config';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Checkbox from 'primevue/checkbox';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import Password from 'primevue/password';
import { Form as PvForm, FormField } from '@primevue/forms';
import 'primeicons/primeicons.css';

export function installPrimeVue(app: App): void {
    app
        .use(PrimeVue, {
            theme: {
                preset: Aura,
            },
        })
        .component('Card', Card)
        .component('Checkbox', Checkbox)
        .component('Button', Button)
        .component('InputText', InputText)
        .component('Message', Message)
        .component('Password', Password)
        .component('PvForm', PvForm)
        .component('FormField', FormField);
}
