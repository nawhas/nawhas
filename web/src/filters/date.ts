import moment from 'moment';

const DEFAULT_FORMAT = 'LL';

export default function date(value: string, format: string = DEFAULT_FORMAT): string {
  return moment.utc(value).format(format);
}
