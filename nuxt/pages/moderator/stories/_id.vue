<router>
  path: /moderator/stories/:id
  name: "moderator.stories.edit"
</router>

<template>
  <v-container class="app__section mt-4 story-editor">
    <div v-if="$fetchState.pending" class="text-center py-12">
      <v-progress-circular indeterminate />
    </div>
    <template v-else-if="story">
      <div class="story-editor__header">
        <h2 class="story-editor__title">
          Edit story
        </h2>
        <v-btn color="error" text @click="confirmDelete = true">
          Delete
        </v-btn>
      </div>
      <v-form @submit.prevent="onSubmit">
        <v-text-field v-model="form.title" label="Title" required outlined />
        <v-text-field v-model="form.slug" label="Slug" outlined />
        <v-text-field v-model="form.display_date" label="Display date (YYYY-MM-DD)" outlined />
        <v-text-field v-model="form.hero_image_url" label="Hero image URL" outlined />
        <v-textarea v-model="form.excerpt" label="Excerpt (HTML allowed)" outlined rows="3" />
        <v-textarea v-model="form.body" label="Body" outlined rows="12" />
        <v-switch v-model="form.published" label="Published" color="primary" />
        <div class="story-editor__actions">
          <v-btn text :to="{ name: 'moderator.stories.index' }">
            Back to list
          </v-btn>
          <v-btn color="primary" type="submit" :loading="saving">
            Save
          </v-btn>
        </div>
      </v-form>
    </template>
    <div v-else class="story-editor__missing">
      <p>Story not found.</p>
      <v-btn color="primary" :to="{ name: 'moderator.stories.index' }">
        Back to list
      </v-btn>
    </div>

    <v-dialog v-model="confirmDelete" max-width="400">
      <v-card>
        <v-card-title>Delete story?</v-card-title>
        <v-card-text>This cannot be undone.</v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn text @click="confirmDelete = false">
            Cancel
          </v-btn>
          <v-btn color="error" :loading="deleting" @click="onDelete">
            Delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script lang="ts">
import Vue from 'vue';
import type { UpdateStoryPayload } from '@/api/stories';
import { Story } from '@/entities/story';
import { showToast } from '@/events/toaster';

interface Form {
  title: string;
  slug: string;
  excerpt: string;
  body: string;
  hero_image_url: string;
  display_date: string;
  published: boolean;
}

interface Data {
  story: Story | null;
  form: Form;
  saving: boolean;
  deleting: boolean;
  confirmDelete: boolean;
}

function storyToForm(story: Story): Form {
  return {
    title: story.title,
    slug: story.slug,
    excerpt: story.excerpt || '',
    body: story.body || '',
    hero_image_url: story.heroImageUrl || '',
    display_date: story.displayDate || '',
    published: Boolean(story.publishedAt),
  };
}

export default Vue.extend({
  layout: 'moderator',
  data(): Data {
    return {
      story: null,
      form: {
        title: '',
        slug: '',
        excerpt: '',
        body: '',
        hero_image_url: '',
        display_date: '',
        published: false,
      },
      saving: false,
      deleting: false,
      confirmDelete: false,
    };
  },
  head() {
    return {
      title: this.story ? `Edit: ${this.story.title}` : 'Edit story',
    };
  },
  async fetch() {
    const id = this.$route.params.id as string;
    try {
      const story = await this.$api.stories.show(id);
      this.story = story;
      this.form = storyToForm(story);
    } catch {
      this.story = null;
    }
  },
  methods: {
    async onSubmit() {
      if (!this.story || !this.form.title.trim()) {
        showToast({ text: 'Title is required', type: 'error' });
        return;
      }
      this.saving = true;
      try {
        const wasPublished = Boolean(this.story.publishedAt);
        const payload: UpdateStoryPayload = {
          title: this.form.title.trim(),
          slug: this.form.slug.trim() || null,
          excerpt: this.form.excerpt || null,
          body: this.form.body || null,
          hero_image_url: this.form.hero_image_url || null,
          display_date: this.form.display_date || null,
        };
        if (this.form.published !== wasPublished) {
          payload.published = this.form.published;
        }
        const updated = await this.$api.stories.update(this.story.id, payload);
        this.story = updated;
        this.form = storyToForm(updated);
        showToast({ text: 'Story saved', type: 'success' });
      } catch {
        showToast({ text: 'Could not save story', type: 'error' });
      } finally {
        this.saving = false;
      }
    },
    async onDelete() {
      if (!this.story) {
        return;
      }
      this.deleting = true;
      try {
        await this.$api.stories.destroy(this.story.id);
        showToast({ text: 'Story deleted', type: 'success' });
        await this.$router.replace({ name: 'moderator.stories.index' });
      } catch {
        showToast({ text: 'Could not delete story', type: 'error' });
      } finally {
        this.deleting = false;
        this.confirmDelete = false;
      }
    },
  },
});
</script>

<style scoped lang="scss">
.story-editor__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 24px;
}
.story-editor__title {
  font-weight: 300;
  font-size: 34px;
  margin: 0;
}
.story-editor__actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
  margin-top: 16px;
}
.story-editor__missing {
  text-align: center;
  padding: 48px 0;
}
</style>
