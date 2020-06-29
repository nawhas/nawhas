<template>
  <div>
    <v-card class="audit-card" outlined @click="open = !open">
      <div class="audit-card__avatar">
        <v-avatar size="40" class="avatar">
          <v-icon color="white">{{ icon }}</v-icon>
        </v-avatar>
      </div>
      <div class="audit-card__text">
        <div class="audit-card__name body-2">
          {{ name }}
          <span class="subtitle" v-if="subtitle">{{ subtitle }}</span>
        </div>
        <div class="audit-card__name caption">
          <v-avatar :color="changeTypeColor" size="18" class="change-type-icon" />
          <span class="change-type">{{ audit.type }}</span>
        </div>
      </div>
      <div class="audit-card__text audit-card__text-right">
        <div class="audit-card__name body-2">2 hours ago</div>
        <div class="audit-card__name caption">{{ audit.user.email }}</div>
      </div>
    </v-card>
    <v-card v-show="open">
      <template v-for="(value, propertyName) in audit.new">
        <div v-if="displayChange(value, propertyName)" :key="propertyName" class="changes">
          <div
            class="old-values"
            v-if="!isCreated"
          >Old {{ propertyName }} => {{ audit.old[propertyName] }}</div>
          <div class="new-values">New {{ propertyName }} => {{ value }}</div>
        </div>
      </template>
    </v-card>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator';
import { Data as AuditData, ChangeType, Entity } from '@/entities/audit';

@Component
export default class RevisionHistoryCard extends Vue {
  private open = false;
  @Prop() private audit!: AuditData;

  get changeTypeColor() {
    if (this.isCreated) {
      return 'green';
    }
    if (this.isModified) {
      return 'orange';
    }
    if (this.isDeleted) {
      return 'red';
    }
    return null;
  }

  get name() {
    if (this.audit.new === undefined && this.audit.old === undefined) {
      return false;
    }
    if (this.isDeleted) {
      return this.audit.old.name;
    }
    return this.audit.new.name;
  }

  // Need to return the correct value
  get subtitle() {
    if (this.audit.entity === Entity.Reciter) {
      return false;
    }
    if (this.isDeleted) {
      return this.audit.old.name;
    }
    return this.audit.new.name;
  }

  get icon() {
    if (this.audit.entity === Entity.Album) {
      return 'album';
    }
    if (this.audit.entity === Entity.Track) {
      return 'music_note';
    }
    return 'record_voice_over';
  }

  get isCreated() {
    return this.audit.type === ChangeType.Created;
  }

  get isModified() {
    return this.audit.type === ChangeType.Modified;
  }

  get isDeleted() {
    return this.audit.type === ChangeType.Deleted;
  }

  displayChange(value, propertyName) {
    if (this.isDeleted) {
      return false;
    }
    if (this.isCreated && !value) {
      return false;
    }
    if (this.isCreated) {
      return true;
    }
    if (this.audit.old[propertyName] === undefined) {
      return false;
    }
    console.log(propertyName, value);
    if (value === this.audit.old[propertyName]) {
      return false;
    }
    return true;
  }
}
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

.change-type {
  text-transform: uppercase;
}

.change-type-icon {
  margin-top: -4px;
  margin-right: 6px;
}

.changes {
  display: flex;

  .old-values {
    margin-right: 6px;
  }
}
</style>
