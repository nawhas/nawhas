<template>
  <div>
    <hero-banner :background="require('../../assets/shrine.jpg')" class="mb-4">
      <hero-quote author="Imam Al-Ridha (a.s.)">
        I desire that you recite for me poetry, for surely,
        these days are the days of grief and sorrow,
        which have passed over us, Ahlul Bayt.
      </hero-quote>
    </hero-banner>
  </div>
</template>

<script>
import HeroBanner from '@/components/HeroBanner.vue';
import HeroQuote from '@/components/HeroQuote.vue';

export default {
  name: 'Home',
  components: {
    HeroBanner,
    HeroQuote,
  },
};
</script>
