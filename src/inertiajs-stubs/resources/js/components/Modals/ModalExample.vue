<template>
    <v-card>
        <v-card-header :title="'Modal title'" color="primary">
            <v-btn icon color="white" text @click="$modal.close()">
                <v-icon>close</v-icon>
            </v-btn>
        </v-card-header>
        <v-card-text>
            Your content Here
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary">Action Button</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
    
}
</script>