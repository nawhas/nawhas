<template>
  <v-footer class="footer">
    <v-container class="footer-content">
      <div class="content__section content__section--left">
        <nuxt-link class="footer-link" to="/about">
          About
        </nuxt-link>
      </div>
      <div class="content__section content__section--center">
        <nuxt-link class="footer-link" to="/">
          <footer-icon />
        </nuxt-link>
      </div>
      <div class="content__section content__section--right">
        <a href="#" class="footer-link" @click.prevent="showFeedbackDialog">
          Feedback
        </a>
      </div>
    </v-container>
  </v-footer>
</template>

<script lang="ts">
import { Component, Vue } from 'nuxt-property-decorator';
import { EventBus } from '@/events';
import { SHOW_FEEDBACK } from '@/events/dialogs';
const FooterIcon = require('@/assets/svg/footer-icon.svg?inline');

@Component({
  components: {
    FooterIcon,
  },
})
export default class AppFooter extends Vue {
  showFeedbackDialog() {
    EventBus.$emit(SHOW_FEEDBACK);
  }
}
</script>

<style lang="scss" scoped>
.footer-content {
  padding: 12px 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.content__section {
  display: flex;
  align-items: center;
  flex: 1;
}
.content__section--left {
  justify-content: flex-start;
}
.content__section--right {
  justify-content: flex-end;
}
.content__section--center {
  justify-content: center;
}
.footer-link {
  text-decoration: none;
}
</style>
