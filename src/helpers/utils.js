const SUPER_ADMIN = "SUPER_ADMIN";

const KEPALA = "KEPALA";

const STAF = "STAF";

const KASUB = "KASUB";

const KABID = "KABID";

const DIREKTUR = "DIREKTUR";

const months = [
  {
    id: 1,
    name: "Januari",
  },
  {
    id: 2,
    name: "Februari",
  },
  {
    id: 3,
    name: "Maret",
  },
  {
    id: 4,
    name: "April",
  },
  {
    id: 5,
    name: "Mei",
  },
  {
    id: 6,
    name: "Juni",
  },
  {
    id: 7,
    name: "Juli",
  },
  {
    id: 8,
    name: "Agustus",
  },
  {
    id: 9,
    name: "September",
  },
  {
    id: 10,
    name: "Oktober",
  },
  {
    id: 11,
    name: "November",
  },
  {
    id: 12,
    name: "Desember",
  },
];

const getYears = () => {
  var max = new Date().getFullYear() + 1;
  var min = max - 5;
  var years = [];

  for (var i = max; i >= min; i--) {
    years.push(i);
  }
  return years;
};

const IZIN_CUTI = "izin_cuti";

const IZIN_KRS = "izin_krs";

const IPC = "IPC";

const ICT = "ICT";

const ICM = "ICM";

const ILL = "ILL";

const IS = "IS";

const TIDAK_HADIR = 3;

const UNIT_IGD = 25;

const KOORDINATOR_IGD = 51;

const KEP_PENGADAAN = 175;

const K3 = 31;

const TELAT = "TELAT";

const TEPAT = "TEPAT";

const JADWAL_STATUS = {
  PROGRESS: 1,
  SELESAI: 2,
  TIDAK_HADIR: 3,
  IZIN: 4,
};

const JENIS_ABSEN = {
  FOTO: "FOTO",
  GPS: "GPS",
  BACKDATE: "BACKDATE",
};

/**
 * CKI
 */
const LEMBUR_STATUS = {
  PENGAJUAN: 0,
  SELESAI: 1,
  TOLAK: 2,
};

export {
  months,
  getYears,
  SUPER_ADMIN,
  KEPALA,
  KASUB,
  KABID,
  DIREKTUR,
  STAF,
  IZIN_CUTI,
  IZIN_KRS,
  IPC,
  ICM,
  ICT,
  ILL,
  IS,
  TIDAK_HADIR,
  UNIT_IGD,
  KOORDINATOR_IGD,
  K3,
  KEP_PENGADAAN,
  TELAT,
  TEPAT,
  JADWAL_STATUS,
  JENIS_ABSEN,

  /**
   * CKI
   */
  LEMBUR_STATUS,
};
