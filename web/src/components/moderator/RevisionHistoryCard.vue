<template>
  <div>
    <v-card class="audit-card" outlined>
      <div class="audit-card__avatar">
        <v-avatar size="40" class="avatar">
          <v-icon color="white">{{ icon }}</v-icon>
        </v-avatar>
      </div>
      <div class="audit-card__text">
        <div class="audit-card__name body-1">
          {{ name }}
          <span class="subtitle" v-if="subtitle">{{ subtitle }}</span>
        </div>
        <div class="audit-card__name change-type-container">
          <v-avatar :color="changeTypeColor" size="12" class="change-type-icon" />
          <span class="change-type overline">{{ audit.type }}</span>
        </div>
      </div>
      <div class="audit-card__text audit-card__text-right">
        <div class="audit-card__name caption">2 hours ago</div>
        <div class="audit-card__name caption">{{ audit.user.email }}</div>
      </div>
    </v-card>
    <v-card v-if="diff">
      <prism language="diff" class="language-diff-json" :code="diff"></prism>
    </v-card>
  </div>
</template>

<script lang="ts">
import { Component, Vue, Prop } from 'vue-property-decorator';
import { Data as AuditData, ChangeType, Entity } from '@/entities/audit';
import * as Diff from 'diff';
import 'prismjs';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-diff';
import 'prismjs/plugins/diff-highlight/prism-diff-highlight';
import Prism from 'vue-prism-component';

@Component({
  components: {
    Prism,
  },
})
export default class RevisionHistoryCard extends Vue {
  @Prop() private audit!: AuditData;

  get test() {
    return JSON.stringify(this.audit.old, null, 2);
  }

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

  get diff() {
    if (!this.isModified) {
      return null;
    }
    return Diff.createPatch('patch', JSON.stringify(this.audit.old, null, 2), JSON.stringify(this.audit.new, null, 2));
  }
}
</script>

<style lang="scss">
@import '../../styles/theme';

.audit-card {
  padding: 16px;
  display: flex;
  align-items: center;
  cursor: pointer;
  background-color: transparent;
  @include transition(background-color, box-shadow);
  margin-bottom: 10px;

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
    text-align: right;
  }

  .audit-card__avatar .avatar {
    background-color: grey;
  }
}

.change-type-container {
  display: flex;
  align-items: center;
}

.change-type {
  text-transform: uppercase;
}

.change-type-icon {
  margin-right: 6px;
}

.changes {
  display: flex;

  .old-values {
    margin-right: 6px;
  }
}

pre,
code {
  margin: 0;
  background: transparent;
  font-family: 'Inconsolata', monospace;
  font-weight: 300;
  font-size: 15px;
  line-height: 1.55;
}
code {
  position: relative;
  box-shadow: none;
  overflow-x: auto;
  overflow-y: hidden;
  word-break: break-word;
  flex-wrap: wrap;
  align-items: center;
  vertical-align: middle;
  white-space: pre-wrap;
}

code[class*='language-'],
pre[class*='language-'] {
  color: #ccc;
  background: none;
  font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
  font-size: 1rem;
  text-align: left;
  white-space: pre;
  word-spacing: normal;
  word-break: normal;
  word-wrap: normal;
  line-height: 1.5;
  tab-size: 4;
  hyphens: none;
}
pre[class*='language-'] {
  padding: 1rem;
  margin: 0;
  overflow: auto;
}
:not(pre) > code[class*='language-'] {
  padding: 0.1rem;
  border-radius: 0.3rem;
  white-space: normal;
}
.token.comment,
.token.block-comment,
.token.prolog,
.token.doctype,
.token.cdata {
  color: #999;
}
.token.punctuation {
  color: #ccc;
}
.token.tag,
.token.attr-name,
.token.namespace,
.token.deleted {
  color: #e2777a;
}
.token.function-name {
  color: #6196cc;
}
.token.boolean,
.token.number,
.token.function {
  color: #f08d49;
}
.token.property,
.token.class-name,
.token.constant,
.token.symbol {
  color: #f8c555;
}
.token.selector,
.token.important,
.token.atrule,
.token.keyword,
.token.builtin {
  color: #cc99cd;
}
.token.string,
.token.char,
.token.attr-value,
.token.regex,
.token.variable {
  color: #7ec699;
}
.token.operator,
.token.entity,
.token.url {
  color: #67cdcc;
}
.token.important,
.token.bold {
  font-weight: bold;
}
.token.italic {
  font-style: italic;
}
.token.entity {
  cursor: help;
}
.token.inserted {
  color: green;
}
</style>
