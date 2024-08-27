<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center text-7xl">Short URL</div>
                </div>

                <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-5">
                    <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-12 text-gray-900 text-center text-4xl">Insert your url here.</div>

                        <div class="pb-6 max-w-3xl mx-auto">
                            <form @submit.prevent="submit" class="px-6">
                                <div>
                                    <div class="flex">
                                        <p class="flex-shrink-0 z-10 inline-flex items-center tracking-widest py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 dark:border-gray-700 dark:text-white rounded-l-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">URL</p>
                                        <div class="relative w-full">
                                            <input type="url" id="url" required autofocus autocomplete="url" v-model="form.url" 
                                            class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-r-lg border-l-gray-100 border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500" placeholder="https://localhost/">
                                        </div>
                                    </div>
                                    
                                    <InputError class="mt-2" :message="form.errors.url" />
                                </div>
                                <div class="flex justify-around mt-4">
                                    <div>
                                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                            :disabled="form.processing">
                                            Convert
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </form>

                            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-5">
                                <h1 class="text-xl font-bold">Your URL</h1>
                                <a v-show="this.$page.props.shorturl" :href="this.$page.props.shorturl">{{ this.$page.props.shorturl ?? "http://localhost:8000/shorturl" }}</a>
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
        shorturl: String
    },
    data() {
        return {
            form: useForm({
                url: '',
            })
        };
    },

    methods: {
        submit() {
            this.form.post(route('store.shorturl'), {
                onFinish: () => this.form.reset('url'),
            });
        },

        cancel() {
            const input = document.getElementById("url");
            input.value = ""
        }
    }
}
</script>
