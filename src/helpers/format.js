const formatInput = function (num) {
  let number_string = num.replace(/[^,\d]/g, "").toString(),
    split = number_string.split(","),
    sisa = split[0].length % 3,
    rupiah = split[0].substr(0, sisa),
    ribuan = split[0].substr(sisa).match(/\d{3}/gi);

  // tambahkan titik jika yang di input sudah menjadi angka ribuan
  if (ribuan) {
    let separator = sisa ? "." : "";
    rupiah += separator + ribuan.join(".");
  }

  rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;

  return rupiah;
};

const numberFormat = function (num) {
  if (num == undefined || num == "") {
    return "Rp -";
  }
  return new Intl.NumberFormat("id-Id", {
    style: "currency",
    currency: "IDR",
  }).format(parseInt(num));
};

const parseFormatInput = (num) => {
  let number = num.toString().replaceAll(".", "").replaceAll(",", ".");
  return parseInt(number);
};

const createDateFromLocale = (date) => {
  var dmy = date.split("-");

  var d = new Date(dmy[2], dmy[1] - 1, dmy[0]);

  return d;
};
const convertMinutesToHours = (minutes) => {
  const hours = Math.floor(minutes / 60);
  const remainingMinutes = minutes % 60;
  return { hours, remainingMinutes };
};

export {
  numberFormat,
  formatInput,
  parseFormatInput,
  createDateFromLocale,
  convertMinutesToHours,
};
