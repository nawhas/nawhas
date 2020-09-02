import { EventBus } from '@/events';

export const TOAST_SHOW = 'toast:show';

export interface ToastOptions {
  text: string;
  icon?: string;
  type?: 'success'|'error'|'message';
}

export function showToast(options: ToastOptions) {
  EventBus.$emit(TOAST_SHOW, options);
}
