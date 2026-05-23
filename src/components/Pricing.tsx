'use client'

import { motion } from 'framer-motion'
import { CheckCircle2 } from 'lucide-react'

const pricingTiers = [
  {
    name: 'Starter',
    volume: '1 - 9 Books',
    price: '$98',
    priceLabel: 'Setup Fee',
    highlight: false,
    features: [
      'Professional printing',
      'Standard binding',
      'Design consultation',
      'Quality assurance',
    ],
  },
  {
    name: 'Growth',
    volume: '10 - 24 Books',
    price: '10%',
    priceLabel: 'OFF',
    highlight: false,
    features: [
      'Professional printing',
      'Multiple binding options',
      'Priority support',
      'Bulk pricing',
    ],
  },
  {
    name: 'Professional',
    volume: '25 - 49 Books',
    price: '20%',
    priceLabel: 'OFF',
    highlight: true,
    features: [
      'Premium printing',
      'Custom finishes',
      'Dedicated account manager',
      'Marketing support',
    ],
  },
  {
    name: 'Enterprise',
    volume: '50+ Books',
    price: '30-90%',
    priceLabel: 'OFF',
    highlight: false,
    features: [
      'Custom solutions',
      'Premium finishes',
      'Global distribution',
      'White-label options',
    ],
  },
]

export default function Pricing() {
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.15,
      },
    },
  }

  const itemVariants = {
    hidden: { opacity: 0, y: 30 },
    visible: {
      opacity: 1,
      y: 0,
      transition: { duration: 0.6 },
    },
  }

  return (
    <section id="pricing" className="py-section px-4 sm:px-6 lg:px-8 bg-white">
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
            Transparent Pricing
          </span>
          <h2 className="text-h1 font-playfair font-bold text-ink-950 mt-4 mb-4">
            Transparent Pricing Tiers
          </h2>
          <p className="text-body text-text-secondary max-w-2xl mx-auto">
            Scalable costs for every author. Start small and grow with us.
          </p>
        </motion.div>

        {/* Pricing Cards */}
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: '-100px' }}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8"
        >
          {pricingTiers.map((tier, index) => (
            <motion.div
              key={index}
              variants={itemVariants}
              className={`relative rounded-premium overflow-hidden transition-all duration-500 ${
                tier.highlight
                  ? 'lg:scale-105 shadow-glow-lg border-2 border-gold-600/50'
                  : 'premium-card'
              }`}
              whileHover={{ y: tier.highlight ? -12 : -8 }}
            >
              {/* Highlight Badge */}
              {tier.highlight && (
                <motion.div
                  className="absolute top-0 left-0 right-0 bg-gold-600 text-ink-950 py-2 text-center font-semibold text-sm"
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                >
                  Author Favorite
                </motion.div>
              )}

              <div className={`p-8 ${tier.highlight ? 'pt-16' : ''}`}>
                {/* Tier Name */}
                <h3 className="text-h4 font-playfair font-bold text-ink-950 mb-2">
                  {tier.name}
                </h3>

                {/* Volume */}
                <p className="text-body-sm text-text-secondary mb-6">{tier.volume}</p>

                {/* Price */}
                <div className="mb-8">
                  <div className="text-4xl font-bold text-gold-600 font-playfair">
                    {tier.price}
                  </div>
                  <p className="text-body-sm text-text-secondary mt-1">{tier.priceLabel}</p>
                </div>

                {/* Features */}
                <div className="space-y-4 mb-8">
                  {tier.features.map((feature, idx) => (
                    <motion.div
                      key={idx}
                      className="flex items-start gap-3"
                      initial={{ opacity: 0, x: -10 }}
                      whileInView={{ opacity: 1, x: 0 }}
                      transition={{ delay: idx * 0.1 }}
                    >
                      <CheckCircle2 className="w-5 h-5 text-gold-600 flex-shrink-0 mt-0.5" />
                      <span className="text-body-sm text-text-primary">{feature}</span>
                    </motion.div>
                  ))}
                </div>

                {/* CTA Button */}
                <motion.button
                  className={`w-full py-3 rounded-luxury font-semibold transition-all duration-300 ${
                    tier.highlight
                      ? 'bg-gold-600 text-ink-950 hover:bg-gold-700'
                      : 'border-2 border-ink-950 text-ink-950 hover:bg-ink-950/5'
                  }`}
                  whileHover={{ scale: 1.02 }}
                  whileTap={{ scale: 0.98 }}
                >
                  Get Started
                </motion.button>
              </div>
            </motion.div>
          ))}
        </motion.div>
      </div>
    </section>
  )
}
