<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
                    <a :href="route('export')" class="max-w-3xl mx-auto sm:px-6 lg:px-8 ">
                        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg hover:bg-gray-400">
                            <div class="p-6 text-gray-900 text-center text-xl">Export Data</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
