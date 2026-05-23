import React from "react";
import type { Feature as FeatureType } from "../../Types/landing";

interface FeaturesSectionProps {
  features: FeatureType[];
}

const iconMap: Record<string, string> = {
  Zap: "bolt",
  Gamepad2: "sports_esports",
  Headphones: "headphones",
  Wrench: "build",
  Cable: "cable",
  Infinity: "all_inclusive",
  MonitorCheck: "monitor_heart",
  Banknote: "payments",
};

const FeaturesSection: React.FC<FeaturesSectionProps> = ({ features }) => (
  <section id="features" className="relative py-24 md:py-32">
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      {/* Header */}
      <div className="text-center mb-16">
        <span className="inline-block px-4 py-1.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-sm font-semibold mb-4">
          Kenapa RJNet?
        </span>
        <h2 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
          Keunggulan{" "}
          <span className="bg-gradient-to-r from-cyan-400 to-blue-600 bg-clip-text text-transparent">Layanan</span> Kami
        </h2>
        <p className="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
          Kami menghadirkan internet berkualitas dengan teknologi terkini dan pelayanan profesional.
        </p>
      </div>

      {/* Feature cards */}
      <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {features.map((feature) => (
          <div
            key={feature.id}
            className="group relative p-6 rounded-2xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hover:border-cyan-200 dark:hover:border-cyan-700 hover:shadow-xl transition-all duration-300"
          >
            {/* Icon */}
            <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-cyan-400/10 to-blue-600/10 dark:from-cyan-400/20 dark:to-blue-600/20 flex items-center justify-center text-cyan-500 dark:text-cyan-400 mb-4 group-hover:scale-110 transition-transform">
              <i className="material-symbols-outlined !text-[28px]">{iconMap[feature.icon] || "bolt"}</i>
            </div>

            <h3 className="text-lg font-bold text-slate-800 dark:text-white mb-2">{feature.title}</h3>
            <p className="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{feature.description}</p>
          </div>
        ))}
      </div>
    </div>
  </section>
);

export default FeaturesSection;
