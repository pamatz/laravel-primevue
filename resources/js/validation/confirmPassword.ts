import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from './zod';

const confirmPasswordSchema = z.object({
    password: z
        .string({ error: 'Ingresa tu contraseña.' })
        .min(1, 'Ingresa tu contraseña.')
});

export const confirmPasswordResolver = zodResolver(confirmPasswordSchema);
