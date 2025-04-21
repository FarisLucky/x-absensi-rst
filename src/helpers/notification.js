import { LocalNotifications } from "@capacitor/local-notifications";

export const NotificationUtil = {
  async requestPermission() {
    const { display } = await LocalNotifications.requestPermissions();
    if (display === "granted") {
      console.log("Notification permissions granted");
    } else {
      console.log("Notification permissions denied");
    }
  },

  async scheduleAttendanceNotification() {
    await LocalNotifications.schedule({
      notifications: [
        {
          id: 1, // Unique ID for the notification
          title: "Attendance Reminder",
          body: "It’s time to mark your attendance!",
          schedule: { at: new Date(new Date().getTime() + 5000) }, // 5 seconds from now
          sound: "default", // Optional: Play a sound
          attachments: null, // Optional: Add attachments
          actionTypeId: "", // Optional: Add action type
          extra: null, // Optional: Add extra data
        },
      ],
    });
  },

  async scheduleRecurringNotification() {
    await LocalNotifications.schedule({
      notifications: [
        {
          id: 2, // Unique ID for the notification
          title: "Daily Attendance Reminder",
          body: "Don’t forget to mark your attendance!",
          schedule: {
            at: new Date(new Date().setHours(9, 0, 0)), // Today at 9 AM
            every: "day", // Repeat daily
          },
        },
      ],
    });
  },
};
