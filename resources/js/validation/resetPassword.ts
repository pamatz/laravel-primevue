import { z } from './zod';
import { zodResolver } from '@primevue/forms/resolvers/zod';

const resetPasswordSchema = z
    .object({
        email: z
            .string({ required_error: 'Ingresa tu correo electrónico.' })
            .pipe(z.email()),
        password: z
            .string({ required_error: 'Ingresa una nueva contraseña.' })
            .min(8, 'La contraseña debe tener al menos 8 caracteres.'),
        password_confirmation: z
            .string({ required_error: 'Confirma tu contraseña.' })
            .min(1, 'Confirma tu contraseña.'),
    })
    .refine((data) => data.password === data.password_confirmation, {
        path: ['password_confirmation'],
        message: 'Las contraseñas no coinciden.',
    });

export const resetPasswordResolver = zodResolver(resetPasswordSchema);
