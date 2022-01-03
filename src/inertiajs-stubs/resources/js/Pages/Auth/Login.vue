<template>
    <div>
        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <v-text-field
                label="Email"
                type="email"
                :error-messages="form.errors.email"
                v-model="form.email"
                name="email"
                append-icon="email"
                class="w-full"
                outlined
            ></v-text-field>
            <v-text-field
                label="Mot de passe"
                type="password"
                :error-messages="form.errors.password"
                v-model="form.password"
                name="password"
                class="w-full"
                append-icon="lock"
                outlined
            ></v-text-field>

            <div class="flex items-center justify-end mt-4">
                <inertia-link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900">
                    Forgot your password?
                </inertia-link>

                <v-btn type="submit" color="primary" class="ml-4 capitalize" :loading="form.processing" :disabled="form.processing">
                    Log in
                </v-btn>
            </div>
        </form>
    </div>
</template>

<script>
    
    import GuestLayout from '@/Layouts/Guest';

    export default {
        layout: GuestLayout,

        props: {
            canResetPassword: Boolean,
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    password: '',
                    remember: true
                })
            }
        },

        methods: {
            submit() {
                this.form
                    .transform(data => ({
                        ... data,
                        remember: this.form.remember ? 'on' : ''
                    }))
                    .post(this.route('login'), {
                        onFinish: () => this.form.reset('password'),
                    })
            }
        }
    }
</script>
