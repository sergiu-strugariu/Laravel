<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

import { useForm } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <PrimaryButton @click="openModal">Creeaza Lac</PrimaryButton>

        <Modal :show="modalBoolean" @close="closeModal">
            <div class="p-6">
                <div class="mb-6">
                    <h1 class="text-4xl text-white font-bold">Creeaza Lac</h1>
                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-if="form.recentlySuccessful" class="text-lg text-center mt-5 text-white bg-green-500 rounded-lg font-bold">Lac creeat cu success.</p>
                    </Transition>
                </div>

                <form @submit.prevent="submit">
                    <div class="p-4">
                        <InputLabel for="nume" class="text-white" value="Nume Lac" />

                        <TextInput id="nume" type="text" class="mt-1 block w-full" v-model="form.nume" required
                            autocomplete="nume" />

                        <InputError class="mt-2" :message="form.errors.nume" />
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <SecondaryButton class="m-2 text-sm" @click="closeModal">Inchide</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Creeaza Lac
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

    </div>
</template>

<script>
export default {
    data: () => ({
        modalBoolean: false,

        form: useForm({
            nume: '',
        }),
    }),

    methods: {
        submit() {
            this.form.post(route('store.lac'), {
                onSuccess: () => this.form.reset(),
                onError: () => console.log("Erroare adaugare lac."),
                onFinish: () => [],
            });
        },

        closeModal() {
            this.modalBoolean = false;
            this.selectedPhoto = false;
        },

        openModal() {
            this.modalBoolean = true;
        },
    },
}

</script>