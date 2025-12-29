// import { KEY_AUTH } from "../configs/constant"
const KEY_AUTH = `dashboard_session_dipo`
// Ambil session dari sessionStorage
export function getSession() {
  const result = sessionStorage.getItem(KEY_AUTH)
  if (!result) return null

  const split = result.split("__")
  return {
    data: JSON.parse(split[0]),
    token: split[1],
  }
}

// Simpan session ke sessionStorage
export function setSession(data) {
  sessionStorage.setItem(KEY_AUTH, data)
}

// Hapus session dari sessionStorage
export function clearSession() {
  sessionStorage.removeItem(KEY_AUTH)
}

// Generate warna acak (misalnya untuk chart)
export function generateColor() {
  return `#${Math.floor(Math.random() * 16777215).toString(16)}`
}
