// =============================================================================
// Landing Page TypeScript Types — RJNet ISP Modern Landing
// =============================================================================

export interface PricingPlan {
  id: string;
  name: string;
  speed: number; // Mbps
  speedLabel: string;
  price: number; // monthly
  discountedPrice?: number;
  features: string[];
  isPopular?: boolean;
  color: string; // gradient tailwind class
  badge?: string;
}

export interface CoverageArea {
  id: string;
  name: string;
  type: 'kecamatan' | 'kelurahan' | 'kota';
  parent: string;
  status: 'available' | 'soon' | 'planned';
  color: string;
}

export interface Feature {
  id: string;
  icon: string; // lucide icon name
  title: string;
  description: string;
}

export interface Stat {
  id: string;
  value: number;
  suffix?: string;
  prefix?: string;
  label: string;
  icon: string;
}

export interface Testimonial {
  id: string;
  name: string;
  role: string;
  avatar: string;
  rating: number;
  comment: string;
  packageName: string;
}

export interface FAQItem {
  id: string;
  question: string;
  answer: string;
}

export interface TopologyItem {
  id: string;
  icon: string;
  title: string;
  description: string;
}

export interface NavLink {
  label: string;
  href: string;
  isButton?: boolean;
}

export interface LandingData {
  pricingPlans: PricingPlan[];
  coverageAreas: CoverageArea[];
  features: Feature[];
  stats: Stat[];
  testimonials: Testimonial[];
  faqItems: FAQItem[];
  topologyItems: TopologyItem[];
  navLinks: NavLink[];
}
