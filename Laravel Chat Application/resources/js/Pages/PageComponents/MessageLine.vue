<script setup>
import Sender from '@/Pages/PageComponents/Sender.vue'
import Receiver from '@/Pages/PageComponents/Receiver.vue'
</script>

<template>
    <Sender :message="message" v-if="$page.props.auth.user.id === message.sender_id" />
    <Receiver :message="message" v-else />
</template>

<script>
export default {
    props: {
        user: Object,
        message: Object
    },
}
</script>

