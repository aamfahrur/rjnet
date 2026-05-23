import React from "react";
import AnimatedCounter from "./AnimatedCounter";
import type { Stat } from "../../Types/landing";

interface HeroSectionProps {
    stats: Stat[];
}

const iconClass = "material-symbols-outlined !text-[20px]";

const HeroSection: React.FC<HeroSectionProps> = ({ stats }) => (
    <section id="home" className="relative min-h-screen flex items-center overflow-hidden">
        {/* Modern 2026 background */}
        <div className="absolute inset-0 bg-gradient-to-br from-slate-50 via-cyan-50/20 to-blue-50 dark:from-slate-950 dark:via-cyan-950/10 dark:to-slate-950" />

        {/* Animated grid pattern */}
        <div className="absolute inset-0" style={{
            backgroundImage: `radial-gradient(circle at 1px 1px, ${"#cbd5e1"} 1px, transparent 0)`,
            backgroundSize: "40px 40px",
            opacity: 0.15,
        }} />

        {/* Glow orbs */}
        <div className="absolute top-1/4 -left-32 w-96 h-96 bg-cyan-400/10 dark:bg-cyan-500/5 rounded-full blur-3xl animate-pulse-slow" />
        <div className="absolute bottom-1/4 -right-32 w-96 h-96 bg-blue-500/10 dark:bg-blue-500/5 rounded-full blur-3xl animate-pulse-slow" style={{ animationDelay: "2s" }} />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-indigo-500/[0.03] rounded-full blur-3xl" />

        {/* Network line decoration */}
        <svg className="absolute inset-0 w-full h-full opacity-[0.04] dark:opacity-[0.06]" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="lineGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stopColor="#06b6d4" />
                    <stop offset="100%" stopColor="#3b82f6" />
                </linearGradient>
            </defs>
            {Array.from({ length: 12 }).map((_, i) => (
                <line key={i} x1={`${10 + i * 8}%`} y1="10%" x2={`${80 + Math.sin(i) * 20}%`} y2="90%" stroke="url(#lineGrad)" strokeWidth="1" />
            ))}
        </svg>

        <div className="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-16 md:pt-32 md:pb-24">
            <div className="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                {/* Left: Text content */}
                <div className="text-center lg:text-left">
                    {/* Badge */}
                    <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-cyan-500/10 dark:bg-cyan-500/10 border border-cyan-200/50 dark:border-cyan-500/20 text-cyan-700 dark:text-cyan-400 text-sm font-medium mb-6">
                        <span className="w-2 h-2 rounded-full bg-cyan-500 animate-pulse" />
                        Layanan tersedia di Jabodetabek
                    </div>

                    <h1 className="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold leading-tight tracking-tight mb-6">
                        <span className="text-slate-800 dark:text-white">Internet Cepat & </span>
                        <br />
                        <span className="bg-gradient-to-r from-cyan-400 via-blue-500 to-indigo-600 bg-clip-text text-transparent">
                            Stabil Tanpa Lemot
                        </span>
                    </h1>

                    <p className="text-lg md:text-xl text-slate-500 dark:text-slate-400 leading-relaxed mb-8 max-w-xl mx-auto lg:mx-0">
                        Nikmati internet fiber optic dengan kecepatan hingga <strong className="text-slate-700 dark:text-slate-200">100 Mbps</strong>,
                        latency rendah untuk gaming, dan support 24 jam. Installasi gratis, tanpa biaya tersembunyi.
                    </p>

                    {/* CTA Buttons */}
                    <div className="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                        <a
                            href="#pricing"
                            className="group px-8 py-4 bg-gradient-to-r from-cyan-400 to-blue-600 text-white rounded-2xl text-base font-bold hover:shadow-xl hover:shadow-blue-500/25 transition-all duration-300 hover:scale-[1.02] flex items-center justify-center gap-2"
                        >
                            Pasang Sekarang
                            <i className="material-symbols-outlined !text-[18px] transition-transform group-hover:translate-x-1">arrow_forward</i>
                        </a>
                        <a
                            href="#coverage"
                            className="px-8 py-4 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-2xl text-base font-semibold border border-slate-200 dark:border-slate-700 hover:border-cyan-300 dark:hover:border-cyan-600 hover:shadow-lg transition-all flex items-center justify-center gap-2"
                        >
                            <i className="material-symbols-outlined !text-[18px]">location_on</i>
                            Cek Coverage
                        </a>
                    </div>

                    {/* Stats row */}
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        {stats.map((s) => (
                            <div key={s.id} className="text-center lg:text-left">
                                <div className="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">
                                    <AnimatedCounter value={s.value} suffix={s.suffix} prefix={s.prefix} />
                                </div>
                                <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">{s.label}</p>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Right: Visual */}
                <div className="relative hidden lg:block">
                    {/* Main card */}
                    <div className="relative z-10 bg-white/70 dark:bg-slate-800/70 backdrop-blur-2xl rounded-3xl border border-slate-200/50 dark:border-slate-700/50 p-8 shadow-2xl shadow-slate-200/50 dark:shadow-black/20">
                        {/* ISP Mock UI */}
                        <div className="flex items-center gap-3 mb-6">
                            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center">
                                <span className="text-white font-bold text-sm">R</span>
                            </div>
                            <div>
                                <p className="font-bold text-slate-800 dark:text-white text-sm">RJNet ISP</p>
                                <p className="text-xs text-green-500 font-medium">● Connected</p>
                            </div>
                        </div>

                        {/* Speed meter */}
                        <div className="bg-slate-100 dark:bg-slate-900 rounded-2xl p-6 mb-4">
                            <p className="text-xs text-slate-500 dark:text-slate-400 mb-1">Current Speed</p>
                            <div className="flex items-baseline gap-2">
                                <span className="text-4xl font-extrabold text-slate-800 dark:text-white">97.8</span>
                                <span className="text-lg text-slate-500 dark:text-slate-400 font-medium">Mbps</span>
                            </div>
                            <div className="mt-3 h-2 bg-slate-200 dark:bg-slate-800 rounded-full overflow-hidden">
                                <div className="h-full w-4/5 bg-gradient-to-r from-cyan-400 to-blue-600 rounded-full animate-pulse" />
                            </div>
                        </div>

                        {/* Stats row */}
                        <div className="grid grid-cols-3 gap-3">
                            {[
                                { label: "Latency", value: "5ms", icon: "monitoring", color: "text-green-500" },
                                { label: "Uptime", value: "99.9%", icon: "speed", color: "text-blue-500" },
                                { label: "Users", value: "1.2K", icon: "group", color: "text-cyan-500" },
                            ].map((item) => (
                                <div key={item.label} className="bg-slate-100 dark:bg-slate-900 rounded-xl p-3 text-center">
                                    <div className={`flex justify-center mb-0.5 ${item.color}`}>
                                        <i className="material-symbols-outlined !text-[16px]">{item.icon}</i>
                                    </div>
                                    <p className="text-sm font-bold text-slate-800 dark:text-white">{item.value}</p>
                                    <p className="text-[10px] text-slate-400">{item.label}</p>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Floating cards */}
                    <div className="absolute -top-4 -right-4 z-20 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl p-4 animate-float">
                        <p className="text-2xl font-extrabold text-green-500">99.9%</p>
                        <p className="text-xs text-slate-500 dark:text-slate-400">Uptime SLA</p>
                    </div>

                    <div className="absolute -bottom-2 -left-4 z-20 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-xl p-4 animate-float" style={{ animationDelay: "1s" }}>
                        <p className="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-1">
                            <span className="text-yellow-400">★</span> 4.9
                        </p>
                        <p className="text-xs text-slate-500 dark:text-slate-400">Rating Pelanggan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
);

export default HeroSection;
