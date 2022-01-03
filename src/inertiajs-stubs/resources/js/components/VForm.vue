<template>
    <ValidationObserver v-slot="{ handleSubmit }" slim ref="observer">
        <form @submit.prevent="action ? handleSubmit(onSubmit) : validate()" 
            :action="action" method="POST" ref="vForm" enctype="multipart/form-data"
            v-bind="$attrs"
        >
            <template v-if="action">
                <input v-if="csrfToken" type="hidden" name="_token" :value="csrfToken">
                <input v-if="method && method.toLowerCase() != 'post'" type="hidden" name="_method" :value="method">
            </template>
            <slot></slot>
        </form>
    </ValidationObserver>
</template>

<script>
export default {
    props: ['action', 'method'],
    data() {
        return {
            csrfToken: null
        }
    },
    created: function() {
        if (this.action) {
            this.csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
            if (!this.csrfToken) {
                console.error('VForm => CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
            }
        }
    },
    methods: {
        onSubmit() {
            this.$emit('on-submit')
            this.$root.$emit('v-form-submit')
            if (this.action) {
                this.$refs.vForm.submit();
            }
        },
        async validate() {
            const validation = await this.$refs.observer.validate()
            if (validation) {
                this.$emit('on-validate')
            }
        }
    }
}
</script>