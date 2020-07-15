import { utc, MomentInput } from 'moment';

export function relative(date: MomentInput) {
  return utc(date).fromNow();
}
