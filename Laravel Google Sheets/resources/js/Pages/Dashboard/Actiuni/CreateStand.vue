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
        <PrimaryButton @click="openModal">Creeaza Stand</PrimaryButton>

        <Modal :show="modalBoolean" @close="closeModal">
            <div class="p-6">
                <div class="mb-6">
                    <h1 class="text-4xl text-white font-bold">Creeaza Stand</h1>
                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-if="form.recentlySuccessful" class="text-lg text-center mt-5 text-white bg-green-500 rounded-lg font-bold">Stand creeat cu success.</p>
                    </Transition>
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="nume_stand" class="text-white" value="Nume Stand" />

                        <TextInput id="nume_stand" type="text" class="mt-1 block w-full" v-model="form.nume_stand" required
                            autocomplete="nume_stand" />

                        <InputError class="mt-2" :message="form.errors.nume_stand" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="lac" value="Selecteaza Lacul" class="font-bold text-white mb-1" />

                        <select id="lac" v-model="form.lac" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <option selected v-for="(lac, index) in lacuri" :key="index" :value="lac.id">{{ lac.Nume }}</option>
                        </select>
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <SecondaryButton class="m-2 text-sm" @click="closeModal">Inchide</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Creeaza Stand
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script>
export default {
    props: {
        lacuri: Array,
    },

    data: () => ({
        modalBoolean: false,

        form: useForm({
            nume_stand: '',
            lac: ''
        }),
    }),

    methods: {
        submit() {
            this.form.post(route('store.stand'), {
                onSuccess: () => this.form.reset(),
                onError: () => console.log("Erroare adaugare stand."),
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