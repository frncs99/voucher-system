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

import { ref, reactive } from 'vue';
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
    users: {
        type: Object,
        default: () => ({}),
    },
});

const userIdInput = ref(null);
const form = useForm({
    user_id: '',
});

const check = reactive({
    currentGroup:'',
    belongToAGroup: false,
    allowsaveBtn: true,
});

const checkCurrentGroup = () => {
    axios.get(
        route('groups-member-current-group', form.user_id)
    ).then(response => {
        if (response.data[0]) {
            check.currentGroup = response.data[0];
            check.belongToAGroup = true;
        } else {
            check.currentGroup = '';
            check.belongToAGroup = false;
        }

        if (response.data[0] == props.group.name) {
            check.allowsaveBtn = false;
        } else check.allowsaveBtn = true;
    }).catch(error => {
        check.currentGroup = '';
        check.belongToAGroup = false;

        Toast.fire({
            icon: 'error',
            title: 'Error fetching user info. Please reselect.',
        });

        check.allowsaveBtn = false;
    });
};

const createMember = () => {
    form.post(route('groups-member-add', props.group.group_id), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset();
            Toast.fire({
                icon: 'success',
                title: 'Member created successfully.'
            });
        },
        onError: (error) => {
            console.log(error);
            if (form.errors.user_id) {
                form.reset('user_id');
                userIdInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <AppLayout title="Groups">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <BackButton /> &nbsp; ADD NEW MEMBER &nbsp;

                <span style="float: right;"><TimeStamp /> <RefreshPage /></span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <FormSection @submitted="createMember">
                                <template #title>
                                    Add a New Member
                                </template>

                                <template #description>
                                    Select a User to be assigned to the Group <b>{{ group.name }}</b>.

                                    <p class="mt-2">
                                        Note: User can only belong to one group, assigning them to "{{ group.name }}" will result of their eviction from their current group.
                                    </p>

                                    <p class="mt-2" v-if="check.belongToAGroup">
                                        <b v-if="check.currentGroup != group.name">The selected user will be evicted from {{ check.currentGroup }}.</b>
                                        <b v-else>The selected user is already a member of the {{ group.name }}</b>
                                    </p>
                                </template>

                                <template #form>
                                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel for="user_admin_id" value="NAME" />
                                        <select @change="checkCurrentGroup()" v-model="form.user_id" class="mt-2 block appearance-none w-full bg-white border text-slate text-sm py-3 px-4 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray">
                                            <option v-for="item in users" :value="item.id" :key="item.id" class="text-slate text-sm">
                                                {{ item.name }} ({{ item.email }})
                                            </option>
                                        </select>

                                        <InputError :message="form.errors.user_id" class="mt-2" />
                                    </div>
                                </template>

                                <template #actions>
                                    <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                                        Saved.
                                    </ActionMessage>

                                    <PrimaryButton v-if="check.allowsaveBtn" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                        Save
                                    </PrimaryButton>
                                    <PrimaryButton v-else class="opacity-25" :disabled="true">
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
