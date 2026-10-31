/**
 * Berasera Audio Notifier - Suara Notifikasi Pembayaran Berasera
 * Menggunakan Web Audio API (Chime/Bell) + Web Speech Synthesis (Bahasa Indonesia)
 */

// Konversi angka nominal ke kalimat terbilang Bahasa Indonesia
function angkaKeTerbilang(nilai) {
    nilai = Math.floor(Math.abs(Number(nilai) || 0));
    if (nilai === 0) return 'nol';

    const satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

    function terbilang(n) {
        if (n < 12) {
            return satuan[n];
        } else if (n < 20) {
            return terbilang(n - 10) + ' belas';
        } else if (n < 100) {
            return terbilang(Math.floor(n / 10)) + ' puluh ' + terbilang(n % 10);
        } else if (n < 200) {
            return 'seratus ' + terbilang(n - 100);
        } else if (n < 1000) {
            return terbilang(Math.floor(n / 100)) + ' ratus ' + terbilang(n % 100);
        } else if (n < 2000) {
            return 'seribu ' + terbilang(n - 1000);
        } else if (n < 1000000) {
            return terbilang(Math.floor(n / 1000)) + ' ribu ' + terbilang(n % 1000);
        } else if (n < 1000000000) {
            return terbilang(Math.floor(n / 1000000)) + ' juta ' + terbilang(n % 1000000);
        } else if (n < 1000000000000) {
            return terbilang(Math.floor(n / 1000000000)) + ' miliar ' + terbilang(n % 1000000000);
        } else {
            return terbilang(Math.floor(n / 1000000000000)) + ' triliun ' + terbilang(n % 1000000000000);
        }
    }

    return terbilang(nilai).replace(/\s+/g, ' ').trim();
}

// Bunyi Chime Kasir (Dua nada bell yang jernih dengan Web Audio API)
function bunyikanChimeKasir() {
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!AudioContext) return;
        const ctx = new AudioContext();

        // Nada 1: 587.33 Hz (D5)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(587.33, ctx.currentTime);
        gain1.gain.setValueAtTime(0.25, ctx.currentTime);
        gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(ctx.currentTime);
        osc1.stop(ctx.currentTime + 0.35);

        // Nada 2: 880 Hz (A5)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(880, ctx.currentTime + 0.12);
        gain2.gain.setValueAtTime(0.3, ctx.currentTime + 0.12);
        gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.65);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(ctx.currentTime + 0.12);
        osc2.stop(ctx.currentTime + 0.65);
    } catch (e) {
        console.warn('Web Audio chime not supported:', e);
    }
}

// Variabel global agar objek utterance tidak di-garbage-collect oleh browser di tengah ucapan
window._currentBeraseraUtterance = null;

// Fungsi Utama: Bunyikan Notifikasi Pembayaran Berhasil
function bunyikanSuaraPembayaran(nominal, aktor = 'customer', onFinishCallback = null) {
    // 1. Mainkan Chime Bel Kasir
    bunyikanChimeKasir();

    // 2. Cek ketersediaan Web Speech API
    if (!('speechSynthesis' in window)) {
        console.warn('Browser tidak mendukung Speech Synthesis.');
        if (typeof onFinishCallback === 'function') onFinishCallback();
        return;
    }

    // Pastikan antrean sebelumnya dihentikan dan resume engine jika ter-pause
    window.speechSynthesis.cancel();
    if (window.speechSynthesis.paused) {
        window.speechSynthesis.resume();
    }

    // Tunggu chime selesai sebentar (0.35 detik) sebelum membaca
    setTimeout(() => {
        const nominalRupiah = angkaKeTerbilang(nominal) + ' rupiah';
        
        let teks = '';
        if (aktor === 'admin') {
            teks = `Pembayaran berhasil divalidasi. Sebesar ${nominalRupiah}. Terima kasih telah berbelanja di Berasera.`;
        } else {
            teks = `Pembayaran berhasil diterima. Sebesar ${nominalRupiah}. Terima kasih telah berbelanja di Berasera.`;
        }

        const utterance = new SpeechSynthesisUtterance(teks);
        utterance.lang = 'id-ID';
        utterance.rate = 0.92; // Sedikit lebih santai agar sangat jelas
        utterance.pitch = 1.0; 

        // Cari voice bahasa Indonesia terbaik
        const voices = window.speechSynthesis.getVoices();
        const indoVoice = voices.find(v => (v.lang && (v.lang.toLowerCase().includes('id') || v.lang.toLowerCase().includes('ind'))) || (v.name && v.name.toLowerCase().includes('indonesia')));
        if (indoVoice) {
            utterance.voice = indoVoice;
        }

        // Simpan ke window agar tidak dipotong oleh garbage collector
        window._currentBeraseraUtterance = utterance;

        utterance.onend = function () {
            window._currentBeraseraUtterance = null;
            if (typeof onFinishCallback === 'function') {
                onFinishCallback();
            }
        };

        utterance.onerror = function (e) {
            console.error('SpeechSynthesis error:', e);
            window._currentBeraseraUtterance = null;
            if (typeof onFinishCallback === 'function') {
                onFinishCallback();
            }
        };

        window.speechSynthesis.speak(utterance);
    }, 350);
}

// Inisialisasi daftar voice saat browser memuatnya
if ('speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = function() {
        window.speechSynthesis.getVoices();
    };
}

