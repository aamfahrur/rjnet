import React from "react";
import AnimatedCounter from "./AnimatedCounter";
import type { Stat } from "../../Types/landing";

interface StatsSectionProps {
    stats: Stat[];
}

const iconMap: Record<string, string> = {
    Users: "group",
    Activity: "monitoring",
    Gauge: "speed",
    MapPin: "location_on",
};

const StatsSection: React.FC<StatsSectionProps> = ({ stats }) => (
    <section className="relative py-16 md:py-20">
        {/* Background */}
        <div className="absolute inset-0 bg-gradient-to-r from-cyan-500 via-blue-600 to-indigo-700 rounded-3xl mx-4 sm:mx-6 lg:mx-8 overflow-hidden" />
        <div className="absolute inset-0 mx-4 sm:mx-6 lg:mx-8 rounded-3xl overflow-hidden opacity-20" style={{
            backgroundImage: `repeating-linear-gradient(45deg, transparent, transparent 8px, rgba(255,255,255,0.05) 8px, rgba(255,255,255,0.05) 16px)`,
        }} />

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center text-white py-12 md:py-16">
                {stats.map((s) => (
                    <div key={s.id} className="flex flex-col items-center gap-3">
                        <div className="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center">
                            <i className="material-symbols-outlined !text-[24px]">{iconMap[s.icon] || "group"}</i>
                        </div>
                        <div>
                            <div className="text-3xl md:text-4xl font-extrabold tracking-tight">
                                <AnimatedCounter value={s.value} suffix={s.suffix} prefix={s.prefix} />
                            </div>
                            <p className="text-white/70 text-sm mt-1">{s.label}</p>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    </section>
);

export default StatsSection;
