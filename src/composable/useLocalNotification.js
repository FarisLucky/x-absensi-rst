// src/composables/useNotification.js
import router from "@/router";
import { Device } from "@capacitor/device";
import { LocalNotifications } from "@capacitor/local-notifications";
import { ref } from "vue";

export function useNotification() {
  const isPermissionGranted = ref(false);

  const requestPermission = async () => {
    try {
      const result = await LocalNotifications.requestPermissions();
      isPermissionGranted.value = result.display === "granted";
      return isPermissionGranted.value;
    } catch (error) {
      console.error("Error requesting notification permission:", error);
      return false;
    }
  };

  async function createNotificationChannel() {
    await LocalNotifications.createChannel({
      id: "attendance_reminder",
      name: "Attendance Reminders",
      description: "Notifications for attendance reminders",
      importance: 4, // IMPORTANCE_HIGH
      visibility: 1, // VISIBILITY_PUBLIC
      sound: "notification.wav",
      lights: true,
      vibration: true,
    });
  }

  const setupNotificationListeners = () => {
    // When notification is clicked/opened
    LocalNotifications.addListener(
      "localNotificationActionPerformed",
      (notification) => {
        console.log("Notification action performed:", notification);

        // Handle based on notification ID or extra data
        switch (notification.notification.extra?.action) {
          case "presensi":
            router.push({ name: "PresensiMain" });
            break;
        }
      }
    );
  };

  const handleNotificationOpen = (notification) => {
    const action = notification.extra?.action;

    if (action === "presensi") {
      router.push({ name: "MainPresensi" });
    }
  };

  const scheduleNotifications = async (jadwals) => {
    const granted = await requestPermission();
    if (!granted) return;

    let deviceInfo = await Device.getInfo();
    if (deviceInfo.platform === "android") {
      await createNotificationChannel();
    }

    setupNotificationListeners();

    try {
      let notifPending = await LocalNotifications.getPending();
      if (notifPending.length > 0) {
        await LocalNotifications.cancel();
      }
      let notificationId = 1;
      const notifications = [];

      jadwals.forEach((jadwal) => {
        if (jadwal.libur < 1) {
          const checkInTime = jadwal.jam_masuk.split(":");
          const checkOutTime = jadwal.jam_pulang.split(":");
          const scheduleDate = new Date(jadwal.tanggal);

          // Check-in notification
          notifications.push({
            id: notificationId++,
            title: "Waktunya Absen Masuk!",
            body:
              "Jangan lupa untuk melakukan absensi masuk hari ini " +
              `${jadwal.tanggal} ${jadwal.jam_masuk}`,
            schedule: {
              at: new Date(
                scheduleDate.getFullYear(),
                scheduleDate.getMonth(),
                scheduleDate.getDate(),
                parseInt(checkInTime[0]),
                parseInt(checkInTime[1])
              ),
              allowWhileIdle: true,
            },
            sound: "default",
            smallIcon: "ic_stat_notification",
            iconColor: "#488AFF",
            channelId: "attendance_reminder",
            actions: [
              {
                id: "presensi",
                title: "Absen Masuk",
                extra: { immediate: true },
              },
            ],
          });

          // Check-out notification
          notifications.push({
            id: notificationId++,
            title: "Waktunya Absen Pulang!",
            body:
              "Jangan lupa untuk melakukan absensi pulang hari ini" +
              `${jadwal.tanggal} ${jadwal.jam_pulang}`,
            schedule: {
              at: new Date(
                scheduleDate.getFullYear(),
                scheduleDate.getMonth(),
                scheduleDate.getDate(),
                parseInt(checkOutTime[0]),
                parseInt(checkOutTime[1])
              ),
              allowWhileIdle: true,
            },
            sound: "default",
            smallIcon: "ic_stat_notification",
            iconColor: "#488AFF",
            channelId: "attendance_reminder",
            actions: [
              {
                id: "presensi",
                title: "Absen Masuk",
                extra: { immediate: true },
              },
            ],
          });
        }
      });

      await LocalNotifications.schedule({ notifications });

      LocalNotifications.addListener(
        "localNotificationReceived",
        (notification) => {
          console.log("Notification received:", notification);
        }
      );

      LocalNotifications.getDeliveredNotifications().then(
        ({ notifications }) => {
          if (notifications.length > 0) {
            handleNotificationOpen(notifications[0]);
          }
        }
      );
    } catch (error) {
      console.error("Error scheduling notifications:", error);
    }
  };

  return {
    isPermissionGranted,
    requestPermission,
    scheduleNotifications,
  };
}
