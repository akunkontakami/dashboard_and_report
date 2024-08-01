<template>
    <header
        class="bg-white w-full border-b p-4 py-3 z-30 flex justify-between sticky top-0"
        v-if="user"
        x-data="{confirmation:false}"
    >
        <div>
            <AppLogoSm class="md:hidden w-[35px] h-[35px]" />
        </div>
        <ul class="flex gap-2 items-center">
            <li x-data="{ dropdownProfile: false }">
                <button
                    type="button"
                    class="flex text-end gap-2 items-center"
                    x-ref="dropdownProfile"
                    x-on:click="dropdownProfile=true"
                >
                    <div class="w-[350px]">
                        <b
                            class="text-[12px] line-clamp-1"
                            id="header-user-name"
                        >
                            {{ user.name }}
                        </b>
                        <p class="text-yellow text-[11px]">
                            {{  roleUser[user.role as string] }}
                        </p>
                    </div>
                    <img
                        :src="user.avatar"
                        :alt="user.name"
                        id="header-user-profile"
                        class="w-[34px] h-[30px] rounded-full object-cover border"
                    />
                    <i class="isax icon-arrow-down-1 text-[12px] ms-[-4px]"></i>
                </button>
                <div
                    x-show="dropdownProfile"
                    x-clock
                    x-on:click.away="dropdownProfile=false"
                    x-anchor.bottom-end="$refs.dropdownProfile"
                    class="absolute z-50 mt-3 w-fit whitespace-nowrap"
                >
                    <div
                        class="py-1 bg-white border rounded-md shadow-sm border-neutral-200/70 w-fit min-w-[130px] text-neutral-700"
                    >
                        <ul
                            class="font-krub-semibold text-[13px] px-1 min-w-[100px]"
                        >
                            <li>
                                <Link
                                    href=""
                                    class="text-dark text-[12px] flex gap-2 items-center px-3 py-[7px] rounded-md hover:bg-[#E6E9EF] relative"
                                >
                                    <i class="isax-b icon-user text-[15px]"></i>
                                    <span>Account</span>
                                </Link>
                            </li>
                            <li>
                                <button
                                    type="button"
                                    x-on:click="confirmation=true"
                                    class="text-dark text-[12px] w-full flex gap-2 items-center px-3 py-[7px] rounded-md hover:bg-[#E6E9EF]"
                                >
                                    <i
                                        class="isax-b icon-logout rotate-180 text-[15px]"
                                    ></i>
                                    <span>Logout</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>

        <Confirmation
            confirmation="Are you sure you want to logout?"
            @action="logout"
        />
    </header>
</template>

<script setup lang="ts">
import Confirmation from "@/Components/Popup/Confirmation.vue";
import AppLogoSm from "@/Components/Icon/Logo/AppLogoSm.vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { leaveConnectionBroadcast } from "@/socket";

const user = usePage().props.auth?.user;
const roleUser: any = {
    ba: "Business Account - Superadmin",
    admin: "Business Account - Admin",
    spv: "Supervisor",
    am: "AM",
    spv_escalation: "Escalation SPV",
};
const logout = () => {
    leaveConnectionBroadcast();
    router.get(route("auth.logout"));
};
</script>
