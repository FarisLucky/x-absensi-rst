import { register } from "register-service-worker";

const NODE_ENV = "production";

if (NODE_ENV === "production") {
  register(`https://timesheet-cki.co.id/service-worker.js`, {
    ready() {
      console.log("Service worker is active.");
    },
    registered() {
      console.log("Service worker has been registered.");
    },
    cached() {
      console.log("Content has been cached for offline use.");
    },
    updatefound() {
      console.log("New content is downloading.");
    },
    updated(registration) {
      console.log("New content is available.");
      const answer = window.confirm(
        "A new version of the app is available. Do you want to update?"
      );
      if (answer) {
        registration.waiting.postMessage("skipWaiting");
      }
    },
    offline() {
      console.log(
        "No internet connection found. App is running in offline mode."
      );
    },
    error(error) {
      console.error("Error during service worker registration:", error);
    },
  });
}
