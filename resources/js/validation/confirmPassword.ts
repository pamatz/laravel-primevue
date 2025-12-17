import { z } from './zod';
import { zodResolver } from '@primevue/forms/resolvers/zod';

export const confirmPasswordSchema = z.object({
    password: z
        .string({ required_error: 'La contraseña es obligatoria.' })
        .min(1, { message: 'La contraseña es obligatoria.' }),
});

export const confirmPasswordResolver = zodResolver(confirmPasswordSchema);
