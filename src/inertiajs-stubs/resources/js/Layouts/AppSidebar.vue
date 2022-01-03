<template>
  <v-navigation-drawer
    v-model="drawer"
    @input="onDrawerStateChanged"
    app
    :right="$appLocal == 'ar'"
    :clipped="$vuetify.breakpoint.mdAndUp"
    :mini-variant="$vuetify.breakpoint.xsOnly"
  >
    <v-list class="pa-1 sidebar-profile-container">
      <inertia-link as="v-list-item"
        two-line
        :class="$vuetify.breakpoint.xsOnly && 'px-0'"
        href="#"
      >
        <!-- :href="route('dashboard.users.edit', $page.props.auth.user.id)" -->
        <v-list-item-avatar>
          <img class="bg-gray-400 border border-solid border-gray-500 object-cover" src="/images/logo.png" />
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>{{ $page.props.auth.user.name }}</v-list-item-title>
        </v-list-item-content>
      </inertia-link>
    </v-list>

    <v-divider light></v-divider>

    <v-list nav dense>
      <app-menu />
    </v-list>
  </v-navigation-drawer>
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
    onDrawerStateChanged(ev) {
      this.$emit('change', ev)
    }
  }
}
</script>