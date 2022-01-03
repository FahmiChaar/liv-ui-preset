<template>
  <v-app id="inspire" light v-cloak>
    <v-main>
      <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <flash-messages />
        <app-logo />
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
          <slot />
        </div>
      </div>
    </v-main>
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