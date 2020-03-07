<template>
  <div>
    <div class="hero" :style="{'background-color': background, color: textColor}">
      <v-container class="hero__content">
        <v-avatar :size="heroArtworkSize" class="hero__artwork" tile>
            <img v-if="track && album" crossorigin :src="image" :alt="album.name" />
        </v-avatar>
        <div class="hero__text">
          <h4 class="hero__title">
            <template v-if="track">{{ track.title }}</template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="220px" class="py-md-8 py-2"></v-skeleton-loader>
            </template>
          </h4>
          <div class="hero__meta">
            <template v-if="reciter && album">
              <router-link class="meta__line"
                           :to="{ name: 'reciters.show', params: { reciter: reciter.slug } }"
                           exact
              >
                <span class="meta__line__text">{{ reciter.name }}</span>
              </router-link>
              <br>
              <div class="meta__line">
                <span class="meta__line__text">{{ album.year }} &bull; {{ album.title }}</span>
              </div>
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="150px" class="my-2"></v-skeleton-loader>
              <v-skeleton-loader type="text" dark width="100px" class="my-2"></v-skeleton-loader>
            </template>
          </div>
        </div>
      </v-container>
      <div class="hero__bar">
        <v-container class="bar__content">
          <div class="bar__actions bar__actions--visible">
            <template v-if="track">
              <v-btn text
                     :color="this.textColor"
                     v-if="hasAudio && !isSameTrackPlaying"
                     @click="playTrack"
              >
                <v-icon left>play_circle_filled</v-icon> Play
              </v-btn>
              <v-btn text
                    :color="this.textColor"
                    v-else-if="hasAudio && isSameTrackPlaying"
                    @click="stopPlaying"
              >
                <v-icon>stop</v-icon> Stop Playing
              </v-btn>
              <v-btn text
                     :color="this.textColor"
                     v-if="hasAudio && !isInQueue"
                     @click="addToQueue"
              >
                <v-icon left>playlist_add</v-icon> Add to Queue
              </v-btn>
              <v-btn text
                     :color="this.textColor"
                     v-else-if="hasAudio && isInQueue && !isSameTrackPlaying"
                     @click="removeFromQueue"
              >
                <v-icon left>remove_circle_outline</v-icon> Remove from Queue
              </v-btn>
            </template>
            <template v-else>
              <v-skeleton-loader type="text" dark width="100px" class="mt-3"></v-skeleton-loader>
            </template>
          </div>
          <div class="bar__actions bar__actions--overflow">
            <v-btn icon
                   :color="textColor"
                   v-if="track && track.lyrics"
                   @click="print"
            >
              <v-icon>print</v-icon>
            </v-btn>
            <v-btn dark icon v-if="false"><v-icon>more_vert</v-icon></v-btn>
          </div>
        </v-container>
      </div>
    </div>

    <v-container class="app__section">
      <v-row>
        <v-col cols="12" md="8">
          <v-card class="card card--lyrics lyrics">
            <v-card-title class="card__title subtitle-1">
              <v-icon class="card__title__icon material-icons-outlined">speaker_notes</v-icon>
              <div>Write-Up</div>
            </v-card-title>
            <v-card-text class="lyrics__content">
              <template v-if="track">
                <div v-if="track.lyrics">
                  <div v-html="prepareLyrics(track.lyrics.content)"></div>
                </div>
                <div class="lyrics__empty" v-else>
                  <div class="lyrics__empty-message">We don't have a write-up for this nawha yet.</div>
                </div>
              </template>
              <div v-else>
                <lyrics-skeleton />
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card class="card card--album">
            <v-card-title class="card__title subtitle-1">
              <v-icon class="card__title__icon">format_list_bulleted</v-icon>
              <div>More From This Album</div>
            </v-card-title>
            <v-card-text class="pa-0" v-if="track && album">
              <router-link
                v-for="(albumTrack, index) in album.tracks.data"
                :key="albumTrack.id"
                class="album__track"
                :exact="true"
                active-class="album__track--active"
                tag="div"
                :to="`/reciters/${reciter.slug}/albums/${album.year}/tracks/${albumTrack.slug}`"
              >
                <v-avatar class="album__track__avatar" size="28">
                  <span>{{ index+1 }}</span>
                </v-avatar>
                <div class="album__track__text">{{ albumTrack.title }}</div>
              </router-link>
            </v-card-text>
            <v-card-text v-else>
              <more-tracks-skeleton />
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-snackbar v-model="addedToQueueSnackbar" right>
      <v-icon color="white">playlist_add_check</v-icon> Added to Queue
      <v-btn color="deep-orange" text @click="addedToQueueSnackbar = false">
        Close
      </v-btn>
    </v-snackbar>
  </div>
