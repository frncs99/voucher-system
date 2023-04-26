<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import BackButton from "@/Components/BackButton.vue";
import RefreshPage from "@/Components/RefreshButton.vue";
import TimeStamp from "@/Components/TimeStamp.vue";

import Button from "@/Components/PrimaryButton.vue";
import Pagination from "@/Components/Pagination.vue";
import { router } from '@inertiajs/vue3';

import Swal from 'sweetalert2';

const props = defineProps({
    groups: {
        type: Object,
        default: () => ({}),
    },
});

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

function addGroup() {
    router.get(route('groups-create'));
}

function assignAdmin(id) {
    router.get(route('groups-admin', id));
}

function assignMembers(id) {
    router.get(route('group-member', id));
}

function edit(id) {
    router.get(route('groups-edit', id));
}

function destroy(isDelete, id) {
    if (confirm("Are you sure you want to " + (isDelete ? "delete?" : "restore?"))) {
        router.delete(route('groups-destroy', id), {
            onSuccess: (response) => {
                Toast.fire({
                    icon: 'success',
                    title: 'Group ' + (isDelete ? "deleted" : "restored") + ' successfully.',
                });
            },
            onError: (response) => {
                Toast.fire({
                    icon: 'error',
                    title: 'Failed deleting Group.',
                    text: JSON.stringify(response.error),
                });
            }
        });
    }
}
</script>

<template>
    <AppLayout title="Groups">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <BackButton /> &nbsp; GROUPS &nbsp;

                <span style="float: right;"><TimeStamp /> <RefreshPage /></span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="mb-2">
                                <Button @click="addGroup()" class="mr-2">
                                    Add Group
                                </Button>
                            </div>
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Id
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Added Date
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="groups && groups.data.length > 0">
                                        <tr
                                            v-for="group in groups.data"
                                            :key="group.group_id"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                        >
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ group.group_id }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ group.name }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ group.created_at }}
                                            </td>
                                            <td class="px-4 py-4">
                                                <Button v-if="!group.deleted_at" class="text-white bg-white bg-opacity-5 hover:bg-white hover:bg-opacity-25" @click="assignAdmin(group.group_id)">
                                                    Admin
                                                </Button>
                                                <Button disabled v-else class="cursor-not-allowed hover:text-white line-through text-white bg-white bg-opacity-5 hover:bg-white hover:bg-opacity-25">
                                                    Admin
                                                </Button>
                                                &nbsp;
                                                <Button v-if="!group.deleted_at" class="text-white bg-white bg-opacity-25" @click="assignMembers(group.group_id)">
                                                    Members
                                                </Button>
                                                <Button disabled v-else class="cursor-not-allowed line-through text-white bg-white bg-opacity-25">
                                                    Members
                                                </Button>
                                                &nbsp;
                                                <Button v-if="!group.deleted_at" class="bg-indigo-500" @click="edit(group.group_id)">
                                                    Edit
                                                </Button>
                                                <Button disabled v-else class="cursor-not-allowed line-through bg-indigo-500">
                                                    Edit
                                                </Button>
                                                &nbsp;
                                                <Button v-if="group.deleted_at" class="bg-red-600" @click="destroy(false, group.group_id)">
                                                    Restore
                                                </Button>
                                                <Button v-else class="bg-red-600 bg-opacity-75" @click="destroy(true, group.group_id)">
                                                    Delete
                                                </Button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tbody v-else>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <th colspan="5" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                NO DATA FOUND
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <Pagination :links="groups.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
