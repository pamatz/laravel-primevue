import { z } from './zod';
import { zodResolver } from '@primevue/forms/resolvers/zod';

export const resetPasswordSchema = z
    .object({
        email: z
            .string({ required_error: 'El correo electrónico es obligatorio.' })
            .email({ message: 'Ingresa un correo electrónico válido.' }),
        password: z
            .string({ required_error: 'La contraseña es obligatoria.' })
            .min(8, {
                message: 'La contraseña debe tener al menos 8 caracteres.',
            }),
        password_confirmation: z
            .string({
                required_error: 'La confirmación de contraseña es obligatoria.',
            })
            .min(1, {
                message: 'La confirmación de contraseña es obligatoria.',
            }),
    })
    .superRefine((data, ctx) => {
        if (data.password !== data.password_confirmation) {
            ctx.addIssue({
                code: z.ZodIssueCode.custom,
                path: ['password_confirmation'],
                message: 'Las contraseñas no coinciden.',
            });
        }
    });

export const resetPasswordResolver = zodResolver(resetPasswordSchema);
