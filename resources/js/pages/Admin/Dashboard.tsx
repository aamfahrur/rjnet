import React from "react";
import AdminLayout from "../../layouts/AdminLayout";
import { Head, Link } from "@inertiajs/react";
import Chart from "react-apexcharts";

interface Props {
    stats: {
        total_customers: number;
        active_customers: number;
        suspended_customers: number;
        total_routers: number;
        online_routers: number;
        total_packages: number;
        active_subscriptions: number;
        pending_invoices: number;
        overdue_invoices: number;
        revenue_this_month: number;
        open_tickets: number;
        online_users: number;
    };
    revenue_chart?: { month: string; amount: number }[];
    customer_growth?: { month: string; count: number }[];
    recent_activities?: { id: number; description: string; created_at: string }[];
}

const StatCard: React.FC<{
    title: string; value: string | number; icon: string;
    gradient: string; trend?: string; trendUp?: boolean; subtitle?: string;
}> = ({ title, value, icon, gradient, trend, trendUp, subtitle }) => (
    <div className="group relative bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] p-5 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300 hover:shadow-lg hover:shadow-primary-500/5">
        <div className="flex items-start justify-between">
            <div className="space-y-3">
                <p className="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{title}</p>
                <div>
                    <h3 className="text-3xl font-bold text-gray-800 dark:text-white tracking-tight">{value}</h3>
                    {trend && (
                        <div className="flex items-center gap-1 mt-1">
                            <i className={`material-symbols-outlined !text-[14px] ${trendUp ? "text-green-500" : "text-red-500"}`}>
                                {trendUp ? "trending_up" : "trending_down"}
                            </i>
                            <span className={`text-xs font-medium ${trendUp ? "text-green-500" : "text-red-500"}`}>{trend}</span>
                        </div>
                    )}
                </div>
                {subtitle && <p className="text-xs text-gray-400 dark:text-gray-500">{subtitle}</p>}
            </div>
            <div className={`w-11 h-11 rounded-xl bg-gradient-to-br ${gradient} flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300`}>
                <i className="material-symbols-outlined text-white !text-[22px]">{icon}</i>
            </div>
        </div>
    </div>
);

