import moment from 'moment';

export function relative(date: moment.MomentInput) {
  return moment.utc(date).fromNow();
}
