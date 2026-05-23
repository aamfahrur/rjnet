import React from "react";

const FloatingWhatsApp: React.FC = () => (
    <a
        href="https://wa.me/6281234567890?text=Halo%20RJNet%2C%20saya%20tertarik%20dengan%20paket%20internet%20Anda%20dan%20ingin%20info%20lebih%20lanjut."
        target="_blank"
        rel="noopener noreferrer"
        className="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-full bg-green-500 text-white flex items-center justify-center shadow-lg shadow-green-500/30 hover:scale-110 hover:shadow-green-500/50 transition-all duration-300 animate-bounce-gentle"
        aria-label="Chat via WhatsApp"
    >
        <i className="material-symbols-outlined !text-[26px]">chat</i>
        <span className="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white dark:border-slate-900 animate-pulse" />
    </a>
);

export default FloatingWhatsApp;
