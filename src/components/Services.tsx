'use client'

import { motion } from 'framer-motion'
import {
  GraduationCap,
  Heart,
  Sparkles,
  Palette,
  BookOpen,
  BookMarked,
} from 'lucide-react'

const services = [
  {
    icon: GraduationCap,
    title: 'Yearbooks',
    description:
      'Professional yearbook printing for schools, colleges, and medical institutions with custom layouts and premium finishes.',
  },
  {
    icon: Heart,
    title: 'Memoirs & Biographies',
    description:
      'Preserve family history and personal stories with beautifully printed memoirs and biographical collections.',
  },
  {
    icon: Sparkles,
    title: 'Spiritual & Religious',
    description:
      'Islamic guides, religious literature, and spiritual works printed with the utmost care and respect.',
  },
  {
    icon: Palette,
    title: 'Art & Poetry',
    description:
      'Fine art portfolios and poetry collections that showcase your creative vision in stunning print.',
  },
  {
    icon: BookOpen,
    title: 'Fiction & Novels',
    description:
      'Bring your stories to life with professional printing and distribution for independent authors.',
  },
  {
    icon: BookMarked,
    title: 'Educational Materials',
    description:
      'Textbooks, workbooks, and educational resources printed to the highest academic standards.',
  },
]

export default function Services() {
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.1,
      },
    },
  }

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: {
      opacity: 1,
      y: 0,
      transition: { duration: 0.6 },
    },
  }

  return (
    <section id="services" className="py-section px-4 sm:px-6 lg:px-8 bg-ivory">
      <div className="max-w-6xl mx-auto">
        {/* Section Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          viewport={{ once: true }}
        >
          <span className="text-gold-600 font-medium text-sm tracking-widest uppercase">
            Our Expertise
          </span>
          <h2 className="text-h1 font-playfair font-bold text-ink-950 mt-4 mb-4">
            Our Specialized Services
          </h2>
          <p className="text-body text-text-secondary max-w-2xl mx-auto">
            Tailored publishing solutions for every genre and institutional need
          </p>
        </motion.div>

        {/* Services Grid */}
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: '-100px' }}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
        >
          {services.map((service, index) => {
            const Icon = service.icon
            return (
              <motion.div
                key={index}
                variants={itemVariants}
                className="premium-card p-8 group"
                whileHover={{ y: -8 }}
              >
                <motion.div
                  className="inline-flex items-center justify-center w-14 h-14 rounded-luxury bg-gold-600/10 mb-6 group-hover:bg-gold-600/20 transition-colors"
                  whileHover={{ scale: 1.1, rotate: 5 }}
                >
                  <Icon className="w-7 h-7 text-gold-600" />
                </motion.div>

                <h3 className="text-h4 font-playfair font-bold text-ink-950 mb-3">
                  {service.title}
                </h3>

                <p className="text-body-sm text-text-secondary leading-relaxed">
                  {service.description}
                </p>
              </motion.div>
            )
          })}
        </motion.div>
      </div>
    </section>
  )
}
