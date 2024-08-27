<template>
  <div class="max-w-md mx-auto mt-10">
    <div v-if="$page.props.auth.user"
         class="w-full mb-4 cursor-default mt-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
      Logged in as {{ $page.props.auth.user.name }}
    </div>
    <h1 class="text-2xl font-bold mb-5 text-center">{{ app_name }}</h1>
    <form @submit.prevent="getQuotation">
      <div class="mb-4">
        <label for="age" class="block text-sm font-medium text-gray-700">Ages (comma-separated):</label>
        <input v-model="form.age" id="age" type="text" required
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
      </div>
      <div class="mb-4">
        <label for="currency_id" class="block text-sm font-medium text-gray-700">Currency:</label>
        <select v-model="form.currency_id" id="currency_id" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          <option value="EUR">EUR</option>
          <option value="GBP">GBP</option>
          <option value="USD">USD</option>
        </select>
      </div>
      <div class="mb-4">
        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date:</label>
        <input v-model="form.start_date" id="start_date" type="date" required
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
      </div>
      <div class="mb-4">
        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date:</label>
        <input v-model="form.end_date" id="end_date" type="date" required
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
      </div>
      <div>
        <button type="submit"
                class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          Get Quotation
        </button>

        <div v-if="!$page.props.auth.user" class="flex justify-between items-center gap-4">
          <a :href="route('login')"
             class="w-full mt-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Sign In
          </a>
          <a :href="route('register')"
             class="w-full mt-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Sign Up
          </a>
        </div>
      </div>
    </form>

    <div v-if="result" class="mt-6 p-4 border border-gray-300 rounded-md shadow-sm">
      <h2 class="text-xl font-bold mb-4">Quotation Result</h2>
      <p><strong>Total:</strong> {{ result.total }}</p>
      <p><strong>Currency:</strong> {{ result.currency_id }}</p>
      <p><strong>Quotation ID:</strong> {{ result.quotation_id }}</p>
    </div>

    <div v-if="error" class="mt-6 p-4 border border-red-300 bg-red-100 rounded-md shadow-sm">
      <p class="text-red-700">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import {ref} from 'vue';
import axios from 'axios';
import {usePage} from "@inertiajs/vue3";

export default {
  data() {
    return {
      app_name: import.meta.env.VITE_APP_NAME,
    };
  },

  setup() {
    const form = ref({
      age: '28,35',
      currency_id: 'EUR',
      start_date: '',
      end_date: ''
    });

    const result = ref(null);
    const error = ref(null);

    const authToken = usePage().props.auth.user;

    const getQuotation = async () => {
      try {
        const response = await axios.post('/api/quotation', form.value, {
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${authToken.api_token}`
          }
        });
        result.value = response.data;
        error.value = null;
      } catch (err) {
        result.value = null;
        error.value = err.response?.data.error || 'An error occurred. Are you logged in?.';
      }
    };

    return {
      form,
      result,
      error,
      getQuotation
    };
  }
};
</script>
