import React, { useState, useEffect, type ReactNode } from "react";
import { Link, usePage } from "@inertiajs/react";
import { ToastProvider, useToast } from "../components/Toast";

interface AdminLayoutProps { children: ReactNode; }

interface MenuChild {
    path: string;
    label: string;
    badge?: string;
    badgeClass?: string;
}

interface MenuItem {
    key: string;
    icon: string;
    label: string;
    children: MenuChild[];
}

const menuItems: MenuItem[] = [
    {
        key: "dashboard", icon: "dashboard", label: "Dashboard",
        children: [
            { path: "/admin/dashboard", label: "Overview" },
            { path: "/admin/monitoring", label: "NOC Monitoring" },
        ]
    },
    {
        key: "customers", icon: "group", label: "Pelanggan",
        children: [
            { path: "/admin/customers", label: "Semua Pelanggan" },
            { path: "/admin/customers/create", label: "Tambah Pelanggan", badge: "Baru", badgeClass: "text-success-600 bg-success-100 dark:bg-[#ffffff14]" },
        ]
    },
    {
        key: "routers", icon: "router", label: "Router",
        children: [
            { path: "/admin/routers", label: "Semua Router" },
            { path: "/admin/routers/create", label: "Tambah Router", badge: "Baru", badgeClass: "text-success-600 bg-success-100 dark:bg-[#ffffff14]" },
        ]
    },
    {
        key: "billing", icon: "receipt_long", label: "Billing",
        children: [
            { path: "/admin/invoices", label: "Invoice" },
            { path: "/admin/payments", label: "Pembayaran" },
        ]
    },
    {
        key: "tickets", icon: "confirmation_number", label: "Tiket Support",
        children: [
            { path: "/admin/tickets", label: "Semua Tiket" },
        ]
    },
    {
        key: "network", icon: "share", label: "Jaringan",
        children: [
            { path: "/admin/topology", label: "Topologi" },
        ]
    },
];

const adminMenuItems = [
    { key: "users", icon: "manage_accounts", label: "Pengguna", path: "/admin/users" },
    { key: "packages", icon: "inventory_2", label: "Paket Internet", path: "/admin/packages" },
    { key: "settings", icon: "tune", label: "Konfigurasi", path: "/admin/settings" },
    { key: "reports", icon: "lab_profile", label: "Laporan", path: "/admin/reports" },
    { key: "activity", icon: "history", label: "Aktivitas", path: "/admin/activity-logs" },
];

