<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';

import Layout from '@/Layouts/Layout.vue';
import { ref } from 'vue';

import { useForm } from '@inertiajs/vue3';

</script>

<template>
    <Layout>
        <div class="relative px-6 lg:px-8 overflow-hidden">
            <div class="mx-auto max-w-3xl pt-20 pb-32 sm:pt-48 sm:pb-40">
                <div>
                    <div v-if="$props.warning" class="mb-10">
                        <div v-if="$props.warning.status" class="bg-gray-700 overflow-hidden shadow-sm rounded-lg mt-4">
                            <div class="p-6 text-white font-bold">{{ $props.warning.message }}</div>
                        </div>
                        <div v-else class="bg-red-800 overflow-hidden shadow-sm rounded-lg mt-4">
                            <div class="p-6 text-white font-bold">{{ $props.warning.message }}</div>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold tracking-tight sm:text-center sm:text-6xl text-white">
                            Asociere date istorice <br>
                            <span
                                class="text-6xl text-center text-transparent bg-clip-text bg-gradient-to-br from-pink-400 to-red-600">
                                Fish Arena.
                            </span>
                        </h1>

                        <p class="mt-4 text-gray-500 ">
                            Introduceti in casuta de mai jos numele cu care va regasiti de obicei in foile de concurs. In
                            cazul in care stiti ca ati fost trecut in moduri diferite, e.g. Adrian Alexandrescu,
                            Alexandrescu Adrian sau Adi Alexandrescu, repetati procesul pentru fiecare nume in parte.
                        </p>

                        <div class="mx-auto max-w-xl mt-10 relative">
                            <div class="flex">
                                <input v-model="form.conturi_asociere" type="text" placeholder="Search names..."
                                       @input="onAutocompleteInput" @focus="showSuggestions = true"
                                       class="w-full px-4 py-2 border text-black rounded focus:outline-none focus:border-blue-500" >
                                <PrimaryButton @click="submit()">Save</PrimaryButton>
                            </div>


                            <div v-if="showSuggestions"
                                 class="top-full left-0 w-full mt-2 bg-white border rounded shadow-lg max-h-80 overflow-y-auto">
                                <div v-for="(concurs, index) in filteredSuggestions" :key="index"
                                     class="px-4 py-2 cursor-pointer hover:bg-gray-100 text-black" :class="{
                                        'bg-emerald-400 text-black': conturi_asociate.includes(concurs),

                                    }" @click="selectSuggestion(concurs)">
                                    {{ concurs }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    props: {
        concursuri: Array,
        warning: Array
    },
    data: () => ({
        form: useForm({
            conturi_asociere: [],
        }),
        selectedNames: ref([]),
        conturi_asociate: ref([]),
        filteredSuggestions: ref([]),
        showSuggestions: ref(false)
    }),

    mounted() {
        this.confursuriFilterd = ref(this.concursuri);
        this.filteredSuggestions = ref(this.concursuri);
    },

    methods: {
        async submit() {
            this.form.conturi_asociere = this.conturi_asociate;

            this.form.post(route('store.asociaza'), {
                onSuccess: () => this.form.reset(),
                onError: () => console.log("Erroare gasire utilizator."),
                onFinish: () => [],
            });
        },

        removePescar(index) {
            this.selectedNames.splice(index, 1);
        },

        onAutocompleteInput() {
            this.filteredSuggestions = this.concursuri.filter(suggestion =>
                suggestion.toLowerCase().includes(this.form.conturi_asociere.toLowerCase())
            );
        },

        selectSuggestion(concurs) {
            const existingIndex = this.conturi_asociate.indexOf(concurs);

            if (existingIndex === -1) {
                return this.conturi_asociate = [...this.conturi_asociate, concurs];
            }

            return this.conturi_asociate.splice(existingIndex, 1);
        }

    },

}
</script>
