import { GetterTree } from 'vuex';
import { Story } from '@/entities/story';
import { stories as storyList } from '@/data/stories';

export interface StoriesState {
  stories: Array<Story>;
}

const state = (): StoriesState => ({
  stories: storyList,
});

const getters: GetterTree<StoriesState, Record<string, unknown>> = {
  stories(st): Array<Story> {
    return st.stories;
  },
  story: (st) => (slug: string): Story | undefined => st.stories.find((s) => s.slug === slug),
};

export default {
  namespaced: true,
  state,
  getters,
};
