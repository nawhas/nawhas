// Define global filters here.
import Vue from 'vue';
import pluralize from '@/filters/pluralize';
import date from '@/filters/date';

Vue.filter('pluralize', pluralize);
Vue.filter('date', date);
