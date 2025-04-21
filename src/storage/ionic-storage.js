import { Drivers, Storage } from "@ionic/storage";

const store = new Storage({
  name: "__mydb",
  driverOrder: [Drivers.IndexedDB, Drivers.LocalStorage],
});

store.create();

const get = async (key) => {
  const data = await store.get(key);

  return data;
};

const set = async (key, val) => {
  const data = await store.set(key, val);

  return data;
};

const remove = async (key) => {
  const data = await store.remove(key);

  return data;
};

const clear = async () => {
  const data = await store.clear();

  return data;
};

export { store, get, set, remove, clear };
