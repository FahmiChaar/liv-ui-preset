<template>
    <v-app-bar class="app-toolbar shadow" color="primary" dark app
        :clipped-left="$vuetify.breakpoint.mdAndUp">
        <v-app-bar-nav-icon v-if="$page.props.auth.user" @click.stop="toggleNavbar"></v-app-bar-nav-icon>
        <inertia-link href="/dashboard" class="d-flex router-link-active">
            <img src="/images/logo.png" style="height:40px">
        </inertia-link>
        <v-spacer></v-spacer>
        <v-toolbar-items>
            <!-- <v-menu offset-y transition="slide-y-transition">
                <template v-slot:activator="{ on }">
                    <v-btn text v-on="on">
                        <v-icon class="mr-1">language</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item ripple href="#">
                        <v-list-item-title>العربية</v-list-item-title>
                    </v-list-item>
                    <v-list-item ripple href="#">
                        <v-list-item-title>English</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu> -->
            <v-menu offset-y transition="slide-y-transition" v-if="$page.props.auth.user">
                <template v-slot:activator="{ on }">
                    <v-btn text v-on="on">
                        <v-icon>account_circle</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item ripple href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <v-list-item-action>
                            <v-icon>power_settings_new</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>Déconnexion</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-form id="logout-form" action="/logout" method="POST" style="display: none;"></v-form>
                </v-list>
            </v-menu>
        </v-toolbar-items>
    </v-app-bar>
</template>

<script>
export default {
    props: ['state'],
    data() {
        return {
            drawer: this.state
        }
    },
    watch: {
        state: function(val) {
            this.drawer = val
        }
    },
    methods: {
        toggleNavbar() {
            this.drawer = !this.drawer
            this.$emit('change', this.drawer)
        }
    }
}
</script>