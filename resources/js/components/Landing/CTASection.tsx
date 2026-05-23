import React from "react";

const CTASection: React.FC = () => (
    <section className="relative py-20 md:py-28 overflow-hidden">
        {/* Background */}
        <div className="absolute inset-0 bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-700 mx-4 sm:mx-6 lg:mx-8 rounded-3xl overflow-hidden">
            {/* Decorative circles */}
            <div className="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4" />
            <div className="absolute bottom-0 left-0 w-72 h-72 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/4" />
        </div>

        <div className="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-16 md:py-20">
            <h2 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white mb-6">
                Siap Internet Tanpa Lemot?
            </h2>
            <p className="text-lg text-white/80 mb-10 max-w-2xl mx-auto">
                Daftar sekarang dan nikmati internet fiber optic super cepat dengan instalasi gratis. Tim kami akan menghubungi Anda dalam waktu kurang dari 30 menit.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <a
                    href="https://wa.me/6281234567890?text=Halo%20RJNet%2C%20saya%20ingin%20pasang%20internet%20sekarang!"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="group px-8 py-4 bg-white text-slate-800 rounded-2xl text-base font-bold hover:shadow-2xl hover:scale-[1.02] transition-all flex items-center justify-center gap-2"
                >
                    <i className="material-symbols-outlined !text-[20px]">chat</i>
                    Pasang via WhatsApp
                    <i className="material-symbols-outlined !text-[18px] group-hover:translate-x-1 transition-transform">arrow_forward</i>
                </a>
                <a
                    href="#pricing"
                    className="px-8 py-4 bg-white/10 backdrop-blur text-white rounded-2xl text-base font-bold border border-white/20 hover:bg-white/20 transition-all flex items-center justify-center gap-2"
                >
                    Lihat Paket
                </a>
            </div>
        </div>
    </section>
);

export default CTASection;
