import { z } from './zod';
import { zodResolver } from '@primevue/forms/resolvers/zod';

export const registerSchema = z
    .object({
        name: z
            .string({ required_error: 'El nombre es obligatorio.' })
            .min(2, { message: 'El nombre debe tener al menos 2 caracteres.' }),
        email: z
            .string({ required_error: 'El correo electrónico es obligatorio.' })
            .min(1, { message: 'El correo electrónico es obligatorio.' })
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

export const registerResolver = zodResolver(registerSchema);
