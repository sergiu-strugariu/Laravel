<script setup>
</script>

<template>
    <div class="p-4 rounded-lg overflow-auto">
        <div class="flow-root">
            <ul role="list">
                <li v-for="user in $page.props.default.users" class="py-1 sm:py-2">
                    <div class="flex items-center space-x-4 p-3 bg-gray-100 rounded-lg cursor-pointer"
                        @click="getSelctedUser(user)">
                        <div class="flex-shrink-0">
                            <img class="w-8 h-8 rounded-full"
                                src="https://flowbite.com/docs/images/people/profile-picture-1.jpg" alt="profileimage">
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
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        getSelctedUser(user) {
            this.$emit('getSelctedUser', user);
        }
    },
}

</script>

