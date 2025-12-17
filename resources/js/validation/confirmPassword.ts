import { z } from './zod';
import { zodResolver } from '@primevue/forms/resolvers/zod';

const confirmPasswordSchema = z.object({
    password: z
        .string({ required_error: 'Ingresa tu contraseña.' })
        .min(1, 'Ingresa tu contraseña.'),
});

export const confirmPasswordResolver = zodResolver(confirmPasswordSchema);
