import React, { useState } from "react";
import type { CoverageArea } from "../../Types/landing";

interface CoverageSectionProps {
  areas: CoverageArea[];
}

const statusConfig = {
  available: { label: "Tersedia", icon: "check_circle", className: "bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400" },
  soon: { label: "Segera", icon: "schedule", className: "bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400" },
  planned: { label: "Rencana", icon: "calendar_month", className: "bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400" },
};

const CoverageSection: React.FC<CoverageSectionProps> = ({ areas }) => {
  const [search, setSearch] = useState("");
  const [checkResult, setCheckResult] = useState<"available" | "soon" | "not_found" | null>(null);

  const filtered = areas.filter(
    (a) =>
      a.name.toLowerCase().includes(search.toLowerCase()) ||
      a.parent.toLowerCase().includes(search.toLowerCase()),
  );

  const handleCheck = () => {
    if (!search.trim()) return;
    const found = areas.find(
      (a) => a.name.toLowerCase().includes(search.toLowerCase()),
    );
    if (found) {
      setCheckResult(found.status);
    } else {
      setCheckResult("not_found");
    }
  };

  return (
    <section id="coverage" className="relative py-24 md:py-32 bg-slate-50/50 dark:bg-slate-900/30">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="text-center mb-16">
          <span className="inline-block px-4 py-1.5 rounded-full bg-blue-500/10 text-blue-600 dark:text-blue-400 text-sm font-semibold mb-4">
            Coverage Area
          </span>
          <h2 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-800 dark:text-white mb-4">
            Cek{" "}
            <span className="bg-gradient-to-r from-cyan-400 to-blue-600 bg-clip-text text-transparent">Coverage</span>{" "}
            Area Anda
          </h2>
          <p className="text-lg text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
            Masukkan alamat atau nama area untuk mengecek apakah lokasi Anda sudah tercover jaringan RJNet.
          </p>
        </div>

        {/* Search bar */}
        <div className="max-w-xl mx-auto mb-12">
          <div className="flex gap-3">
            <div className="relative flex-1">
              <i className="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 !text-[20px]">search</i>
              <input
                type="text"
                value={search}
                onChange={(e) => { setSearch(e.target.value); setCheckResult(null); }}
                onKeyDown={(e) => e.key === "Enter" && handleCheck()}
                placeholder="Masukkan nama area atau kecamatan..."
                className="w-full pl-12 pr-4 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-sm text-slate-700 dark:text-slate-200 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-400 transition-all"
              />
            </div>
            <button
              onClick={handleCheck}
              className="px-6 py-4 bg-gradient-to-r from-cyan-400 to-blue-600 text-white rounded-2xl text-sm font-bold hover:shadow-xl hover:shadow-blue-500/25 transition-all flex items-center gap-2"
            >
              <i className="material-symbols-outlined !text-[18px]">search</i>
              Cek
            </button>
          </div>

          {/* Result indicator */}
          {checkResult && (
            <div className={`mt-4 p-4 rounded-xl text-sm font-medium flex items-center gap-2 ${
              checkResult === "available" ? "bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400" :
              checkResult === "soon" ? "bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400" :
              "bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400"
            }`}>
              {checkResult === "available" && <><i className="material-symbols-outlined !text-[18px]">check_circle</i> Area ini tersedia! Silakan pilih paket Anda.</>}
              {checkResult === "soon" && <><i className="material-symbols-outlined !text-[18px]">schedule</i> Area ini akan segera hadir. Daftar sekarang untuk dapat notifikasi.</>}
              {checkResult === "not_found" && <><i className="material-symbols-outlined !text-[18px]">location_on</i> Maaf, area ini belum tercover. Kami terus memperluas jaringan.</>}
            </div>
          )}
        </div>

        {/* Coverage grid */}
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
          {filtered.map((area) => (
            <div
              key={area.id}
              className="flex items-center gap-3 p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 hover:border-cyan-200 dark:hover:border-cyan-700 transition-all"
            >
              <i className={`material-symbols-outlined !text-[18px] shrink-0 ${area.color}`}>location_on</i>
              <div className="min-w-0">
                <p className="text-sm font-medium text-slate-700 dark:text-slate-200 truncate">{area.name}</p>
                <p className="text-xs text-slate-400 truncate">{area.parent}</p>
              </div>
              <span className={`ml-auto shrink-0 inline-flex items-center gap-1 px-2 py-1 rounded-full text-[10px] font-semibold ${statusConfig[area.status].className}`}>
                <i className="material-symbols-outlined !text-[14px]">{statusConfig[area.status].icon}</i>
                {statusConfig[area.status].label}
              </span>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default CoverageSection;
