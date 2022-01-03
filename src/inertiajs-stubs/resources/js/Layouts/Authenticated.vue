<template>
  <v-app id="inspire" light v-cloak class="bg-gray-100">
    <modal-stack></modal-stack>
    <template v-if="$page.props.auth.user">
      <app-sidebar :state="sidebarState"/>
      <app-toolbar @change="sidebarState = $event"/>
    </template>
    <div class="transition-fade">
      <v-main>
        <v-container p-10>
          <flash-messages />
          <slot />
        </v-container>
      </v-main>
    </div>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      sidebarState: null
    }
  }
}
</script>