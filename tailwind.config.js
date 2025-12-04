/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Palet Warna Asli TIMGRAVID
        primary: '#3D614C',       // Hijau Tua (Main Color)
        'primary-dark': '#32503F', // Hijau Lebih Gelap (Hover)
        accent: '#B48F58',        // Emas/Perunggu (Tombol/Highlight)
        cream: '#FDFCF9',         // Putih Tulang (Background)
        'text-dark': '#1C1C1C',   // Hitam Teks
        'text-muted': '#5B5B5B',  // Abu-abu Teks
      },
      fontFamily: {
        serif: ['Lora', 'serif'],      // Font untuk Judul (Elegan)
        sans: ['Inter', 'sans-serif'], // Font untuk Body (Modern)
      }
    },
  },
  plugins: [],
}