<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, index, update } from '@/routes/admin/permissions';
import { Head, useForm } from '@inertiajs/vue3';
import { Button, Column, ConfirmDialog, DataTable, Dialog, InputText, Textarea } from 'primevue';
import { useConfirm } from 'primevue/useconfirm';
import { computed, ref } from 'vue';

interface Permission {
    id: number;
    name: string;
    key: string;
    group: string | null;
    description: string | null;
}

interface PaginatedPermissions {
    data: Permission[];
}

const props = defineProps<{
    permissions: PaginatedPermissions;
}>();

const dialogVisible = ref(false);
const isEditing = ref(false);
const currentId = ref<number | null>(null);

const form = useForm({
    name: '',
    key: '',
    group: '',
    description: ''
});

const openCreate = (): void => {
    isEditing.value = false;
    currentId.value = null;
    form.reset();
    form.clearErrors();
    dialogVisible.value = true;
};

const openEdit = (permission: Permission): void => {
    isEditing.value = true;
    currentId.value = permission.id;
    form.key = permission.key;
    form.name = permission.name;
    form.group = permission.group ?? '';
    form.description = permission.description ?? '';
    form.clearErrors();
    dialogVisible.value = true;
};

const closeDialog = (): void => {
    dialogVisible.value = false;
};

const onSubmit = (): void => {
    if (isEditing.value && currentId.value !== null) {
        form.submit(update(currentId.value), {
            onSuccess: () => {
                dialogVisible.value = false;
            }
        });

        return;
    }

    form.submit(index(), {
        onSuccess: () => {
            dialogVisible.value = false;
        }
    });
};

const confirm = useConfirm();

const remove = (permission: Permission): void => {
    confirm.require({
        message: `¿Eliminar el permiso "${permission.name}"?`,
        header: 'Confirmación',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancelar',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Eliminar'
        },
        accept: () => {
            form.delete(destroy(permission.id).url);
        },
        reject: () => {

        }
    });
};

const breadcrumbItems = computed(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Permisos', href: '/admin/permissions' }
]);
</script>

<template>
    <AppLayout title="Permisos" :breadcrumbs="breadcrumbItems">
        <Head title="Permisos" />
        <ConfirmDialog></ConfirmDialog>

        <section class="space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-lg font-semibold">Permisos</h1>
                    <p class="text-sm text-surface-500 dark:text-surface-400">
                        Administra los permisos que controlan el acceso a las distintas secciones.
                    </p>
                </div>

                <Button icon="pi pi-plus" label="Nuevo permiso" size="small" @click="openCreate" />
            </div>

            <div
                class="card overflow-hidden rounded-lg border border-surface-200 bg-surface-0 dark:border-surface-800 dark:bg-surface-900"
            >
                <DataTable
                    :value="props.permissions.data"
                    dataKey="id"
                    :rows="15"
                    responsiveLayout="stack"
                    class="text-sm"
                >
                    <Column field="name" header="Nombre" sortable />
                    <Column field="key" header="Clave" sortable />
                    <Column field="group" header="Grupo" />
                    <Column field="description" header="Descripción" />
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
            :style="{ width: '32rem' }"
            :header="isEditing ? 'Editar permiso' : 'Nuevo permiso'"
        >
            <form class="flex flex-col gap-4" @submit.prevent="onSubmit">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="name">Nombre</label>
                    <InputText id="name" v-model="form.name" class="w-full" autocomplete="off" />
                    <small v-if="form.errors.name" class="text-xs text-red-500">
                        {{ form.errors.name }}
                    </small>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="key">Clave</label>
                    <InputText id="key" v-model="form.key" class="w-full" autocomplete="off" />
                    <small class="text-xs text-surface-500 dark:text-surface-400">
                        Ejemplo: <code>users.view</code>, <code>roles.create</code>.
                    </small>
                    <small v-if="form.errors.key" class="text-xs text-red-500">
                        {{ form.errors.key }}
                    </small>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="group">Grupo</label>
                    <InputText
                        id="group"
                        v-model="form.group"
                        class="w-full"
                        placeholder="Opcional, para agrupar en el menú"
                        autocomplete="off"
                    />
                    <small v-if="form.errors.group" class="text-xs text-red-500">
                        {{ form.errors.group }}
                    </small>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-sm font-medium" for="description">Descripción</label>
                    <Textarea id="description" v-model="form.description" rows="3" autoResize
                              class="w-full" />
                    <small v-if="form.errors.description" class="text-xs text-red-500">
                        {{ form.errors.description }}
                    </small>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    <Button type="button" label="Cancelar" severity="secondary" text @click="closeDialog" />
                    <Button
                        type="submit"
                        :label="isEditing ? 'Guardar cambios' : 'Crear permiso'"
                        :loading="form.processing"
                    />
                </div>
            </form>
        </Dialog>
    </AppLayout>
</template>
