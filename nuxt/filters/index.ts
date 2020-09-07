// Define global filters here.
import Vue from 'vue';
import { startCase } from 'lodash';
import { pluralize } from '@/filters/string';

Vue.filter('pluralize', pluralize);
Vue.filter('startCase', startCase);
