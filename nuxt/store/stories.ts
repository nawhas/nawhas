import { GetterTree, MutationTree } from 'vuex';
import { Story } from '@/entities/story';

export interface StoriesState {
  stories: Array<Story>;
}

const state = (): StoriesState => ({
  stories: [],
});

const mutations: MutationTree<StoriesState> = {
  setStories(st, stories: Array<Story>) {
    st.stories = stories;
  },
};

const getters: GetterTree<StoriesState, Record<string, unknown>> = {
  stories(st): Array<Story> {
    return st.stories;
  },
  story: (st) => (slug: string): Story | undefined => st.stories.find((s) => s.slug === slug),
};

export default {
  namespaced: true,
  state,
  mutations,
  getters,
};
