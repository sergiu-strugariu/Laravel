<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
</script>

<template>
    <nav>
        <div class="py-5 md:py-10 px-4 md:px-10">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0 flex items-center gap-10">
                    <img src="https://www.arena.fish/logo.png" width="50" alt="">
                    <!-- <a :href="route('concursuri')" class="text-white text-sm">Concursuri</a> -->
                </div>
                <div class="flex justify-center items-center gap-6">
                    <a :href="route('concursuri.2024')" class="text-white text-lg">Concursuri 2024</a>

                    <PrimaryButton v-if="!$page.props.auth.user">
                        <a :href="route('autentificare')">Autentificare</a>
                    </PrimaryButton>

                    <Dropdown v-else align="right" width="48">
                        <template #trigger>
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    {{ $page.props.auth.user.Prenume }} 

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        </template>

                        <template #content>
                            <DropdownLink v-if="$page.props.auth.user.Tip === 'Organizator'" :href="route('dashboard')"
                                method="get" as="button">Dashboard</DropdownLink>
                            <DropdownLink :href="route('vizualizeaza')" method="get" as="button">Palmares</DropdownLink>
                            <DropdownLink :href="route('asociaza')" method="get" as="button">Cauta palmares</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </div>
    </nav>
</template>