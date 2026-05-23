import React from "react";
import { Head } from "@inertiajs/react";
import LandingLayout from "../../Layouts/LandingLayout";
import HeroSection from "../../Components/Landing/HeroSection";
import PricingSection from "../../Components/Landing/PricingSection";
import CoverageSection from "../../Components/Landing/CoverageSection";
import FeaturesSection from "../../Components/Landing/FeaturesSection";
import StatsSection from "../../Components/Landing/StatsSection";
import TopologySection from "../../Components/Landing/TopologySection";
import TestimonialsSection from "../../Components/Landing/TestimonialsSection";
import FAQSection from "../../Components/Landing/FAQSection";
import CTASection from "../../Components/Landing/CTASection";
import landingData from "../../data/landingData";

const LandingIndex: React.FC = () => (
    <LandingLayout navLinks={landingData.navLinks}>
        <Head>
            <title>RJNet — Internet Cepat & Stabil untuk Rumah dan Bisnis Anda</title>
            <meta name="description" content="RJNet menyediakan layanan internet fiber optic cepat, stabil, dan terjangkau untuk rumah dan bisnis. Dukungan 24/7, instalasi gratis, tanpa biaya tersembunyi." />
            <meta name="keywords" content="internet rumah, internet bisnis, fiber optic, ISP, RT RW Net, internet murah, wifi cepat" />
            {/* Open Graph */}
            <meta property="og:title" content="RJNet — Internet Cepat & Stabil" />
            <meta property="og:description" content="Internet fiber optic super cepat dengan harga terjangkau. Instalasi gratis, support 24/7." />
            <meta property="og:type" content="website" />
            <meta property="og:url" content="https://rjnet.id" />
            <meta property="og:image" content="https://rjnet.id/images/og-image.jpg" />
            {/* Twitter Card */}
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" content="RJNet — Internet Cepat & Stabil" />
            <meta name="twitter:description" content="Internet fiber optic super cepat dengan harga terjangkau. Instalasi gratis, support 24/7." />
            {/* Canonical */}
            <link rel="canonical" href="https://rjnet.id" />
        </Head>

        <HeroSection stats={landingData.stats} />
        <PricingSection plans={landingData.pricingPlans} />
        <CoverageSection areas={landingData.coverageAreas} />
        <FeaturesSection features={landingData.features} />
        <StatsSection stats={landingData.stats} />
        <TopologySection items={landingData.topologyItems} />
        <TestimonialsSection testimonials={landingData.testimonials} />
        <FAQSection items={landingData.faqItems} />
        <CTASection />
    </LandingLayout>
);

export default LandingIndex;