</template>

<script lang="ts">
/* eslint-disable dot-notation */
import {
  Component, Watch, Prop, Vue,
} from 'vue-property-decorator';
import Vibrant from 'node-vibrant';
import ReciterHeroSkeleton from '@/components/loaders/ReciterHeroSkeleton.vue';
import LyricsSkeleton from '@/components/loaders/LyricsSkeleton.vue';
import MoreTracksSkeleton from '@/components/loaders/MoreTracksSkeleton.vue';
import { getTrack } from '@/services/tracks';

@Component({
  components: {
    ReciterHeroSkeleton,
    LyricsSkeleton,
    MoreTracksSkeleton,
  },
})
export default class TrackPage extends Vue {
  @Prop({ type: Object }) private trackObject!: any;
  private background = 'rgb(150, 37, 2)';
  private textColor = '#fff';
  private track: any = null;
  private addedToQueueSnackbar = false;

  get reciter() {
    return this.track && this.track.reciter;
  }

  get album() {
    return this.track && this.track.album;
  }

  get image() {
    if (this.album) {
      return this.album.artwork || '/img/default-album-image.png';
    }
    return '/img/default-album-image.png';
  }

  get heroArtworkSize() {
    if (this.$vuetify.breakpoint.xsOnly) {
      return 96;
    }
    if (this.$vuetify.breakpoint.smOnly) {
      return 128;
    }
    return 192;
  }

  get hasAudio() {
    return this.track && this.track.media.data.length > 0;
  }

  get isSameTrackPlaying() {
    const { player } = this.$store.state;
    if (player.queue.length) {
      if (player.queue[player.current].track === this.track) {
        return true;
      }
      return false;
    }
    return false;
  }

  get isInQueue() {
    const { player } = this.$store.state;
    for (let index = 0; index < player.queue.length; index++) {
      const element = player.queue[index];
      if (element.track.id === this.track.id) {
        return true;
      }
    }
    return false;
  }

  mounted() {
    this.fetchData();
    const handler = (e) => {
      e.preventDefault();
      this.print();
    };
    this.$el['__onPrintHandler__'] = handler;
    window.addEventListener('beforeprint', handler);
  }

  beforeDestroy() {
    window.removeEventListener('beforeprint', this.$el['__onPrintHandler__']);
    delete this.$el['__onPrintHandler__'];
  }

  @Watch('$route')
  onRouteUpdate() {
    this.fetchData();
  }

  async fetchData() {
    this.$Progress.start();
    const { reciter, album, track } = this.$route.params;

    if (this.trackObject) {
      this.track = this.trackObject;
    }

    if (!this.track || !this.isSameTrack((this.$route.params as any))) {
      await getTrack(reciter, album, track, {
        include: 'reciter,lyrics,album.tracks,media',
      }).then((r) => {
        this.track = r.data;
      });
    }

    this.setBackgroundFromImage();
    this.$Progress.finish();
  }

  setBackgroundFromImage() {
    if (!this.track) {
      return;
    }
    Vibrant.from(this.image)
      .getPalette()
      .then((palette) => {
        const swatch = palette.DarkMuted;
        if (!swatch) {
          return;
        }
        this.background = swatch.getHex();
        this.textColor = swatch.getBodyTextColor();
      });
  }

