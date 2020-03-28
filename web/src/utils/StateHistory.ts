import { clone } from '@/utils/clone';

export default class StateHistory<T> {
  public previous: Array<T> = [];
  public current: T;
  public future: Array<T> = [];

  constructor(current: T) {
    this.current = clone(current);
  }

  commit(value: T) {
    // Take the current value,
    // move it to the history.
    this.previous.push(this.current);

    // Make current equal to the passed in value.
    this.current = clone(value);
    this.future = [];
  }

  canUndo(): boolean {
    return this.previous.length !== 0;
  }

  canRedo(): boolean {
    return this.future.length !== 0;
  }

  undo(): T {
    if (!this.canUndo()) {
      throw new Error('No previous changes.');
    }

    this.future.push(this.current);
    this.current = clone(this.previous.pop() as T);
    return this.current;
  }

  redo(): T {
    if (!this.canRedo()) {
      throw new Error('No future changes.');
    }

    this.previous.push(this.current);
    this.current = clone(this.future.pop() as T);
    return this.current;
  }
}
