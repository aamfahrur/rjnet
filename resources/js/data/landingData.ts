// =============================================================================
// Dummy Data — RJNet Landing Page
// =============================================================================

import type { LandingData } from "../Types/landing";

const landingData: LandingData = {
    navLinks: [
        { label: "Home", href: "#home" },
        { label: "Paket", href: "#pricing" },
        { label: "Coverage", href: "#coverage" },
        { label: "Fitur", href: "#features" },
        { label: "Testimoni", href: "#testimonials" },
        { label: "FAQ", href: "#faq" },
        { label: "Kontak", href: "#contact" },
        { label: "Login Pelanggan", href: "/panel/dashboard", isButton: true },
    ],

    pricingPlans: [
        {
            id: "basic",
            name: "Basic",
            speed: 10,
            speedLabel: "10 Mbps",
            price: 150000,
            features: [
                "Unlimited Kuota (FUP 500GB)",
                "Support 24/7 via WhatsApp",
                "Gratis Pemasangan",
                "Router WiFi standar",
                "Cocok untuk browsing & sosmed",
            ],
            color: "from-slate-500 to-gray-600",
        },
        {
            id: "standard",
            name: "Standard",
            speed: 20,
            speedLabel: "20 Mbps",
            price: 250000,
            features: [
                "Unlimited Kuota (FUP 1TB)",
                "Support 24/7 Prioritas",
                "Gratis Pemasangan",
                "Router WiFi Dual-Band",
                "Cocok untuk streaming HD",
                "Public IP (opsional)",
            ],
            color: "from-blue-500 to-cyan-500",
        },
        {
            id: "popular",
            name: "Pro",
            speed: 50,
            speedLabel: "50 Mbps",
            price: 400000,
            features: [
                "Unlimited Kuota (FUP 2TB)",
                "Support 24/7 VIP",
                "Gratis Pemasangan",
                "Router WiFi AX (WiFi 6)",
                "Cocok untuk gaming & 4K",
                "Public IP Static",
                "Prioritas bandwidth",
            ],
            isPopular: true,
            color: "from-indigo-500 to-violet-600",
            badge: "Paling Populer",
        },
        {
            id: "ultimate",
            name: "Ultimate",
            speed: 100,
            speedLabel: "100 Mbps",
            price: 750000,
            features: [
                "Unlimited Kuota (Tanpa FUP)",
                "Support 24/7 Prioritas #1",
                "Gratis Pemasangan",
                "Router WiFi AX Premium",
                "Gaming, 8K, & heavy use",
                "Public IP Static /24",
                "Prioritas bandwidth tertinggi",
                "Gratis MikroTik managed",
                "Dashboard monitoring realtime",
            ],
            color: "from-cyan-500 to-blue-600",
            badge: "Best Value",
        },
    ],

    coverageAreas: [
        { id: "c1", name: "Kota Administrasi Jakarta Pusat", type: "kota", parent: "DKI Jakarta", status: "available", color: "text-green-400" },
        { id: "c2", name: "Kota Administrasi Jakarta Selatan", type: "kota", parent: "DKI Jakarta", status: "available", color: "text-green-400" },
        { id: "c5", name: "Tangerang Selatan", type: "kota", parent: "Banten", status: "available", color: "text-green-400" },
        { id: "c7", name: "Cilandak", type: "kecamatan", parent: "Jakarta Selatan", status: "available", color: "text-green-400" },
        { id: "c8", name: "Pasar Minggu", type: "kecamatan", parent: "Jakarta Selatan", status: "available", color: "text-green-400" },
        { id: "c9", name: "Pondok Aren", type: "kecamatan", parent: "Tangerang Selatan", status: "available", color: "text-green-400" },
        { id: "c10", name: "Bintaro", type: "kecamatan", parent: "Tangerang Selatan", status: "available", color: "text-green-400" },
        { id: "c11", name: "Bekasi Selatan", type: "kecamatan", parent: "Bekasi", status: "soon", color: "text-amber-400" },
        { id: "c12", name: "Depok", type: "kota", parent: "Jawa Barat", status: "soon", color: "text-amber-400" },
        { id: "c13", name: "Bogor", type: "kota", parent: "Jawa Barat", status: "planned", color: "text-gray-400" },
        { id: "c14", name: "Tangerang", type: "kota", parent: "Banten", status: "planned", color: "text-gray-400" },
    ],

    features: [
        { id: "f1", icon: "Zap", title: "Internet Super Stabil", description: "Fiber optic backbone dengan redundant link memastikan koneksi Anda selalu stabil 24/7 tanpa downtime." },
        { id: "f2", icon: "Gamepad2", title: "Low Latency Gaming", description: "Routing dioptimalkan untuk gaming dengan latency rendah ke server Asia Tenggara. Cocok untuk Mobile Legends, PUBG, Valorant." },
        { id: "f3", icon: "Headphones", title: "Support 24 Jam", description: "Tim support siap membantu kapanpun via WhatsApp, telepon, atau live chat. Respon rata-rata di bawah 5 menit." },
        { id: "f4", icon: "Wrench", title: "Teknisi Cepat Tanggap", description: "Garansi kunjungan teknisi maksimal 2×24 jam. 90% masalah terselesaikan dalam kunjungan pertama." },
        { id: "f5", icon: "Cable", title: "100% Fiber Optik", description: "Jaringan full fiber optic sampai ke rumah, bukan wireless. Koneksi simetris upload = download." },
        { id: "f6", icon: "Infinity", title: "Unlimited Kuota", description: "Tanpa batasan kuota harian. FUP (Fair Usage Policy) kami paling tinggi di kelasnya." },
        { id: "f7", icon: "MonitorCheck", title: "Monitoring Realtime", description: "Pantau bandwidth, latency, dan uptime koneksi Anda secara realtime melalui dashboard pelanggan." },
        { id: "f8", icon: "Banknote", title: "Harga Terjangkau", description: "Internet cepat dengan harga RT/RW Net. Harga mulai dari Rp 150.000/bulan. Tanpa biaya tersembunyi." },
    ],

    stats: [
        { id: "s1", value: 1240, suffix: "+", label: "Pelanggan Aktif", icon: "Users" },
        { id: "s2", value: 99.9, suffix: "%", label: "Uptime Network", icon: "Activity" },
        { id: "s3", value: 10, suffix: " Gbps", label: "Total Bandwidth", icon: "Gauge" },
        { id: "s4", value: 15, suffix: "+", label: "Area Tercover", icon: "MapPin" },
    ],

    testimonials: [
        {
            id: "t1",
            name: "Rudi Hartono",
            role: "Pemilik Warung Kopi",
            avatar: "https://ui-avatars.com/api/?name=Rudi+Hartono&background=605CFF&color=fff&size=64",
            rating: 5,
            comment: "Internetnya stabil banget buat streaming musik di cafe. Pelanggan betah karena WiFi kencang. Support responsif banget!",
            packageName: "Paket Pro 50 Mbps",
        },
        {
            id: "t2",
            name: "Sari Wulandari",
            role: "Ibu Rumah Tangga",
            avatar: "https://ui-avatars.com/api/?name=Sari+Wulandari&background=06b6d4&color=fff&size=64",
            rating: 5,
            comment: "Anak-anak bisa sekolah online lancar, suami WFH juga aman. Harga terjangkau tapi kualitas setara provider besar.",
            packageName: "Paket Standard 20 Mbps",
        },
        {
            id: "t3",
            name: "Andi Prasetyo",
            role: "Gamer & Streamer",
            avatar: "https://ui-avatars.com/api/?name=Andi+Prasetyo&background=8b5cf6&color=fff&size=64",
            rating: 5,
            comment: "Latency rendah banget! Main Valorant ping stabil 5-15ms. Streaming di Twitch lancar. Rekomendasi buat gamer!",
            packageName: "Paket Ultimate 100 Mbps",
        },
        {
            id: "t4",
            name: "Linda Kusuma",
            role: "Pemilik Kosan",
            avatar: "https://ui-avatars.com/api/?name=Linda+Kusuma&background=10b981&color=fff&size=64",
            rating: 4,
            comment: "Pasang RJNet buat 10 kamar kos, semuanya bisa streaming bareng tanpa buffering. Manajemen bandwidth-nya bagus.",
            packageName: "Paket Pro 50 Mbps",
        },
    ],

    faqItems: [
        {
            id: "faq1",
            question: "Apakah kuota benar-benar unlimited?",
            answer: "Ya! Semua paket kami bersifat unlimited. Namun kami menerapkan FUP (Fair Usage Policy) untuk menjaga kualitas jaringan bagi semua pelanggan. FUP kami termasuk yang tertinggi di kelasnya: 500GB (Basic) hingga tanpa FUP (Ultimate). Setelah FUP tercapai, kecepatan turun ke 20% sampai periode berikutnya.",
        },
        {
            id: "faq2",
            question: "Berapa biaya pemasangan?",
            answer: "Pemasangan GRATIS untuk semua paket! Termasuk kabel fiber optic hingga 30 meter dan konfigurasi router. Biaya tambahan hanya berlaku jika diperlukan kabel lebih panjang atau instalasi khusus.",
        },
        {
            id: "faq3",
            question: "Apakah bisa untuk gaming online?",
            answer: "Sangat bisa! Jaringan kami dioptimasi untuk gaming dengan latency rendah. Paket Pro (50 Mbps) ke atas cocok untuk gaming dan streaming. Kami menggunakan routing langsung ke exchange internet utama untuk meminimalkan latency.",
        },
        {
            id: "faq4",
            question: "Area mana saja yang sudah tercover?",
            answer: "Saat ini kami melayani Jakarta Pusat, Jakarta Selatan, dan Tangerang Selatan (Bintaro, Pondok Aren, Ciputat). Bekasi dan Depok akan segera hadir. Cek coverage Anda dengan memasukkan alamat di bagian Coverage di atas.",
        },
        {
            id: "faq5",
            question: "Apakah kecepatan upload dan download simetris?",
            answer: "Ya! Karena kami menggunakan 100% fiber optic, kecepatan upload dan download simetris (1:1). Cocok untuk content creator, video conference, dan upload file besar.",
        },
        {
            id: "faq6",
            question: "Berapa lama proses pemasangan?",
            answer: "Setelah pendaftaran dan pembayaran, teknisi kami akan melakukan survey dan pemasangan dalam 1-2 hari kerja. Proses pemasangan memakan waktu sekitar 1-2 jam tergantung kondisi lapangan.",
        },
        {
            id: "faq7",
            question: "Apakah bisa pindah dari provider lain?",
            answer: "Bisa! Kami bantu proses transisi dari provider lama Anda. Tim teknisi kami akan memastikan internet tetap berjalan selama proses migrasi. Tidak ada biaya tambahan untuk migrasi.",
        },
        {
            id: "faq8",
            question: "Metode pembayaran apa yang tersedia?",
            answer: "Kami menerima pembayaran via transfer bank (BCA, Mandiri, BRI), QRIS, GoPay, OVO, Dana, dan gerai Indomaret/Alfamart. Pembayaran otomatis bulanan juga tersedia.",
        },
    ],

    topologyItems: [
        { id: "tp1", icon: "Network", title: "Fiber Optik Backbone", description: "Jaringan backbone fiber optic langsung ke rumah dengan teknologi GPON terbaru, menjamin kecepatan dan stabilitas maksimal." },
        { id: "tp2", icon: "Server", title: "MikroTik Core", description: "Menggunakan router MikroTik CCR series sebagai core network dengan kapasitas routing hingga 100 Gbps dan manajemen trafik cerdas." },
        { id: "tp3", icon: "Eye", title: "NOC Monitoring 24/7", description: "Network Operations Center memantau seluruh jaringan secara realtime. Deteksi otomatis dan notifikasi instan jika ada anomali." },
        { id: "tp4", icon: "BatteryWarning", title: "UPS & Backup Power", description: "Setiap node dilengkapi UPS dan backup generator. Jaringan tetap online bahkan saat pemadaman listrik PLN." },
        { id: "tp5", icon: "ShieldCheck", title: "Redundansi Jaringan", description: "Multi-homing ke beberapa upstream provider dengan automatic failover. Koneksi Anda tetap lancar meskipun ada gangguan di satu jalur." },
    ],
};

export default landingData;
