import Vue from 'vue';
import * as Search from './search';
import * as Dialogs from './dialogs';

export const EventBus: Vue = new Vue();

export {
  Search,
  Dialogs,
};
