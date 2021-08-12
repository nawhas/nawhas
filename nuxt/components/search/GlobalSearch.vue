<template>
  <div v-click-outside="clickOutsideConfig">
    <v-btn v-if="mobile" icon @click="activate">
      <v-icon>search</v-icon>
    </v-btn>
    <div
      v-show="!mobile || activated"
      :class="{
        search: true,
        'search--focused': activated,
        'search--dark': isDark,
        'search--hero': hero,
      }"
    >
      <div class="search__bar">
        <v-btn v-if="mobile" icon @click="resetSearch">
          <v-icon>arrow_back</v-icon>
        </v-btn>
        <v-text-field
          ref="search"
          v-model="search"
          solo
          flat
          single-line
          hide-details
          background-color="transparent"
          autocomplete="off"
          autocorrect="off"
          autocapitalize="off"
          spellcheck="false"
          :prepend-inner-icon="mobile ? undefined : 'search'"
          :dense="false"
          :full-width="mobile"
          :clearable="!!search"
          :placeholder="hero ? 'Search Nawhas.com' : 'Search for nawhas, reciters, or lyrics...'"
          :class="{'search__input': true, 'search__input--dark': isDark}"
          @focus="onFocus"
          @blur="onBlur"
          @keydown.esc="onEsc"
        />
      </div>
      <v-expand-transition>
        <div
          v-show="activated"
          class="search__container"
          :style="{
            background: $vuetify.theme.currentTheme.background,
          }"
        >
          <index-results
            :client="$search"
            :search="search"
            :highlight="['name']"
            collection="reciters"
            heading="Reciters"
          >
            <reciter-result slot-scope="{ item }" :reciter="item" />
          </index-results>
          <index-results
            :client="$search"
            :search="search"
            :highlight="['title', 'reciter', 'lyrics', 'year']"
            :crop="['lyrics:20']"
            collection="tracks"
            heading="Nawhas"
          >
            <track-result slot-scope="{ item }" :track="item" />
          </index-results>
          <index-results
            :client="$search"
            :search="search"
            :highlight="['title', 'year', 'reciter']"
            collection="albums"
            heading="Albums"
          >
            <album-result slot-scope="{ item }" :album="item" />
          </index-results>
          <div class="search__footer" :class="{ 'white--text': isDark }">
            <div v-if="!search" class="search__footer-hint body-2">
              Start typing to see results...
            </div>
            <div v-else class="search__footer-hint body-2">
              Showing results for &ldquo;{{ search }}&rdquo;
            </div>
          </div>
        </div>
      </v-expand-transition>
    </div>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop, Ref, Watch } from 'nuxt-property-decorator';
import ClickOutside, { ClickOutsideConfiguration } from '@/directives/click-outside';
import IndexResults from '@/components/search/IndexResults.vue';
import ReciterResult from '@/components/search/ReciterResult.vue';
import TrackResult from '@/components/search/TrackResult.vue';
import AlbumResult from '@/components/search/AlbumResult.vue';

@Component({
  components: {
    IndexResults,
    ReciterResult,
    TrackResult,
    AlbumResult,
  },
  directives: {
    ClickOutside,
  },
})
export default class GlobalSearch extends Vue {
  private activated = false;
  private focused = false;
  private search = '';
  @Prop({ type: Boolean, default: false }) private readonly hero!: boolean
  @Ref('search') readonly input!: HTMLElement;

  get mobile() {
    return this.$vuetify.breakpoint.smAndDown && !this.hero;
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }

  @Watch('$route')
  onRouteChange() {
    this.resetSearch();
  }

  @Watch('focused')
  onFocusChange(focused) {
    if (focused) {
      this.activated = true;
    }
    if (!focused && !this.search) {
      this.activated = false;
    }
  }

  onBlur() {
    this.focused = false;
  }

  onEsc() {
    this.input.blur();
  }

  onFocus() {
    this.focused = true;
  }

  get clickOutsideConfig(): ClickOutsideConfiguration {
    return {
      active: () => !!this.search && this.activated,
      handler: () => {
        this.activated = false;
      },
    };
  }

  resetSearch() {
    this.search = '';
    this.focused = false;
    this.activated = false;
  }

  activate() {
    this.focused = true;
    this.$nextTick(() => {
      this.input.focus();
    });
  }
}
</script>

<style lang="scss" scoped>
@import '~assets/theme';

$width: 400px;
.search {
  $easing: cubic-bezier(0.4, 0, 0.2, 1);
  $duration: 280ms;
  will-change: background-color, box-shadow;
  transition: background-color $duration $easing, box-shadow $duration $easing;
  display: flex;
  flex-direction: column;
  background: rgba(0, 0, 0, 0.1);
  width: $width;
  border-radius: 4px;
  margin-top: 4px;

  &--focused {
    background-color: white;
    @include elevation(4);
  }
  &--dark {
    background-color: map-get($material-dark-elevation-colors, '8');
  }

  .search__bar {
    display: flex;
    flex-direction: row;
    align-items: center;

    .search__input--dark {
      background-color: map-get($material-dark-elevation-colors, '8');
    }
  }

  .search__container {
    width: $width;
    max-height: calc(100vh - 100px);
    overflow-y: auto;
    border-top: 1px solid rgba(0, 0, 0, 0.06);
    position: relative;

    .search__footer {
      color: rgba(0, 0, 0, 0.3);
      display: flex;
      justify-content: space-between;
      padding: 8px;
      img {
        height: 16px;
        width: auto;
      }
    }
  }
}

.search--hero {
  @include elevation(2);
  background-color: white;
  width: 600px;

  &.search--dark {
    background-color: map-get($material-dark-elevation-colors, '8');
  }

  &.search--focused {
    @include elevation(4);
  }

  .search__bar {
    padding: 8px 0;
  }

  .search__container {
    width: 600px;
  }
}

@include breakpoint('sm-and-down') {
  .search {
    width: 100%;
    margin-top: 0;

    &--focused {
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1;
    }
    .search__container {
      width: 100%;
    }
  }

  .search--hero {
    width: calc(100vw - 96px);

    &.search--focused {
      position: relative;
      top: initial;
      left: initial;
    }

    .search__bar {
      padding: 4px 0;
    }

    .search__container {
      width: calc(100vw - 96px);
    }
  }
}
</style>
