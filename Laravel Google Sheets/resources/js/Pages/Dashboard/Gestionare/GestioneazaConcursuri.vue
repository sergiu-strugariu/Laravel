<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

import Layout from '@/Layouts/Layout.vue';

import { useForm } from '@inertiajs/vue3';

</script>

<template>
    <Layout>
        <div class="py-12 p-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-x-auto">
                    <div class="mb-4">
                        <h1 class="text-white text-4xl font-bold">Participanti - {{ concurs['Nume'] }}</h1>
                    </div>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                        <thead class="text-xs uppercase bg-gray-700 text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Participant
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Concurs
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Mansa
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Stand
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Lac
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Sector
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Puncte Penalizare
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Trofeu
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Actiuni
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(participant, index) in participanti" :key="index" :value="participanti.id"
                                class="border-b bg-gray-800 border-gray-700">
                                <th scope="row" class="px-6 py-4">
                                    {{ participant['id'] }}
                                </th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium whitespace-nowrap text-white">
                                    {{ participant['Nume Pescar'] }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ participant['Nume Concurs'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ participant['Nume Mansa'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ participant['Nume Stand'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ participant['Nume Lac'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ participant['Sector'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ participant['Punce Penalizare'] }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ participant['Nume Trofeu'] }}
                                </td>
                                <td class="px-6 py-4">
                                    <PrimaryButton
                                        @click="openModal(participant['id'], 'participant', participant['Nume Lac'])">
                                        Configureaza
                                    </PrimaryButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="relative overflow-x-auto mt-10">
                    <div class="mb-4">
                        <h1 class="text-white text-4xl font-bold">Manse - {{ concurs['Nume'] }}</h1>
                    </div>
                    <table class="w-full text-sm text-left rtl:text-right text-gray-400">
                        <thead class="text-xsuppercase bg-gray-700 text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Concurs
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Lac
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nume Mansa
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Status Mansa
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Participanti
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Participanti Maximi
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Start Mansa
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Start Mansa
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Actiuni
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(mansa, index) in manse" :key="index" :value="mansa.id"
                                class="border-b bg-gray-800 border-gray-700">
                                <th scope="row" class="px-6 py-4">
                                    {{ mansa['id'] }}
                                </th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium whitespace-nowrap text-white">
                                    {{ mansa['Nume Concurs'] }}
                                </th>
                                <th scope="row" class="px-6 py-4">
                                    {{ mansa['Nume Lac'] }}
                                </th>
                                <th scope="row" class="px-6 py-4">
                                    {{ mansa['Nume Mansa'] }}
                                </th>
                                <th scope="row" class="px-6 py-4">
                                    {{ mansa['Status Mansa'] }}
                                </th>
                                <th scope="row" class="px-6 py-4">
                                    {{ mansa['Participanti'] }}
                                </th>
                                <th scope="row" class="px-6 py-4">
                                    {{ mansa['Participanti Maximi'] }}
                                </th>
                                <th scope="row" class="px-6 py-4">
                                    {{ mansa['Start Mansa'] }}
                                </th>
                                <th scope="row" class="px-6 py-4">
                                    {{ mansa['Final Mansa'] }}
                                </th>
                                <td class="px-6 py-4">
                                    <PrimaryButton @click="openModal(mansa['id'], 'mansa')">Configureaza
                                    </PrimaryButton>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Modal :show="modalBoolean" @close="closeModal">
                    <div class="p-6 rounded-xl">
                        <div>
                            <div v-if="modalType">
                                <h1 class="text-2xl text-white rounded-xl">Configureaza</h1>
                                <div>
                                    <div class="flex justify-center py-20" role="status" v-if="loading">
                                        <svg aria-hidden="true"
                                            class="w-8 h-8 animate-spin text-gray-600 fill-blue-600"
                                            viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                fill="currentColor" />
                                            <path
                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                fill="currentFill" />
                                        </svg>
                                    </div>
                                    <div v-if="!loading">
                                        <form @submit.prevent="submit">
                                            <div class="mt-4">
                                                <InputLabel for="standuri" value="Selecteaza Lacul"
                                                    class="font-bold text-white mb-1" />

                                                <select id="standuri" v-model="form.stand_id" required
                                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5  placeholder-gray-400  focus:ring-blue-500 focus:border-blue-500">
                                                    <option selected v-for="(stand, index) in standuri" :key="index"
                                                        :value="stand.id">{{ stand['Nume Stand'] }}</option>
                                                </select>
                                            </div>

                                            <div class="mt-4">
                                                <InputLabel for="sector" class="text-white" value="Sector" />

                                                <TextInput id="sector" type="text" class="mt-1 block w-full"
                                                    v-model="form.sector" required autocomplete="sector" />

                                                <InputError class="mt-2" :message="form.errors.sector" />
                                            </div>

                                            <div class="mt-4">
                                                <InputLabel for="puncte_penalizare" class="text-white"
                                                    value="Puncte Penalizare" />

                                                <TextInput id="puncte_penalizare" type="text" class="mt-1 block w-full"
                                                    v-model="form.puncte_penalizare" required
                                                    autocomplete="puncte_penalizare" />

                                                <InputError class="mt-2" :message="form.errors.puncte_penalizare" />
                                            </div>

                                            <div class="mt-4">
                                                <InputLabel for="nume_trofeu" class="text-white" value="Nume Trofeu" />

                                                <TextInput id="nume_trofeu" type="text" class="mt-1 block w-full"
                                                    v-model="form.nume_trofeu" required autocomplete="nume_trofeu" />

                                                <InputError class="mt-2" :message="form.errors.nume_trofeu" />
                                            </div>

                                            <div class="flex justify-between items-center mt-4">
                                                <SecondaryButton class="m-2 text-sm" @click="closeModal">Inchide
                                                </SecondaryButton>
                                                <PrimaryButton :class="{ 'opacity-25': form.processing }"
                                                    :disabled="form.processing">
                                                    Salveaza
                                                </PrimaryButton>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <h1 class="text-5xl text-white rounded-xl">{{ titlu }}</h1>
                                <div>
                                    <div role="status" v-if="loading">
                                        <div class="flex justify-center py-20" role="status" v-if="loading">
                                            <svg aria-hidden="true"
                                                class="w-8 h-8 animate-spin text-gray-600 fill-blue-600"
                                                viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                    fill="currentFill" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div v-if="!loading">
                                        {{ mansa }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </Modal>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    props: {
        concurs: Object,
        manse: Object,
        participanti: Object,
    },

    data: () => ({
        modalBoolean: false,
        modalType: null,
        loading: false,

        titlu: null,

        participant: null,
        mansa: null,
        standuri: null,

        form: useForm({
            user: '',
            stand_id: '',
            sector: '',
            puncte_penalizare: '',
            nume_trofeu: '',
        }),
    }),

    methods: {
        submit() {
            this.form.post(route('update.user.inscriere'), {
                onSuccess: () => this.form.reset(),
                onError: () => console.log("Erroare adaugare mansa."),
                onFinish: () => [this.closeModal()],
            });
        },

        async openModal(id, modal, numeLac) {
            if (modal === 'mansa') {
                this.modalBoolean = true;
                this.titlu = 'Mansa';
                this.modalType = false;
                this.loading = true;

                const response = await axios.post(route('get.mansa.details'), {
                    id: id,
                });

                if (response.data) {
                    this.loading = false
                }

                this.mansa = response.data;
                return;
            }

            this.modalBoolean = true;
            this.titlu = "Participant";
            this.modalType = true;
            this.loading = true;

            const response = await axios.post(route('get.participant'), {
                id: id,
            });

            const standuriDetails = await axios.post(route('get.standuri'), {
                numeLac: numeLac,
            });

            if (!response.data) {
                return console.log("Response data invalid.");
            }

            if (!standuriDetails.data) {
                return console.log("Standuri data invalid");
            }

            this.loading = false
            this.standuri = standuriDetails.data
            this.participant = response.data;
            this.form.user = this.participant;
            return;
        },

        closeModal() {
            this.modalBoolean = false;
            this.modalType = null;
        }
    },
}
</script>