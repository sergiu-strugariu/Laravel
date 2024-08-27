<script setup>
import TextInput from '@/Components/TextInput.vue';
import InputError from "@/Components/InputError.vue";

import { useForm } from '@inertiajs/vue3';

</script>

<template>
    <div>
        <div class="relative px-6 lg:px-8 overflow-hidden">
            <div class="mx-auto max-w-3xl pt-20 pb-32 sm:pt-48 sm:pb-40">
                <form @submit.prevent="submit">
                    <div>
                        <input type="file" name="file" @change="onFileChange" accept=".csv">
                    </div>

                    <button type="submit" class="bg-black py-2 px-12 text-white">Import CSV</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {
            form: useForm({
                file: '',
            }),
        }
    },

    methods: {
        submit() {
            console.log(this.form.file);
            return this.form.post(route('importStore', {
                onFinish: () => this.form.reset("file")
            }));
        },
        onFileChange(event) {
            // Access the selected file from the event
            const selectedFile = event.target.files[0];
            // Update the form data with the selected file
            this.form.file = selectedFile;
        },
    },
}

</script>
