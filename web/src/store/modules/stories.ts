import { Story } from '@/entities/story';
import { stories } from '@/data/stories';

/*
|--------------------------------------------------------------------------
| Interfaces & Types
|--------------------------------------------------------------------------
*/
export interface StoriesState {
  stories: Array<Story>;
}

/*
|--------------------------------------------------------------------------
| Store State, Mutations, Actions, & Getters
|--------------------------------------------------------------------------
*/
const state: StoriesState = {
  stories, // Data from `@/data/stories`
};

const mutations = {};
const actions = {};

const getters = {
  stories(state: StoriesState): Array<Story> {
    return state.stories;
  },
  story(state: StoriesState, slug: string): Story|undefined {
    return state.stories.find((story) => story.slug === slug);
  },
};

export default {
  state,
  mutations,
  actions,
  getters,
};
