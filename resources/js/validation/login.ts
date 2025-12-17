import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from './zod';

const loginSchema = z.object({
    email: z.string('Ingresa tu correo electrónico.').pipe(z.email()),
    password: z
        .string('Ingresa tu contraseña.')
        .min(1, 'Ingresa tu contraseña.'),
    remember: z.boolean().optional(),
});

export const loginResolver = zodResolver(loginSchema);
