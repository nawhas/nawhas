<template>
  <v-dialog
    v-model="dialog"
    persistent
    fullscreen
    no-click-animation
    hide-overlay
    transition="dialog-bottom-transition"
  >
    <template #activator="{ on }">
      <v-btn
        v-if="track"
        dark
        icon
        v-on="on"
      >
        <v-icon>edit</v-icon>
      </v-btn>
      <v-btn
        v-else
        text
        v-on="on"
      >
        Add Track
      </v-btn>
    </template>
    <v-card :loading="loading">
      <v-app-bar
        fixed
        elevate-on-scroll
      >
        <v-btn
          icon
          @click="close"
        >
          <v-icon>close</v-icon>
        </v-btn>
        <v-toolbar-title>{{ track ? 'Edit' : 'Add' }} Track</v-toolbar-title>
        <v-spacer />
        <div class="toolbar__actions">
          <v-btn
            v-if="track"
            text
            @click="confirmDelete"
          >
            <v-icon>delete_forever</v-icon>
          </v-btn>
          <v-btn
            color="primary"
            @click="submit"
          >
            Save
          </v-btn>
        </div>
      </v-app-bar>
      <v-card-text class="dialog__content">
        <v-text-field
          v-model="form.title"
          outlined
          label="Name"
          required
        />
        <v-text-field
          v-model="form.video"
          outlined
          label="YouTube Video"
          prepend-icon="video_library"
        />
        <v-select
          v-model="form.topics"
          :items="availableTopics"
          item-value="slug"
          item-text="name"
          label="Select Topics"
          multiple
          outlined
        />
        <div
          class="file-input"
          @drop.prevent="addFile"
          @dragover.prevent
        >
          <v-file-input
            v-model="form.audio"
            label="Audio File"
            placeholder="Upload Track Audio File"
            prepend-icon="volume_up"
            outlined
            accept="audio/*"
            :show-size="1000"
          >
            <template #selection="{ text }">
              <v-chip
                color="deep-orange accent-4"
                dark
                label
                small
              >
                {{ text }}
              </v-chip>
            </template>
          </v-file-input>
        </div>
        <timestamped-editor
          v-model="form.lyrics"
          :track="track"
        />
      </v-card-text>
      <v-card-actions />
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
import { Component, Prop, Vue, Watch } from 'nuxt-property-decorator';
import { clone } from '@/utils/clone';
import { RequestOptions, TrackIncludes } from '@/api/tracks';
import TimestampedEditor from '@/components/edit/lyrics/TimestampedEditor.vue';
import { Documents, Format } from '@/entities/lyrics';
import { getTrackUri, Track } from '@/entities/track';
import { getReciterUri } from '@/entities/reciter';
import { Topic } from '@/entities/topic';
import JsonV1Document = Documents.JsonV1.Document;
import GroupType = Documents.JsonV1.LineGroupType;
import LyricsData = Documents.JsonV1.LyricsData;
import { Album } from '@/entities/album';

interface Form {
  title: string|null;
  lyrics: JsonV1Document|null;
  audio: File|null;
  video: string|null;
  topics: Array<Topic>|null;
}
const defaults: Form = {
  title: null,
  lyrics: null,
  audio: null,
  video: null,
  topics: null,
};

@Component({
  components: { TimestampedEditor },
})
export default class EditTrackDialog extends Vue {
  @Prop({ default: null })
  readonly track!: Track|null;

  @Prop({ required: true })
  readonly album!: Album;

  @Prop({ required: true })
  readonly availableTopics!: Topic;

  private dialog = false;
  private form: Form = { ...defaults };
  private loading = false;

  getRequestOptions(): RequestOptions {
    return {
      include: [
        TrackIncludes.Reciter,
        TrackIncludes.Lyrics,
        TrackIncludes.Media,
        'album.tracks',
      ],
    };
  }

