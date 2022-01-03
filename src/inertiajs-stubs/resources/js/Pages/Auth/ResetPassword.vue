<template>
<div>
    <breeze-validation-errors class="mb-4" />

    <form @submit.prevent="submit">
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
            <v-btn type="submit" color="primary" :loading="form.processing" :disabled="form.processing">
                Reset Password
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
            email: String,
            token: String,
        },

        data() {
            return {
                form: this.$inertia.form({
                    token: this.token,
                    email: this.email,
                    password: '',
                    password_confirmation: '',
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.update'), {
                    onFinish: () => this.form.reset('password', 'password_confirmation'),
                })
            }
        }
    }
</script>
