import { z } from './zod';
import { zodResolver } from '@primevue/forms/resolvers/zod';

export const forgotPasswordSchema = z.object({
    email: z
        .string({ required_error: 'El correo electrónico es obligatorio.' })
        .min(1, { message: 'El correo electrónico es obligatorio.' })
        .email({ message: 'Ingresa un correo electrónico válido.' }),
});

export const forgotPasswordResolver = zodResolver(forgotPasswordSchema);
