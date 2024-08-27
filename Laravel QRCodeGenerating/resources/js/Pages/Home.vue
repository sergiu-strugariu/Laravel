<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import QRCodeURL from "@/Pages/PageComponents/QRCodeURL.vue";
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center text-7xl">QRCode Generate</div>
                </div>

                <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-5">
                    <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                        <QRCodeURL />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>