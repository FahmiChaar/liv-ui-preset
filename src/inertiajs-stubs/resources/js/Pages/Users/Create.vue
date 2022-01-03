<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">
            <inertia-link
                class="text-blue-400 hover:text-blue-500"
                :href="route('dashboard.books.store')"
                >Users</inertia-link
            >
            <span class="font-extralight">-</span> 
            <template class="text-sm" v-if="!form.id">Create</template>
            <template v-else>Edit</template>
        </h1>
        <div class="bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="store">
                <div class="p-8">
                    <image-preview 
                        class="mb-5"
                        label="Select your Avatar"
                        name="avatar"
                        @change="form.avatarImage = $event"
                        :value="form.avatar"
                        :error="!form.avatarImage && form.errors.avatarImage ? form.errors.avatarImage : null"
                        type="avatar">
                    </image-preview>
                    <v-text-field
                        label="Name"
                        type="text"
                        :error-messages="!form.name && form.errors.name ? form.errors.name : null"
                        v-model="form.name"
                        class="w-full"
                        outlined
                    ></v-text-field>
                    <v-text-field
                        label="Email"
                        type="email"
                        :error-messages="!form.email && form.errors.email ? form.errors.email : null"
                        v-model="form.email"
                        class="w-full"
                        outlined
                    ></v-text-field>
                    <div class="pt-2 flex justify-start items-center">
                        <v-btn :loading="form.processing" color="primary" type="submit" class="capitalize">
                            Save
                        </v-btn>
                        <div class="p-3 flex-1" v-if="form.progress">
                            <v-progress-linear 
                                :value="form.progress.percentage"
                                rounded
                                height="20"    
                            >
                                <strong class="text-white text-sm">{{ Math.ceil(form.progress.percentage) }}%</strong>
                            </v-progress-linear>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import AppLayoutVue from '@/Layouts/AppLayout.vue';

export default {
    layout: AppLayoutVue,
    props: ['user'],
    data() {
        return {
            form: this.$inertia.form({
                id: null,
                name: null,
                email: null,
                avatarImage: null,
                avatar: null,
                ...this.user
            }),
        }
    },
    methods: {
        store() {
            this.form.post(this.route("dashboard.books.store"))
        },
    },
}
</script>