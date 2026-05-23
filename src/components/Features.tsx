'use client'

import { motion } from 'framer-motion'
import { Clock, Package, Shield } from 'lucide-react'

const features = [
  {
    icon: Clock,
    title: 'Fast Turnaround',
    description: '3-4 weeks from final proof to delivery. Professional quality, rapid execution.',
  },
  {
    icon: Package,
    title: 'Zero Inventory',
    description: 'Print on demand means no storage costs. Sell what you print, print what you sell.',
  },
  {
    icon: Shield,
    title: 'Author Control',
    description: 'You retain full creative control and ownership. Maximum royalties, minimum hassle.',
  },
]

export default function Features() {
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.2,
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
    <section className="py-section px-4 sm:px-6 lg:px-8 bg-ivory">
      <div className="max-w-6xl mx-auto">
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: '-100px' }}
          className="grid grid-cols-1 md:grid-cols-3 gap-8"
        >
          {features.map((feature, index) => {
            const Icon = feature.icon
            return (
              <motion.div
                key={index}
                variants={itemVariants}
                className="premium-card p-8 text-center group"
                whileHover={{ y: -8 }}
              >
                <motion.div
                  className="inline-flex items-center justify-center w-16 h-16 rounded-luxury bg-gold-600/10 mb-6 group-hover:bg-gold-600/20 transition-colors"
                  whileHover={{ scale: 1.1 }}
                >
                  <Icon className="w-8 h-8 text-gold-600" />
                </motion.div>

                <h3 className="text-h4 font-playfair font-bold text-ink-950 mb-4">
                  {feature.title}
                </h3>

                <p className="text-body-sm text-text-secondary leading-relaxed">
                  {feature.description}
                </p>
              </motion.div>
            )
          })}
        </motion.div>
      </div>
    </section>
  )
}