  @Watch('dialog')
  onDialogStateChanged(opened: boolean) {
    if (opened) {
      this.resetForm();
    }
  }

  get lyrics(): JsonV1Document|null {
    if (!this.track.lyrics) {
      return null;
    }

    const { content, format } = this.track.lyrics;
    if (format === Format.JsonV1) {
      return JSON.parse(content);
    }

    const data: LyricsData = content.trim().split(/\n/gi).map((text, index) => {
      if (text.trim().length === 0) {
        return {
          timestamp: null,
          lines: [{ text: '', repeat: 0 }],
          type: GroupType.Spacer,
        };
      }

      return {
        timestamp: index === 0 ? 0 : null,
        lines: [{ text: text.trim(), repeat: 0 }],
      };
    });

    return {
      meta: {
        timestamps: false,
      },
      data,
    };
  }

  addFile(e) {
    const file = e.dataTransfer.files[0];
    if (file.type.match(/audio.*/)) {
      this.form.audio = file;
    }
  }

  removeFile() {
    this.form.audio = null;
  }

  resetForm() {
    this.form = { ...defaults };
    if (this.track) {
      const { title, video } = this.track;
      this.form = {
        ...this.form,
        title,
        video,
        lyrics: this.lyrics,
      };
    }
  }

  async submit() {
    this.loading = true;
    if (this.track) {
      await this.update();
    } else {
      await this.create();
    }
    this.close();
  }

  async create() {
    const data: any = {};
    if (this.form.title) {
      data.title = this.form.title;
    }
    if (this.form.video) {
      data.video = this.form.video;
    }
    if (this.form.lyrics) {
      data.lyrics = this.prepareLyrics();
    }
    if (this.form.topics && this.form.topics.length > 0) {
      data.topics = this.form.topics;
    }
    const { reciterId } = this.album;
    const albumId = this.album.id;
    let response = await this.$api.tracks.store(
      reciterId,
      albumId,
      data,
      this.getRequestOptions(),
    );
    response = await this.uploadAudio(reciterId, albumId, response.id) || response;
    this.redirect(response);
  }

  async update() {
    const data: any = {};
    if (this.track.title !== this.form.title && this.form.title) {
      data.title = this.form.title;
    }
    if (this.track.video !== this.form.video && this.form.video) {
      data.video = this.form.video;
    }
    const lyricsString = this.prepareLyrics();
    if (this.track.lyrics?.content !== lyricsString && this.form.lyrics) {
      data.lyrics = lyricsString;
    }
    // TODO - make dynamic
    data.format = Format.JsonV1;
    const { id, reciterId, albumId } = this.track;
    let response = await this.$api.tracks.update(
      reciterId,
      albumId,
      id,
      data,
      this.getRequestOptions(),
    );
    if (this.form.audio) {
      response = await this.uploadAudio(reciterId, albumId, id) || response;
    }
    this.redirect(response);
  }

  async uploadAudio(reciterId, albumId, trackId) {
    if (!this.form.audio) {
      return false;
    }

    return await this.$api.tracks.changeAudio(
      reciterId,
      albumId,
      trackId,
      this.form.audio,
      this.getRequestOptions(),
    );
  }

  redirect(response) {
    this.$router.push(getTrackUri(response));
  }

  async confirmDelete() {
    if (window.confirm(`Are you sure you want to delete '${this.track.title}'?`)) {
      const { id, reciterId, albumId } = this.track;
      await this.$api.tracks.delete(reciterId, albumId, id);
      this.$router.push(getReciterUri(this.track.reciter));
    }
  }

  close() {
    this.dialog = false;
    this.loading = false;
  }

  prepareLyrics() {
    const lyrics = clone(this.form.lyrics);

    return JSON.stringify(lyrics);
  }
}
</script>

<style lang="scss" scoped>
.dialog__content {
  max-width: 800px;
  margin: 0 auto;
  padding: 96px 12px !important;
}
</style>
