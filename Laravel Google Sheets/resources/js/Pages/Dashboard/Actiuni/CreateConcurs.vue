<script setup>
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

import VueDatePicker from '@vuepic/vue-datepicker';

import { useForm } from '@inertiajs/vue3';

import "cropperjs/dist/cropper.css";
import Cropper from "cropperjs";
import axios from "axios";
</script>

<template>
    <div>
        <PrimaryButton @click="openModal">Creeaza Concurs</PrimaryButton>

        <Modal :show="modalBoolean" @close="closeModal">
            <div class="p-6">
                <div class="mb-6">
                    <h1 class="text-4xl text-white font-bold">Creeaza Concurs</h1>

                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-if="form.recentlySuccessful"
                            class="text-lg text-center mt-5 text-white bg-green-500 rounded-lg font-bold">Concurs creeat cu
                            success.</p>
                    </Transition>

                    <div>
                        <input type="file" ref="photo" required name="photo" @change="onPhotoChange" class="hidden"
                            accept="image/*" />

                        <div class="flex justify-center">
                            <img ref="image" :src="photoUrl" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 mt-2" v-if="photoUrl">
                            <PrimaryButton class="m-2 text-sm" @click="selectPhoto">Schimba poza</PrimaryButton>
                            <PrimaryButton class="m-2 text-sm" @click="enableCropper" v-show="!croppingImage">Taie poza
                            </PrimaryButton>
                            <DangerButton class="m-2 text-sm" @click="cropPhoto" v-show="croppingImage">Taie poza
                            </DangerButton>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="mt-6 space-y-6">
                    <div class="mb-2 rounded-lg" v-if="!photoUrl">
                        <div>
                            <label class="inline-block  text-white">Poza Concurs</label>
                            <input @change="onPhotoChange"
                                class="relative m-0 block w-full min-w-0 flex-auto cursor-pointer rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-1 text-lg font-normal text-white transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:cursor-pointer file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200 focus:border-primary focus:text-neutral-700 focus:shadow-te-primary focus:outline-none "
                                type="file" />

                            <InputError class="mt-2" :message="form.errors.image" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="nume_concurs" class="text-white" value="Nume Concurs" />

                        <TextInput id="nume_concurs" type="text" class="mt-1 block w-full" v-model="form.nume_concurs"
                            required autocomplete="nume_concurs" />

                        <InputError class="mt-2" :message="form.errors.nume_concurs" />
                    </div>

                    <div>
                        <InputLabel for="descriere" class="text-white" value="Descriere Concurs" />

                        <textarea type="text" placeholder="Descrierea concursului." v-model="form.descriere" required
                            class="flex border-2 w-full h-auto min-h-[80px] px-3 py-2 text-sm bg-white rounded-md border-neutral-300 ring-offset-background placeholder:text-neutral-400 focus:border-neutral-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-400 disabled:cursor-not-allowed disabled:opacity-50"></textarea>

                        <InputError :message="form.errors.descriere" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="reguli" class="text-white" value="Reguli Concurs" />

                        <textarea type="text" placeholder="Regulile Concursului." v-model="form.reguli" required
                            class="flex border-2 w-full h-auto min-h-[80px] px-3 py-2 text-sm bg-white rounded-md border-neutral-300 ring-offset-background placeholder:text-neutral-400 focus:border-neutral-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neutral-400 disabled:cursor-not-allowed disabled:opacity-50"></textarea>

                        <InputError :message="form.errors.reguli" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2">
                        <div class="p-4">
                            <InputLabel for="data_inceperii" value="Data Inceperii" class="font-bold text-white mb-1" />

                            <VueDatePicker v-model="form.data_inceperii" required class="rounded-lg mt-2">
                            </VueDatePicker>

                            <InputError class="mt-2" :message="form.errors.data_inceperii" />
                        </div>
                        <div class="p-4">
                            <InputLabel for="data_inchiderii" value="Data Inchidere" class="font-bold text-white mb-1" />

                            <VueDatePicker v-model="form.data_inchiderii" required class="rounded-lg mt-2">
                            </VueDatePicker>

                            <InputError class="mt-2" :message="form.errors.data_inchiderii" />
                        </div>
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <SecondaryButton class="m-2 text-sm" @click="closeModal">Inchide</SecondaryButton>
                        <PrimaryButton class="m-2 text-sm" :disabled="form.processing" @click="submit">Save</PrimaryButton>
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

        photo: null,
        photoUrl: null,
        croppingImage: false,

        selectedPhoto: false,

        form: useForm({
            image: '',
            nume_concurs: '',
            descriere: '',
            reguli: '',
            data_inceperii: '',
            data_inchiderii: '',
        }),
    }),

    methods: {
        submit() {
            this.form.post(route('store.concurs'), {
                onSuccess: () => [this.form.reset(), this.photoUrl = ''],
                onError: () => console.log("Erroare poza."),
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

        selectPhoto() {
            this.$refs.photo.click();
        },

        onPhotoChange(event) {
            const file = event.target.files[0];
            this.photo = file;
            this.photoUrl = URL.createObjectURL(this.photo);
            this.selectedPhoto = true;
            this.cropPhoto();
        },

        enableCropper() {
            this.croppingImage = true

            const cropper = new Cropper(this.$refs.image, {
                aspectRatio: 1,
                viewMode: 1,
            });

            this.$watch("photo", (newValue) => {
                if (newValue instanceof Blob) {
                    const reader = new FileReader();
                    reader.onload = (event) => {
                        cropper.replace(event.target.result);
                    };
                    reader.readAsDataURL(newValue);
                }
            });
        },

        async cropPhoto(event) {
            this.croppingImage = false
            const cropper = this.$refs.image.cropper;

            if (cropper) {
                const croppedCanvas = cropper.getCroppedCanvas();
                const croppedImage = croppedCanvas.toDataURL('image/jpeg');
                this.photoUrl = croppedImage;
                this.form.image = this.photoUrl;
                this.closeModal();

                setTimeout(() => {
                    this.openModal();
                }, 100);

            }

            if (!cropper) {
                const base64 = await this.blobToBase64(this.photoUrl);
                this.photoUrl = base64;
                this.form.image = this.photoUrl;
            }
        },

        async getImageUrl() {
            try {
                const response = await axios.get(route('photo.show'));
                this.photoUrl = response.data;
            } catch (error) {
                console.log(error);
                this.closeModal();
                alerts.add({
                    message: "A intervenit o erroare la afisarea pozei.",
                    alert_role: 'danger'
                });
            }
        },

        blobToBase64(dataUrl) {
            return fetch(dataUrl)
                .then(response => response.blob())
                .then(blob => {
                    return new Promise((resolve, _) => {
                        const reader = new FileReader();
                        reader.onloadend = () => resolve(reader.result);
                        reader.readAsDataURL(blob);
                    });
                });
        }
    },
}
</script>