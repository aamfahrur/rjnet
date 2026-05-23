import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head } from "@inertiajs/react";

const Index: React.FC = () => {
    const nodes = [
        { id: "1", name: "Router POP Utama", type: "router", status: "online", x: 400, y: 50 },
        { id: "2", name: "Switch Dist A", type: "switch", status: "online", x: 250, y: 200 },
        { id: "3", name: "Switch Dist B", type: "switch", status: "online", x: 550, y: 200 },
        { id: "4", name: "ODP Blok A", type: "odp", status: "online", x: 100, y: 350 },
        { id: "5", name: "ODP Blok B", type: "odp", status: "online", x: 400, y: 350 },
        { id: "6", name: "ODP Blok C", type: "odp", status: "offline", x: 700, y: 350 },
    ];
    const links = [{ s: "1", t: "2" }, { s: "1", t: "3" }, { s: "2", t: "4" }, { s: "2", t: "5" }, { s: "3", t: "6" }];
    const getColor = (type: string, status: string) => status === "offline" ? "bg-red-500" : type === "router" ? "bg-blue-600" : type === "switch" ? "bg-indigo-500" : "bg-purple-500";

    return (
        <AdminLayout>
            <Head title="Topologi Jaringan" />
            <div className="mb-6"><h1 className="text-xl font-bold text-black dark:text-white">Topologi Jaringan</h1><p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Visualisasi topologi jaringan</p></div>
            <div className="flex flex-wrap gap-4 mb-4 text-xs">
                {[{ label: "Router", color: "bg-blue-600" }, { label: "Switch", color: "bg-indigo-500" }, { label: "ODP", color: "bg-purple-500" }, { label: "Offline", color: "bg-red-500" }].map(i => (
                    <div key={i.label} className="flex items-center gap-1.5"><span className={`w-3 h-3 rounded ${i.color}`} /><span className="text-gray-600 dark:text-gray-400">{i.label}</span></div>
                ))}
            </div>
            <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] overflow-hidden">
                <svg viewBox="0 0 800 450" className="w-full" style={{ minHeight: "450px" }}>
                    {links.map((l, i) => { const s = nodes.find(n => n.id === l.s), t = nodes.find(n => n.id === l.t); if (!s || !t) return null; return <line key={`l${i}`} x1={s.x} y1={s.y} x2={t.x} y2={t.y} stroke="#6366f1" strokeWidth={2} /> })}
                    {nodes.map(n => (<g key={n.id} transform={`translate(${n.x},${n.y})`}><circle r={n.type === "router" ? 22 : n.type === "switch" ? 18 : 16} className={`${getColor(n.type, n.status)} stroke-white dark:stroke-[#0c1427]`} strokeWidth={2} /><text y={n.type === "router" ? 35 : n.type === "switch" ? 28 : 24} textAnchor="middle" className="fill-gray-700 dark:fill-gray-300" style={{ fontSize: "11px" }}>{n.name}</text><title>{`${n.name}\nStatus: ${n.status}`}</title></g>))}
                </svg>
            </div>
        </AdminLayout>
    );
};

export default Index;
