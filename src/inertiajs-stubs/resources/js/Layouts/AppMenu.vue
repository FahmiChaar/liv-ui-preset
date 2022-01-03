<template>
<div class="app-menu">
    <inertia-link as="v-list-item" :href="route('dashboard.home')" ripple :class="isUrl('') ? 'v-list-item--active primary--text' : ''">
        <v-list-item-action>
            <v-icon>dashboard</v-icon>
        </v-list-item-action>

        <v-list-item-content>
            <v-list-item-title>
                Dashboard
            </v-list-item-title>
        </v-list-item-content>
    </inertia-link>

    <inertia-link as="v-list-item" :href="route('dashboard.users.index')" ripple :class="isUrl('users') ? 'v-list-item--active primary--text' : ''">
        <v-list-item-action>
            <v-icon>account</v-icon>
        </v-list-item-action>

        <v-list-item-content>
            <v-list-item-title>
                Users
            </v-list-item-title>
        </v-list-item-content>
    </inertia-link>
</div>
</template>

<script>
export default {
    methods: {
        isUrl(...urls) {
            let currentUrl = this.$page.url.substr(1)
            currentUrl = currentUrl.replace('dashboard', '')
            currentUrl = currentUrl.replace('/', '')
            if (urls[0] === '') {
                return currentUrl === ''
            }
            return urls.filter(url => currentUrl.startsWith(url)).length
        },
    },
}
</script>