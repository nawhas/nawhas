<template>
  <v-card class="album">
    <div class="album__header" :style="{ 'background-color': background }">
      <v-avatar tile size="128px" :elevation="2" class="album__artwork white">
        <img :src="artwork" :alt="name" ref="artwork" />
      </v-avatar>
      <div class="album__details" :style="{ color: textColor }">
        <h5 class="album__title">{{ name }}</h5>
        <h6 class="album__release-date">
          <strong>{{ year }}</strong>
          &bull; {{ tracks.data.length }} tracks
        </h6>
      </div>
    </div>
    <v-data-table :headers="headers" :items="tracks.data" class="album__tracks-1">
      <template v-slot:item="props">
        <tr @click="goToTrack(props.item)" class="album__track">
          <td>{{ props.item.title }}</td>
          <td>{{ reciter.name }}</td>
        </tr>
      </template>
    </v-data-table>
  </v-card>
</template>

<script>
import Vibrant from 'node-vibrant';

export default {
  name: 'album',
  props: ['name', 'album', 'year', 'tracks', 'artwork', 'reciter'],
  mounted() {
    this.setBackgroundFromImage();
  },
  methods: {
    setBackgroundFromImage() {
      Vibrant.from(this.artwork)
        .getPalette()
        .then((palette) => {
          const swatch = palette.DarkMuted;
          if (!swatch) {
            return;
          }
          this.background = swatch.getHex();
          this.textColor = swatch.getBodyTextColor();
        });
    },
    goToTrack(track) {
      this.$router.push(
        `/reciters/${this.reciter.slug}/albums/${this.year}/tracks/${track.slug}`,
      );
    },
  },
  data() {
    return {
      headers: [
        {
          text: 'Name',
          align: 'left',
          value: 'name',
        },
        {
          text: 'Reciter',
          align: 'left',
          value: 'reciter.name',
        },
      ],
      background: '#444444',
      textColor: 'white',
    };
  },
  computed: {
    reciterYear() {
      if (this.showReciter) {
        return `${this.reciter.name} • ${this.year}`;
      }
      return this.year;
    },
    gradient() {
      const rgb = Vibrant.Util.hexToRgb(this.background);
      return `linear-gradient(to right, rgba(${rgb.join(
        ', ',
      )}, 1), rgba(${rgb.join(', ')}, 0)`;
    },
    artworkBackground() {
      return `url(${this.artwork})`;
    },
  },
};
</script>

<style lang="scss">
@import '../../node_modules/vuetify/src/styles/settings/_elevations.scss';

.album {
  margin-top: 90px;
  .album__header {
    position: relative;

    .album__artwork {
      margin-top: -48px;
      margin-left: 24px;
      border: 5px solid white;
      float: left;
      overflow: hidden;
      box-sizing: content-box;
    }

    .album__details {
      margin-left: 128px + 24px;
      padding: 40px 32px;
      color: white;

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
    }
  }

  .album__tracks {
    .datatable {
      th:focus,
      td:focus {
        outline: none !important;
      }
      .album__track {
        cursor: pointer;
      }
    }
  }
}
</style>
