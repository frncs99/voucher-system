<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import BackButton from "@/Components/BackButton.vue";
import RefreshPage from "@/Components/RefreshButton.vue";
import TimeStamp from "@/Components/TimeStamp.vue";
import ApplicationLogo from '@/Components/ApplicationLogoVertical.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
    admin: {
        type: Boolean
    },
    voucherCount: {
        default: null,
    },
    ownGroups: {
        type: Array
    },
    userCount: {
        default: null,
    },
    adminCount: {
        default: null,
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="text-gray-800 leading-tight">
                <span class="font-semibold text-xl"><BackButton /> &nbsp; DASHBOARD</span>
                
                <span style="float: right;">
                    Welcome to VoucherSystem Web Application!<br />
                    <span style="float: right;"><TimeStamp /> <RefreshPage /></span>
                </span>
            </h2>
            
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div>
                        <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
                            <div v-if="voucherCount">
                                <div class="flex items-center">
                                    <h1 class="mb-1 text-8xl font-semibold text-gray-900">
                                        {{ voucherCount }}
                                    </h1>
                                    <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                        <p>
                                            <span v-if="!admin">You have</span>
                                            <span v-else>Number of</span>
                                            <br />
                                            VOUCHERS
                                        </p>
                                    </h2>
                                </div>

                                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                    View, add, and delete vouchers here. Maximum of 10 Vouchers per User.
                                </p>

                                <p class="mt-4 text-sm">
                                    <NavLink :href="route('vouchers-index')" class="inline-flex items-center font-semibold text-indigo-700">
                                        Explore the Vouchers Menu

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-indigo-500">
                                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                        </svg>
                                    </NavLink>
                                </p>
                            </div>

                            <div v-if="ownGroups">
                                <div class="flex items-center">
                                    <ApplicationLogo v-if="!admin" class="block h-12 w-auto" />
                                        <span class="text-3xl" v-if="!admin">
                                            {{ ownGroups[0] }}
                                        </span>
                                        <h1 v-if="admin" class="mb-1 text-8xl font-semibold text-gray-900">
                                            {{ ownGroups.length }}
                                        </h1>
                                        <h2 v-if="admin" class="ml-5 text-xl font-semibold text-gray-900">
                                            Number of <br />GROUPS
                                        </h2>
                                </div>

                                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                    <span v-if="!admin">
                                        You belong to Group named {{ ownGroups[0] }}. <br />Contact support if assistance is needed.
                                    </span>
                                    <span v-else>
                                        View and edit groups here. As Admin, you have privilege to this feature.
                                    </span>
                                </p>

                                <p v-if="admin" class="mt-4 text-sm">
                                    <NavLink :href="route('groups-index')" class="inline-flex items-center font-semibold text-indigo-700">
                                        Explore the Groups Menu

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-indigo-500">
                                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                        </svg>
                                    </NavLink>
                                </p>
                            </div>

                            <div v-if="userCount && admin">
                                <div class="flex items-center">
                                    <h1 class="mb-1 text-8xl font-semibold text-gray-900">
                                        {{ userCount }}
                                    </h1>
                                    <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                        <p>
                                            Number of
                                            <br />
                                            USERS
                                        </p>
                                    </h2>
                                </div>

                                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                    Assign and remove users from Group here. As Admin, you have privilege to this feature.
                                </p>

                                <p class="mt-4 text-sm">
                                    <NavLink :href="route('dashboard')" class="inline-flex items-center font-semibold text-indigo-700">
                                        Explore the Users Menu

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-indigo-500">
                                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                        </svg>
                                    </NavLink>
                                </p>
                            </div>

                            <div v-if="adminCount && admin">
                                <div class="flex items-center">
                                    <h1 class="mb-1 text-8xl font-semibold text-gray-900">
                                        {{ adminCount }}
                                    </h1>
                                    <h2 class="ml-3 text-xl font-semibold text-gray-900">
                                        <p>
                                            Number of
                                            <br />
                                            GROUP ADMIN
                                        </p>
                                    </h2>
                                </div>

                                <p class="mt-4 text-gray-500 text-sm leading-relaxed">
                                    View and edit group admins here. As Super Admin, you have privilege to this feature.
                                </p>

                                <p class="mt-4 text-sm">
                                    <NavLink :href="route('dashboard')" class="inline-flex items-center font-semibold text-indigo-700">
                                        Explore the Group Admin Menu

                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ml-1 w-5 h-5 fill-indigo-500">
                                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                                        </svg>
                                    </NavLink>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
