<template>
  <v-layout>
    <v-flex class="text-center">
      <h1>Components Playground</h1>

      <hero-banner background="/backgrounds/imam-hussain-header.jpg">
        <hero-quote author="Bob Marley">
          Don't worry. Be happy.
        </hero-quote>
      </hero-banner>
    </v-flex>
  </v-layout>
</template>

<script type="ts">
import { defineComponent } from '@vue/composition-api';
import HeroBanner from '@/components/HeroBanner';
import HeroQuote from '@/components/HeroQuote';

export default defineComponent({
  components: {
    HeroBanner,
    HeroQuote,
  },
});
</script>
