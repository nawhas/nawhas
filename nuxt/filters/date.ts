import moment from 'moment';

export function relative(date: moment.MomentInput) {
  return moment.utc(date).fromNow();
}

export function local(date: moment.MomentInput) {
  return moment.utc(date).format('LLL');
}
