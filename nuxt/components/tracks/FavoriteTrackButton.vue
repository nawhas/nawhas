<template>
  <v-btn icon>
    <v-icon
      :class="{
        'material-icons-outlined': true,
        'track-favorite--disabled': !saved && !isDark,
        'track-favorite--disabled--dark': !saved && isDark
      }"
      color="primary"
      @click="toggleSaveState"
    >
      favorite
    </v-icon>
  </v-btn>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'nuxt-property-decorator';

@Component
export default class FavoriteTrackButton extends Vue {
  @Prop({ type: String, required: true }) private readonly track!: string;

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

<style lang="scss" scoped>

</style>
