import React from "react";
import type { TopologyItem as TopologyItemType } from "../../Types/landing";

interface TopologySectionProps {
    items: TopologyItemType[];
}

const iconMap: Record<string, string> = {
    Network: "hub",
    Server: "dns",
    Eye: "visibility",
    BatteryWarning: "battery_alert",
    ShieldCheck: "verified_user",
};

const TopologySection: React.FC<TopologySectionProps> = ({ items }) => (
    <section className="relative py-24 md:py-32 bg-slate-50/50 dark:bg-slate-900/30">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {/* Header */}
            <div className="text-center mb-16">
                <span className="inline-block px-4 py-1.5 rounded-full bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 text-sm font-semibold mb-4">
                    Infrastruktur
                </span>
                <h2 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
                    Didukung{" "}
                    <span className="bg-gradient-to-r from-cyan-400 to-blue-600 bg-clip-text text-transparent">Teknologi</span>{" "}
                    Terbaik
                </h2>
                <p className="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                    Infrastruktur jaringan kami dibangun dengan perangkat enterprise-grade untuk menjamin performa dan keandalan.
                </p>
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-5 gap-6">
                {items.map((item, i) => (
                    <div
                        key={item.id}
                        className="group relative text-center p-6 rounded-2xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hover:border-cyan-200 dark:hover:border-cyan-700 hover:shadow-xl transition-all"
                    >
                        {/* Step number */}
                        <div className="absolute -top-3 left-1/2 -translate-x-1/2 w-8 h-8 rounded-full bg-gradient-to-r from-cyan-400 to-blue-600 text-white text-xs font-bold flex items-center justify-center shadow-lg">
                            {i + 1}
                        </div>

                        <div className="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-cyan-400/10 to-blue-600/10 dark:from-cyan-400/20 dark:to-blue-600/20 flex items-center justify-center text-cyan-500 dark:text-cyan-400 mb-4 group-hover:scale-110 transition-transform">
                            <i className="material-symbols-outlined !text-[28px]">{iconMap[item.icon] || "hub"}</i>
                        </div>

                        <h3 className="text-base font-bold text-slate-800 dark:text-white mb-2">{item.title}</h3>
                        <p className="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{item.description}</p>
                    </div>
                ))}
            </div>
        </div>
    </section>
);

export default TopologySection;
