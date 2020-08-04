<template>
  <v-layout>
    <v-container fluid>
      <h1>Components Playground</h1>

      <h3>Hero Banner & Hero Quote</h3>
      <hero-banner background="/backgrounds/imam-hussain-header.jpg">
        <hero-quote author="Bob Marley">
          Don't worry. Be happy.
        </hero-quote>
      </hero-banner>

      <h3>Entity Cards</h3>
      <div style="max-width:300px;">
        <reciter-card v-bind="reciter" />
      </div>
      <div style="max-width:300px;">
        <reciter-card v-bind="reciter" featured />
      </div>
      <div style="max-width:300px;">
        <track-card v-bind="track" />
      </div>

      <h3>Auth</h3>
      <auth-dialog />
      <login-form />
      <register-form />

      <h3>Misc.</h3>
      <app-changelog />
      <labeled-divider />
      <bug-report-form />
      <toaster />

      <h3>Loaders</h3>
      <album-skeleton />
      <lyrics-skeleton />
      <more-tracks-skeleton />
      <reciter-hero-skeleton />
      <skeleton-card-grid />
      <track-card-skeleton />
    </v-container>
  </v-layout>
</template>

<script>
import HeroBanner from '@/components/HeroBanner';
import HeroQuote from '@/components/HeroQuote';
import ReciterCard from '@/components/ReciterCard';
import TrackCard from '@/components/tracks/TrackCard';
import AlbumSkeleton from '@/components/loaders/AlbumSkeleton';
import LyricsSkeleton from '@/components/loaders/LyricsSkeleton';
import MoreTracksSkeleton from '@/components/loaders/MoreTracksSkeleton';
import ReciterHeroSkeleton from '@/components/loaders/ReciterHeroSkeleton';
import SkeletonCardGrid from '@/components/loaders/SkeletonCardGrid';
import TrackCardSkeleton from '@/components/loaders/TrackCardSkeleton';
import AppChangelog from '@/components/notifications/AppChangelog';
import LabeledDivider from '@/components/ui/LabeledDivider';
import BugReportForm from '@/components/BugReportForm';
import Toaster from '@/components/utils/Toaster';
import AuthDialog from '@/components/auth/AuthDialog';
import LoginForm from '@/components/auth/LoginForm';
import RegisterForm from '@/components/auth/RegisterForm';

export default {
  components: {
    HeroBanner,
    HeroQuote,
    ReciterCard,
    TrackCard,
    AlbumSkeleton,
    LyricsSkeleton,
    MoreTracksSkeleton,
    ReciterHeroSkeleton,
    SkeletonCardGrid,
    TrackCardSkeleton,
    AppChangelog,
    LabeledDivider,
    BugReportForm,
    AuthDialog,
    LoginForm,
    RegisterForm,
    Toaster,
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
    track: {
      title: 'Testing Track',
      slug: 'testing-track',
      album: {
        id: 'aljflajfla',
        year: 2020,
        title: 'Ya Hussain AS',
        artwork: null,
      },
      reciter: {
        id: 'ajflasdjflajfalsfj',
        name: 'Zain Mehdi',
        slug: 'zain-mehdi',
        avatar: null,
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

<style scoped>
h3 {
  padding-top: 36px;
  padding-bottom: 20px;
}
</style>
