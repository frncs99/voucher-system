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

const codeInput = ref(null);
const form = useForm({
    user_id: props.userId,
    code: '',
});

const props = defineProps({
    userId: {
        type: Int32Array
    },
});

const createVoucher = () => {
    form.post(route('vouchers-store'), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset();
            Toast.fire({
                icon: 'success',
                title: 'Voucher created successfully.'
            });
        },
        onError: (error) => {
            if (form.errors.code) {
                form.reset('code');
                codeInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <AppLayout title="Vouchers">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <BackButton /> &nbsp; CREATE VOUCHER &nbsp;

                <span style="float: right;"><TimeStamp /> <RefreshPage /></span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <FormSection @submitted="createVoucher">
                                <template #title>
                                    Create a New Voucher
                                </template>

                                <template #description>
                                    Code must be unique. Maximum of 10 Vouchers per User are allowed.
                                </template>

                                <template #form>
                                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel for="code" value="CODE" />
                                        <TextInput
                                            id="code"
                                            ref="codeInput"
                                            v-model="form.code"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="code"
                                        />
                                        <InputError :message="form.errors.code" class="mt-2" />
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
