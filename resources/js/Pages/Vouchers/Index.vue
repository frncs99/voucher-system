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
    vouchers: {
        type: Object,
        default: () => ({}),
    },
    allowedToAdd: {
        type: Boolean
    },
    addIsOnLimit: {
        type: Boolean
    },
    allowedToDelete: {
        type: Boolean
    },
    showOwner: {
        type: Boolean
    },
    showGroup: {
        type: Boolean
    },
    group: {
        type: Object,
        default: () => ({}),
    },
    userId: {
        type: Int32Array
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

function destroy(id) {
    if (confirm("Are you sure you want to delete?")) {
        axios.delete(
            route('vouchers-destroy', id)
        )
        .then((response) => {
            Toast.fire({
                icon: 'success',
                title: 'Voucher deleted successfully.'
            });

            router.get(window.location.href);
        }).catch(error => {
            Toast.fire({
                icon: 'error',
                title: 'Failed deleting Voucher.',
                text: JSON.stringify(error.message),
            });
        });
    }
}

function checkLimit() {
    if (props.addIsOnLimit) {
        Toast.fire({
            icon: 'error',
            title: 'Failed adding Voucher.',
            text: 'Reached 10 vouchers limit per user.',
        });

        return;
    }

    router.get(route('vouchers-create'));
}

function exportToExcel() {
    var loading = document.getElementById('exportLoadingIcon');
    loading.style = "display: ";
    let filename = '';
    fetch(route('vouchers-export', { id: props.userId }))
        .then((res) => {
            let header = res.headers.get('Content-Disposition');
            let parts = header.split(';');
            filename = parts[1].split('=')[1];
            return res.blob();
        })
        .then((blob) => {
            const file = window.URL.createObjectURL(blob);
            var a = document.createElement("a");
            document.body.appendChild(a);
            a.style = "display: none";
            a.href = file;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(blob);

            Toast.fire({
                icon: 'success',
                title: 'Vouchers exported successfully.'
            });
            
            loading.style = "display: none";
        });
}
</script>

<template>
    <AppLayout title="Vouchers">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <BackButton /> &nbsp; VOUCHERS &nbsp;

                <span style="float: right;"><TimeStamp /> <RefreshPage /></span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div v-if="allowedToAdd" class="mb-2">
                                <span v-if="group" style="float: right;">GROUP: {{ group.name }}</span>
                                <Button class="mr-2" @click="checkLimit()">Add Voucher</Button>
                            </div>
                            <div v-else class="mb-2">
                                <Button class="mr-2" @click="exportToExcel()">
                                    Export
                                    <div style="display: none;" id="exportLoadingIcon" role="status">
                                        <svg aria-hidden="true" class="inline w-4 h-4 ml-2 text-gray-200 animate-spin dark:text-gray-600 fill-gray-600 dark:fill-gray-300" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                                            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
                                        </svg>
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </Button>
                            </div>
                            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th v-if="showGroup" scope="col" class="px-6 py-3">
                                                Group Name
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Code
                                            </th>
                                            <th v-if="showOwner" scope="col" class="px-6 py-3">
                                                Owner
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Added Date
                                            </th>
                                            <th v-if="allowedToDelete" scope="col" class="px-6 py-3">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="vouchers && vouchers.data.length > 0">
                                        <tr
                                            v-for="voucher in vouchers.data"
                                            :key="voucher.voucher_id"
                                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                                        >
                                            <th v-if="showGroup" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                <span v-if="voucher.is_active == 1">{{ voucher.group_name }}</span>
                                                <span v-else>NO GROUP</span>
                                            </th>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ voucher.code }}
                                            </td>
                                            <td v-if="showOwner" class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ voucher.name }} ({{ voucher.email }})
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap">
                                                {{ voucher.created_at }}
                                            </td>
                                            <td v-if="allowedToDelete" class="px-6 py-4">
                                                <Button class="bg-red-600" @click="destroy(voucher.voucher_id)">
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
                            <Pagination :links="vouchers.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
