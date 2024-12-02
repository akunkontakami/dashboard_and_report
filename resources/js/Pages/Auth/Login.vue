<template>
    <AuthLayout title="Login">
        <section
            class="bg-white rounded-lg border shadow-lg md:w-[60%] w-[95%] z-[2]"
        >
            <div class="flex md:flex-row flex-col">
                <form
                    @submit.prevent="submit"
                    class="flex-1 md:px-10 md:py-8 px-7 py-5 flex flex-col justify-center"
                    x-data="{popup : false}"
                >
                    <div
                        class="bg-[#38a36321] border-[#38A363] border text-dark text-[11px] w-full mb-3 text-center rounded-md py-2"
                        v-if="$page.props.flash.success"
                    >
                        {{ $page.props.flash.success }}
                    </div>
                    <div
                        class="bg-[#3943B724] border-yellow border text-dark text-[11px] w-full mb-3 text-center rounded-md py-2"
                        v-if="$page.props.flash.error"
                    >
                        {{ $page.props.flash.error }}
                    </div>

                    <h1 class="text-dark text-[25px] font-krub-bold mb-5">
                        Login - Dashboard
                    </h1>
                    <Select
                        v-model="form.role"
                        required="true"
                        icon="isax icon-profile-2user"
                        :error="form.errors.role"
                    >
                        <option
                            v-for="row in roles"
                            :value="row.type"
                            :selected="form.role === row.type"
                        >
                            {{ row.text }}
                        </option>
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
                    <ButtonYellow
                        type="submit"
                        :disabled="
                            !form.email || !form.password || form.processing
                        "
                        :loading="form.processing"
                        class="w-full block py-3 font-krub-medium uppercase"
                    >
                        SIGN IN
                    </ButtonYellow>
                </form>
                <div
                    class="flex-1 bg-[#EFEFEF] md:px-10 md:py-8 px-7 py-5 flex flex-col justify-center"
                >
                    <h1 class="text-dark text-[24px] font-krub-bold mb-3">
                        Login to other roles
                    </h1>
                    <ul class="flex flex-col gap-2">
                        <li v-for="menu in menus">
                            <a
                                :href="menu.url"
                                class="bg-white border-yellow border rounded-md flex justify-between items-center px-3 py-2 hover:shadow-md"
                            >
                                <div>
                                    <p
                                        class="text-yellow font-krub-bold text-[14px] mb-0"
                                    >
                                        {{ menu.label }}
                                    </p>
                                    <span class="text-dark text-[10px] block">
                                        {{ menu.description }}
                                    </span>
                                </div>
                                <i
                                    class="isax icon icon-arrow-right-3 ms-10 text-[18px]"
                                ></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </AuthLayout>
</template>
<script setup lang="ts">
import AuthLayout from "@/Layouts/AuthLayout.vue";
import Input from "@/Components/Input/Index.vue";
import InputPassword from "@/Components/Input/Password.vue";
import Select from "@/Components/Input/Select.vue";
import ButtonYellow from "@/Components/Button/ButtonYellow.vue";
import { joinConnectionBroadcast } from "@/socket";
import { useForm,Link } from "@inertiajs/vue3";
import { ref } from "vue";

defineProps(["menus"]);
const form = useForm({
    role: "ba",
    email: "",
    password: "",
});

const roles = ref([
    {
        type: "ba",
        text: "Superadmin",
    },
    {
        type: "admin",
        text: "Admin",
    },
    {
        type: "am",
        text: "Account Manager - I/O & Escalation",
    },
    {
        type: "spv",
        text: "Supervisor - I/O",
    },
    {
        type: "spv_escalation",
        text: "Supervisor - Escalation",
    },
]);
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
