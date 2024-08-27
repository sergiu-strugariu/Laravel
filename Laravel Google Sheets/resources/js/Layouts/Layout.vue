<script setup>
import NavBar from '@/Components/NavBar/Main.vue';
</script>

<template>
    <div class="flex flex-col min-h-screen">
        <div class="flex-grow bg-gradient-to-br from-gray-950 to-sky-950">
            <NavBar />

            <main class="min-h-screen">
                <slot />
            </main>
        </div> 
    </div>
</template>
