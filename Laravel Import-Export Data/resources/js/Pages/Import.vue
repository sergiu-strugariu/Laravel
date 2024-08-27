<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center text-4xl">Import CSV</div>
                </div>

                <div class="flex justify-between items-center mt-5">
                    <a :href="route('export')" class="max-w-3xl mx-auto sm:px-6 lg:px-8 ">
                        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-400">
                            <div class="p-6 text-gray-900 text-center text-xl">Export Data</div>
                        </div>
                    </a>
                </div>
                <div class="flex">
                    <div class="max-w-4xl w-full mx-auto sm:px-6 lg:px-8 mt-4">
                        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                            <form @submit.prevent="importCompany" class="px-6 flex justify-center items-center">
                                <div class="mx-auto max-w-xs py-6">
                                    <input type="file" @change="onFileChange" required
                                        class="block w-full text-sm file:mr-4 file:rounded-md file:border-0 file:bg-primary-500 file:py-2.5 file:px-4 file:text-sm file:font-semibold hover:file:bg-primary-700 focus:outline-none disabled:pointer-events-none disabled:opacity-60" />
                                </div>
                                <InputError class="mt-2" :message="form.errors.file" />
                                <div>
                                    <PrimaryButton class="px-10" :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing">
                                        Upload
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5">
                    <div class="bg-green-500 overflow-hidden shadow-sm sm:rounded-lg" v-show="this.$page.props.message">
                        <div class="p-6 text-black text-center text-xl">{{ this.$page.props.message }}</div>
                    </div>

                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-5" v-show="this.$page.props.message">
                        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg p-6">

                            <div class="relative overflow-x-auto rounded-lg">
                                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                    <thead
                                        class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
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
        message: String,
        companies: Array
    },
    data() {
        return {
            form: useForm({
                file: "",
            })
        };
    },

    methods: {
        onFileChange(event) {
            this.form.file = event.target.files[0];
        },
        
        importCompany() {
            this.form.post(route('import.company'), {
                onFinish: () => this.form.reset('file'),
            });
        },
    }
}
</script>