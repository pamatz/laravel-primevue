import { z } from 'zod';
import { zodResolver } from '@primevue/forms/resolvers/zod';

export const loginSchema = z.object({
    email: z
        .string({ required_error: 'El correo electrónico es obligatorio.' })
        .min(1, { message: 'El correo electrónico es obligatorio.' })
        .email({ message: 'Ingresa un correo electrónico válido.' }),
    password: z
        .string({ required_error: 'La contraseña es obligatoria.' })
        .min(1, { message: 'La contraseña es obligatoria.' }),
    remember: z.boolean().optional(),
});

export const loginResolver = zodResolver(loginSchema);
