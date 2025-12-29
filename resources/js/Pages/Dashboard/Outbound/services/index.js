// src/services/index.js
import { BASE_URL } from "../configs/constant"
import { ref } from "vue"

// ✅ State loading global (bisa diakses di komponen)
export const isLoading = ref(false)

// ✅ Fungsi helper default error response
function responseFailed(msg) {
  return {
    success: 0,
    message: msg || "Request failed",
    results: [],
  }
}

// ✅ Helper untuk parsing JSON dengan aman
async function safeJson(res) {
  try {
    return await res.json()
  } catch (err) {
    console.warn("⚠️ Response bukan JSON valid:", err)
    return responseFailed("Invalid JSON response")
  }
}

// =======================
// 🔐 AUTH SERVICE
// =======================
export async function Auth(data) {
  isLoading.value = true
  try {
    const res = await fetch(`${BASE_URL}/auth`, {
      method: "POST",
      body: data,
    })

    if (!res.ok) {
      return responseFailed(`HTTP Error: ${res.status}`)
    }

    const json = await safeJson(res)
    return json
  } catch (e) {
    console.error("❌ Error Auth:", e)
    return responseFailed(e.message || "Auth failed")
  } finally {
    isLoading.value = false
  }
}

// =======================
// 📥 GET DATA
// =======================
export async function GetData(param_url) {
  isLoading.value = true
  try {
    const res = await fetch(`${BASE_URL}/${param_url}`)

    if (!res.ok) {
      return responseFailed(`HTTP Error: ${res.status}`)
    }

    const json = await safeJson(res)
    return json
  } catch (e) {
    console.error("❌ Error GetData:", e)
    return responseFailed(e.message || "Failed to fetch data")
  } finally {
    isLoading.value = false
  }
}

// =======================
// 📤 POST DATA
// =======================
export async function PostData(param_url, data) {
  isLoading.value = true
  try {
    const res = await fetch(`${BASE_URL}/${param_url}`, {
      method: "POST",
      body: data,
    })

    if (!res.ok) {
      return responseFailed(`HTTP Error: ${res.status}`)
    }

    const json = await safeJson(res)
    return json
  } catch (e) {
    console.error("❌ Error PostData:", e)
    return responseFailed(e.message || "Failed to post data")
  } finally {
    isLoading.value = false
  }
}
