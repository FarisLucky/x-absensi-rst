import CryptoJS from "crypto-js";

const SECRET_KEY = "Jg6rzmWWLLEuEsPEuLad"; // Ganti dengan kunci rahasia Anda

export const encryptData = (data) => {
  return CryptoJS.AES.encrypt(JSON.stringify(data), SECRET_KEY).toString();
};

export const decryptData = (ciphertext) => {
    if(ciphertext !== undefined) {
        const bytes = CryptoJS.AES.decrypt(ciphertext, SECRET_KEY);
        return JSON.parse(bytes.toString(CryptoJS.enc.Utf8));
    }

    return undefined;
};