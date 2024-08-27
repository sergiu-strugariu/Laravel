<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
</script>

<template>
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-5">
        <div class="bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-6 py-12 text-gray-900 text-center text-4xl">Write any city.</div>

            <div class="pb-6 max-w-3xl mx-auto">
                <form @submit.prevent="submit" class="px-6">
                    <div>
                        <div class="flex">
                            <p
                                class="flex-shrink-0 z-10 inline-flex items-center tracking-widest py-2.5 px-4 text-sm font-medium text-center text-gray-900 bg-gray-100 border border-gray-300 dark:border-gray-700 dark:text-white rounded-l-lg hover:bg-gray-200 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                City</p>
                            <div class="relative w-full">
                                <input type="text" id="city" required autofocus autocomplete="Bucharest" v-model="form.city"
                                    class="block p-2.5 w-full z-20 text-sm text-gray-900 bg-gray-50 rounded-r-lg border-l-gray-100 border-l-2 border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:border-blue-500"
                                    placeholder="Bucharest">
                            </div>
                        </div>

                        <InputError class="mt-2" :message="form.errors.city" />
                    </div>
                    <div class="flex justify-around mt-4">
                        <div>
                            <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing">
                                Get Weather
                            </PrimaryButton>
                        </div>
                    </div>
                </form>

                <div v-if="$page.props.weatherData" class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-5">
                    <h2 class="mb-2 text-2xl font-semibold text-gray-900 dark:text-white">City {{ $page.props.weatherData.name }}</h2>
                    <div>
                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Coord</h2>
                        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                            <li>Coord: {{ $page.props.weatherData.coord.lon }} {{ $page.props.weatherData.coord.lat }}</li>
                        </ul>
                    </div>
                    <div class="mt-5">
                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Wind</h2>
                        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                            <li>Speed: {{ $page.props.weatherData.wind.speed }} km/h</li>
                            <li>Deg: {{ $page.props.weatherData.wind.deg }}</li>
                        </ul>
                    </div>
                    <div class="mt-5">
                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Counds</h2>
                        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                            <li>Counds: {{ $page.props.weatherData.weather[0].description }} </li>
                        </ul>
                    </div>

                    <div class="mt-5">
                        <h2 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Temperature (Celsius)</h2>
                        <ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400">
                            <li>
                                Fells Like: {{ $page.props.weatherData.main.feels_like }}°C
                                <br>
                                <span class="text-xs">This temperature parameter accounts for the human perception of weather.</span>
                            </li>
                            <li>Maximum temperature at the moment: {{ $page.props.weatherData.main.temp_max }}°C</li>
                            <li>Minimum temperature at the moment: {{ $page.props.weatherData.main.temp_min }}°C</li>
                        </ul>
                    </div>
                </div>

                <span class="text-xs">Credits and more details on <a class="text-blue-700" href="https://openweathermap.org/">Open Weather Map</a>.</span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        weatherData: Array
    },
    data() {
        return {
            form: useForm({
                city: '',
            })
        };
    },

    methods: {
        submit() {
            this.form.get(route('get.weather'), {
                onFinish: () => this.form.reset('city'),
            });
        },
    }
}
</script>