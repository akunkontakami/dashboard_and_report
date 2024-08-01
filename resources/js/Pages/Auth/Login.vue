<template>
    <AuthLayout title="Login">
        <form @submit.prevent="submit" class="w-[80%]" x-data="{popup : false}">
            <div
                class="bg-[#38a36321] border-[#38A363] border text-dark text-[11px] w-full mb-3 text-center rounded-md py-2"
                v-if="$page.props.flash.success"
            >
                {{ $page.props.flash.success }}
            </div>
            <div
                class="bg-[#feb50024] border-yellow border text-dark text-[11px] w-full mb-3 text-center rounded-md py-2"
                v-if="$page.props.flash.error"
            >
                {{ $page.props.flash.error }}
            </div>

            <div class="text-center">
                <AppLogo class="inline mb-7" />
                <p class="text-dark text-[35px] font-krub-bold mb-5">Login</p>
            </div>
            <Select
                v-model="form.role"
                required="true"
                icon="isax icon-profile-2user"
                :error="form.errors.role"
            >
                <option value="ba">Business Account - Superadmin</option>
                <option value="admin">Business Account - Admin</option>
            </Select>
            <Input
                type="email"
                placeholder="Email"
                id="email"
                name="email"
                icon="isax-b icon-sms"
                required
                v-model="form.email"
                :error="form.errors.email"
            />

            <InputPassword
                placeholder="Password"
                id="password"
                name="password"
                icon="isax-b icon-lock"
                required
                v-model="form.password"
                :error="form.errors.password"
            />
            <p class="text-center text-[14px] font-krub-medium mb-4 mt-7">
                Forgot your password ?
                <Link
                    :href="route('auth.forgot-password.index')"
                    class="text-yellow underline"
                >
                    click here
                </Link>
            </p>
            <ButtonYellow
                type="submit"
                :disabled="!form.email || !form.password || form.processing"
                :loading="form.processing"
                class="w-full block py-3 font-krub-medium uppercase"
            >
                SIGN IN
            </ButtonYellow>
        </form>
    </AuthLayout>
</template>
<script setup lang="ts">
import AuthLayout from "@/Layouts/AuthLayout.vue";
import AppLogo from "@/Components/Icon/Logo/AppLogo.vue";
import Input from "@/Components/Input/Index.vue";
import InputPassword from "@/Components/Input/Password.vue";
import Select from "@/Components/Input/Select.vue";
import ButtonYellow from "@/Components/Button/ButtonYellow.vue";
import { joinConnectionBroadcast } from "@/socket";
import { useForm, Link } from "@inertiajs/vue3";

const props = defineProps([
    "phone_country",
    "term_condition",
    "privacy_policy",
]);
const form = useForm({
    role: "ba",
    email: "",
    password: "",
});

const submit = () => {
    if (!form.processing) {
        form.post(route("auth.login.store"), {
            onSuccess: () => {
                joinConnectionBroadcast();
            },
        });
    }
};
</script>
