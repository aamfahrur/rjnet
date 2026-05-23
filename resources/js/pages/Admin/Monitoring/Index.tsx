import React from "react";
import AdminLayout from "../../../layouts/AdminLayout";
import { Head } from "@inertiajs/react";

const Index: React.FC = () => {
    const routers = [
        { name: "Router POP Utama", cpu: 35, mem: 62, uptime: "14d 6h", status: "online", pppoe: 127, hotspot: 0 },
        { name: "Router Cabang A", cpu: 22, mem: 45, uptime: "30d 1h", status: "online", pppoe: 53, hotspot: 12 },
    ];
    const getColor = (v: number, t: "cpu" | "mem") => t === "cpu" ? v > 80 ? "text-red-500" : v > 60 ? "text-yellow-500" : "text-green-500" : v > 85 ? "text-red-500" : v > 70 ? "text-yellow-500" : "text-green-500";
    const getBg = (v: number, t: "cpu" | "mem") => t === "cpu" ? v > 80 ? "bg-red-500" : v > 60 ? "bg-yellow-500" : "bg-green-500" : v > 85 ? "bg-red-500" : v > 70 ? "bg-yellow-500" : "bg-green-500";

    return (
        <AdminLayout>
            <Head title="NOC Monitoring" />
            <div className="mb-6"><h1 className="text-xl font-bold text-black dark:text-white">NOC Monitoring</h1><p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Real-time monitoring router</p></div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {routers.map(r => (<div key={r.name} className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-5">
                    <div className="flex items-center justify-between mb-3"><h3 className="font-semibold text-black dark:text-white text-sm">{r.name}</h3><div className="flex items-center gap-1"><span className="w-2 h-2 rounded-full bg-green-500" /><span className="text-xs text-green-600">{r.status}</span></div></div>
                    {[{ label: "CPU", v: r.cpu }, { label: "Memory", v: r.mem }].map(m => (<div key={m.label} className="mb-2"><div className="flex justify-between text-xs text-gray-500 mb-1"><span>{m.label}</span><span className={getColor(m.v, m.label === "CPU" ? "cpu" : "mem")}>{m.v}%</span></div><div className="w-full bg-gray-200 dark:bg-[#15203c] rounded-full h-1.5"><div className={`h-1.5 rounded-full ${getBg(m.v, m.label === "CPU" ? "cpu" : "mem")}`} style={{ width: `${m.v}%` }} /></div></div>))}
                    <div className="mt-3 flex items-center justify-between text-xs text-gray-500"><span>🕐 {r.uptime}</span><span>👤 PPPoE:{r.pppoe} · HS:{r.hotspot}</span></div>
                </div>))}
            </div>
        </AdminLayout>
    );
};

export default Index;
