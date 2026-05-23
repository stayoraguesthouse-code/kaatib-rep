'use client'

import { motion } from 'framer-motion'
import { CheckCircle2 } from 'lucide-react'

const benefits = [
  'Bespoke printing in custom sizes up to 12"x12"',
  'Hardcover, Softcover & eBook formats',
  'Amazon KDP integration',
  'Global distribution network',
  'Professional design consultation',
]

export default function About() {
  return (
    <section id="about" className="py-section px-4 sm:px-6 lg:px-8 bg-white">
      <div className="max-w-6xl mx-auto">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          {/* Text Content */}
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
          >
            <motion.span
              className="text-gold-600 font-medium text-sm tracking-widest uppercase"
              initial={{ opacity: 0 }}
              whileInView={{ opacity: 1 }}
              transition={{ delay: 0.2 }}
            >
              Why Choose Us
            </motion.span>

            <h2 className="text-h1 font-playfair font-bold text-ink-950 mt-4 mb-6">
              Why Choose Kaatib Publishers?
            </h2>

            <div className="space-y-6 mb-8">
              <p className="text-body text-text-secondary leading-relaxed">
                We believe every manuscript deserves a platform. Whether you're publishing a medical
                yearbook, a collection of Sufi poetry, or a captivating novel, Kaatib Publishers
                provides the expertise and infrastructure to bring your vision to life.
              </p>

              <p className="text-body text-text-secondary leading-relaxed">
                Unlike traditional publishing where authors receive only 6-10% royalties, our model
                puts the profit back in your hands. You keep <span className="text-gold-600 font-semibold">30% to 100%</span> of your
                royalties.
              </p>
            </div>

            {/* Benefits List */}
            <div className="space-y-4">
              {benefits.map((benefit, index) => (
                <motion.div
                  key={index}
                  className="flex items-start gap-4"
                  initial={{ opacity: 0, x: -20 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  transition={{ delay: index * 0.1 }}
                  viewport={{ once: true }}
                >
                  <CheckCircle2 className="w-6 h-6 text-gold-600 flex-shrink-0 mt-1" />
                  <span className="text-body text-text-primary">{benefit}</span>
                </motion.div>
              ))}
            </div>
          </motion.div>

          {/* Image */}
          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            transition={{ duration: 0.8 }}
            viewport={{ once: true }}
            className="relative"
          >
            <div className="relative rounded-premium overflow-hidden shadow-luxury">
              <div className="aspect-square bg-gradient-luxury flex items-center justify-center">
                <motion.div
                  className="text-center"
                  animate={{
                    y: [0, -10, 0],
                  }}
                  transition={{ duration: 4, repeat: Infinity }}
                >
                  <div className="text-6xl mb-4">📚</div>
                  <p className="text-ink-950 font-playfair text-2xl font-bold">
                    Premium Publishing
                  </p>
                </motion.div>
              </div>
            </div>

            {/* Floating accent */}
            <motion.div
              className="absolute -bottom-6 -right-6 w-32 h-32 bg-gold-600/10 rounded-premium"
              animate={{
                rotate: [0, 5, 0],
                y: [0, 10, 0],
              }}
              transition={{ duration: 6, repeat: Infinity }}
            />
          </motion.div>
        </div>
      </div>
    </section>
  )
}
