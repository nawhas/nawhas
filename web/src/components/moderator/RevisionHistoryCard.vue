<template>
  <div>
    <v-card class="audit-card" outlined>
      <div class="audit-card__avatar">
        <v-avatar size="40" class="avatar">
          <v-icon color="white">record_voice_over</v-icon>
        </v-avatar>
      </div>
      <div class="audit-card__text">
        <div class="audit-card__name body-2">Nadeem Sarwar</div>
        <div class="audit-card__name caption">
          <v-avatar color="green" size="18" class="change-type-icon" />
          <span>CREATED</span>
        </div>
      </div>
      <div class="audit-card__text audit-card__text-right">
        <div class="audit-card__name body-2">2 hours ago</div>
        <div class="audit-card__name caption">shea786@live.co.uk</div>
      </div>
    </v-card>
  </div>
</template>

<script lang="ts">
import { Component, Vue } from 'vue-property-decorator';

@Component
export default class RevisionHistoryCard extends Vue {}
</script>

<style lang="scss" scoped>
@import '../../styles/theme';

.audit-card {
  padding: 16px;
  display: flex;
  align-items: center;
  cursor: pointer;
  @include transition(background-color, box-shadow);

  &:hover:not(.audit-card--featured) {
    background-color: rgba(0, 0, 0, 0.1) !important;
  }

  .audit-card__text {
    margin-left: 16px;
    overflow: hidden;
    @include transition(color);

    .audit-card__name {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      width: auto;
    }
  }

  .audit-card__text-right {
    margin-left: auto;
  }

  .audit-card__avatar .avatar {
    background-color: grey;
  }
}

.change-type-icon {
  margin-top: -4px;
  margin-right: 6px;
}
</style>
