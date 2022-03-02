<template>
  <div v-if="$page.props.flash || $page.props.errors">
    <!-- <div v-if="$page.props.flash && $page.props.flash.length">
      <v-alert text v-model="show" dismissible transition="fade-transition" dense :type="$page.props.flash[0].level == 'danger' ? 'error' : $page.props.flash[0].level">
        <div v-for="(message, index) of $page.props.flash" :key="index">
          {{message.message}}
        </div>
      </v-alert>
    </div> -->
    <!-- <div v-if="$page.props.errors && Object.keys($page.props.errors).length">
      <v-alert text v-model="show" dismissible transition="fade-transition" dense type="error">
        <div v-for="(message, index) in $page.props.errors" :key="index">
          {{message}}
        </div>
      </v-alert>
    </div> -->
  </div>
</template>

<script>
export default {
  data() {
    return {
      show: true,
    }
  },
  created() {
    setTimeout(() => {
      this.show = false
    }, 7000);
  },
  watch: {
    '$page.props.flash': {
      handler() {
        // this.show = true
        // setTimeout(() => {
        //   this.show = false
        // }, 7000);
        if (this.$page.props.flash && this.$page.props.flash.length) {
          const errorStatus = this.$page.props.flash[0].level == 'danger' ? 'error' : this.$page.props.flash[0].level
          let messages = ''
          for (let message of this.$page.props.flash) {
            messages += message.message + '\n'
          }
          this.$toast[errorStatus](messages)
        }
      },
      deep: true,
    },
    // '$page.props.errors': {
    //   handler() {
    //     this.show = true
    //     setTimeout(() => {
    //       this.show = false
    //     }, 7000);
    //   },
    //   deep: true,
    // },
  },
}
</script>
