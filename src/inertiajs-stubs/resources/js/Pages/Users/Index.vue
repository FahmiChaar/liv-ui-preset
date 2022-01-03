<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">Users</h1>
        <laravel-data-table 
            :data='datatable'
        >
            <!-- <template #search-after :selectedRows="selectedRows">
                <inertia-link as="v-btn" class="text-capitalize" color="primary" :href="route('dashboard.users.create')">
                    <span>Add </span> 
                    <span class="hidden md:inline">a user</span>
                </inertia-link>
            </template> -->
        </laravel-data-table>
    </div>
</template>

<script>

import AppLayout from '@/Layouts/AppLayout.vue'
export default {
    props: ['datatable'],
    layout: AppLayout,
    methods: {
        
    }
}
</script>

