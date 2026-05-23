import React from "react";
import type { Testimonial } from "../../Types/landing";

interface TestimonialsSectionProps {
  testimonials: Testimonial[];
}

const TestimonialsSection: React.FC<TestimonialsSectionProps> = ({ testimonials }) => (
  <section id="testimonials" className="relative py-24 md:py-32">
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      {/* Header */}
      <div className="text-center mb-16">
        <span className="inline-block px-4 py-1.5 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-sm font-semibold mb-4">
          Testimoni
        </span>
        <h2 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
          Dipercaya{" "}
          <span className="bg-gradient-to-r from-cyan-400 to-blue-600 bg-clip-text text-transparent">Pelanggan</span>
        </h2>
        <p className="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
          Dengarkan pengalaman pelanggan kami yang telah menikmati internet cepat dan stabil dari RJNet.
        </p>
      </div>

      {/* Testimonial cards */}
      <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
        {testimonials.map((t) => (
          <div
            key={t.id}
            className="relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 hover:shadow-xl hover:border-cyan-200 dark:hover:border-cyan-700 transition-all group"
          >
            {/* Quote icon */}
            <i className="material-symbols-outlined !text-[28px] text-cyan-100 dark:text-cyan-900 mb-3">format_quote</i>

            {/* Stars */}
            <div className="flex gap-0.5 mb-3">
              {Array.from({ length: 5 }).map((_, i) => (
                <i key={i} className={`material-symbols-outlined !text-[16px] ${i < t.rating ? "text-amber-400" : "text-slate-200 dark:text-slate-600"}`}>
                  {i < t.rating ? "star" : "star"}
                </i>
              ))}
            </div>

            {/* Comment */}
            <p className="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mb-5">{t.comment}</p>

            {/* User */}
            <div className="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
              <img src={t.avatar} alt={t.name} className="w-10 h-10 rounded-full ring-2 ring-cyan-100 dark:ring-cyan-900" />
              <div>
                <p className="text-sm font-bold text-slate-800 dark:text-white">{t.name}</p>
                <p className="text-xs text-slate-400">{t.role}</p>
              </div>
            </div>

            {/* Package badge */}
            <span className="absolute top-4 right-4 px-2.5 py-1 bg-cyan-50 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 text-[10px] font-semibold rounded-full">
              {t.packageName}
            </span>
          </div>
        ))}
      </div>
    </div>
  </section>
);

export default TestimonialsSection;
