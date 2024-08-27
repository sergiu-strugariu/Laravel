<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchBar from '@/Pages/PageComponents/SearchBar.vue';
import ChatBox from '@/Pages/PageComponents/ChatBox.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="container mx-auto">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <SearchBar @getSelctedUser="getSelctedUser($event)" />
                        </div>
                        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg col-span-2 p-6">
                            <div class="flow-root">
                                <ul role="list">
                                    <ChatBox @sendMessage="sendMessage($event)" :user="this.user"
                                        :messages="this.messages" />
                                </ul>
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
    data() {
        return {
            user: ref({}),
            messages: ref({}),
        };
    },
    methods: {
        async getSelctedUser(user) {
            this.user = user

            const result = await axios.get(route('get.messages'), {
                params: {
                    receiver_id: user.id
                }
            });

            if (result) {
                return this.messages = result.data.messages.reverse();
            }

            return;
        },

        sendMessage(message) {
            if (!message) {
                return;
            }

            const payload = {
                receiver_id: this.user.id,
                content: message
            }

            return axios.post(route('store.message', payload)).then(response => {
                if (response && response.data.status) {
                    this.messages.push(response.data.message)
                }
            }).catch(error => {
                console.error(error.response)
            });
        },
    },

    mounted() {
        if (this.$page.props.auth) {
            window.Echo.private(`messages.${this.$page.props.auth.user.id}`).listen('\\App\\Events\\MessageSent', e => {
                this.messages.push(e.message)
            });
        }
    },
}
</script>
