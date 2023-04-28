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

import { useForm, usePage, router } from '@inertiajs/vue3';
import { onMounted, computed } from "vue";

import Swal from 'sweetalert2';

const props = defineProps({
    groups: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    group_id: 0,
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

const page = usePage()
const user = computed(() => page.props.auth.user)

const checkIfNotAdmin = async () => {
    if (user.value.user_type == 'user') {
        router.get(route('vouchers-index', 0));
    }
}

onMounted(checkIfNotAdmin);

function filterGroup() {
    router.get(route('vouchers-index', form.group_id));
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" v-if="$page.props.auth.user.user_type != 'user'">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="mb-2">
                                <FormSection @submitted="filterGroup">
                                    <template #title>
                                        Filter by Group
                                    </template>

                                    <template #description>
                                        Select a Group first then click Next.
                                    </template>

                                    <template #form>
                                        <div class="col-span-6 sm:col-span-4">
                                            <InputLabel for="group_id" value="GROUP" />
                                            <select v-model="form.group_id" class="mt-2 block appearance-none w-full bg-white border text-slate text-sm py-3 px-4 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray">
                                                <option class="text-slate text-sm" value="0">ALL GROUPS</option>
                                                <option v-for="item in groups" :value="item.group_id" :key="item.group_id" class="text-slate text-sm">
                                                    {{ item.name }}
                                                </option>
                                            </select>

                                            <InputError :message="form.errors.group_id" class="mt-2" />
                                        </div>
                                    </template>

                                    <template #actions>
                                        <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                                            Wait.
                                        </ActionMessage>

                                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                            Next
                                        </PrimaryButton>
                                    </template>
                                </FormSection>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
