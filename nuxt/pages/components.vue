<template>
  <v-layout>
    <v-flex class="text-center">
      <h1>Components Playground</h1>

      <hero-banner background="/backgrounds/imam-hussain-header.jpg">
        <hero-quote author="Bob Marley">
          Don't worry. Be happy.
        </hero-quote>
      </hero-banner>
      <div style="max-width:300px;">
        <reciter-card v-bind="reciter" />
      </div>
      <div style="max-width:300px;">
        <reciter-card v-bind="reciter" featured />
      </div>
    </v-flex>
  </v-layout>
</template>

<script>
import HeroBanner from '@/components/HeroBanner';
import HeroQuote from '@/components/HeroQuote';
import ReciterCard from '@/components/ReciterCard';

export default {
  components: {
    HeroBanner,
    HeroQuote,
    ReciterCard,
  },

  async fetch() {
    const mountains = await fetch(
      'https://api.nuxtjs.dev/mountains',
    ).then(res => res.json());

    this.title = mountains[0].title;
  },

  data: () => ({
    title: 'Component Playground',
    reciter: {
      id: 'ajflasdjflajfalsfj',
      name: 'Zain Mehdi',
      slug: 'zain-mehdi',
      avatar: null,
      related: {
        albums: 2,
      },
    },
  }),

  head() {
    return {
      title: this.title,
    };
  },
};
</script>
