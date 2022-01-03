<template>
<div>
    <breeze-validation-errors class="mb-4" />

    <form @submit.prevent="submit">
        <v-text-field
            :label="__('dashboard.fields.name')"
            type="text"
            :error-messages="form.errors.name"
            v-model="form.name"
            name="name"
            class="w-full"
            outlined
        ></v-text-field>

        <v-text-field
            :label="__('dashboard.fields.email')"
            type="email"
            :error-messages="form.errors.email"
            v-model="form.email"
            name="email"
            class="w-full"
            outlined
        ></v-text-field>

        <v-text-field
            :label="__('dashboard.fields.password')"
            type="password"
            :error-messages="form.errors.password"
            v-model="form.password"
            name="password"
            class="w-full"
            outlined
        ></v-text-field>

        <v-text-field
            :label="__('dashboard.fields.password_confirmation')"
            type="password_confirmation"
            :error-messages="form.errors.password_confirmation"
            v-model="form.password_confirmation"
            name="password_confirmation"
            class="w-full"
            outlined
        ></v-text-field>

        <div class="flex items-center justify-end mt-4">
            <inertia-link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900">
                Already registered?
            </inertia-link>

            <v-btn type="submit" color="primary" class="ml-4" :loading="form.processing" :disabled="form.processing">
                Register
            </v-btn>
        </div>
    </form>
</div>
</template>

<script>

    import GuestLayout from "@/Layouts/Guest"

    export default {
        layout: GuestLayout,

        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    terms: false,
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('register'), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                })
            }
        }
    }
</script>
