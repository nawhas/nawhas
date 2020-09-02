<template>
  <header :class="{ 'header': true, 'header--dark': $vuetify.theme.dark }">
    <v-container class="app__section">
      <h1 class="header__title">
        <slot />
      </h1>
    </v-container>
  </header>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({

});
</script>

<style lang="scss" scoped>
@import '~assets/theme';

.header {
  width: 100%;
  background-color: rgba(255, 21, 0, 0.1);
  margin-bottom: 48px;
  position: relative;
}
.header--dark {
  background: linear-gradient(30deg, #340808, #1a0000);
}
.header .app__section {
  padding: 96px !important;
}
.header__title {
  font-family: Roboto, sans-serif;
  font-weight: 200;
  font-size: 64px;
  line-height: 75px;
  text-align: center;
  letter-spacing: -1.5px;
}

@include breakpoint('sm-and-down') {
  .header {
    margin-bottom: 0;
  }
  .header .app__section {
    padding: 32px !important;
  }

  .header__title {
    font-size: 48px;
  }
}
</style>
