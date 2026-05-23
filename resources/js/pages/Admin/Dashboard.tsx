import React from "react";
import AdminLayout from "../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";

interface Props { stats: { total_customers: number; active_customers: number; suspended_customers: number; total_routers: number; online_routers: number; total_packages: number; active_subscriptions: number; pending_invoices: number; overdue_invoices: number; revenue_this_month: number; open_tickets: number; online_users: number; }; }

const StatCard: React.FC<{ title: string; value: string | number; icon: string; color: string; subtitle?: string }> = ({ title, value, icon, color, subtitle }) => (
    <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-5">
        <div className="flex items-center justify-between">
            <div>
                <p className="text-sm text-gray-500 dark:text-gray-400">{title}</p>
                <h3 className="text-2xl font-bold text-black dark:text-white mt-1">{value}</h3>
                {subtitle && <p className="text-xs text-gray-400 mt-1">{subtitle}</p>}
            </div>
            <div className={`w-12 h-12 rounded-full flex items-center justify-center ${color}`}>
                <i className="material-symbols-outlined text-white !text-[24px]">{icon}</i>
            </div>
        </div>
    </div>
);

const Dashboard: React.FC<Props> = ({ stats }) => (
    <AdminLayout>
        <Head title="Dashboard" />
        <div className="mb-6"><h1 className="text-xl font-bold text-black dark:text-white">Dashboard</h1><p className="text-sm text-gray-500 dark:text-gray-400 mt-1">Selamat datang di Sistem Manajemen RT/RW Net</p></div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatCard title="Total Pelanggan" value={stats.total_customers} icon="people" color="bg-primary-500" subtitle={`${stats.active_customers} Aktif · ${stats.suspended_customers} Suspend`} />
            <StatCard title="Pendapatan Bulan Ini" value={`Rp ${(stats.revenue_this_month ?? 0).toLocaleString("id-ID")}`} icon="payments" color="bg-green-500" />
            <StatCard title="Router Online" value={`${stats.online_routers}/${stats.total_routers}`} icon="router" color="bg-blue-500" subtitle={`${stats.online_users} user online`} />
            <StatCard title="Tiket Support" value={stats.open_tickets} icon="support_agent" color="bg-orange-500" />
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <StatCard title="Langganan Aktif" value={stats.active_subscriptions} icon="subscriptions" color="bg-indigo-500" />
            <StatCard title="Invoice Pending" value={stats.pending_invoices} icon="pending_actions" color="bg-yellow-500" subtitle={`${stats.overdue_invoices} overdue`} />
            <StatCard title="Invoice Overdue" value={stats.overdue_invoices} icon="warning" color="bg-red-500" />
            <StatCard title="Paket Internet" value={stats.total_packages} icon="inventory_2" color="bg-purple-500" />
        </div>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-6">
                <h2 className="text-lg font-semibold text-black dark:text-white mb-4">Aksi Cepat</h2>
                <div className="grid grid-cols-2 gap-3">
                    {[
                        { href: "/admin/customers/create", icon: "person_add", color: "text-primary-500", label: "Tambah Pelanggan" },
                        { href: "/admin/routers/create", icon: "router", color: "text-blue-500", label: "Tambah Router" },
                        { href: "/admin/invoices", icon: "receipt_long", color: "text-green-500", label: "Generate Invoice" },
                        { href: "/admin/topology", icon: "account_tree", color: "text-purple-500", label: "Topologi" },
                    ].map(a => (
                        <a key={a.href} href={a.href} className="flex items-center gap-3 p-3 rounded-lg border border-gray-100 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c] transition-colors">
                            <i className={`material-symbols-outlined ${a.color}`}>{a.icon}</i>
                            <span className="text-sm font-medium text-black dark:text-white">{a.label}</span>
                        </a>
                    ))}
                </div>
            </div>
            <div className="bg-white dark:bg-[#0c1427] rounded-lg border border-gray-100 dark:border-[#172036] p-6">
                <h2 className="text-lg font-semibold text-black dark:text-white mb-4">Status Sistem</h2>
                <div className="space-y-3">
                    {[
                        { label: "Router Online", value: `${stats.online_routers}/${stats.total_routers}`, color: "green" },
                        { label: "Pelanggan Aktif", value: `${stats.active_customers}/${stats.total_customers}`, color: "blue" },
                        { label: "Online Users", value: stats.online_users, color: "indigo" },
                        { label: "Open Tickets", value: stats.open_tickets, color: stats.open_tickets > 10 ? "red" : "green" },
                    ].map((item) => (
                        <div key={item.label} className="flex items-center justify-between">
                            <span className="text-sm text-gray-600 dark:text-gray-300">{item.label}</span>
                            <div className="flex items-center gap-2">
                                <span className={`inline-block w-2 h-2 rounded-full bg-${item.color}-500`}></span>
                                <span className="text-sm font-medium text-black dark:text-white">{item.value}</span>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    </AdminLayout>
);

export default Dashboard;