const AdminSidebar: React.FC<{ toggleActive: () => void }> = ({ toggleActive }) => {
    const { url } = usePage();
    const [openIndex, setOpenIndex] = useState<number | null>(0); // first menu open by default

    const toggleAccordion = (index: number) => {
        setOpenIndex((prev) => (prev === index ? null : index));
    };

    const isActive = (path: string) => url === path || url.startsWith(path + "/");

    return (
        <div className="sidebar-area bg-white dark:bg-[#0c1427] fixed z-50 top-0 h-screen transition-all rounded-r-md w-[285px]">
            {/* Logo */}
            <div className="logo bg-white dark:bg-[#0c1427] border-b border-gray-100 dark:border-[#172036] px-[25px] pt-[19px] pb-[15px] absolute z-[2] right-0 top-0 left-0">
                <Link href="/admin/dashboard" className="transition-none relative flex items-center outline-none">
                    <div className="w-[26px] h-[26px] rounded-md bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center shrink-0">
                        <span className="text-white font-bold text-xs">R</span>
                    </div>
                    <span className="font-bold text-black dark:text-white relative ltr:ml-[8px] top-px text-xl">RJNet</span>
                </Link>
                <button type="button" className="burger-menu inline-block absolute z-[3] top-[24px] ltr:right-[25px] transition-all hover:text-primary-500 lg:hidden" onClick={toggleActive}>
                    <i className="material-symbols-outlined">close</i>
                </button>
            </div>

            {/* Menu */}
            <div className="pt-[89px] px-[22px] pb-[20px] h-screen overflow-y-scroll sidebar-custom-scrollbar">
                <div className="accordion">
                    {/* Section: MAIN */}
                    <span className="block relative font-medium uppercase text-gray-400 mb-[8px] text-xs">Menu Utama</span>

                    {menuItems.map((menu, index) => (
                        <div key={menu.key} className="accordion-item rounded-md text-black dark:text-white mb-[5px] whitespace-nowrap">
                            <button
                                className={`accordion-button toggle flex items-center transition-all py-[9px] ltr:pl-[14px] ltr:pr-[30px] rounded-md font-medium w-full relative hover:bg-gray-50 text-left dark:hover:bg-[#15203c] ${openIndex === index ? "open" : ""}`}
                                type="button"
                                onClick={() => toggleAccordion(index)}
                            >
                                <i className="material-symbols-outlined transition-all text-gray-500 dark:text-gray-400 ltr:mr-[7px] !text-[22px] leading-none relative -top-px">
                                    {menu.icon}
                                </i>
                                <span className="title leading-none">{menu.label}</span>
                            </button>

                            <div className={`accordion-collapse ${openIndex === index ? "block" : "hidden"} pt-[4px]`}>
                                <ul className="sidebar-sub-menu">
                                    {menu.children.map((child) => (
                                        <li key={child.path} className="sidemenu-item relative mb-[4px] last:mb-0">
                                            <Link
                                                href={child.path}
                                                className={`sidemenu-link rounded-md flex items-center relative transition-all font-medium text-gray-500 dark:text-gray-400 py-[9px] ltr:pl-[38px] ltr:pr-[30px] hover:text-primary-500 hover:bg-primary-50 w-full text-left dark:hover:bg-[#15203c] ${isActive(child.path) ? "active" : ""}`}
                                            >
                                                {child.label}
                                                {child.badge && (
                                                    <span className={`text-[10px] font-medium py-[1px] px-[8px] ltr:ml-[8px] inline-block rounded-sm ${child.badgeClass || "text-primary-500 bg-primary-100 dark:bg-[#ffffff14]"}`}>
                                                        {child.badge}
                                                    </span>
                                                )}
                                            </Link>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    ))}

                    {/* Section: ADMIN ONLY */}
                    <span className="block relative font-medium uppercase text-gray-400 mb-[8px] text-xs [&:not(:first-child)]:mt-[22px]">Administrasi</span>

                    {adminMenuItems.map((menu, index) => (
                        <div key={menu.key} className="accordion-item rounded-md text-black dark:text-white mb-[5px] whitespace-nowrap">
                            <Link
                                href={menu.path}
                                className={`accordion-button flex items-center transition-all py-[9px] ltr:pl-[14px] ltr:pr-[30px] rounded-md font-medium w-full relative hover:bg-gray-50 text-left dark:hover:bg-[#15203c] ${isActive(menu.path) ? "active" : ""}`}
                            >
                                <i className="material-symbols-outlined transition-all text-gray-500 dark:text-gray-400 ltr:mr-[7px] !text-[22px] leading-none relative -top-px">
                                    {menu.icon}
                                </i>
                                <span className="title leading-none">{menu.label}</span>
                            </Link>
                        </div>
                    ))}

                    {/* Logout */}
                    <span className="block relative font-medium uppercase text-gray-400 mb-[8px] text-xs [&:not(:first-child)]:mt-[22px]">Akun</span>
                    <div className="accordion-item rounded-md text-black dark:text-white mb-[5px] whitespace-nowrap">
                        <Link
                            href="/auth/logout"
                            method="post"
                            as="button"
                            className="accordion-button flex items-center transition-all py-[9px] ltr:pl-[14px] ltr:pr-[30px] rounded-md font-medium w-full relative hover:bg-gray-50 text-left dark:hover:bg-[#15203c] text-red-500 hover:text-red-600"
                        >
                            <i className="material-symbols-outlined transition-all ltr:mr-[7px] !text-[22px] leading-none relative -top-px">logout</i>
                            <span className="title leading-none">Logout</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
};

const AdminHeader: React.FC<{ toggleActive: () => void; darkMode: boolean; setDarkMode: (v: boolean) => void }> = ({ toggleActive, darkMode, setDarkMode }) => {
    const { auth } = usePage().props as any;
    const user = auth?.user;
    const [profileOpen, setProfileOpen] = useState(false);

    // Close on outside click
    useEffect(() => {
        if (!profileOpen) return;
        const handler = () => setProfileOpen(false);
        document.addEventListener("click", handler);
        return () => document.removeEventListener("click", handler);
    }, [profileOpen]);

    return (
        <div id="header" className="header-area bg-white dark:bg-[#0c1427] py-[13px] px-[20px] md:px-[25px] fixed top-0 z-[6] rounded-b-md transition-all shadow-sm">
            <div className="flex items-center justify-between">
                {/* Hamburger (mobile toggle sidebar) */}
                <button
                    type="button"
                    className="hide-sidebar-toggle transition-all inline-block hover:text-primary-500"
                    onClick={toggleActive}
                >
                    <i className="material-symbols-outlined !text-[20px]">menu</i>
                </button>

                {/* Right section */}
                <div className="flex items-center gap-3">
                    {/* Dark mode */}
                    <button onClick={() => setDarkMode(!darkMode)} className="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-[#15203c] transition-colors">
                        <i className="material-symbols-outlined !text-[20px] text-gray-500 dark:text-gray-400">{darkMode ? "light_mode" : "dark_mode"}</i>
                    </button>

                    {/* Profile dropdown */}
                    <div className="profile-menu relative">
                        <button
                            type="button"
                            className="flex items-center gap-2 cursor-pointer"
                            onClick={(e) => { e.stopPropagation(); setProfileOpen(!profileOpen); }}
                        >
                            <div className="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-indigo-600 flex items-center justify-center text-white text-sm font-medium shrink-0">
                                {user?.name?.charAt(0)?.toUpperCase() || "A"}
                            </div>
                            <span className="text-sm font-medium text-black dark:text-white hidden md:block">
                                {user?.name || "Admin"}
                            </span>
                            <i className={`material-symbols-outlined !text-[18px] text-gray-400 dark:text-gray-300 transition-transform ${profileOpen ? "rotate-180" : ""}`}>expand_more</i>
                        </button>

                        {/* Dropdown */}
                        {profileOpen && (
                            <div className="absolute z-50 ltr:right-0 rtl:left-0 mt-2 w-56 bg-white dark:bg-[#0c1427] rounded-md shadow-3xl border border-gray-100 dark:border-[#172036]" onClick={(e) => e.stopPropagation()}>
                                <div className="px-4 py-3 border-b border-gray-100 dark:border-[#172036]">
                                    <p className="text-sm font-semibold text-black dark:text-white">{user?.name || "Admin"}</p>
                                    <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{user?.email || "admin@rjnet.id"}</p>
                                </div>
                                <ul className="py-2">
                                    <li>
                                        <Link href="/admin/dashboard" className="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#15203c] transition-colors" onClick={() => setProfileOpen(false)}>
                                            <i className="material-symbols-outlined !text-[18px]">dashboard</i>
                                            Dashboard
                                        </Link>
                                    </li>
                                    <li>
                                        <Link href="/admin/settings" className="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#15203c] transition-colors" onClick={() => setProfileOpen(false)}>
                                            <i className="material-symbols-outlined !text-[18px]">settings</i>
                                            Pengaturan
                                        </Link>
                                    </li>
                                </ul>
                                <div className="border-t border-gray-100 dark:border-[#172036] py-2">
                                    <Link
                                        href="/auth/logout"
                                        method="post"
                                        as="button"
                                        className="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                                        onClick={() => setProfileOpen(false)}
                                    >
                                        <i className="material-symbols-outlined !text-[18px]">logout</i>
                                        Logout
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

const AdminFooter: React.FC = () => (
    <div className="bg-white dark:bg-[#0c1427] border-t border-gray-100 dark:border-[#172036] py-4 px-[25px] text-center">
        <p className="text-sm text-gray-500">
            &copy; {new Date().getFullYear()} <span className="font-medium text-black dark:text-white">RJNet</span>. All rights reserved.
        </p>
    </div>
);

const AdminLayoutContent: React.FC<AdminLayoutProps> = ({ children }) => {
    const [sidebarActive, setSidebarActive] = useState(false);
    const [darkMode, setDarkMode] = useState(() => {
        if (typeof window !== "undefined") return document.documentElement.classList.contains("dark");
        return false;
    });
    const { flash } = usePage().props as any;
    const { setFlash } = useToast();

    useEffect(() => {
        document.documentElement.classList.toggle("dark", darkMode);
        localStorage.setItem("darkMode", darkMode ? "dark" : "light");
    }, [darkMode]);

    useEffect(() => {
        if (flash && Object.keys(flash).length > 0) {
            setFlash(flash);
        }
    }, [flash]);

    const toggleSidebar = () => setSidebarActive((prev) => !prev);

    return (
        <div className={darkMode ? "dark" : ""}>
            <div className={`main-content-wrap ${sidebarActive ? "active" : ""}`}>
                <AdminSidebar toggleActive={toggleSidebar} />
                <AdminHeader toggleActive={toggleSidebar} darkMode={darkMode} setDarkMode={setDarkMode} />
                <div className="main-content">
                    <div className="px-[15px] md:px-[25px] pb-[25px]">
                        <main className="min-h-[calc(100vh-160px)]">{children}</main>
                    </div>
                    <AdminFooter />
                </div>
            </div>
        </div>
    );
};

const AdminLayout: React.FC<AdminLayoutProps> = ({ children }) => (
    <ToastProvider>
        <AdminLayoutContent>{children}</AdminLayoutContent>
    </ToastProvider>
);

export default AdminLayout;
