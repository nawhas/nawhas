<template>
  <v-container :class="['hero-quote', 'hero-quote--' + $vuetify.breakpoint.name]">
    <div class="hero-quote__wrapper">
      <div class="hero-quote__lig hero-quote__lig--left">
        &ldquo;
      </div>
      <div class="hero-quote__content">
        <slot />
      </div>
      <div class="hero-quote__lig hero-quote__lig--right">
        &rdquo;
      </div>
    </div>
    <div class="hero-quote__author">
      &mdash; {{ author }}
    </div>
  </v-container>
</template>

<script lang="ts">
import { defineComponent } from '@vue/composition-api';

export default defineComponent({
  name: 'HeroQuote',
  props: {
    author: {
      type: String,
      required: true,
    },
  },
});
</script>

<style lang="scss" scoped>
.hero-quote {
  font-family: 'Bellefair', 'Roboto Slab', serif;
  color: white;
  padding: 24px 80px;
  margin: 0 auto;

  .hero-quote__wrapper {
    display: flex;
    text-align: center;
    align-items: center;
    justify-content: center;
  }

  .hero-quote__content {
    font-size: 32px;
  }

  .hero-quote__lig {
    font-size: 112px;
    height: 112px;
  }

  .hero-quote__author {
    font-size: 22px;
    text-align: right;
  }
}

.hero-quote--xs {
  padding: 36px 24px;
  max-width: 90%;
  margin: 0 auto;

  .hero-quote__content {
    font-size: 22px;
  }
  .hero-quote__lig {
    font-size: 48px;
    height: 48px;
  }
  .hero-quote__author {
    font-size: 16px;
  }
}

.hero-quote--sm {
  padding: 36px 24px;
  max-width: 85%;
  margin: 0 auto;

  .hero-quote__content {
    font-size: 32px;
  }
  .hero-quote__lig {
    font-size: 58px;
    height: 58px;
  }
  .hero-quote__author {
    font-size: 20px;
  }
}
</style>
