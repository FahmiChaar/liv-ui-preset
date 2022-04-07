<template>
    <div>
        <breadcrumbs :items="breadcrumbsItems"/>
        <div class="bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="store">
                <div class="p-8">
                    <image-preview 
                        class="mb-5"
                        label="Séléctionez une image"
                        name="avatar"
                        @change="form.avatarImage = $event"
                        :value="form.avatar"
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
                        label="Mobile"
                        type="phone"
                        :error-messages="!form.phone && form.errors.phone ? form.errors.phone : null"
                        v-model="form.phone"
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
                    <v-text-field
                        v-if="form.id"
                        name="password"
                        type="text"
                        v-model="form.password"
                        hint="Laissez le champ vide pour ne pas changer le mot de passe"
                        persistent-hint
                        outlined
                        label="Mot de passe">
                    </v-text-field>
                    <div class="pt-2 flex justify-start items-center">
                        <v-btn :loading="form.processing" color="primary" type="submit" class="capitalize">Enregistrer</v-btn>
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
    props: ['user', 'role'],
    data() {
        return {
            breadcrumbsItems: [
                { text: 'Utilisateurs', href: `${route('dashboard.users.index')}?filter[role]=${this.role}` },
                { text: (this.user ? 'Modifier' : 'Ajouter') }
            ],
            form: this.$inertia.form({
                id: null,
                name: null,
                email: null,
                phone: null,
                avatarImage: null,
                avatar: null,
                role: this.role,
                ...this.user,
                password: null,
            }),
        }
    },
    methods: {
        store() {
            this.form.post(this.route("dashboard.users.store"))
        },
    },
}
</script>