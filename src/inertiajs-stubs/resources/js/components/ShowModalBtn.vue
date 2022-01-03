<template>
    <v-btn v-bind="$attrs" small :color="color || 'secondary'" :disabled="disabled" @click="$modal.show({
        component,
        props,
        options
    })">
        <slot></slot>
    </v-btn>
</template>

<script>
export default {
    props: ['component', 'props', 'options', 'color', 'disabled']
}
</script>