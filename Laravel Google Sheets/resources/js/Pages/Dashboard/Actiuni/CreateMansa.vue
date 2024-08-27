<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

import VueDatePicker from '@vuepic/vue-datepicker';

import { useForm } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <PrimaryButton @click="openModal">Creeaza Mansa</PrimaryButton>

        <Modal :show="modalBoolean" @close="closeModal">
            <div class="p-6">
                <div class="mb-6">
                    <h1 class="text-4xl text-white font-bold">Creeaza Mansa</h1>
                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-if="form.recentlySuccessful"
                            class="text-lg text-center mt-5 text-white bg-green-500 rounded-lg font-bold">Mansa creeata cu
                            success.</p>
                    </Transition>
                </div>

                <form @submit.prevent="submit">
                    <div class="mt-4">
                        <InputLabel for="nume_mansa" class="text-white" value="Nume Mansa" />

                        <TextInput id="nume_mansa" type="text" class="mt-1 block w-full" v-model="form.nume_mansa" required
                            autocomplete="nume_mansa" />

                        <InputError class="mt-2" :message="form.errors.nume_mansa" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="concurs" value="Selecteaza Concurs" class="font-bold text-white mb-1" />

                        <select id="concurs" v-model="form.concurs" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <option selected v-for="(concurs, index) in concursuri" :key="index" :value="concurs.id">{{ concurs.Nume }}</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="lac" value="Selecteaza Lacul" class="font-bold text-white mb-1" />

                        <select id="lac" v-model="form.lac" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <option selected v-for="(lac, index) in lacuri" :key="index" :value="lac.id">{{ lac.Nume }}</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="status_mansa" value="Status Mansa" class="font-bold text-white mb-1" />

                        <select id="status_mansa" v-model="form.status_mansa" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <option selected value="Inscrieri Deschise">Inscrieri Deschise</option>
                            <option value="Inscrieri doar pe lista de asteptare">Inscrieri doar pe lista de asteptare
                            </option>
                            <option value="Inscrieri inchise">Inscrieri inchise</option>
                            <option value="Mansa incheiata">Mansa incheiata</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <InputLabel for="numar_participanti" class="text-white" value="Numar Participanti" />

                        <TextInput id="numar_participanti" type="number" class="mt-1 block w-full" v-model="form.numar_participanti" required
                            autocomplete="numar_participanti" />

                        <InputError class="mt-2" :message="form.errors.numar_participanti" />
                    </div>

                    <!-- <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2">
                        <div class="p-4">
                            <InputLabel for="start_mansa" value="Start Mansa" class="font-bold text-white mb-1" />

                            <VueDatePicker v-model="form.start_mansa" required class="rounded-lg mt-2">
                            </VueDatePicker>

                            <InputError class="mt-2" :message="form.errors.start_mansa" />
                        </div>
                        <div class="p-4">
                            <InputLabel for="stop_mansa" value="Stop Mansa" class="font-bold text-white mb-1" />

                            <VueDatePicker v-model="form.stop_mansa" required class="rounded-lg mt-2">
                            </VueDatePicker>

                            <InputError class="mt-2" :message="form.errors.stop_mansa" />
                        </div>
                    </div> -->

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
    props: {
        concursuri: Array,
        lacuri: Array,
    },

    data: () => ({
        modalBoolean: false,

        form: useForm({
            nume_mansa: '',
            concurs: '',
            lac: '',
            status_mansa: '',
            numar_participanti: '',
            start_mansa: '',
            stop_mansa: '',
        }),
    }),

    methods: {
        submit() {
            this.form.post(route('store.mansa'), {
                onSuccess: () => this.form.reset(),
                onError: () => console.log("Erroare adaugare mansa."),
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