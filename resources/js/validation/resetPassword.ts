import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from './zod';

const resetPasswordSchema = z
    .object({
        email: z
            .string({ error: 'Ingresa tu correo electrónico.' })
            .pipe(z.email()),
        password: z
            .string({ error: 'Ingresa una nueva contraseña.' })
            .min(8, 'La contraseña debe tener al menos 8 caracteres.'),
        password_confirmation: z
            .string({ error: 'Confirma tu contraseña.' })
            .min(1, 'Confirma tu contraseña.')
    })
    .refine((data) => data.password === data.password_confirmation, {
        path: ['password_confirmation'],
        message: 'Las contraseñas no coinciden.'
    });

export const resetPasswordResolver = zodResolver(resetPasswordSchema);
