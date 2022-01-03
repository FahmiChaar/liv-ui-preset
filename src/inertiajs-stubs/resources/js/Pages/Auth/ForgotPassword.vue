<template>
<div>
    <div class="mb-4 text-sm text-gray-600">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </div>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
    </div>

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

        <div class="flex items-center justify-end mt-4">
            <v-btn type="submit" color="primary" class="capitalize" :loading="form.processing" :disabled="form.processing">
                Email Password Reset Link
            </v-btn>
        </div>
    </form>
</div>
</template>

<script>
    import GuestLayout from "@/Layouts/Guest"

    export default {
        layout: GuestLayout,

        props: {
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: ''
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.email'))
            }
        }
    }
</script>
