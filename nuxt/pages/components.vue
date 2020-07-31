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

<script>
import HeroBanner from '@/components/HeroBanner';
import HeroQuote from '@/components/HeroQuote';

export default {
  components: {
    HeroBanner,
    HeroQuote,
  },

  async fetch() {
    const mountains = await fetch(
      'https://api.nuxtjs.dev/mountains',
    ).then(res => res.json());

    this.title = mountains[0].title;
  },

  data: () => ({
    title: 'Component Playground',
  }),

  head() {
    return {
      title: this.title,
    };
  },
};
</script>
