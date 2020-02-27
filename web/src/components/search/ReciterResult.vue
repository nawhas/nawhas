<template>
  <router-link :to="reciter.url" class="link">
    <div class="reciter-result">
      <div class="reciter-result__avatar">
        <v-avatar size="36">
          <img crossorigin ref="avatarElement" :src="image" :alt="reciter.name" />
        </v-avatar>
      </div>
      <div class="reciter-result__text">
        <div class="reciter-result__name body-1" :title="reciter.name">
          <ais-highlight :hit="reciter" attribute="name" />
        </div>
      </div>
    </div>
  </router-link>
</template>

<script>
export default {
  name: 'ReciterResult',
  props: ['reciter'],
  computed: {
    image() {
      return this.reciter.avatar || '/img/default-reciter-avatar.png';
    },
  },
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
  }

  .reciter-result__avatar .avatar {
    border: 2px solid white;
    overflow: hidden;
  }
}
</style>
