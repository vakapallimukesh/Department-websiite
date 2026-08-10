import React from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { STARTUPS_DATA } from '../data/startups';

export const StartupDetails: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const startupKey = id?.toLowerCase() || 'bhimavaram-online';
  const startup = STARTUPS_DATA[startupKey] || STARTUPS_DATA['bhimavaram-online'];
  const isBhimavaramDigitals = startupKey === 'bhimavaram-digitals' || startupKey === 'bhimavaram-digital';
  const isBhimavaramOnline = startupKey === 'bhimavaram-online' || startupKey === 'bhimavaramonline';
  const isLunchBox = startupKey === 'lunch-box' || startupKey === 'lunchbox';

  const [galleryFilter, setGalleryFilter] = React.useState('all');
  const [lightboxIndex, setLightboxIndex] = React.useState<number | null>(null);

  const galleryPhotos = [
    {
      src: 'public/startups/nutridelight/gallery/gallery1.jpg',
      title: 'NutriDelight Natural Milk & Juices',
      desc: 'Signature fresh milk and healthy juice bottles displayed at cold storage booth.',
      category: ['products', 'all'],
      type: 'featured'
    },
    {
      src: 'public/startups/nutridelight/gallery/gallery2.jpg',
      title: 'Ribbon Cutting Inauguration Ceremony',
      desc: 'Official store opening ceremony with founders, dignitaries and guests.',
      category: ['launch', 'all'],
      type: 'medium'
    },
    {
      src: 'public/startups/nutridelight/gallery/gallery3.jpg',
      title: 'NutriDelight Store & Product Display',
      desc: 'Clean, modern booth layout featuring healthy dry fruits, spices, and cold juices.',
      category: ['store', 'products', 'all'],
      type: 'large'
    },
    {
      src: 'public/startups/nutridelight/gallery/gallery4.jpg',
      title: 'Founding Team & Mentors',
      desc: 'SRKR Engineering College faculty and startup founders during launch event.',
      category: ['team', 'all'],
      type: 'medium'
    },
    {
      src: 'public/startups/nutridelight/gallery/gallery5.jpg',
      title: 'Outdoor Campus Community Stall',
      desc: 'Serving healthy beverages and snacks directly to students and visitors.',
      category: ['journey', 'all'],
      type: 'large'
    }
  ];

  const swGalleryPhotos = [
    {
      src: 'public/startups/smartwash/gallery/gallery1.jpg',
      title: 'BO Smart Wash Storefront Inauguration',
      desc: 'Founders, faculty, and dignitaries gathered outside the decorated Smart Wash outlet during grand opening.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery2.jpg',
      title: 'Inauguration Bouquet Presentation',
      desc: 'Felicitation ceremony presenting flower bouquets to dignitaries inside Smart Wash.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery3.jpg',
      title: 'Smart Wash Fabric Care & Products Setup',
      desc: 'Hygienic detergent supplies, garment care products, and professional laundry equipment.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery4.jpg',
      title: 'BO Smart Wash Student Team & Founders',
      desc: 'Large group photo of student team members and founders in front of the main BO Smart Wash store.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery5.jpg',
      title: 'Campus Interactive Team Session',
      desc: 'Outdoor team meeting discussing operations and student fabric care services.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery6.jpg',
      title: 'Tumble Drier Control Unit Inauguration',
      desc: 'Dignitary commissioning the IFB RTD 30 heavy-duty tumble drier machine.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery7.jpg',
      title: 'Industrial Washer Ribbon-Cutting',
      desc: 'Official ribbon cutting for the high-capacity commercial washing machine unit.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery8.jpg',
      title: 'Management & Dignitaries Walkthrough',
      desc: 'SRKR dignitaries and guests discussing operations inside the Smart Wash facility.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery9.jpg',
      title: 'Flatwork Ironer & Finishing Plant Inspection',
      desc: 'Inspection of commercial steam ironer and heavy-duty garment finishing line.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery10.jpg',
      title: 'Grand Inaugural Group Photo',
      desc: 'Full inaugural group photo with SRKR management, founders, and entire Smart Wash team.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery11.jpg',
      title: 'Smart Wash Doorstep Laundry Flyers',
      desc: 'Official promotional pamphlets and service menu cards for student laundry and dry clean services.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery12.jpg',
      title: 'Official Ribbon Cutting Ceremony',
      desc: 'Chief guest cutting the ribbon during the grand opening of Smart Wash store outlet.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery13.jpg',
      title: 'BO Smart Wash Official Poster Launch',
      desc: 'Dignitaries and student team holding the official BO Smart Wash branding poster.'
    },
    {
      src: 'public/startups/smartwash/gallery/gallery14.jpg',
      title: 'Smart Wash Team & Faculty Photo',
      desc: 'Founders and faculty members gathered near the detergent and fabric care counter.'
    }
  ];

  return (
    <div className="startup-details-page bg-slate-50 min-h-screen text-slate-800">
      {/* NAVBAR */}
      <header className="bg-slate-900 border-b border-slate-800 py-4 px-6 sticky top-0 z-50">
        <div className="max-w-7xl mx-auto flex justify-between items-center">
          <Link to="/" className="text-white font-bold text-xl flex items-center gap-2">
            <span className="text-amber-400">SRKREC</span> CSD & CSIT Department
          </Link>
          <button
            onClick={() => navigate('/startup-club')}
            className="text-slate-300 hover:text-white text-sm font-medium flex items-center gap-2"
          >
            ← Back to Startups
          </button>
        </div>
      </header>

      {/* SECTION 1 — FULL-WIDTH HERO SECTION */}
      <section className="relative w-full min-h-[80vh] flex items-end justify-start py-16 px-6 bg-white overflow-hidden">
        {/* Full-width Hero Background Image */}
        <div className="absolute inset-0 w-full h-full z-10 flex items-center justify-center bg-white">
          <motion.img
            initial={{ opacity: 0, scale: 0.96 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8 }}
            src={startup.primaryImage}
            alt={`${startup.name} Full Hero Visual`}
            className="w-full h-full object-contain object-center drop-shadow-md hover:scale-[1.02] transition-transform duration-700"
          />
          {/* Subtle Gradient Overlay */}
          <div className="absolute inset-0 z-20 bg-gradient-to-b from-slate-900/10 via-slate-900/35 to-slate-900/85 pointer-events-none" />
        </div>

        {/* Content Overlay */}
        <div className="relative z-30 max-w-7xl mx-auto w-full">
          <motion.div
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.2 }}
            className="max-w-3xl"
          >
            <span className="inline-block bg-blue-600 text-white text-xs font-extrabold uppercase px-4 py-1.5 rounded-full tracking-wider shadow-md mb-3">
              {(startup as any).eyebrow || startup.category}
            </span>
            <h1 className="text-5xl md:text-7xl font-black text-white mb-3 tracking-tight drop-shadow-lg">
              {startup.name}
            </h1>
            <p className="text-xl md:text-2xl font-bold text-sky-300 mb-8 drop-shadow-md">
              {startup.tagline}
            </p>

            <div>
              <button
                onClick={() => navigate('/startup-club')}
                className="px-8 py-3.5 rounded-full bg-white text-slate-900 font-extrabold hover:bg-blue-600 hover:text-white transition-all shadow-xl hover:scale-105 flex items-center gap-2"
              >
                ← Back to Startups
              </button>
            </div>
          </motion.div>
        </div>
      </section>

      {/* LOWER CONTENT WRAPPER */}
      <div className="py-20 px-6 max-w-7xl mx-auto space-y-16">
        {/* SECTION 2 — ABOUT US & DETAIL IMAGE (2 COLUMNS ON DESKTOP) */}
        <motion.section
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-stretch"
        >
          {/* LEFT COLUMN: About Us Text */}
          <div className="lg:col-span-7 bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-center">
            <h2 className="text-3xl font-extrabold text-slate-900 mb-6">
              {isBhimavaramOnline ? 'About Bhimavaram Online' : isLunchBox ? 'About Lunch Box' : 'About Us'}
            </h2>
            {startup.about && (
              <div className="text-slate-600 text-base leading-relaxed space-y-4">
                {startup.about.split('\n\n').map((paragraph, index) => (
                  <p key={index} className={index === 2 ? 'font-bold text-slate-900 text-lg' : ''}>
                    {paragraph}
                  </p>
                ))}
              </div>
            )}
          </div>

          {/* RIGHT COLUMN: Supporting Detail Image (detail3.png) */}
          <div className="lg:col-span-5 bg-white p-6 rounded-3xl border-2 border-slate-200 shadow-md flex flex-col items-center justify-center">
            <img
              src={startup.secondaryImage}
              alt={`${startup.name} Details Poster Image`}
              className="w-full max-h-[480px] object-contain rounded-2xl"
            />
            <p className="mt-4 text-xs font-extrabold tracking-wider uppercase text-blue-600">
              Official {startup.name} Details Poster
            </p>
          </div>
        </motion.section>

        {/* SECTION 3 — DETAILS GRID */}
        {(startup.founder || startup.phone || startup.email || startup.instagram || startup.address || startup.services || startup.website || isBhimavaramOnline) && (
          <motion.section
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.1 }}
            className="space-y-6 pt-4"
          >
            <h2 className="text-3xl font-extrabold text-slate-900">
              {isBhimavaramDigitals ? 'Contact & Location Details' : 'Details'}
            </h2>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {/* Category */}
              <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-5 hover:-translate-y-1 hover:border-blue-500 hover:shadow-md transition-all">
                <div className="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                  <i className="fas fa-tags text-xl"></i>
                </div>
                <div>
                  <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                    Category
                  </span>
                  <span className="font-extrabold text-slate-900 text-lg">{startup.category}</span>
                </div>
              </div>

              {/* Founder if available */}
              {startup.founder && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-5 hover:-translate-y-1 hover:border-blue-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i className="fas fa-user text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      Founder
                    </span>
                    <span className="font-extrabold text-slate-900 text-lg">{startup.founder}</span>
                  </div>
                </div>
              )}

              {/* Services offered */}
              {startup.services && !(startup as any).keyAreas && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-start gap-5 hover:-translate-y-1 hover:border-blue-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-1">
                    <i className="fas fa-shopping-bag text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      Services
                    </span>
                    <div className="flex flex-wrap gap-2 mt-1">
                      {startup.services.map((svc, i) => (
                        <span key={i} className="px-3 py-1 bg-blue-50 text-blue-700 rounded-full font-bold text-xs border border-blue-200">
                          {svc}
                        </span>
                      ))}
                    </div>
                  </div>
                </div>
              )}

              {/* Key Areas */}
              {(startup as any).keyAreas && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-start gap-5 hover:-translate-y-1 hover:border-red-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center shrink-0 mt-1">
                    <i className="fas fa-layer-group text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      Key Areas
                    </span>
                    <div className="flex flex-wrap gap-2 mt-1">
                      {(startup as any).keyAreas.map((ka: string, i: number) => (
                        <span key={i} className="px-3 py-1 bg-red-50 text-red-700 rounded-full font-bold text-xs border border-red-200">
                          • {ka}
                        </span>
                      ))}
                    </div>
                  </div>
                </div>
              )}

              {/* Android App / Play Store URL for Bhimavaram Online */}
              {(startup as any).appUrl && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-5 hover:-translate-y-1 hover:border-emerald-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i className="fab fa-google-play text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      Android Mobile App
                    </span>
                    <a
                      href={(startup as any).appUrl}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="font-extrabold text-emerald-700 hover:text-emerald-800 transition-colors text-base flex items-center gap-1.5"
                    >
                      Get Bhimavaram Online App on Play Store
                      <i className="fas fa-external-link-alt text-xs"></i>
                    </a>
                    <span className="inline-block mt-1 px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full font-bold text-xs border border-emerald-300">
                      ONDC-Enabled Hyperlocal App
                    </span>
                  </div>
                </div>
              )}

              {/* Address / Location */}
              {startup.address && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-start gap-5 hover:-translate-y-1 hover:border-red-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center shrink-0 mt-1">
                    <i className="fas fa-map-marker-alt text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      Location
                    </span>
                    <a
                      href={startup.mapUrl || `https://maps.google.com/?q=${encodeURIComponent(startup.address)}`}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="font-extrabold text-slate-900 hover:text-red-600 transition-colors text-base whitespace-pre-line leading-relaxed flex items-center gap-1.5"
                    >
                      {startup.address}
                      <i className="fas fa-external-link-alt text-xs text-red-500 shrink-0"></i>
                    </a>
                  </div>
                </div>
              )}

              {/* Phone */}
              {startup.phone && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-5 hover:-translate-y-1 hover:border-emerald-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i className="fas fa-phone-alt text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      Phone
                    </span>
                    <span className="font-extrabold text-slate-900 text-lg">
                      <a href={`tel:${startup.phone.replace(/[^0-9+]/g, '')}`} className="hover:text-blue-600 transition-colors">
                        {startup.phone}
                      </a>
                      {startup.phone2 && (
                        <>
                          {' / '}
                          <a href={`tel:${startup.phone2.replace(/[^0-9+]/g, '')}`} className="hover:text-blue-600 transition-colors">
                            {startup.phone2}
                          </a>
                        </>
                      )}
                    </span>
                  </div>
                </div>
              )}

              {/* Website */}
              {startup.website && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-5 hover:-translate-y-1 hover:border-sky-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                    <i className="fas fa-globe text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      Website
                    </span>
                    <a
                      href={startup.website.startsWith('http') ? startup.website : `https://${startup.website}`}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="font-extrabold text-blue-600 hover:underline text-lg flex items-center gap-1.5"
                    >
                      {startup.website}
                      <i className="fas fa-external-link-alt text-xs"></i>
                    </a>
                  </div>
                </div>
              )}

              {/* Email */}
              {startup.email && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-5 hover:-translate-y-1 hover:border-sky-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                    <i className="fas fa-envelope text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      Email
                    </span>
                    <a href={`mailto:${startup.email}`} className="font-extrabold text-blue-600 hover:underline text-lg">
                      {startup.email}
                    </a>
                  </div>
                </div>
              )}

              {/* Instagram */}
              {startup.instagram && (
                <div className="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center gap-5 hover:-translate-y-1 hover:border-pink-500 hover:shadow-md transition-all">
                  <div className="w-14 h-14 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center shrink-0">
                    <i className="fab fa-instagram text-xl"></i>
                  </div>
                  <div>
                    <span className="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">
                      {isBhimavaramDigitals ? 'Official Website / Instagram' : 'Instagram'}
                    </span>
                    <a
                      href={startup.instagram}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="font-extrabold text-pink-600 hover:underline text-lg flex items-center gap-1.5"
                    >
                      {startup.instagram.includes('bo_lunch_box')
                        ? 'Visit Lunch Box on Instagram →'
                        : startup.instagram.includes('bhimavaram_online')
                        ? 'Visit Bhimavaram Online on Instagram →'
                        : startup.instagram.includes('bhimavaramdigitals')
                        ? 'Visit Bhimavaram Digitals on Instagram →'
                        : startup.instagram.includes('bo_smartwash')
                        ? '@bo_smartwash'
                        : '@nutri__delight'}
                    </a>
                  </div>
                </div>
              )}
            </div>
          </motion.section>
        )}

        {/* SECTION 4 — NUTRIDELIGHT 3-ROW CONTINUOUS SCROLLING GALLERY */}
        {startupKey === 'nutridelight' && (
          <motion.section
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="pt-10 pb-6 border-t border-slate-200"
          >
            <div className="text-center max-w-3xl mx-auto mb-10">
              <span className="inline-block bg-emerald-100 text-emerald-800 text-xs font-black uppercase px-4 py-1.5 rounded-full tracking-widest shadow-sm mb-3">
                <i className="fas fa-leaf me-1"></i> NUTRIDELIGHT
              </span>
              <h2 className="text-4xl md:text-5xl font-black text-slate-900 mb-2 tracking-tight">
                NutriDelight Gallery
              </h2>
              <h4 className="text-xl font-bold text-emerald-600 italic mb-4">
                "Making Bhimavaram Healthy"
              </h4>
              <p className="text-slate-600 text-base leading-relaxed">
                Explore the moments, people, products and milestones that shaped the NutriDelight journey. A story of nutrition, innovation and community.
              </p>
            </div>

            {/* 3-ROW CONTINUOUS MARQUEE */}
            <div className="space-y-6 overflow-hidden py-4">
              {/* ROW 1 — SLIDES LEFT */}
              <div className="flex gap-5 overflow-hidden group">
                <motion.div
                  animate={{ x: ['0%', '-50%'] }}
                  transition={{ duration: 35, repeat: Infinity, ease: 'linear' }}
                  className="flex gap-5 shrink-0 group-hover:[animation-play-state:paused]"
                >
                  {[...galleryPhotos, ...galleryPhotos].map((photo, idx) => (
                    <div
                      key={`row1-${idx}`}
                      onClick={() => setLightboxIndex(idx % galleryPhotos.length)}
                      className="w-72 h-48 shrink-0 rounded-2xl overflow-hidden border-4 border-white shadow-md hover:shadow-xl hover:scale-105 hover:border-emerald-600 cursor-pointer relative group/card transition-all duration-300"
                    >
                      <img
                        src={photo.src}
                        alt={photo.title}
                        className="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-transparent p-4 flex flex-col justify-between opacity-0 group-hover/card:opacity-100 transition-opacity">
                        <span className="self-end bg-white text-emerald-700 font-black text-[10px] px-2.5 py-0.5 rounded-full">
                          View ↗
                        </span>
                        <div>
                          <h6 className="text-white font-bold text-sm leading-tight">
                            {photo.title}
                          </h6>
                          <p className="text-slate-300 text-[11px]">
                            {photo.desc}
                          </p>
                        </div>
                      </div>
                    </div>
                  ))}
                </motion.div>
              </div>

              {/* ROW 2 — SLIDES RIGHT */}
              <div className="flex gap-5 overflow-hidden group">
                <motion.div
                  animate={{ x: ['-50%', '0%'] }}
                  transition={{ duration: 40, repeat: Infinity, ease: 'linear' }}
                  className="flex gap-5 shrink-0 group-hover:[animation-play-state:paused]"
                >
                  {[...galleryPhotos.slice().reverse(), ...galleryPhotos.slice().reverse()].map((photo, idx) => (
                    <div
                      key={`row2-${idx}`}
                      onClick={() => setLightboxIndex(idx % galleryPhotos.length)}
                      className="w-72 h-48 shrink-0 rounded-2xl overflow-hidden border-4 border-white shadow-md hover:shadow-xl hover:scale-105 hover:border-emerald-600 cursor-pointer relative group/card transition-all duration-300"
                    >
                      <img
                        src={photo.src}
                        alt={photo.title}
                        className="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-transparent p-4 flex flex-col justify-between opacity-0 group-hover/card:opacity-100 transition-opacity">
                        <span className="self-end bg-white text-emerald-700 font-black text-[10px] px-2.5 py-0.5 rounded-full">
                          View ↗
                        </span>
                        <div>
                          <h6 className="text-white font-bold text-sm leading-tight">
                            {photo.title}
                          </h6>
                          <p className="text-slate-300 text-[11px]">
                            {photo.desc}
                          </p>
                        </div>
                      </div>
                    </div>
                  ))}
                </motion.div>
              </div>

              {/* ROW 3 — SLIDES LEFT FAST */}
              <div className="flex gap-5 overflow-hidden group">
                <motion.div
                  animate={{ x: ['0%', '-50%'] }}
                  transition={{ duration: 28, repeat: Infinity, ease: 'linear' }}
                  className="flex gap-5 shrink-0 group-hover:[animation-play-state:paused]"
                >
                  {[...galleryPhotos, ...galleryPhotos].map((photo, idx) => (
                    <div
                      key={`row3-${idx}`}
                      onClick={() => setLightboxIndex(idx % galleryPhotos.length)}
                      className="w-72 h-48 shrink-0 rounded-2xl overflow-hidden border-4 border-white shadow-md hover:shadow-xl hover:scale-105 hover:border-emerald-600 cursor-pointer relative group/card transition-all duration-300"
                    >
                      <img
                        src={photo.src}
                        alt={photo.title}
                        className="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-transparent p-4 flex flex-col justify-between opacity-0 group-hover/card:opacity-100 transition-opacity">
                        <span className="self-end bg-white text-emerald-700 font-black text-[10px] px-2.5 py-0.5 rounded-full">
                          View ↗
                        </span>
                        <div>
                          <h6 className="text-white font-bold text-sm leading-tight">
                            {photo.title}
                          </h6>
                          <p className="text-slate-300 text-[11px]">
                            {photo.desc}
                          </p>
                        </div>
                      </div>
                    </div>
                  ))}
                </motion.div>
              </div>
            </div>
          </motion.section>
        )}

        {/* SECTION 4 — SMART WASH 3-ROW CONTINUOUS SCROLLING GALLERY */}
        {(startupKey === 'smart-wash' || startupKey === 'smartwash') && (
          <motion.section
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="pt-10 pb-6 border-t border-slate-200"
          >
            <div className="text-center max-w-3xl mx-auto mb-10">
              <span className="inline-block bg-blue-100 text-blue-800 text-xs font-black uppercase px-4 py-1.5 rounded-full tracking-widest shadow-sm mb-3">
                <i className="fas fa-tshirt me-1"></i> BO SMART WASH
              </span>
              <h2 className="text-4xl md:text-5xl font-black text-slate-900 mb-2 tracking-tight">
                Smart Wash Gallery
              </h2>
              <h4 className="text-xl font-bold text-blue-600 italic mb-4">
                "For Smart People — Laundry & Fabric Care"
              </h4>
              <p className="text-slate-600 text-base leading-relaxed">
                Explore the inauguration moments, founding team, state-of-the-art fabric care setup, and campus community milestones of BO Smart Wash.
              </p>
            </div>

            {/* 3-ROW CONTINUOUS MARQUEE */}
            <div className="space-y-6 overflow-hidden py-4">
              {/* ROW 1 — SLIDES LEFT */}
              <div className="flex gap-5 overflow-hidden group">
                <motion.div
                  animate={{ x: ['0%', '-50%'] }}
                  transition={{ duration: 35, repeat: Infinity, ease: 'linear' }}
                  className="flex gap-5 shrink-0 group-hover:[animation-play-state:paused]"
                >
                  {[...swGalleryPhotos, ...swGalleryPhotos].map((photo, idx) => (
                    <div
                      key={`sw-row1-${idx}`}
                      onClick={() => setLightboxIndex(idx % swGalleryPhotos.length)}
                      className="w-72 h-48 shrink-0 rounded-2xl overflow-hidden border-4 border-white shadow-md hover:shadow-xl hover:scale-105 hover:border-blue-600 cursor-pointer relative group/card transition-all duration-300"
                    >
                      <img
                        src={photo.src}
                        alt={photo.title}
                        className="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-transparent p-4 flex flex-col justify-between opacity-0 group-hover/card:opacity-100 transition-opacity">
                        <span className="self-end bg-white text-blue-700 font-black text-[10px] px-2.5 py-0.5 rounded-full">
                          View ↗
                        </span>
                        <div>
                          <h6 className="text-white font-bold text-sm leading-tight">
                            {photo.title}
                          </h6>
                          <p className="text-slate-300 text-[11px]">
                            {photo.desc}
                          </p>
                        </div>
                      </div>
                    </div>
                  ))}
                </motion.div>
              </div>

              {/* ROW 2 — SLIDES RIGHT */}
              <div className="flex gap-5 overflow-hidden group">
                <motion.div
                  animate={{ x: ['-50%', '0%'] }}
                  transition={{ duration: 40, repeat: Infinity, ease: 'linear' }}
                  className="flex gap-5 shrink-0 group-hover:[animation-play-state:paused]"
                >
                  {[...swGalleryPhotos.slice().reverse(), ...swGalleryPhotos.slice().reverse()].map((photo, idx) => (
                    <div
                      key={`sw-row2-${idx}`}
                      onClick={() => setLightboxIndex(idx % swGalleryPhotos.length)}
                      className="w-72 h-48 shrink-0 rounded-2xl overflow-hidden border-4 border-white shadow-md hover:shadow-xl hover:scale-105 hover:border-blue-600 cursor-pointer relative group/card transition-all duration-300"
                    >
                      <img
                        src={photo.src}
                        alt={photo.title}
                        className="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-transparent p-4 flex flex-col justify-between opacity-0 group-hover/card:opacity-100 transition-opacity">
                        <span className="self-end bg-white text-blue-700 font-black text-[10px] px-2.5 py-0.5 rounded-full">
                          View ↗
                        </span>
                        <div>
                          <h6 className="text-white font-bold text-sm leading-tight">
                            {photo.title}
                          </h6>
                          <p className="text-slate-300 text-[11px]">
                            {photo.desc}
                          </p>
                        </div>
                      </div>
                    </div>
                  ))}
                </motion.div>
              </div>

              {/* ROW 3 — SLIDES LEFT FAST */}
              <div className="flex gap-5 overflow-hidden group">
                <motion.div
                  animate={{ x: ['0%', '-50%'] }}
                  transition={{ duration: 28, repeat: Infinity, ease: 'linear' }}
                  className="flex gap-5 shrink-0 group-hover:[animation-play-state:paused]"
                >
                  {[...swGalleryPhotos, ...swGalleryPhotos].map((photo, idx) => (
                    <div
                      key={`sw-row3-${idx}`}
                      onClick={() => setLightboxIndex(idx % swGalleryPhotos.length)}
                      className="w-72 h-48 shrink-0 rounded-2xl overflow-hidden border-4 border-white shadow-md hover:shadow-xl hover:scale-105 hover:border-blue-600 cursor-pointer relative group/card transition-all duration-300"
                    >
                      <img
                        src={photo.src}
                        alt={photo.title}
                        className="w-full h-full object-cover group-hover/card:scale-110 transition-transform duration-500"
                      />
                      <div className="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-transparent to-transparent p-4 flex flex-col justify-between opacity-0 group-hover/card:opacity-100 transition-opacity">
                        <span className="self-end bg-white text-blue-700 font-black text-[10px] px-2.5 py-0.5 rounded-full">
                          View ↗
                        </span>
                        <div>
                          <h6 className="text-white font-bold text-sm leading-tight">
                            {photo.title}
                          </h6>
                          <p className="text-slate-300 text-[11px]">
                            {photo.desc}
                          </p>
                        </div>
                      </div>
                    </div>
                  ))}
                </motion.div>
              </div>
            </div>
          </motion.section>
        )}

        {/* LIGHTBOX MODAL */}
        {lightboxIndex !== null && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md">
            <div
              className="absolute inset-0"
              onClick={() => setLightboxIndex(null)}
            />
            <div className="relative z-10 max-w-4xl w-full bg-slate-900 border border-slate-800 rounded-3xl p-4 shadow-2xl">
              <button
                onClick={() => setLightboxIndex(null)}
                className="absolute -top-12 right-0 text-white hover:text-emerald-400 text-2xl font-bold bg-white/10 w-10 h-10 rounded-full flex items-center justify-center backdrop-blur-sm"
              >
                ✕
              </button>
              <div className="max-h-[70vh] flex items-center justify-center overflow-hidden rounded-2xl bg-black">
                <img
                  src={galleryPhotos[lightboxIndex].src}
                  alt={galleryPhotos[lightboxIndex].title}
                  className="max-h-[70vh] w-auto object-contain rounded-xl"
                />
              </div>
              <div className="mt-4 flex items-center justify-between px-2">
                <div>
                  <h3 className="text-white font-bold text-lg">
                    {galleryPhotos[lightboxIndex].title}
                  </h3>
                  <p className="text-slate-400 text-sm">
                    {galleryPhotos[lightboxIndex].desc}
                  </p>
                </div>
                <div className="flex items-center gap-3">
                  <button
                    onClick={() =>
                      setLightboxIndex(
                        (lightboxIndex - 1 + galleryPhotos.length) %
                          galleryPhotos.length
                      )
                    }
                    className="p-2 rounded-full bg-slate-800 text-white hover:bg-emerald-600 transition-colors"
                  >
                    ←
                  </button>
                  <span className="text-emerald-400 font-bold text-sm">
                    {lightboxIndex + 1} / {galleryPhotos.length}
                  </span>
                  <button
                    onClick={() =>
                      setLightboxIndex(
                        (lightboxIndex + 1) % galleryPhotos.length
                      )
                    }
                    className="p-2 rounded-full bg-slate-800 text-white hover:bg-emerald-600 transition-colors"
                  >
                    →
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* BOTTOM BACK BUTTON */}
        <div>
          <button
            onClick={() => navigate('/startup-club')}
            className="px-6 py-2.5 rounded-full border border-slate-300 text-slate-700 font-semibold hover:bg-slate-100 transition-colors"
          >
            ← Back to Startups
          </button>
        </div>
      </div>
    </div>
  );
};

export default StartupDetails;
