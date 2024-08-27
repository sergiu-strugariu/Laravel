<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import axios from "axios";
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center text-4xl">Import Export Data</div>
                </div>

                <div class="flex justify-between items-center mt-5">
                    <a :href="route('import')" class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-400">
                            <div class="p-6 text-gray-900 text-center text-xl">Import Data</div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
                    <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg p-6">

                        <div class="relative overflow-x-auto rounded-lg">

                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            ID
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Phone
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Website
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Employes
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Remote Employes
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="company in companies"
                                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                        <th class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ company.id }}
                                        </th>
                                        <td class="px-6 py-4">
                                            {{ company.name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ company.email }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ company.phone }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ company.website }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ company.employes }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ company.remote_employes }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div v-if="company.status">
                                                <h1 class="text-green-500 font-bold">ONLINE</h1>
                                            </div>
                                            <div v-else>
                                                <h1 class="text-red-500 font-bold">OFFLINE</h1>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="text-right">
                                <PrimaryButton class="px-10 mt-5">
                                    <a :href="route('export.company')">
                                        Export
                                    </a>
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<script>
export default {
    props: {
        companies: Array
    },
}
</script>