const Dashboard: React.FC<Props> = ({ stats, revenue_chart = [], customer_growth = [], recent_activities = [] }) => {
    const isDark = typeof document !== "undefined" && document.documentElement.classList.contains("dark");
    const chartText = isDark ? "#9ca3af" : "#6b7280";
    const chartGrid = isDark ? "#1f2937" : "#e5e7eb";

    const revenueSeries = [{ name: "Pendapatan", data: revenue_chart.map((d) => d.amount) }];
    const revenueCategories = revenue_chart.map((d) => d.month);

    const customerSeries = [{ name: "Pelanggan Baru", data: customer_growth.map((d) => d.count) }];
    const customerCategories = customer_growth.map((d) => d.month);

    return (
        <AdminLayout>
            <Head title="Dashboard" />
            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-gray-800 dark:text-white">Dashboard</h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Ringkasan sistem manajemen RT/RW Net</p>
                </div>
                <div className="flex gap-2">
                    <Link href="/admin/customers/create" className="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition-colors shadow-sm shadow-primary-500/25">
                        <i className="material-symbols-outlined !text-[18px]">person_add</i>
                        Pelanggan Baru
                    </Link>
                </div>
            </div>

            {/* Stat Cards Row 1 */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <StatCard title="Total Pelanggan" value={stats.total_customers} icon="people"
                    gradient="from-primary-500 to-indigo-600" trend="+12% bulan ini" trendUp
                    subtitle={`${stats.active_customers} aktif · ${stats.suspended_customers} suspend`} />
                <StatCard title="Pendapatan Bulan Ini" value={`Rp ${(stats.revenue_this_month ?? 0).toLocaleString("id-ID")}`} icon="payments"
                    gradient="from-green-500 to-emerald-600" trend="+8% bulan ini" trendUp />
                <StatCard title="Router Online" value={`${stats.online_routers}/${stats.total_routers}`} icon="router"
                    gradient="from-blue-500 to-cyan-600" subtitle={`${stats.online_users} user terhubung`} />
                <StatCard title="Tiket Support" value={stats.open_tickets} icon="support_agent"
                    gradient={stats.open_tickets > 10 ? "from-red-500 to-rose-600" : "from-orange-500 to-amber-600"}
                    trend={stats.open_tickets > 10 ? "↑ 3 tiket baru" : "Terkendali"} trendUp={stats.open_tickets <= 10} />
            </div>

            {/* Stat Cards Row 2 */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <StatCard title="Langganan Aktif" value={stats.active_subscriptions} icon="subscriptions"
                    gradient="from-indigo-500 to-purple-600" />
                <StatCard title="Invoice Pending" value={stats.pending_invoices} icon="pending_actions"
                    gradient="from-amber-500 to-yellow-600"
                    subtitle={stats.overdue_invoices > 0 ? `${stats.overdue_invoices} overdue` : "Semua lancar"} />
                <StatCard title="Invoice Overdue" value={stats.overdue_invoices} icon="warning"
                    gradient={stats.overdue_invoices > 0 ? "from-red-500 to-pink-600" : "from-gray-400 to-gray-500"}
                    trend={stats.overdue_invoices > 0 ? "Perlu tindakan!" : "Aman"} trendUp={false} />
                <StatCard title="Paket Internet" value={stats.total_packages} icon="inventory_2"
                    gradient="from-violet-500 to-purple-600" />
            </div>

            {/* Charts + Activities */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                {/* Revenue Chart */}
                <div className="lg:col-span-2 bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] p-6">
                    <div className="flex items-center justify-between mb-4">
                        <h3 className="text-base font-semibold text-gray-800 dark:text-white">Grafik Pendapatan</h3>
                        <select className="text-xs border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1.5 bg-gray-50 dark:bg-[#15203c] text-gray-600 dark:text-gray-300">
                            <option>6 Bulan Terakhir</option>
                            <option>Tahun Ini</option>
                        </select>
                    </div>
                    {revenueSeries[0].data.length > 0 ? (
                        <Chart
                            type="area"
                            height={280}
                            series={revenueSeries}
                            options={{
                                chart: { type: "area", toolbar: { show: false }, fontFamily: "Inter, sans-serif", background: "transparent" },
                                colors: ["#605CFF"],
                                fill: { type: "gradient", gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1, stops: [0, 100] } },
                                stroke: { curve: "smooth", width: 2.5 },
                                grid: { borderColor: chartGrid, strokeDashArray: 4 },
                                xaxis: { categories: revenueCategories, labels: { style: { colors: chartText, fontSize: "12px" } }, axisBorder: { show: false } },
                                yaxis: { labels: { style: { colors: chartText, fontSize: "12px" }, formatter: (v: number) => `Rp ${v.toLocaleString("id-ID")}` } },
                                dataLabels: { enabled: false },
                                tooltip: { theme: isDark ? "dark" : "light", y: { formatter: (v: number) => `Rp ${v.toLocaleString("id-ID")}` } },
                            }}
                        />
                    ) : (
                        <div className="h-[280px] flex items-center justify-center text-sm text-gray-400">
                            <div className="text-center">
                                <i className="material-symbols-outlined !text-[40px] mb-2">bar_chart</i>
                                <p>Belum ada data pendapatan</p>
                            </div>
                        </div>
                    )}
                </div>

                {/* Recent Activities */}
                <div className="bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] p-6">
                    <h3 className="text-base font-semibold text-gray-800 dark:text-white mb-4">Aktivitas Terbaru</h3>
                    {recent_activities.length > 0 ? (
                        <div className="space-y-3">
                            {recent_activities.map((a) => (
                                <div key={a.id} className="flex gap-3 text-sm">
                                    <div className="w-1.5 h-1.5 rounded-full bg-primary-500 mt-1.5 shrink-0" />
                                    <div>
                                        <p className="text-gray-700 dark:text-gray-300 text-xs leading-relaxed">{a.description}</p>
                                        <p className="text-gray-400 dark:text-gray-500 text-[10px] mt-0.5">{a.created_at}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="flex flex-col items-center justify-center h-48 text-gray-400 text-sm">
                            <i className="material-symbols-outlined !text-[36px] mb-2">history</i>
                            <p>Belum ada aktivitas</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Customer Growth + Quick Actions */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Customer Growth Chart */}
                <div className="lg:col-span-2 bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] p-6">
                    <h3 className="text-base font-semibold text-gray-800 dark:text-white mb-4">Pertumbuhan Pelanggan</h3>
                    {customerSeries[0].data.length > 0 ? (
                        <Chart
                            type="bar"
                            height={280}
                            series={customerSeries}
                            options={{
                                chart: { type: "bar", toolbar: { show: false }, fontFamily: "Inter, sans-serif", background: "transparent" },
                                colors: ["#605CFF"],
                                plotOptions: { bar: { borderRadius: 6, columnWidth: "40%", borderRadiusApplication: "end" } },
                                grid: { borderColor: chartGrid, strokeDashArray: 4 },
                                xaxis: { categories: customerCategories, labels: { style: { colors: chartText, fontSize: "12px" } }, axisBorder: { show: false } },
                                yaxis: { labels: { style: { colors: chartText, fontSize: "12px" } } },
                                dataLabels: { enabled: false },
                                tooltip: { theme: isDark ? "dark" : "light" },
                            }}
                        />
                    ) : (
                        <div className="h-[280px] flex items-center justify-center text-sm text-gray-400">
                            <div className="text-center">
                                <i className="material-symbols-outlined !text-[40px] mb-2">trending_up</i>
                                <p>Belum ada data pertumbuhan</p>
                            </div>
                        </div>
                    )}
                </div>

                {/* Quick Actions */}
                <div className="bg-white dark:bg-[#0c1427] rounded-xl border border-gray-100 dark:border-[#172036] p-6">
                    <h3 className="text-base font-semibold text-gray-800 dark:text-white mb-4">Aksi Cepat</h3>
                    <div className="space-y-2">
                        {[
                            { href: "/admin/customers/create", icon: "person_add", color: "from-primary-500 to-indigo-600", label: "Tambah Pelanggan" },
                            { href: "/admin/routers/create", icon: "dns", color: "from-blue-500 to-cyan-600", label: "Tambah Router" },
                            { href: "/admin/invoices", icon: "receipt_long", color: "from-green-500 to-emerald-600", label: "Generate Invoice" },
                            { href: "/admin/tickets", icon: "support_agent", color: "from-orange-500 to-amber-600", label: "Lihat Tiket" },
                            { href: "/admin/topology", icon: "account_tree", color: "from-purple-500 to-violet-600", label: "Topologi Jaringan" },
                            { href: "/admin/monitoring", icon: "monitor_heart", color: "from-red-500 to-rose-600", label: "NOC Monitoring" },
                        ].map((a) => (
                            <Link key={a.href} href={a.href}
                                className="flex items-center gap-3 p-3 rounded-lg border border-gray-50 dark:border-[#172036] hover:bg-gray-50 dark:hover:bg-[#15203c] transition-all group">
                                <div className={`w-9 h-9 rounded-lg bg-gradient-to-br ${a.color} flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform`}>
                                    <i className="material-symbols-outlined text-white !text-[18px]">{a.icon}</i>
                                </div>
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{a.label}</span>
                                <i className="material-symbols-outlined text-gray-300 dark:text-gray-600 ml-auto !text-[16px]">chevron_right</i>
                            </Link>
                        ))}
                    </div>
                </div>
            </div>
        </AdminLayout>
    );
};

export default Dashboard;
