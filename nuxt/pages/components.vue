<template>
  <v-layout>
    <v-container>
      <h1>Components Playground</h1>

      <h3>Hero Banner & Hero Quote</h3>
      <hero-banner background="/backgrounds/imam-hussain-header.jpg">
        <hero-quote author="Bob Marley">
          Don't worry. Be happy.
        </hero-quote>
      </hero-banner>

      <div style="background-color: black">
        <edit-reciter-dialog :reciter="reciter" />
      </div>

      <div style="background-color: red">
        <edit-album-dialog :album="albums[0]" :reciter="reciter" />
      </div>

      <div style="background-color: black">
        <edit-track-dialog :track="track" :album="albums[0]" />
      </div>

      <h3>Entity Cards</h3>
      <v-row>
        <v-col cols="4">
          <reciter-card v-bind="reciter" />
        </v-col>
        <v-col cols="4">
          <reciter-card v-bind="reciter" featured />
        </v-col>
        <v-col cols="4">
          <track-card :track="track" />
        </v-col>
      </v-row>

      <h3>Auth</h3>
      <v-row>
        <v-col cols="6">
          <login-form />
        </v-col>
        <v-col cols="6">
          <register-form />
        </v-col>
      </v-row>

      <h3>Misc. Dialogs</h3>
      <v-row>
        <v-col cols="6">
          <app-changelog />
        </v-col>
        <v-col cols="6">
          <bug-report-form />
        </v-col>
      </v-row>

      <h3>Layout</h3>
      <labeled-divider />

      <h3>Loaders</h3>
      <album-skeleton />
      <lyrics-skeleton />
      <more-tracks-skeleton />
      <reciter-hero-skeleton />
      <skeleton-card-grid />
      <track-card-skeleton />

      <!-- Util -->
      <toaster />
    </v-container>
  </v-layout>
</template>

<script>
import HeroBanner from '@/components/HeroBanner';
import HeroQuote from '@/components/HeroQuote';
import EditReciterDialog from '@/components/edit/EditReciterDialog';
import EditAlbumDialog from '@/components/edit/EditAlbumDialog';
import EditTrackDialog from '@/components/edit/EditTrackDialog';
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
import LoginForm from '@/components/auth/LoginForm';
import RegisterForm from '@/components/auth/RegisterForm';
import { AlbumIncludes } from '@/api/albums';

export default {
  components: {
    HeroBanner,
    HeroQuote,
    EditReciterDialog,
    EditAlbumDialog,
    EditTrackDialog,
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
    LoginForm,
    RegisterForm,
    Toaster,
  },

  async fetch() {
    const reciter = await this.$api.reciters.get('4105f6a8-5407-11ea-922c-6eb465563d0f', {});
    const [albums] = await Promise.all([
      this.$api.albums.index(reciter.id, {
        include: [
          AlbumIncludes.Reciter,
          AlbumIncludes.Related,
          AlbumIncludes.Tracks,
        ],
      }),
    ]);
    this.title = reciter.name;
    this.reciter = reciter;
    this.albums = albums.data;
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
    albums: [],
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
