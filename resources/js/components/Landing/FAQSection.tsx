import React, { useState } from "react";
import type { FAQItem } from "../../Types/landing";

interface FAQSectionProps {
  items: FAQItem[];
}

const FAQSection: React.FC<FAQSectionProps> = ({ items }) => {
  const [openId, setOpenId] = useState<string | null>(null);

  return (
    <section id="faq" className="relative py-24 md:py-32 bg-slate-50/50 dark:bg-slate-900/30">
      <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center mb-16">
          <span className="inline-block px-4 py-1.5 rounded-full bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 text-sm font-semibold mb-4">
            FAQ
          </span>
          <h2 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
            Pertanyaan{" "}
            <span className="bg-gradient-to-r from-cyan-400 to-blue-600 bg-clip-text text-transparent">Umum</span>
          </h2>
          <p className="text-lg text-slate-500 dark:text-slate-400">
            Semua yang perlu Anda ketahui tentang layanan internet RJNet.
          </p>
        </div>

        {/* FAQ accordion */}
        <div className="space-y-3">
          {items.map((item) => {
            const isOpen = openId === item.id;
            return (
              <div
                key={item.id}
                className={`bg-white dark:bg-slate-800 rounded-2xl border transition-all ${
                  isOpen
                    ? "border-cyan-200 dark:border-cyan-700 shadow-lg"
                    : "border-slate-100 dark:border-slate-700 hover:border-slate-200 dark:hover:border-slate-600"
                }`}
              >
                <button
                  onClick={() => setOpenId(isOpen ? null : item.id)}
                  className="w-full flex items-center justify-between px-6 py-5 text-left"
                >
                  <span className="text-sm font-semibold text-slate-800 dark:text-white pr-4">{item.question}</span>
                  <i className={`material-symbols-outlined !text-[18px] shrink-0 text-slate-400 transition-transform duration-300 ${isOpen ? "rotate-180" : ""}`}>expand_more</i>
                </button>
                <div
                  className={`overflow-hidden transition-all duration-300 ${
                    isOpen ? "max-h-96" : "max-h-0"
                  }`}
                >
                  <p className="px-6 pb-5 text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    {item.answer}
                  </p>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default FAQSection;
