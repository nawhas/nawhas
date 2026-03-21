import * as moment from 'moment';

export function relative(date: moment.MomentInput) {
  return moment.utc(date).fromNow();
}

export function local(date: moment.MomentInput) {
  return moment.utc(date).format('LLL');
}

/** Long date (e.g. May 5, 2020) for story cards — matches legacy `web` `date` filter default. */
export function calendarDate(value: moment.MomentInput, format = 'LL'): string {
  return moment.utc(value).format(format);
}
