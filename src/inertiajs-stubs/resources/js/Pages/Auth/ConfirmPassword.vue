<template>
<div>
    <div class="mb-4 text-sm text-gray-600">
        This is a secure area of the application. Please confirm your password before continuing.
    </div>

    <form @submit.prevent="submit">
        <v-text-field
            :label="__('dashboard.fields.password')"
            type="password"
            :error-messages="form.errors.password"
            v-model="form.password"
            name="password"
            class="w-full"
            outlined
        ></v-text-field>

        <div class="flex justify-end mt-4">
            <v-btn type="submit" color="primary" class="ml-4" :loading="form.processing" :disabled="form.processing">
                Confirm
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
                    password: '',
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.confirm'), {
                    onFinish: () => this.form.reset(),
                })
            }
        }
    }
</script>
