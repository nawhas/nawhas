// Define global filters here.
import Vue from 'vue';
import _ from 'lodash';
import { pluralize } from '@/filters/string';
import { calendarDate } from '@/filters/date';

Vue.filter('pluralize', pluralize);
Vue.filter('startCase', _.startCase);
Vue.filter('date', calendarDate);