  prepareLyrics(content) {
    return content.replace(/\n/gi, '<br>');
  }

  isSameTrack({ reciter, album, track }) {
    return (
      this.track.reciter.slug === reciter
      && this.track.album.year === album
      && this.track.slug === track
    );
  }

  print() {
    this.$router.replace({
      name: 'print.lyrics',
      params: {
        track: this.track.slug,
        reciter: this.reciter.slug,
        album: this.album.year,
        trackObject: this.track,
      },
    });
  }

  playTrack() {
    this.$store.commit('player/PLAY_TRACK', { track: this.track });
  }

  stopPlaying() {
    this.$store.commit('player/STOP');
  }

  addToQueue() {
    this.$store.commit('player/ADD_TO_QUEUE', { track: this.track });
    this.addedToQueueSnackbar = true;
  }

  removeFromQueue() {
    this.$store.commit('player/REMOVE_TRACK', { track: this.track });
  }
}
</script>

<style lang="scss" scoped>
@import "../../../styles/theme";

.hero {
  width: 100%;
  position: relative;
  padding-bottom: 60px;
}

.hero__artwork {
  border: 4px solid white;
  border-radius: 4px;
  overflow: hidden;
  margin-right: 48px;
  box-sizing: content-box;
  background-color: white;
}

.hero__title {
  font-family: 'Roboto Slab', sans-serif;
  font-weight: bold;
  font-size: 2.4rem;
}

.hero__meta {
  .meta__line {
    font-weight: 400;
    padding: 4px 0;
    text-decoration: none;
    color: inherit;
    display: inline-block;
  }
  .meta__line__icon {
    margin-right: 8px;
  }
}

.hero__content {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-start;
  padding-top: 36px;
  padding-bottom: 36px;
}

.hero__bar {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  text-align: center;
  padding: 12px 24px;
  background: rgba(0, 0, 0, 0.2);

  .bar__content {
    padding: 0;
    display: flex;
    justify-content: space-between;
  }
}

.card__title {
  border-bottom: rgba(0, 0, 0, 0.05) solid 1px;

  display: flex;
  align-items: center;

  .card__title__icon {
    margin-right: 14px;
  }
}

.card--album {
  padding: 0;
  margin-bottom: 12px;

  .album__track {
    padding: 8px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    will-change: background-color;
    transition: background-color 0.2s;
    background-color: transparent;

    &:hover {
      background-color: rgba(0, 0, 0, 0.1);
    }

    .album__track__avatar {
      font-family: 'Roboto Slab', sans-serif;
      margin-right: 15px;
      display: flex;
      align-items: center;
    }

    .album__track__text {
      font-size: 14px;
    }
  }

  .album__track--active {
    background-color: rgba(0, 0, 0, 0.1);

    .album__track__avatar {
      color: white;
      background-color: map-deep-get($colors, 'deep-orange', 'darken-1');
    }
    .album__track__text {
      font-weight: 600;
    }
  }
}

.card--lyrics {
  .lyrics__content {
    padding: 24px;
    font-weight: 400;
    color: rgba(0, 0, 0, 0.76);
    font-family: 'Roboto Slab', sans-serif;
    line-height: 2rem;
    font-size: 1rem;
  }

  .lyrics__empty {
    display: flex;
    justify-content: center;
    color: rgba(0, 0, 0, 0.3);
    font-size: 20px;
    font-weight: 300;
    padding: 60px 0;

    .lyrics__empty-message {
      display: flex;
      margin: auto;
      align-self: center;
    }
  }
}

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .hero__content {
    padding: 32px;
  }
  .hero__title {
    font-size: 1.9rem;
  }
  .hero__artwork {
    margin-right: 24px;
  }
  .hero__meta .meta__line {
    padding: 3px 0;
  }
}

@media #{map-get($display-breakpoints, 'xs-only')} {
  .hero__title {
    font-size: 1.4rem;
  }
  .hero__meta .meta__line {
    padding: 2px 0;
  }
}
</style>
