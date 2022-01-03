<template>
    <v-menu
        v-model="menu"
        :close-on-content-click="false"
        lazy
        transition="scale-transition"
        offset-y
        full-width
        min-width="290px"
    >
        <template v-slot:activator="{ on }">
            <v-text-field
                v-model="date"
                :label="label"
                :rules="[rules]"
                readonly
                :name="name"
                outlined
                v-on="on">
            </v-text-field>
        </template>
        <v-date-picker v-model="date" @input="menu = false"></v-date-picker>
        <input type="hidden" :name="name" :value="date">
    </v-menu>
</template>

<script>
export default {
    props: ['name', 'value', 'label', 'rules'],
    data() {
        return {
            date: this.value,
            menu: false
        }
    }
}
</script>