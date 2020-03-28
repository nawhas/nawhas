import { clone } from '@/utils/clone';

type Value = any;

export default class StateHistory {
  private previous: Array<Value> = [];
  private current: Value;
  private future: Array<Value> = [];

  constructor(current: Value) {
    this.current = clone(current);
  }

  commit(value: Value) {
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

  undo(): Value {
    if (!this.canUndo()) {
      throw new Error('No previous changes.');
    }

    this.future.push(this.current);
    this.current = clone(this.previous.pop());
    return this.current;
  }

  redo(): Value {
    if (!this.canRedo()) {
      throw new Error('No future changes.');
    }

    this.previous.push(this.current);
    this.current = clone(this.future.pop());
    return this.current;
  }
}
