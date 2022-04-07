<template>
    <div>
        <h1 class="mb-8 font-bold text-3xl">Users</h1>
        <laravel-data-table 
            :data='datatable'
        >
        </laravel-data-table>
    </div>
</template>

<script>

import AppLayout from '@/Layouts/AppLayout.vue'
export default {
    props: ['datatable'],
    layout: AppLayout
}
</script>

