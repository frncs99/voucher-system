<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import BackButton from "@/Components/BackButton.vue";
import RefreshPage from "@/Components/RefreshButton.vue";
import TimeStamp from "@/Components/TimeStamp.vue";

import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import ActionMessage from '@/Components/ActionMessage.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

import Swal from 'sweetalert2';

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

const props = defineProps({
    group: {
        type: Object,
        default: () => ({}),
    },
    admins: {
        type: Object,
        default: () => ({}),
    },
});

const userAdminIdInput = ref(null);
const form = useForm({
    user_admin_id: '',
});

const createAdmin = () => {
    form.post(route('groups-admin-add', props.group.group_id), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset();
            Toast.fire({
                icon: 'success',
                title: 'Group created successfully.'
            });
        },
        onError: (error) => {
            if (form.errors.user_admin_id) {
                form.reset('user_admin_id');
                userAdminIdInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <AppLayout title="Groups">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <BackButton /> &nbsp; CREATE NEW ADMIN &nbsp;

                <span style="float: right;"><TimeStamp /> <RefreshPage /></span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <FormSection @submitted="createAdmin">
                                <template #title>
                                    Create a New Admin
                                </template>

                                <template #description>
                                    Select Admin to be assigned to the Group named: <b>{{ group.name }}</b>.
                                </template>

                                <template #form>
                                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel for="user_admin_id" value="NAME" />
                                        <select v-model="form.user_admin_id" class="mt-2 block appearance-none w-full bg-white border text-slate text-sm py-3 px-4 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray">
                                            <option v-for="item in admins" :value="item.id" :key="item.id" class="text-slate text-sm">
                                                {{ item.name }} ({{ item.email }})
                                            </option>
                                        </select>

                                        <InputError :message="form.errors.user_admin_id" class="mt-2" />
                                    </div>
                                </template>

                                <template #actions>
                                    <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                                        Saved.
                                    </ActionMessage>

                                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Save
                                    </PrimaryButton>
                                </template>
                            </FormSection>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
