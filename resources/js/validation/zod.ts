import { z } from 'zod';
import { es } from 'zod/locales';

// Configuración global de Zod para usar mensajes de validación en español
z.config(es());

export { z };
