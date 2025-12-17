<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface Permission {
    id: number;
    name: string;
    key: string;
    group: string | null;
}

interface Role {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    is_superadmin: boolean;
    permissions: Permission[];
}

interface PaginatedRoles {
    data: Role[];
}

const props = defineProps<{
    roles: PaginatedRoles;
    permissions: Permission[];
}>();

const dialogVisible = ref(false);
const isEditing = ref(false);
const currentId = ref<number | null>(null);

const form = useForm({
    name: '',
    slug: '',
    description: '',
    is_superadmin: false,
    permissions: [] as number[],
});

const openCreate = (): void => {
    isEditing.value = false;
    currentId.value = null;
    form.reset({
        name: '',
        slug: '',
        description: '',
        is_superadmin: false,
        permissions: [],
    });
    form.clearErrors();
    dialogVisible.value = true;
};

const openEdit = (role: Role): void => {
    isEditing.value = true;
    currentId.value = role.id;
    form.reset({
        name: role.name,
        slug: role.slug,
        description: role.description ?? '',
        is_superadmin: role.is_superadmin,
        permissions: role.permissions.map((p) => p.id),
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
        permissions: form.permissions,
    };

    if (isEditing.value && currentId.value !== null) {
        form.put(`/admin/roles/${currentId.value}`, {
            data: payload,
            onSuccess: () => {
                dialogVisible.value = false;
            },
        });

        return;
    }

    form.post('/admin/roles', {
        data: payload,
        onSuccess: () => {
            dialogVisible.value = false;
        },
    });
};

const remove = (role: Role): void => {
    if (!confirm(`¿Eliminar el rol "${role.name}"?`)) {
        return;
    }

    router.delete(`/admin/roles/${role.id}`);
};

const breadcrumbItems = computed(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Roles', href: '/admin/roles' },
]);
</script>

<template>
    <AppLayout title="Roles" :breadcrumbs="breadcrumbItems">
        <Head title="Roles" />

        <section class="space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-semibold">Roles</h1>
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                        Administra los roles del sistema y los permisos asociados a cada uno.
                    </p>
                </div>

                <Button icon="pi pi-plus" label="Nuevo rol" size="small" @click="openCreate" />
            </div>

            <div class="card overflow-hidden rounded-lg border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
                <DataTable
                    :value="props.roles.data"
                    dataKey="id"
                    :rows="10"
                    responsiveLayout="stack"
                    class="text-sm"
                >
                    <Column field="name" header="Nombre" sortable />
                    <Column field="slug" header="Slug" />
                    <Column field="is_superadmin" header="Superadmin">
                        <template #body="{ data }">
                            {{ data.is_superadmin ? 'Sí' : 'No' }}
                        </template>
                    </Column>
                    <Column header="Permisos">
                        <template #body="{ data }">
                            {{ data.permissions.map((p: any) => p.key).join(', ') }}
                        </template>
                    </Column>
                    <Column header="Acciones">
                        <template #body="{ data }">
                            <div class="flex items-center justify-end gap-2">
                                <Button
                                    icon="pi pi-pencil"
                                    rounded
                                    text
                                    type="button"
                                    severity="secondary"
                                    @click="openEdit(data)"
                                />
                                <Button
                                    icon="pi pi-trash"
                                    rounded
                                    text
                                    type="button"
                                    severity="danger"
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
            :modal="true"
            :style="{ width: '36rem' }"
            :header="isEditing ? 'Editar rol' : 'Nuevo rol'"
        >
            <form class="flex flex-col gap-4" @submit.prevent="onSubmit">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="name">Nombre</label>
                    <InputText
                        id="name"
                        v-model="form.name"
                        class="w-full"
                        autocomplete="off"
                    />
                    <small v-if="form.errors.name" class="text-xs text-red-500">
                        {{ form.errors.name }}
                    </small>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="slug">Slug</label>
                    <InputText
                        id="slug"
                        v-model="form.slug"
                        class="w-full"
                        autocomplete="off"
                        placeholder="Opcional, se genera a partir del nombre si se deja vacío"
                    />
                    <small v-if="form.errors.slug" class="text-xs text-red-500">
                        {{ form.errors.slug }}
                    </small>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="description">Descripción</label>
                    <Textarea
                        id="description"
                        v-model="form.description"
                        rows="3"
                        autoResize
                        class="w-full"
                    />
                    <small v-if="form.errors.description" class="text-xs text-red-500">
                        {{ form.errors.description }}
                    </small>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <Checkbox
                            id="is_superadmin"
                            v-model="form.is_superadmin"
                            :binary="true"
                        />
                        <label class="text-sm font-medium" for="is_superadmin">
                            Rol superadministrador (acceso total)
                        </label>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="permissions">Permisos</label>
                    <MultiSelect
                        id="permissions"
                        v-model="form.permissions"
                        :options="props.permissions"
                        optionLabel="name"
                        optionValue="id"
                        display="chip"
                        class="w-full"
                        placeholder="Selecciona uno o más permisos"
                    />
                    <small v-if="form.errors.permissions" class="text-xs text-red-500">
                        {{ form.errors.permissions }}
                    </small>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <Button
                        type="button"
                        label="Cancelar"
                        severity="secondary"
                        text
                        @click="closeDialog"
                    />
                    <Button
                        type="submit"
                        :label="isEditing ? 'Guardar cambios' : 'Crear rol'"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
