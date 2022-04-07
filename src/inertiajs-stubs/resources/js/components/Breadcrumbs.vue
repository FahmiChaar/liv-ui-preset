<template>
  <v-breadcrumbs class="px-0" :items="items">
    <template v-slot:item="{ item }">
      <v-breadcrumbs-item class="text-3xl font-bold"
        :disabled="item.disabled || !item.href"
      >
        <inertia-link v-if="item.href" :href="item.href">{{ item.text }}</inertia-link>
        <template v-else>{{ item.text }}</template>
      </v-breadcrumbs-item>
    </template>
    <template v-slot:divider>
        <v-icon v-if="dividerIcon">{{ dividerIcon }}</v-icon>
        <template v-else>{{ divider || '/' }}</template>
    </template>
  </v-breadcrumbs>
</template>

<script>
export default {
    props: ['items', 'divider', 'dividerIcon']
}
</script>