<template>
  <div class="row-bg" style="display: flex; flex-wrap: wrap">
    <div
      v-for="(menu, idx) in menus"
      :key="idx"
      style="flex: 1; text-align: center"
    >
      <div
        style="
          /* border: 1px solid #e5e7eb; */
          border-radius: 0.3rem;
          display: inline-block;
          padding: 0.5rem 1rem;
          box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        "
      >
        <router-link v-if="menu.route !== null" :to="menu.route">
          <div class="avatar text-center">
            <img :src="menu.icon" width="25" class="square" />
          </div>
          <div class="text-center mt-1">
            <strong class="text-muted fs-10">{{ menu.nama }}</strong>
          </div>
        </router-link>
        <BLink v-else @click.prevent="menu.routeFn">
          <div class="avatar text-center">
            <img :src="menu.icon" width="25" class="square" />
          </div>
          <div class="text-center mt-1">
            <strong class="text-muted fs-10">{{ menu.nama }}</strong>
          </div>
        </BLink>
      </div>
    </div>
  </div>
</template>
<script>
import lemburIcon from "@/assets/images/icon/lembur.png";
import hadirIcon from "@/assets/images/icon/kehadiran.png";
import izinIcon from "@/assets/images/icon/izin.png";
import alarmIcon from "@/assets/images/icon/alarm.png";
import { notificationService } from "@/services/NotificationService";
import { useNotification } from "@/composable/useLocalNotification";
import { spinnerMethods } from "@/state/helpers";

export default {
  data() {
    return {
      menus: [
        {
          id: 1,
          nama: "Kehadiran",
          icon: hadirIcon,
          route: { name: "HarianJadwal" },
        },
        {
          id: 2,
          nama: "Lembur",
          icon: lemburIcon,
          route: {
            name: "ListLembur",
          },
        },
        {
          id: 3,
          nama: "Izin",
          icon: izinIcon,
          route: { name: "IzinkuProgress" },
        },
        {
          id: 4,
          nama: "Alarm",
          icon: alarmIcon,
          route: null,
          routeFn: this.scheduleNotification,
        },
      ],
    };
  },
  methods: {
    ...spinnerMethods,
    async scheduleNotification() {
      this.show();
      const [err, resp] = await notificationService.getJadwal();
      if (err) {
        this.toastError({
          title: "Gagal",
          msg: err.response?.data?.errors,
        });
        this.hide();

        return;
      }

      let jadwals = resp.data;

      if (!useNotification().isPermissionGranted) {
        await useNotification().requestPermission();
      }

      await useNotification().scheduleNotifications(jadwals);
      this.hide();
    },
  },
};
</script>
