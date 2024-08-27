<script setup>
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Aside from "@/Components/NavBar/Aside.vue";
</script>

<template>
    <body class="body bg-black min-h-screen">
        <div class="fixed w-full z-30 flex bg-gray-950 p-2 items-center justify-center h-16 px-10">
            <div class="logo ml-12 transform ease-in-out duration-500 flex-none h-full flex items-center justify-center text-white font-extrabold">
                <a :href="route('home')" class="text-white">
                    {{ app_name }}
                </a>
            </div>
            <div class="grow h-full flex items-center justify-center"></div>

            <div class="flex-none h-full flex items-center justify-center">
                <div v-if="!$page.props.auth.user">
                    <PrimaryButton>
                        <a :href="route('autentificare')">Authentificare</a>
                    </PrimaryButton>
                </div>

                <div v-else>
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-100 bg-gray-800 hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                                {{ $page.props.auth.user.prenume }} {{ $page.props.auth.user.nume }}

                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                     fill="currentColor">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd" />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink v-if="$page.props.auth.user.tip === 'Organizator'" :href="route('home')" method="get" as="button">
                                Dashboard
                            </DropdownLink>

                            <DropdownLink :href="route('import')" method="get" as="button">
                                Import
                            </DropdownLink>

                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </div>
        </div>

        <aside
            class="w-60 -translate-x-48 bg-gray-950 p-4 fixed transition transform ease-in-out duration-1000 z-50 flex h-screen">
            <div
                class="max-toolbar translate-x-24 scale-x-0 w-full -right-6 transition transform ease-in duration-300 flex items-center justify-between border-4 border-none absolute top-2 rounded-full h-12">

                <div class="flex pl-4 items-center space-x-2 ">
                    <div class="text-white font-extrabold text-xl">
                        <a :href="route('home')">
                            {{ app_name }}
                        </a>
                    </div>
                </div>
            </div>
            <div @click="openNav()"
                 class="-right-6 transition transform ease-in-out duration-500 bg-gray-800 flex cursor-pointer absolute top-2 p-4 rounded-full hover:rotate-180">
                <svg class="h-4 w-4" viewBox="-4.5 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg"
                     xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Dribbble-Light-Preview" transform="translate(-305.000000, -6679.000000)" fill="#FFF">
                            <g id="icons" transform="translate(56.000000, 160.000000)">
                                <path
                                    d="M249.365851,6538.70769 L249.365851,6538.70769 C249.770764,6539.09744 250.426289,6539.09744 250.830166,6538.70769 L259.393407,6530.44413 C260.202198,6529.66364 260.202198,6528.39747 259.393407,6527.61699 L250.768031,6519.29246 C250.367261,6518.90671 249.720021,6518.90172 249.314072,6519.28247 L249.314072,6519.28247 C248.899839,6519.67121 248.894661,6520.31179 249.302681,6520.70653 L257.196934,6528.32352 C257.601847,6528.71426 257.601847,6529.34685 257.196934,6529.73759 L249.365851,6537.29462 C248.960938,6537.68437 248.960938,6538.31795 249.365851,6538.70769"
                                    id="arrow_right-[#336]">

                                </path>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>

            <Aside />
        </aside>

        <section class="relative mx-auto flex flex-col justify-center px-4">
            <div class="fixed left-0 right-0 top-0">
                <div class="left-16 top-0 overflow-visible opacity-50">
                    <div class="circle-obj absolute h-[900px] w-[700px] rounded-[40rem] mix-blend-multiply"
                         :style="getRandomTranslate()" style="pointer-events: none;"></div>
                </div>
                <div class="absolute left-52 top-28 overflow-visible opacity-50">
                    <div class="circle-obj2 absolute h-[900px] w-[700px] rounded-[40rem] mix-blend-multiply"
                         :style="getRandomTranslate()" style="pointer-events: none;"></div>
                </div>
                <div class="absolute left-20 top-32 overflow-visible opacity-0">
                    <div class="circle-obj3 absolute h-[900px] w-[700px] rounded-[40rem] mix-blend-multiply"
                         :style="getRandomTranslate()" style="pointer-events: none;"></div>
                </div>
            </div>
        </section>

        <div class="content ml-12 transform ease-in-out duration-500 pt-20 px-2 md:px-5 pb-4 ">
            <nav class="px-5 py-3 rounded-lg w-full">
                <slot />
            </nav>
        </div>
    </body>
