<router>
  path: /about
  name: "about"
</router>

<template>
  <div class="about">
    <hero-banner :background="require('@/assets/img/backgrounds/shrine.jpg')" class="mb-4">
      <hero-quote author="Imam Al-Ridha (a.s.)">
        I desire that you recite for me poetry, for surely,
        these days are the days of grief and sorrow,
        which have passed over us, Ahlul Bayt.
      </hero-quote>
    </hero-banner>

    <v-container class="app__section">
      <h5 class="section__title text-center mt-4 mb-8 display-1">
        The Journey
      </h5>
      <v-timeline class="timeline" :dense="$vuetify.breakpoint.mdAndDown">
        <v-timeline-item
          v-for="({date, headline, text}, i) in timeline"
          :key="i"
          color="primary"
          small
        >
          <template v-slot:opposite>
            <span class="headline font-weight-bold" v-text="date" />
          </template>
          <div class="py-4">
            <span
              v-if="$vuetify.breakpoint.mdAndDown"
              class="headline font-weight-bold"
              v-text="date"
            />
            <h2 class="headline font-weight-light mb-4">
              {{ headline }}
            </h2>
            <div v-html="text" />
          </div>
        </v-timeline-item>
      </v-timeline>
    </v-container>
    <v-container class="app__section">
      <h5 class="section__title text-center mt-4 mb-8 display-1">
        Credits
      </h5>
      <v-row>
        <v-col v-for="(contributor, i) in contributors" :key="i" cols="12" lg="4">
          <v-card class="credit__card" outlined>
            <v-list-item>
              <v-list-item-avatar color="grey">
                <img v-if="contributor.avatar" :src="contributor.avatar" :alt="contributor.name">
                <v-icon v-else color="white">
                  person
                </v-icon>
              </v-list-item-avatar>
              <v-list-item-content>
                <v-list-item-title class="subtitle-1 font-weight-regular">
                  {{ contributor.name }}
                </v-list-item-title>
                <v-list-item-subtitle>{{ contributor.caption }}</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
            <v-card-text class="credit__card__text">
              <ul>
                <li v-for="(item, j) in contributor.contributions" :key="j">
                  {{ item }}
                </li>
              </ul>
            </v-card-text>
            <v-card-actions>
              <v-btn
                v-for="(link, k) in contributor.links"
                :key="k"
                text
                :href="link.href"
                target="_blank"
              >
                <v-icon left>
                  {{ link.icon }}
                </v-icon> {{ link.text }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
    <hero-banner :background="require('@/assets/img/backgrounds/azadari-sunset.jpg')" :opacity="0.76">
      <v-container class="app__section app__section--dark app__section--padded">
        <h5 class="section__title text-center mt-4 mb-8 display-1">
          Contribute
        </h5>
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
              <v-icon left>
                {{ github }}
              </v-icon> Github
            </v-btn>
          </div>
        </div>
      </v-container>
    </hero-banner>
  </div>
</template>

<script lang="ts">
import Vue from 'vue';
import HeroBanner from '@/components/HeroBanner.vue';
import HeroQuote from '@/components/HeroQuote.vue';
import { mdiGithub } from '@mdi/js';
import { generateMeta } from '@/utils/meta';

export default Vue.extend({
  components: {
    HeroBanner,
    HeroQuote,
  },

  data: () => ({
    github: mdiGithub,
  }),

  computed: {
    timeline() {
      return [
        {
          date: '1997',
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
          date: '2017',
          headline: 'Work Begins on Rebuilding Nawhas.com',
          text: `
          A young software engineer and frequent user of Nawhas.com, Syed Zain Mehdi reached out
          from California to Shabbir & Abbas Tejani with an offer to voluntarily rebuild the
          Nawhas.com site for the modern web. Work begins on the new site. Soon after,
          Asif Ali from the U.K. joins the team, similarly eager to use his skills
          to contribute back to a site he's used for years.
        `,
        },
        {
          date: '2020',
          headline: 'A New Start',
          text: `
          After hundreds of hours of work sprinkled in between their primary responsiblities,
          Syed Zain Mehdi and Asif Ali complete their combined efforts to rebuild and
          modernize the site. The new version of Nawhas.com launched in March, 2020.
        `,
        },
      ];
    },
    contributors() {
      return [
        {
          name: 'Shabbir & Abbas Tejani',
          avatar: require('@/assets/img/about/credits/tejani-brothers.jpg'),
          caption: 'Creators of Nawhas.com',
          contributions: [
            'Created & maintained Nawhas.com for over 17 years',
            'Wrote write-ups for over 1400 nawhas',
          ],
          links: [
            { icon: 'public', text: 'Website', href: 'http://www.tejanibrothers.com' },
          ],
        },
        {
          name: 'Syed Zain Mehdi',
          avatar: require('@/assets/img/about/credits/zain.jpg'),
          caption: 'Software Engineer',
          contributions: [
            'Project lead for the new Nawhas.com',
            'Redesigned and rebuilt the site',
            'Maintainer of the open source codebase',
          ],
          links: [
            { icon: 'public', text: 'Website', href: 'https://szainmehdi.me' },
          ],
        },
        {
          name: 'Asif Ali',
          avatar: require('@/assets/img/about/credits/asif.jpg'),
          caption: 'Software Engineer',
          contributions: [
            'Devoted hundreds of hours of engineering work on the new Nawhas.com',
            'Maintainer of the open source codebase',
          ],
          links: [
            { icon: mdiGithub, text: 'Github', href: 'https://github.com/shea786' },
          ],
        },
      ];
    },
  },

  head: () => generateMeta({
    title: 'About',
    description: 'Learn about the history behind Nawhas.com, and our plans for the future.',
  }),
});
</script>

<style lang="scss">
@import "~assets/theme";

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
    min-height: 140px;
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
  }
}

@media #{map-get($display-breakpoints, 'md-and-down')} {
  .about {
    .credit__card__text {
      min-height: 0;
    }
  }
}
</style>
