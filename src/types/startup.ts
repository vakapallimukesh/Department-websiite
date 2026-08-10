export interface FeatureItem {
  icon: string;
  color: string;
  title: string;
  desc: string;
}

export interface StatItem {
  num: string;
  label: string;
}

export interface Startup {
  id: string;
  name: string;
  category: string;
  tagline: string;
  description: string;
  about: string;
  what_we_do: string;
  primaryImage: string;   // Image 1: HERO SECTION
  secondaryImage: string; // Image 2: DETAILS SECTION
  website?: string;
  phone?: string;
  phone2?: string;
  email?: string;
  instagram?: string;
  founder?: string;
  address?: string;
  mapUrl?: string;
  workingHours?: string;
  services?: string[];
  themeColor: string;
  gradient: string;
  features?: FeatureItem[];
  stats?: StatItem[];
}
