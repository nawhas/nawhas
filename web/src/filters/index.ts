// Define global filters here.
import Vue from 'vue';
import { pluralize } from '@/filters/string';
import { startCase } from 'lodash';


Vue.filter('pluralize', pluralize);
Vue.filter('startCase', startCase);