</template>

<script>
export default {
    props: {
        userIsSubscribed: Boolean,
        userIsOnTrial: Boolean,
    },
    data() {
        return {
            userTeamTreasholdReached : Boolean || true,
            sidebar: null,
            maxSidebar: null,
            miniSidebar: null,
            roundout: null,
            maxToolbar: null,
            logo: null,
            content: null,
            moon: null,
            sun: null,
            app_name: import.meta.env.VITE_APP_NAME,
            image: null
        }
    },

    mounted() {
        this.userTeamTreasholdReached = this.$page.props.userTeamTreasholdReached;
        this.sidebar = document.querySelector("aside");
        this.maxSidebar = document.querySelector(".max");
        this.miniSidebar = document.querySelector(".mini");
        this.roundout = document.querySelector(".roundout");
        this.maxToolbar = document.querySelector(".max-toolbar");
        this.logo = document.querySelector('.logo');
        this.content = document.querySelector('.content');
        this.moon = document.querySelector(".moon");
        this.sun = document.querySelector(".sun");
        // this.image = 'https://ui-avatars.com/api/?name=' + this.$page.props.auth.user.name
    },

    methods: {
        switchToTeam(team) {
            router.put(route('current-team.update'), {
                team_id: team.id,
            }, {
                preserveState: false,
            });
        },
        checkSubscriptionStatus () {
            return  true;
        },

        logout() {
            router.post(route('logout'));
        },

        getRandomTranslate() {
            const randomTranslateX = `${Math.random() * window.innerWidth}px`;
            const randomTranslateY = `${Math.random() * window.innerHeight}px`;

            return {
                transform: `translate(${randomTranslateX}, ${randomTranslateY})`,
            };
        },

        setDark(val) {
            if (val === "dark") {
                document.documentElement.classList.add('dark')
                this.moon.classList.add("hidden")
                this.sun.classList.remove("hidden")
            } else {
                document.documentElement.classList.remove('dark')
                this.sun.classList.add("hidden")
                this.moon.classList.remove("hidden")
            }
        },

        openNav() {
            if (this.sidebar.classList.contains('-translate-x-48')) {
                this.sidebar.classList.remove("-translate-x-48")
                this.sidebar.classList.add("translate-x-none")
                this.maxSidebar.classList.remove("hidden")
                this.maxSidebar.classList.add("flex")
                this.miniSidebar.classList.remove("flex")
                this.miniSidebar.classList.add("hidden")
                this.maxToolbar.classList.add("translate-x-0")
                this.maxToolbar.classList.remove("translate-x-24", "scale-x-0")
                this.logo.classList.remove("ml-12")
                this.content.classList.remove("ml-12")
                this.content.classList.add("ml-12", "md:ml-60")
            } else {
                this.sidebar.classList.add("-translate-x-48")
                this.sidebar.classList.remove("translate-x-none")
                this.maxSidebar.classList.add("hidden")
                this.maxSidebar.classList.remove("flex")
                this.miniSidebar.classList.add("flex")
                this.miniSidebar.classList.remove("hidden")
                this.maxToolbar.classList.add("translate-x-24", "scale-x-0")
                this.maxToolbar.classList.remove("translate-x-0")
                this.logo.classList.add('ml-12')
                this.content.classList.remove("ml-12", "md:ml-60")
                this.content.classList.add("ml-12")

            }
        }
    },
}
</script>


<style>
.circle-obj {
    background: radial-gradient(closest-side, #2dd4bf, rgba(233, 168, 2, 0));
    animation: traverse-up-left 10s ease-in-out infinite alternate;
}

.circle-obj2 {
    background: radial-gradient(closest-side, #5154ee, rgba(233, 168, 2, 0));
    animation: traverse-up-right 12s ease-in-out infinite alternate;
}

.circle-obj3 {
    background: radial-gradient(closest-side, #e8ee43d9, rgba(233, 168, 2, 0));
    animation: traverse-down-right 8s ease-in-out infinite alternate;
}

@keyframes traverse-up-left {
    100% {
        transform: translateY(-200px) translateX(-350px) rotate(180deg);
    }
}

@keyframes traverse-up-right {
    100% {
        transform: translateY(-300px) translateX(300px) rotate(1turn);
    }
}

@keyframes traverse-down-right {
    100% {
        transform: translateY(250px) translateX(300px) rotate(1turn);
    }
}
</style>

