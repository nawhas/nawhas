<template>
  <div class="about">
    <hero-banner :background="require('../../assets/shrine.jpg')" class="mb-4">
      <hero-quote author="Imam Al-Ridha (a.s.)">
        I desire that you recite for me poetry, for surely,
        these days are the days of grief and sorrow,
        which have passed over us, Ahlul Bayt.
      </hero-quote>
    </hero-banner>

    <v-container class="app__section">
      <h5 class="section__title text-center mt-4 mb-8 display-1">The Journey</h5>
      <v-timeline class="timeline" :dense="$vuetify.breakpoint.mdAndDown">
        <v-timeline-item
          v-for="({date, headline, text}, i) in timeline"
          :key="i"
          color="primary"
          small
        >
          <template v-slot:opposite>
            <span class="headline font-weight-bold" v-text="date"></span>
          </template>
          <div class="py-4">
            <span v-if="$vuetify.breakpoint.mdAndDown"
                  class="headline font-weight-bold"
                  v-text="date"></span>
            <h2 class="headline font-weight-light mb-4">{{ headline }}</h2>
            <div v-html="text"></div>
          </div>
        </v-timeline-item>
      </v-timeline>
    </v-container>
    <v-container class="app__section">
      <h5 class="section__title text-center mt-4 mb-8 display-1">Credits</h5>
      <v-row>
        <v-col cols="12" lg="4">
          <v-card  class="credit__card" outlined>
            <v-list-item>
              <v-list-item-avatar>
                <img src="../../assets/credits/tejani-brothers.jpg"
                     alt="Tejani Brothers" />
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title class="subtitle-1 font-weight-regular">
                  Shabbir & Abbas Tejani
                </v-list-item-title>
                <v-list-item-subtitle>Creators of Nawhas.com</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-card-text class="credit__card__text">
              <ul>
                <li>Created & maintained Nawhas.com for over 17 years.</li>
                <li>Wrote over 1400 write-ups</li>
              </ul>
            </v-card-text>

            <v-card-actions>
              <v-btn text href="http://www.tejanibrothers.com" target="_blank">Website</v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12" lg="4">
          <v-card  class="credit__card" outlined>
            <v-list-item>
              <v-list-item-avatar>
                <img src="../../assets/credits/zain.jpg"
                     alt="Syed Zain Mehdi" />
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title class="subtitle-1 font-weight-regular">
                  Syed Zain Mehdi
                </v-list-item-title>
                <v-list-item-subtitle>Software Engineer</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-card-text class="credit__card__text">
              <ul>
                <li>Designed and re-built the new Nawhas.com</li>
                <li>Maintainer of the open source codebase.</li>
              </ul>
            </v-card-text>
            <v-card-actions>
              <v-btn text href="https://szainmehdi.me" target="_blank">Website</v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
        <v-col cols="12" lg="4">
          <v-card  class="credit__card" outlined>
            <v-list-item>
              <v-list-item-avatar color="grey">
                <v-icon color="white">person</v-icon>
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title class="subtitle-1 font-weight-regular">
                  Syed Asif Ali
                </v-list-item-title>
                <v-list-item-subtitle>Software Engineer</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>

            <v-card-text class="credit__card__text">
              <ul>
                <li>Designed and re-built the new Nawhas.com</li>
                <li>Maintainer of the open source codebase.</li>
              </ul>
            </v-card-text>
            <v-card-actions>
              <v-btn text href="" target="_blank">Website</v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <hero-banner :background="require('../../assets/azadari-sunset.jpg')" :opacity="0.76">
      <v-container class="app__section app__section--dark app__section--padded">
        <h5 class="section__title text-center mt-4 mb-8 display-1">Contribute</h5>
        <div class="section__text">
          <p class="title text-center mb-8">
            We're hard at work on the next stages on Nawhas.com.
          </p>
          <p class="body-1">
            In the coming months, we plan to launch a major feature that will
            enable the site to remain active and up to date for years to come. Anyone will
            be able to contribute to the site! You'll be able to add and correct write-ups for nawhas,
            and even adding missing reciters, albums, and nawhas!
          </p>
          <p class="body-1">
            <strong>Know how to code?</strong> Check out our open source codebase on Github.
            Feel free to open a PR with any contributions – we'll actively review and merge
            any contributions!
          </p>
          <div class="text-center mt-8">
            <v-btn large href="https://github.com/nawhas/nawhas" target="_blank">
              <v-icon color="black" left>mdi-github-circle</v-icon> Github
            </v-btn>
          </div>
        </div>
      </v-container>
    </hero-banner>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';
import HeroBanner from '@/components/HeroBanner.vue';
import HeroQuote from '@/components/HeroQuote.vue';

@Component({
  components: {
    HeroBanner,
    HeroQuote,
  },
})
export default class About extends Vue {
  get timeline() {
    return [
      {
        date: '1995',
        headline: 'The Beginning',
        text: `
          Shabbir & Abbas Tejani, now better know as The Tejani Brothers, started a website at the
           ages of 10 and 11 on Geocitites.com posting write-ups from their mother's
           small book, which was getting old and torn.
        `,
      },
      {
        date: '2003',
        headline: 'Nawhas.com Launched',
        text: `
          Their passion developed further, and they began adding more and more reciters onto
          the site, some due to requests by the nawhakwans themselves and some out of choice.
          <blockquote class="timeline__quote">
            &ldquo;From our experience and also being nawhakhwans ourselves, we know that there are many,
            many reciters who have a deep passion to recite but are unable to do so due
            to the unavailability of kalaams. To encourage other reciters, we, therefore,
            embarked upon putting the kalaams on our website by listening and writing
            the kalaams from the audios of other nawhakhwans.&rdquo;
            <cite>&mdash; Tejani Brothers</cite>
          </blockquote>
        `,
      },
      {
        date: '2020',
        headline: 'A New Start',
        text: `
          Syed Zain Mehdi and Syed Asif Ali finished hundreds of hours of work to completely
          rebuild and modernize the site.
        `,
      },
    ];
  }
}
</script>

<style lang="scss">
@import "../../styles/theme";

.about {
  blockquote.timeline__quote {
    padding-left: 24px;
    margin-top: 12px;
    font-family: "Roboto Slab", sans-serif;
    border-left: 1px solid orangered;

    cite {
      font-family: "Roboto", sans-serif;
      display: block;
      text-align: right;
      margin-top: 12px;
    }
  }
  .credit__card {
    margin: 0 auto;
    padding: 8px;
  }
  .credit__card__text {
    min-height: 100px;
  }
  .app__section {
    margin-bottom: 48px;
  }
  .app__section--dark {
    color: white;
  }
  .app__section--padded {
    padding: 96px !important;
    margin: 0;
  }
  .section__text {
    max-width: 800px;
  }
}

@media #{map-get($display-breakpoints, 'xs-only')} {
  .about {
    .app__section--padded {
      padding: 48px 24px !important;
    }
    .credit__card__text {
      min-height: 0;
    }
  }
}
</style>
