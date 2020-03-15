<template>
  <v-card class="album">
    <div class="album__header" :style="{ 'background-color': background }">
      <v-avatar tile :size="artworkSize" class="album__artwork">
        <img crossorigin :src="image" :alt="album.title" ref="artwork" />
      </v-avatar>
      <div class="album__details" :style="{ color: textColor }">
        <h5 class="album__title">{{ album.title }}</h5>
        <h6 class="album__release-date">
          <strong>{{ year }}</strong>
          &bull; {{ tracks.data.length }} tracks
        </h6>
      </div>

      <div class="album__actions">
        <v-speed-dial v-if="showSpeedPlay" class="album__action__fab" absolute
                      v-model="fab" :open-on-hover="$vuetify.breakpoint.mdAndUp"
                      right bottom direction="left">
          <template v-slot:activator>
            <v-btn :small="$vuetify.breakpoint.smAndDown" v-model="fab" fab :color="fabColor">
              <v-icon v-if="fab">close</v-icon>
              <v-icon v-else>play_arrow</v-icon>
            </v-btn>
          </template>
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn fab small @click="playAlbum" v-on="on">
                <v-icon>play_arrow</v-icon>
              </v-btn>
            </template>
            <span>Play Album</span>
          </v-tooltip>
          <v-tooltip top>
            <template v-slot:activator="{ on }">
              <v-btn fab small @click="addAlbumToQueue" v-on="on">
                <v-icon>playlist_add</v-icon>
              </v-btn>
            </template>
            <span>Add Album to Queue</span>
          </v-tooltip>
        </v-speed-dial>
      </div>
    </div>
    <v-data-table
      :headers="headers"
      :items="tracks.data"
      :disable-sort="true"
      :hide-default-header="true"
      :disable-pagination="true"
      :hide-default-footer="true"
      class="album__tracks"
    >
      <template v-slot:item="props">
        <tr @click="goToTrack(props.item)" class="album__track">
          <td>{{ props.item.title }}</td>
          <td class="track__features" align="right">
            <v-icon :class="{
              'material-icons-outlined': true,
              track__feature: true,
              'track__feature--disabled': !hasLyrics(props.item)
            }">
              <template v-if="hasLyrics(props.item)">speaker_notes</template>
              <template v-else>speaker_notes_off</template>
            </v-icon>
            <v-icon :class="{
              'material-icons-outlined': true,
              track__feature: true,
              'track__feature--disabled': !hasAudioFile(props.item)
            }">
              <template v-if="hasAudioFile(props.item)">volume_up</template>
              <template v-else>volume_off</template>
            </v-icon>
          </td>
        </tr>
      </template>
    </v-data-table>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import Vibrant from 'node-vibrant';

@Component
export default class Album extends Vue {
  private background = '#444444';
  private textColor = 'white';
  private fabColor = 'white';
  private fab = false;

  // TODO - Replace `any` with a proper interface.
  @Prop({ type: Object, required: true }) private album: any;
  @Prop({ type: Object, required: true }) private reciter: any;
  @Prop({ type: Boolean, default: true }) private showReciter!: boolean;

  get headers() {
    return [
      {
        text: 'Name',
        align: 'left',
        value: 'name',
      },
      {
        text: '',
        align: 'right',
        value: null,
      },
    ];
  }

  get year() {
    return this.album.year;
  }

  get tracks() {
    return this.album.tracks;
  }

  get reciterYear() {
    if (this.showReciter) {
      return `${this.reciter.name} • ${this.year}`;
    }
    return this.year;
  }

  get gradient() {
    const rgb = Vibrant.Util.hexToRgb(this.background);

    if (!rgb) {
      return null;
    }

    return `linear-gradient(to right, rgba(${rgb.join(
      ', ',
    )}, 1), rgba(${rgb.join(', ')}, 0)`;
  }

  get artworkBackground() {
    return `url(${this.image})`;
  }

  get image() {
    return this.album.artwork || '/img/default-album-image.png';
  }

  get artworkSize() {
    if (this.$vuetify.breakpoint.smAndDown) {
      return 48;
    }
    return 128;
  }

  get showSpeedPlay(): boolean {
    let hasAudio = false;
    this.tracks.data.map((track) => {
      if (this.hasAudioFile(track)) {
        hasAudio = true;
      }
      return true;
    });
    return hasAudio;
  }

  mounted() {
    this.setBackgroundFromImage();
  }

  setBackgroundFromImage() {
    Vibrant.from(this.image)
      .getPalette()
      .then((palette) => {
        const swatch = palette.DarkMuted;
        if (!swatch) {
          return;
        }
        this.background = swatch.getHex();
        this.textColor = swatch.getBodyTextColor();

        const light = palette.LightVibrant;
        if (light) {
          this.fabColor = light.getHex();
        }
      });
  }

  hasLyrics(track) {
    return track.related ? track.related.lyrics === true : false;
  }

  hasAudioFile(track) {
    return track.related ? track.related.audio === true : false;
  }

  goToTrack(track) {
    this.$router.push(
      `/reciters/${this.reciter.slug}/albums/${this.album.year}/tracks/${track.slug}`,
    );
  }

  playAlbum() {
    this.$store.commit('player/PLAY_ALBUM', { tracks: this.album.tracks.data });
  }

  addAlbumToQueue() {
    this.$store.commit('player/ADD_ALBUM_TO_QUEUE', { tracks: this.album.tracks.data });
  }
}
</script>

<style lang="scss" scoped>
@import '../styles/theme';

.album {
  margin-top: 90px;
}

.album__header {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: flex-start;
}

.album__artwork {
  margin-top: -48px;
  margin-left: 20px;
  border: 5px solid white;
  float: left;
  overflow: hidden;
  box-sizing: content-box;
  background-color: white;
  @include elevation(3);
}

.album__details {
  padding: 24px;
  color: white;
  flex-grow: 1;
}

.album__title {
  margin: 0 0 8px 0;
  padding: 0;
  font-weight: 700;
  font-size: 24px;
}

.album__release-date {
  margin: 0;
  padding: 0;
  font-weight: 400;
  font-size: 20px;
}

.album__tracks {
  .datatable {
    th:focus,
    td:focus {
      outline: none !important;
    }
  }
}

.album__track {
  cursor: pointer;
}

.album__action__fab {
  right: 80px;
  bottom: -24px;
  z-index: 1;
}

.track__features {
  white-space: nowrap;

  .track__feature {
    margin-left: 6px;
    color: map-deep-get($colors, 'deep-orange', 'darken-3');
  }

  .track__feature--disabled {
    color: rgba(0, 0, 0, 0.1);
  }
}

@media #{map-get($display-breakpoints, 'sm-and-down')} {
  .album {
    margin-top: 0;
    margin-bottom: 24px;
  }
  .album__header {
    display: flex;
    align-items: center;
    flex-direction: row;
  }
  .album__details {
    margin: 0;
    padding: 16px;
  }
  .album__artwork {
    float: none;
    margin: 16px 0 16px 16px;
    border: 2px solid white;
    @include elevation(0);
  }
  .album__title {
    font-size: 1.15rem;
    margin: 0;
  }
  .album__release-date {
    font-size: 0.95rem;
  }

  .album__action__fab {
    right: 24px;
    bottom: -16px;
  }
}
</style>
