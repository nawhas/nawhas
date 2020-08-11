<template>
  <nuxt-link :to="reciter.meta.url" class="link">
    <div class="reciter-result">
      <div class="reciter-result__avatar">
        <v-avatar size="36">
          <img ref="avatarElement" crossorigin :src="image" :alt="reciter.name">
        </v-avatar>
      </div>
      <div class="reciter-result__text">
        <div
          :class="{ 'reciter-result__name--dark': isDark }"
          class="reciter-result__name body-1"
          :title="reciter.name"
        >
          {{ reciter.name }}
        </div>
      </div>
    </div>
  </nuxt-link>
</template>

<script lang="ts">
import { Vue, Component, Prop } from 'nuxt-property-decorator';

interface ReciterSearchResult {
  id: string;
  name: string;
  description: string|null;
  meta: {
    avatar: string|null;
    url: string;
  };
}

@Component
export default class ReciterResult extends Vue {
  @Prop({ type: Object }) private readonly reciter!: ReciterSearchResult;

  get image() {
    return this.reciter.meta.avatar ?? '/defaults/default-reciter-avatar.png';
  }

  get isDark() {
    return this.$vuetify.theme.dark;
  }
};
</script>

<style lang="scss">
.link {
  text-decoration: none;
}
.reciter-result {
  color: rgba(0, 0, 0, 0.76);
  padding: 8px 16px;
  display: flex;
  align-items: center;
  cursor: pointer;
  will-change: background-color;
  background-color: rgba(0,0,0,0.0);
  transition: background-color 280ms;

  &:hover {
    background-color: rgba(0,0,0,0.08);
  }

  .reciter-result__text {
    margin-left: 16px;
    overflow: hidden;

    .reciter-result__name {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: auto;

      em, mark {
        font-style: normal;
        background: none;
        padding-bottom: 1px;
        border-bottom: 2px solid orangered;
      }
    }

    .reciter-result__name--dark {
      color: white !important;

      em, mark {
        color: wheat !important;
      }
    }
  }

  .reciter-result__avatar .avatar {
    border: 2px solid white;
    overflow: hidden;
  }
}
</style>
