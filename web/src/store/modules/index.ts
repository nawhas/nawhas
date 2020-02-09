/**
 * The file enables `@/store/index.js` to import all vuex modules
 * in a one-shot manner. There should not be any reason to edit this file.
 */
import { Module, ModuleTree } from 'vuex';

const files = require.context('.', false, /\.js$/);
const modules: ModuleTree<Module<object, object>> = {};

files.keys().forEach((key: string) => {
  if (key === './index.js') {
    return;
  }
  modules[key.replace(/(\.\/|\.js)/g, '')] = files(key).default;
});

export default modules;
