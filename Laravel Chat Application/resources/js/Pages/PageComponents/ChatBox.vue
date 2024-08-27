<script setup>
import PrimaryButton from "@/Components/PrimaryButton.vue";
import MessageLine from '@/Pages/PageComponents/MessageLine.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

</script>

<template>
    <div>
        <div v-show="user.name" class="flex items-center space-x-4 bg-gray-200 p-2 rounded-lg">
            <div class="flex-shrink-0">
                <img class="w-8 h-8 rounded-full" src="https://flowbite.com/docs/images/people/profile-picture-1.jpg"
                    alt="profileimage">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ user.name }}
                </p>
                <p class="text-sm text-gray-500 truncate dark:text-gray-400">
                    {{ user.email }}
                </p>
            </div>
        </div>

        <div v-show="user.name" class="bg-gray-100 rounded-lg mt-5 px-4 p-2 h-[500px] overflow-y-auto">
            <MessageLine v-for="message in messages" :user="user" :message="message" />
        </div>

        <div class="mt-5" v-show="user.name">
            <form @submit.prevent="sendMessage">
                <div class="md:flex md:items-center mb-6">
                    <input placeholder="Write message" v-model="form.message"
                        class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-transparent focus:border-none"
                        id="message.input" type="text">
                    <PrimaryButton class="mx-2" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Upload
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        user: Object,
        messages: Object
    },

    data() {
        return {
            form: useForm({
                message: ref('')
            }),
        };
    },

    methods: {
        sendMessage() {
            this.$emit('sendMessage', this.form.message);
            this.form.message = "";
        },
    },
}
</script>

