import React from "react";
import type { PricingPlan } from "../../Types/landing";

interface PricingSectionProps {
  plans: PricingPlan[];
}

const PricingSection: React.FC<PricingSectionProps> = ({ plans }) => (
  <section id="pricing" className="relative py-24 md:py-32">
    {/* Background */}
    <div className="absolute inset-0 bg-gradient-to-b from-transparent via-cyan-50/30 to-transparent dark:via-cyan-950/10" />

    <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      {/* Section header */}
      <div className="text-center mb-16">
        <span className="inline-block px-4 py-1.5 rounded-full bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 text-sm font-semibold mb-4">
          Paket Internet
        </span>
        <h2 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
          Pilih Paket Yang{" "}
          <span className="bg-gradient-to-r from-cyan-400 to-blue-600 bg-clip-text text-transparent">Tepat</span>
        </h2>
        <p className="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
          Semua paket termasuk unlimited kuota, fiber optic, support 24 jam, dan gratis pemasangan.
        </p>
      </div>

      {/* Pricing cards */}
      <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        {plans.map((plan) => (
          <div
            key={plan.id}
            className={`group relative rounded-2xl transition-all duration-500 hover:-translate-y-2 ${
              plan.isPopular
                ? "bg-white dark:bg-slate-800 border-2 border-cyan-400 dark:border-cyan-500 shadow-2xl shadow-cyan-500/10 scale-[1.02] z-10"
                : "bg-white/80 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700 shadow-lg hover:shadow-xl"
            }`}
          >
            {/* Popular badge */}
            {plan.badge && (
              <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 z-10">
                <span className="px-4 py-1.5 bg-gradient-to-r from-cyan-400 to-blue-600 text-white text-xs font-bold rounded-full shadow-lg shadow-cyan-500/25 whitespace-nowrap">
                  {plan.badge}
                </span>
              </div>
            )}

            <div className="p-6 md:p-8">
              {/* Plan name & speed */}
              <div className="text-center mb-6">
                <p className="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{plan.name}</p>
                <div className="text-5xl font-extrabold text-slate-800 dark:text-white mb-1">{plan.speedLabel}</div>
                <div className="flex items-baseline justify-center gap-1">
                  <span className="text-3xl font-extrabold text-slate-800 dark:text-white">
                    Rp {plan.price.toLocaleString("id-ID")}
                  </span>
                  <span className="text-sm text-slate-400">/bulan</span>
                </div>
              </div>

              {/* Speed bar */}
              <div className="h-2 rounded-full bg-slate-100 dark:bg-slate-700 mb-6 overflow-hidden">
                <div
                  className={`h-full rounded-full bg-gradient-to-r ${plan.color} transition-all duration-1000 group-hover:w-full`}
                  style={{ width: `${Math.min((plan.speed / 100) * 100, 100)}%` }}
                />
              </div>

              {/* Features */}
              <ul className="space-y-3 mb-8">
                {plan.features.map((f, i) => (
                  <li key={i} className="flex items-start gap-2.5 text-sm text-slate-600 dark:text-slate-300">
                    <i className="material-symbols-outlined !text-[16px] text-green-500 mt-0.5 shrink-0">check</i>
                    <span>{f}</span>
                  </li>
                ))}
              </ul>

              {/* CTA */}
              <a
                href={`https://wa.me/6281234567890?text=Halo%20RJNet%2C%20saya%20tertarik%20dengan%20paket%20${plan.speedLabel}`}
                target="_blank"
                rel="noopener noreferrer"
                className={`block w-full text-center py-3.5 rounded-xl text-sm font-bold transition-all ${
                  plan.isPopular
                    ? "bg-gradient-to-r from-cyan-400 to-blue-600 text-white hover:shadow-xl hover:shadow-cyan-500/25"
                    : "bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-cyan-50 dark:hover:bg-slate-600"
                }`}
              >
                {plan.isPopular ? "Langganan Sekarang" : "Pilih Paket"}
              </a>
            </div>
          </div>
        ))}
      </div>
    </div>
  </section>
);

export default PricingSection;
