<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Weather from "@/Pages/PageComponents/Weather.vue";
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center text-7xl">WeatherApp</div>
                </div>

                <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-5">
                    <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                        <Weather />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>