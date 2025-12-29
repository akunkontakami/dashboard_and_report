<template>
  <div class="flex justify-center items-center h-screen bg-gray-100">
    <div class="w-1/2 h-screen hidden lg:block bg-gray-800"></div>

    <div class="lg:p-36 md:p-52 sm:p-20 p-8 w-full lg:w-1/2">
      <h1 class="text-4xl font-bold mb-4 text-gray-800">DASHBOARD - LOGIN</h1>

      <form @submit.prevent="processLogin" class="space-y-4">
        <div>
          <label for="username" class="block text-gray-700">Username</label>
          <input
            v-model.trim="username"
            type="text"
            id="username"
            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-gray-800"
            autocomplete="off"
            required
          />
        </div>

        <div>
          <label for="password" class="block text-gray-700">Password</label>
          <input
            v-model.trim="password"
            type="password"
            id="password"
            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:border-gray-800"
            autocomplete="off"
            required
          />
        </div>

        <div class="flex items-center">
          <input
            v-model="remember"
            type="checkbox"
            id="remember"
            class="text-gray-800"
          />
          <label for="remember" class="text-gray-600 ml-2">Remember Me</label>
        </div>

        <button
          type="submit"
          class="bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-md py-2 px-4 w-full transition"
          :disabled="loading"
        >
          <span v-if="loading">Logging in...</span>
          <span v-else>Login</span>
        </button>

        <!-- Notifikasi -->
        <div
          v-if="notif.show"
          :class="notif.status ? 'text-green-600' : 'text-red-600'"
          class="mt-4 text-center font-medium"
        >
          {{ notif.text }}
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { Auth } from "../services"
import { DURATION_ALERT } from "../configs/constant"
import { setSession } from "../utils"

// STATE
const username = ref("")
const password = ref("")
const remember = ref(false)
const loading = ref(false)
const notif = ref({ status: false, text: "", show: false })

// ✅ Login Function
async function processLogin() {
  notif.value = { status: false, text: "", show: false }

  if (!username.value || !password.value) {
    notif.value = { status: false, text: "Please fill username and password", show: true }
    return
  }

  loading.value = true

  try {
    const formData = new FormData()
    formData.append("username", username.value)
    formData.append("password", password.value)

    const res = await Auth(formData)
    console.log("🔍 API Response:", res)

    if (!res || res.success !== 1) {
      notif.value = {
        status: false,
        text: res?.message || "Login Unsuccessfully! Check your username or password.",
        show: true,
      }
      return
    }

    // ✅ Jika login berhasil
    const sessionData = `${JSON.stringify(res.results)}__${res.token}`
    setSession(sessionData)

    notif.value = { status: true, text: "Login success!", show: true }

    // Redirect ke dashboard setelah delay
    setTimeout(() => {
      window.location.href = "/"
    }, 1000)
  } catch (err) {
    console.error("❌ Login Error:", err)
    notif.value = {
      status: false,
      text: "Network error or invalid API response.",
      show: true,
    }
  } finally {
    loading.value = false
    // Hilangkan notif otomatis
    setTimeout(() => (notif.value.show = false), DURATION_ALERT)
  }
}
</script>
