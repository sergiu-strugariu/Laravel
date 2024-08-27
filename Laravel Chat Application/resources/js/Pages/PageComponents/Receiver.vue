<script setup>

</script>

<template>
    <div>
    {{      }}<div class="mt-2">
            <li class="receiver-message-line">
                <div class="bg-gray-200 p-2 rounded-lg mt-2 text-left">
                    <div class="text-md">
                        {{ message.content }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ message.time_ago }}
                    </div>
                </div>
            </li>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        user: Object,
        message: Array
    },
}
</script>

