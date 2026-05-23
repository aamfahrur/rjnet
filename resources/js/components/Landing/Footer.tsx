import React from "react";
import { Link } from "@inertiajs/react";

const Footer: React.FC = () => {
  const currentYear = new Date().getFullYear();

  const quickLinks = [
    { label: "Paket Internet", href: "#pricing" },
    { label: "Cek Coverage", href: "#coverage" },
    { label: "Fitur", href: "#features" },
    { label: "Testimoni", href: "#testimonials" },
    { label: "FAQ", href: "#faq" },
  ];

  const services = [
    { label: "Internet Rumah", href: "#pricing" },
    { label: "Internet Bisnis", href: "#pricing" },
    { label: "Internet Kosan", href: "#pricing" },
    { label: "Managed Router", href: "#contact" },
  ];

  return (
    <footer className="relative bg-slate-900 text-slate-300 overflow-hidden" id="contact">
      {/* Background decoration */}
      <div className="absolute inset-0 opacity-[0.03]">
        <div className="absolute -top-40 -left-40 w-80 h-80 bg-cyan-500 rounded-full blur-3xl" />
        <div className="absolute -bottom-40 -right-40 w-80 h-80 bg-blue-500 rounded-full blur-3xl" />
      </div>

      <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-10">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-16">
          {/* Brand */}
          <div className="lg:col-span-1">
            <a href="#home" className="flex items-center gap-2.5 mb-4">
              <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center">
                <span className="text-white font-bold text-base">R</span>
              </div>
              <span className="font-bold text-xl text-white">
                RJ<span className="text-cyan-400">Net</span>
              </span>
            </a>
            <p className="text-sm text-slate-400 mb-6 leading-relaxed">
              Internet cepat, stabil, dan terjangkau untuk rumah dan bisnis Anda. Didukung jaringan fiber optic dan tim support profesional.
            </p>
            <div className="flex gap-3">
              {[
                { icon: "https://img.icons8.com/color/24/facebook.png", label: "Facebook" },
                { icon: "https://img.icons8.com/color/24/instagram-new.png", label: "Instagram" },
                { icon: "https://img.icons8.com/color/24/twitterx.png", label: "Twitter" },
                { icon: "https://img.icons8.com/color/24/tiktok.png", label: "TikTok" },
              ].map((s) => (
                <a key={s.label} href="#" aria-label={s.label} className="w-10 h-10 rounded-xl bg-slate-800 hover:bg-slate-700 flex items-center justify-center transition-colors">
                  <img src={s.icon} alt={s.label} className="w-5 h-5" />
                </a>
              ))}
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h4 className="text-white font-semibold mb-4">Menu</h4>
            <ul className="space-y-2.5">
              {quickLinks.map((l) => (
                <li key={l.href}>
                  <a href={l.href} className="text-sm text-slate-400 hover:text-cyan-400 transition-colors">{l.label}</a>
                </li>
              ))}
            </ul>
          </div>

          {/* Services */}
          <div>
            <h4 className="text-white font-semibold mb-4">Layanan</h4>
            <ul className="space-y-2.5">
              {services.map((l) => (
                <li key={l.href}>
                  <a href={l.href} className="text-sm text-slate-400 hover:text-cyan-400 transition-colors">{l.label}</a>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h4 className="text-white font-semibold mb-4">Kontak</h4>
            <ul className="space-y-3">
              <li className="flex items-start gap-3">
                <i className="material-symbols-outlined !text-[16px] text-cyan-400 mt-0.5 shrink-0">location_on</i>
                <span className="text-sm text-slate-400">Jl. Cilandak Raya No. 45, Jakarta Selatan, 12430</span>
              </li>
              <li className="flex items-center gap-3">
                <i className="material-symbols-outlined !text-[16px] text-cyan-400 shrink-0">call</i>
                <a href="tel:+622112345678" className="text-sm text-slate-400 hover:text-cyan-400 transition-colors">(021) 1234-5678</a>
              </li>
              <li className="flex items-center gap-3">
                <i className="material-symbols-outlined !text-[16px] text-cyan-400 shrink-0">mail</i>
                <a href="mailto:info@rjnet.id" className="text-sm text-slate-400 hover:text-cyan-400 transition-colors">info@rjnet.id</a>
              </li>
              <li className="flex items-center gap-3">
                <i className="material-symbols-outlined !text-[16px] text-cyan-400 shrink-0">chat</i>
                <a href="https://wa.me/6281234567890" className="text-sm text-slate-400 hover:text-cyan-400 transition-colors">+62 812-3456-7890 (WA)</a>
              </li>
            </ul>
          </div>
        </div>

        {/* Bottom bar */}
        <div className="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
          <p className="text-sm text-slate-500">
            &copy; {currentYear} <span className="text-slate-400 font-medium">RJNet</span>. All rights reserved.
          </p>
          <div className="flex gap-6">
            <a href="#" className="text-xs text-slate-500 hover:text-cyan-400 transition-colors">Syarat & Ketentuan</a>
            <a href="#" className="text-xs text-slate-500 hover:text-cyan-400 transition-colors">Kebijakan Privasi</a>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
