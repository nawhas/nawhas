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
  }

  canRevert(): boolean {
    return this.previous.length !== 0;
  }

  revert(): Value {
    if (!this.canRevert()) {
      throw new Error('No previous changes.');
    }

    this.current = clone(this.previous.pop());
    return this.current;
  }
}
