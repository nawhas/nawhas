<template>
  <v-app>
    <v-content
      class="content"
      v-if="data"
    >
      <div class="print__header">
        <div class="print__header__title">
          <div class="print__header__title--track--name">{{ data.title }}</div>
          <div class="print__header__title--track--meta">{{ data.reciter.name }} - {{ data.year }}</div>
        </div>
        <v-spacer></v-spacer>
        <div class="print__header_logo">
          <img
            src="./../assets/logo.svg"
            alt="Nawhas.com"
          />
        </div>
      </div>
      <lyrics-viewer
        class="print__content"
        v-if="data.lyrics"
        :model="data.lyrics"
        :current="false"
      ></lyrics-viewer>
      <div class="print__content print__content--empty" v-else>
        We don't have a write-up of this nawha yet.
      </div>
    </v-content>
    <v-content
      class="content"
      v-else
    >Loading...</v-content>
  </v-app>
</template>

<script>
/* eslint-disable dot-notation */
import { getTrack } from '@/services/tracks';
import LyricsViewer from '@/components/LyricsViewer.vue';

export default {
  props: ['trackObject'],

  data: () => ({
    fetchedTrack: undefined,
    timeout: undefined,
  }),

  components: {
    LyricsViewer,
  },

  computed: {
    data() {
      return this.trackObject || this.fetchedTrack;
    },
  },

  created() {
    if (!this.track) {
      const { reciter, album, track } = this.$route.params;
      getTrack(reciter, album, track, { include: 'reciter,lyrics,album.tracks' })
        .then((response) => {
          this.fetchedTrack = response.data;
        });
    }
  },

  mounted() {
    const handler = () => {
      this.goBackToTrack();
    };
    this.$el['__onPrintCompleteHandler__'] = handler;
    window.addEventListener('afterprint', handler);

    if (!this.data) {
      return;
    }

    this.triggerPrint();
  },

  updated() {
    if (!this.data) {
      return;
    }

    this.triggerPrint();
  },

  beforeDestroy() {
    window.removeEventListener('afterprint', this.$el['__onPrintCompleteHandler__']);
    delete this.$el['__onPrintCompleteHandler__'];
  },

  methods: {
    triggerPrint() {
      window.clearTimeout(this.timeout);
      window.timeout = window.setTimeout(() => {
        window.print();
      }, 500);
    },
    goBackToTrack() {
      if (!this.data) {
        return;
      }

      this.$router.replace({
        name: 'tracks.show',
        params: {
          reciter: this.data.reciter.slug,
          album: this.data.year,
          track: this.data.slug,
          trackObject: this.data,
        },
      });
    },
  },
};
</script>

<style lang="scss" scoped>
.print__header {
  width: 100%;
  display: flex;
  border-bottom: rgba(0, 0, 0, 0.3) solid 1px;
  font-family: 'Roboto Slab', sans-serif;

  &__title {
    &--track--name {
      font-size: 20px;
    }

    &--track--meta {
      font-size: 14px;
    }
  }
}

.print__content {
  padding-top: 6mm;
  column-count: 2;
  font-family: 'Roboto Slab', sans-serif;
}

.print__content--empty {
  color: rgba(0, 0, 0, 0.6);
  font-size: 20px;
  column-count: 1;
  padding: 20px;
  text-align: center;
}

.content {
  background: white !important;
  color: black !important;
  padding: 24px !important;
}

@media print {
  .content {
    padding: 0 !important;
  }
}
</style>
