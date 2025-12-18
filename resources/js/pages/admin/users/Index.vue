<script lang="ts" setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Button } from 'primevue';
import { computed, ref } from 'vue';

interface Role {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    role_id: number | null;
    role?: Role | null;
}

interface PaginatedUsers {
    data: User[];
}

const props = defineProps<{
    users: PaginatedUsers;
    roles: Role[];
}>();

const dialogVisible = ref(false);
const isEditing = ref(false);
const currentId = ref<number | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role_id: null as number | null,
});

const openCreate = (): void => {
    isEditing.value = false;
    currentId.value = null;
    form.reset({
        name: '',
        email: '',
        password: '',
        role_id: null,
    });
    form.clearErrors();
    dialogVisible.value = true;
};

const openEdit = (user: User): void => {
    isEditing.value = true;
    currentId.value = user.id;
    form.reset({
        name: user.name,
        email: user.email,
        password: '',
        role_id: user.role_id,
    });
    form.clearErrors();
    dialogVisible.value = true;
};

const closeDialog = (): void => {
    dialogVisible.value = false;
};

const onSubmit = (): void => {
    const payload = {
        ...form.data(),
        role_id: form.role_id,
    };

    if (isEditing.value && currentId.value !== null) {
        form.put(`/admin/users/${currentId.value}`, {
            data: payload,
            onSuccess: () => {
                dialogVisible.value = false;
            },
        });

        return;
    }

    form.post('/admin/users', {
        data: payload,
        onSuccess: () => {
            dialogVisible.value = false;
        },
    });
};

const remove = (user: User): void => {
    if (!confirm(`¿Eliminar el usuario "${user.name}"?`)) {
        return;
    }

    router.delete(`/admin/users/${user.id}`);
};

const breadcrumbItems = computed(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Usuarios', href: '/admin/users' },
]);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems" title="Usuarios">
        <Head title="Usuarios" />

        <section class="space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-semibold">Usuarios</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Administra los usuarios del sistema y sus roles.
                    </p>
                </div>

                <Button
                    icon="pi pi-plus"
                    label="Nuevo usuario"
                    size="small"
                    @click="openCreate"
                />
            </div>

            <div
                class="card overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900"
            >
                <DataTable
                    :rows="15"
                    :value="props.users.data"
                    class="text-sm"
                    dataKey="id"
                    responsiveLayout="stack"
                >
                    <Column field="name" header="Nombre" sortable />
                    <Column field="email" header="Correo" />
                    <Column header="Rol">
                        <template #body="{ data }">
                            {{ data.role ? data.role.name : '—' }}
                        </template>
                    </Column>
                    <Column header="Acciones">
                        <template #body="{ data }">
                            <div class="flex items-center justify-end gap-2">
                                <Button
                                    icon="pi pi-pencil"
                                    rounded
                                    severity="secondary"
                                    text
                                    type="button"
                                    @click="openEdit(data)"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    rounded
                                    severity="danger"
                                    text
                                    type="button"
                                    @click="remove(data)"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </section>

        <Dialog
            v-model:visible="dialogVisible"
            :header="isEditing ? 'Editar usuario' : 'Nuevo usuario'"
            :modal="true"
            :style="{ width: '32rem' }"
        >
            <form class="flex flex-col gap-4" @submit.prevent="onSubmit">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="name">Nombre</label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        autocomplete="off"
                        class="w-full"
                    />
                    <small v-if="form.errors.name" class="text-xs text-red-500">
                        {{ form.errors.name }}
                    </small>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="email"
                        >Correo</label
                    >
                    <InputText
                        id="email"
                        v-model="form.email"
                        autocomplete="off"
                        class="w-full"
                        type="email"
                    />
                    <small
                        v-if="form.errors.email"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.email }}
                    </small>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="password">
                        Contraseña
                        <span
                            v-if="isEditing"
                            class="text-xs text-neutral-500 dark:text-neutral-400"
                        >
                            (déjalo vacío para no cambiarla)
                        </span>
                    </label>
                    <Password
                        id="password"
                        v-model="form.password"
                        :feedback="false"
                        class="w-full"
                        inputClass="w-full"
                        toggleMask
                    />
                    <small
                        v-if="form.errors.password"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.password }}
                    </small>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="role_id">Rol</label>
                    <Dropdown
                        id="role_id"
                        v-model="form.role_id"
                        :options="props.roles"
                        :showClear="true"
                        class="w-full"
                        optionLabel="name"
                        optionValue="id"
                        placeholder="Selecciona un rol"
                    />
                    <small
                        v-if="form.errors.role_id"
                        class="text-xs text-red-500"
                    >
                        {{ form.errors.role_id }}
                    </small>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <Button
                        label="Cancelar"
                        severity="secondary"
                        text
                        type="button"
                        @click="closeDialog"
                    />
                    <Button
                        :label="isEditing ? 'Guardar cambios' : 'Crear usuario'"
                        :loading="form.processing"
                        type="submit"
                    />
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
