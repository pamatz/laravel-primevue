import { zodResolver } from '@primevue/forms/resolvers/zod';
import { z } from './zod';

const forgotPasswordSchema = z.object({
    email: z.string('Ingresa tu correo electrónico.').pipe(z.email())
});

export const forgotPasswordResolver = zodResolver(forgotPasswordSchema);
