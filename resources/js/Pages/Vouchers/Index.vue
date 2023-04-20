<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import BackButton from "@/Components/BackButton.vue";

import Button from "@/Components/PrimaryButton.vue";
import Link from "@/Components/NavLink.vue";
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
})

function destroy(id) {
    if (confirm("Are you sure you want to delete")) {
        router.delete(route('vouchers-destroy', id), {
            onSuccess: (response) => {
                Toast.fire({
                    icon: 'success',
                    title: 'Voucher deleted successfully.'
                });
            },
            onError: (response) => {
                Toast.fire({
                    icon: 'error',
                    title: 'Failed deleting Voucher.',
                    text: JSON.stringify(response.error),
                });
            }
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
</script>

<template>
    <AppLayout title="Vouchers">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <BackButton /> &nbsp; VOUCHERS
            </h2>
            {{ message }}
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div v-if="allowedToAdd" class="mb-2">
                                <span v-if="group" style="float: right;">GROUP: {{ group.name }}</span>
                                <!-- <span ref="voucherCreateBtn">
                                    <Link :href="route('vouchers-create')" style="display: none;">
                                    </Link>
                                </span> -->
                                <Button @click="checkLimit()">Add Voucher</Button>
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
