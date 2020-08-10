<template>
  <v-card class="album">
    <div class="album__header" :style="{ 'background-color': background }">
      <v-avatar
        tile
        :size="artworkSize"
        :class="{ 'album__artwork': true, 'album__artwork--dark': isDark }"
        @click="$router.push(link)"
      >
        <img ref="artwork" crossorigin :src="image" :alt="album.title">
      </v-avatar>
      <div class="album__details" :style="{ color: textColor }">
        <nuxt-link :to="link" class="album__title">
          <h5>{{ album.title }}</h5>
        </nuxt-link>
        <h6 class="album__release-date">
          <strong>{{ year }}</strong>
          &bull; {{ tracks.length }} tracks
        </h6>
      </div>

      <div class="album__edit">
        <edit-album-dialog v-if="album && isModerator" :album="album" />
      </div>

      <div class="album__actions">
        <v-speed-dial
          v-if="showSpeedPlay"
          v-model="fab"
          class="album__action__fab"
          absolute
          :open-on-hover="$vuetify.breakpoint.mdAndUp"
          right
          bottom
          direction="left"
        >
          <template v-slot:activator>
            <v-btn v-model="fab" :small="$vuetify.breakpoint.smAndDown" fab :color="fabColor">
              <v-icon v-if="fab" color="black">
                close
              </v-icon>
              <v-icon v-else color="black">
                play_arrow
              </v-icon>
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
      :items="tracks"
      :disable-sort="true"
      :hide-default-header="true"
      :disable-pagination="true"
      :hide-default-footer="true"
      :class="{'album__tracks': true, 'album__tracks--dark': isDark}"
    >
      <template v-slot:item="props">
        <tr class="album__track" @click="goToTrack(props.item)">
          <td>
            <nuxt-link class="track__link" :to="getTrackLink(props.item)">
              {{ props.item.title }}
            </nuxt-link>
          </td>
          <td class="track__features" align="right">
            <v-icon
              :class="{
                'material-icons-outlined': true,
                track__feature: true,
                'track__feature--disabled': !hasLyrics(props.item) && !isDark,
                'track__feature--disabled--dark': !hasLyrics(props.item) && isDark
              }"
            >
              <template v-if="hasLyrics(props.item)">
                speaker_notes
              </template>
              <template v-else>
                speaker_notes_off
              </template>
            </v-icon>
            <v-icon
              :class="{
                'material-icons-outlined': true,
                track__feature: true,
                'track__feature--disabled': !hasAudioFile(props.item) && !isDark,
                'track__feature--disabled--dark': !hasAudioFile(props.item) && isDark
              }"
            >
              <template v-if="hasAudioFile(props.item)">
                volume_up
              </template>
              <template v-else>
                volume_off
              </template>
            </v-icon>
          </td>
        </tr>
      </template>
    </v-data-table>
    <v-card-actions v-if="album && isModerator" class="d-flex justify-end album__actions">
      <edit-track-dialog :album="album" />
    </v-card-actions>
  </v-card>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import Vibrant from 'node-vibrant';
import { Album } from '@/entities/album';
import { Reciter } from '@/entities/reciter';
import { Track } from '@/entities/track';
import { RawLocation } from 'vue-router';
import { DataTableHeader } from 'vuetify';
import EditTrackDialog from '@/components/edit/EditTrackDialog.vue';
import EditAlbumDialog from '@/components/edit/EditAlbumDialog.vue';

@Component({
  components: {
    EditAlbumDialog,
    EditTrackDialog,
  },
})
export default class AlbumComponent extends Vue {
  private background = '#444444';
  private textColor = 'white';
  private fabColor = 'white';
  private fab = false;

  // TODO - Replace `any` with a proper interface.
  @Prop({ type: Object, required: true }) private album!: Album;
  @Prop({ type: Object, required: true }) private reciter!: Reciter;
  @Prop({ type: Boolean, default: true }) private showReciter!: boolean;

  get headers(): Array<DataTableHeader> {
    return [
      {
        text: 'Name',
        align: 'start',
        value: 'name',
      },
      {
        text: '',
        align: 'end',
        value: '',
      },
    ];
  }

  get year(): string {
    return this.album.year;
  }

  get tracks(): Array<Track> {
    return this.album.tracks?.data || [];
  }

  get reciterYear(): string {
    if (this.showReciter) {
      return `${this.reciter.name} • ${this.year}`;
    }
    return this.year;
  }

  get isDark(): boolean {
    return this.$vuetify.theme.dark;
  }

  get link(): RawLocation {
    if (!this.reciter || !this.album) {
      return '';
    }

    return `reciters/${this.reciter.slug}/albums/${this.album.year}`;
  }

  get gradient(): string | null {
    const rgb = Vibrant.Util.hexToRgb(this.background);

    if (!rgb) {
      return null;
    }

    return `linear-gradient(to right, rgba(${rgb.join(
      ', ',
    )}, 1), rgba(${rgb.join(', ')}, 0)`;
  }

  get artworkBackground(): string {
    return `url(${this.image})`;
  }

  get image(): string {
    return this.album.artwork || '/img/default-album-image.png';
  }

  get artworkSize(): number {
    if (this.$vuetify.breakpoint.smAndDown) {
      return 48;
    }
    return 128;
  }

  get isModerator(): boolean {
    return this.$store.getters['auth/isModerator'];
  }

  get showSpeedPlay(): boolean {
    let hasAudio = false;
    this.tracks.map((track) => {
      if (this.hasAudioFile(track)) {
        hasAudio = true;
      }
      return true;
    });
    return hasAudio;
  }

  mounted(): void {
    this.setBackgroundFromImage();
  }

  setBackgroundFromImage(): void {
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

  hasLyrics(track: Track): boolean {
    return track.related?.lyrics ?? false;
  }

  hasAudioFile(track: Track): boolean {
    return track.related?.audio ?? false;
  }

  getTrackLink(track: Track): RawLocation {
    if (this.link === '') {
      return '';
    }

    return `${this.link}/tracks/${track.slug}`;
  }

  goToTrack(track: Track): void {
    this.$router.push(this.getTrackLink(track));
  }

  playAlbum(): void {
    this.$store.commit('player/PLAY_ALBUM', { tracks: this.album.tracks?.data });
  }

  addAlbumToQueue(): void {
    this.$store.commit('player/ADD_ALBUM_TO_QUEUE', { tracks: this.album.tracks?.data });
  }
}
</script>

<style lang="scss" scoped>
@import '~assets/theme';

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
  cursor: pointer;
  margin-top: -48px;
  margin-left: 20px;
  border: 5px solid white;
  float: left;
  overflow: hidden;
  box-sizing: content-box;
  background-color: white;
  @include elevation(3);

  &.album__artwork--dark {
    background: #1E1E1E;
    border-color: #bbb;
  }
}

.album__details {
  padding: 24px;
  color: white;
  flex-grow: 1;
}

.album__title, .album__title h5 {
  text-decoration: none;
  color: white;
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

.album__edit {
  padding: 0 16px;
}

.album__tracks {
  .datatable {
    th:focus,
    td:focus {
      outline: none !important;
    }
  }

  &--dark {
    tr:hover {
      background-color: map-get($material-dark-elevation-colors, '8') !important;
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

.track__link {
  text-decoration: none;
  color: inherit;
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
  .track__feature--disabled--dark {
    color: rgba(map-get($shades, 'white'), 0.5);
  }
}

.album__actions {
  background: rgba(0,0,0,0.1);
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
