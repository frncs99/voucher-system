<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import BackButton from "@/Components/BackButton.vue";
import RefreshPage from "@/Components/RefreshButton.vue";
import TimeStamp from "@/Components/TimeStamp.vue";

import Button from "@/Components/PrimaryButton.vue";
import { router } from '@inertiajs/vue3';

import Swal from 'sweetalert2';

const props = defineProps({
    groupAdmins: {
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

function addAdmin() {
    let id = window.location.href.split("/").pop();
    router.get(route('groups-new-admin', id));
}

function assign(isDelete, id) {
    if (confirm("Are you sure you want to " + (isDelete ? "remove?" : "restore?"))) {
        axios.patch(
            route('groups-assign-admin', id)
        )
        .then((response) => {
            Toast.fire({
                icon: 'success',
                title: 'Group Admin ' + (isDelete ? "removed" : "restored") + ' successfully.',
            });

            router.get(window.location.href);
        }).catch(error => {
            Toast.fire({
                icon: 'error',
                title: 'Failed ' + (isDelete ? "removing" : "restoring") + ' Group Admin.',
                text: JSON.stringify(error.message),
            });
        });
    }
}
</script>

<template>
    <AppLayout title="Groups">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <BackButton /> &nbsp; GROUP ADMINS &nbsp;

                <span style="float: right;"><TimeStamp /> <RefreshPage /></span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="mb-2">
                                <span v-if="groupAdmins.data[0]" style="float: right;">GROUP: {{ groupAdmins.data[0].group_name ?? 'N/A' }}</span>
                                <Button @click="addAdmin()" class="mr-2">
                                    Add New Admin
                                </Button>
                            </div>
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                Admin Id
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                User
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Added Date
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="groupAdmins && groupAdmins.data.length > 0">
                                        <tr
                                            v-for="groupAdmin in groupAdmins.data"
                                            :key="groupAdmin.group_admin_id"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                        >
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ groupAdmin.group_admin_id }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ groupAdmin.name }} ({{ groupAdmin.email }})
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ groupAdmin.created_at }}
                                            </td>
                                            <td class="px-4 py-4">
                                                <Button v-if="groupAdmin.is_active == 0" class="bg-indigo-600 bg-opacity-75" @click="assign(false, groupAdmin.group_admin_id)">
                                                    Restore
                                                </Button>
                                                <Button v-else class="bg-red-600 bg-opacity-75" @click="assign(true, groupAdmin.group_admin_id)">
                                                    Remove
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
