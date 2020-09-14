<template>
  <v-btn
    icon
    class="track-favorite"
    @click.prevent.stop="toggleSaveState"
  >
    <v-icon
      class="track-favorite__icon"
      :color="saved ? 'primary' : color"
    >
      {{ saved ? 'favorite' : 'favorite_border' }}
    </v-icon>
  </v-btn>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';

@Component
export default class FavoriteTrackButton extends Vue {
  @Prop({ type: String, required: true }) private readonly track!: string;
  @Prop({ type: String, required: false, default: undefined }) private readonly color?: string;

  get saved(): boolean {
    return this.$store.getters['library/isTrackSaved'](this.track);
  }

  get isDark(): boolean {
    return this.$vuetify.theme.dark;
  }

  toggleSaveState() {
    const action = (this.saved) ? 'library/removeTrack' : 'library/saveTrack';
    this.$store.dispatch(action, {
      ids: [this.track],
    });
  }
}
</script>
