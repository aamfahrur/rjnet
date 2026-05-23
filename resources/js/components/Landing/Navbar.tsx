import React, { useState, useEffect } from "react";
import { Link } from "@inertiajs/react";
import type { NavLink } from "../../Types/landing";

interface NavbarProps {
    navLinks: NavLink[];
    darkMode: boolean;
    setDarkMode: (v: boolean) => void;
}

const Navbar: React.FC<NavbarProps> = ({ navLinks, darkMode, setDarkMode }) => {
    const [scrolled, setScrolled] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 20);
        window.addEventListener("scroll", onScroll);
        return () => window.removeEventListener("scroll", onScroll);
    }, []);

    return (
        <nav
            className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${scrolled
                    ? "bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-100 dark:border-slate-800 shadow-sm"
                    : "bg-transparent"
                }`}
        >
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex items-center justify-between h-16 md:h-20">
                    {/* Logo */}
                    <a href="#home" className="flex items-center gap-2.5 group">
                        <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:scale-110 transition-transform">
                            <span className="text-white font-bold text-base">R</span>
                        </div>
                        <span className="font-bold text-xl text-slate-800 dark:text-white tracking-tight">
                            RJ<span className="text-cyan-500">Net</span>
                        </span>
                    </a>

                    {/* Desktop nav */}
                    <div className="hidden lg:flex items-center gap-1">
                        {navLinks.map((link: NavLink, i: number) =>
                            link.isButton ? (
                                <Link
                                    key={`${link.href}-${i}`}
                                    href={link.href}
                                    className="ml-3 px-4 py-2.5 bg-gradient-to-r from-cyan-400 to-blue-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:shadow-blue-500/25 transition-all"
                                >
                                    {link.label}
                                </Link>
                            ) : (
                                <a
                                    key={`${link.href}-${i}`}
                                    href={link.href}
                                    className="px-3.5 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-cyan-500 dark:hover:text-cyan-400 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                                >
                                    {link.label}
                                </a>
                            ),
                        )}
                        {/* Dark mode toggle */}
                        <button
                            onClick={() => setDarkMode(!darkMode)}
                            className="ml-2 p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            aria-label="Toggle dark mode"
                        >
                            {darkMode ? <i className="material-symbols-outlined">light_mode</i> : <i className="material-symbols-outlined">dark_mode</i>}
                        </button>
                    </div>

                    {/* Mobile toggle */}
                    <div className="flex lg:hidden items-center gap-2">
                        <button
                            onClick={() => setDarkMode(!darkMode)}
                            className="p-2.5 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            aria-label="Toggle dark mode"
                        >
                            {darkMode ? <i className="material-symbols-outlined">light_mode</i> : <i className="material-symbols-outlined">dark_mode</i>}
                        </button>
                        <button
                            onClick={() => setMobileOpen(!mobileOpen)}
                            className="p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            aria-label="Toggle menu"
                        >
                            {mobileOpen ? <i className="material-symbols-outlined">close</i> : <i className="material-symbols-outlined">menu</i>}
                        </button>
                    </div>
                </div>
            </div>

            {/* Mobile menu */}
            <div
                className={`lg:hidden transition-all duration-300 overflow-hidden ${mobileOpen ? "max-h-[500px]" : "max-h-0"
                    }`}
            >
                <div className="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 px-4 py-4 space-y-1">
                    {navLinks.map((link: NavLink, i: number) =>
                        link.isButton ? (
                            <Link
                                key={`m-${link.href}-${i}`}
                                href={link.href}
                                onClick={() => setMobileOpen(false)}
                                className="block w-full text-center mt-3 px-4 py-3 bg-gradient-to-r from-cyan-400 to-blue-600 text-white rounded-xl text-sm font-semibold"
                            >
                                {link.label}
                            </Link>
                        ) : (
                            <a
                                key={link.href}
                                href={link.href}
                                onClick={() => setMobileOpen(false)}
                                className="block px-3 py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-cyan-500 rounded-lg"
                            >
                                {link.label}
                            </a>
                        ),
                    )}
                </div>
            </div>
        </nav>
    );
};

export default Navbar;
