<template>
    <div>
        <v-toolbar dark dense text :color="color">

            <v-toolbar-title :class="titleClass">{{title}}</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn v-if="btnText || btnIcon" :href="href" :color="btnColor" :class="btnClass">
                {{btnText}} <v-icon v-if="btnIcon" right dark>{{btnIcon}}</v-icon>
            </v-btn>
            <slot></slot>
        </v-toolbar>
    </div>
</template>

<script>
export default {
    props: ['color', 'titleClass', 'title', 'href', 'btnColor', 'btnClass', 'btnText', 'btnIcon']
}
</script>
