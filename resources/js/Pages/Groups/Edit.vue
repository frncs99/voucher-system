<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import BackButton from "@/Components/BackButton.vue";
import RefreshPage from "@/Components/RefreshButton.vue";
import TimeStamp from "@/Components/TimeStamp.vue";

import FormSection from '@/Components/FormSection.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
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

const nameInput = ref(null);
const form = useForm({
    group_id: props.group.group_id,
    name: props.group.name,
});

const props = defineProps({
    groupId: {
        type: Int32Array
    },
    group: {
        type: Object,
        default: () => ({}),
    },
});

const editGroup = () => {
    form.patch(route('groups-update', form.group_id), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset();
            Toast.fire({
                icon: 'success',
                title: 'Group updated successfully.'
            });
        },
        onError: (error) => {
            if (form.errors.name) {
                form.reset('name');
                nameInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <AppLayout title="Groups">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <BackButton /> &nbsp; EDIT GROUP &nbsp;

                <span style="float: right;"><TimeStamp /> <RefreshPage /></span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <FormSection @submitted="editGroup">
                                <template #title>
                                    Edit Group #{{ group.group_id }}
                                </template>

                                <template #description>
                                    Edit the name of the group.
                                </template>

                                <template #form>
                                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel for="name" value="NAME" />
                                        <TextInput
                                            id="name"
                                            ref="nameInput"
                                            v-model="form.name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="name"
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
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
