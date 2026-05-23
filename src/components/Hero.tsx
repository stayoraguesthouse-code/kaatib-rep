'use client'

import { motion } from 'framer-motion'

export default function Hero() {
  const containerVariants = {
    hidden: { opacity: 0 },
    visible: {
      opacity: 1,
      transition: {
        staggerChildren: 0.2,
        delayChildren: 0.3,
      },
    },
  }

  const itemVariants = {
    hidden: { opacity: 0, y: 20 },
    visible: (index?: number) => ({
      opacity: 1,
      y: 0,
      transition: { duration: 0.8 },
    }),
  }

  return (
    <section className="relative min-h-screen flex items-center justify-center overflow-hidden pt-20">
      {/* Background Elements */}
      <div className="absolute inset-0 z-0">
        {/* Gradient Background */}
        <div className="absolute inset-0 bg-gradient-dark opacity-40" />

        {/* Radial Glow */}
        <motion.div
          className="absolute top-1/4 right-1/4 w-96 h-96 bg-radial-gold rounded-full blur-3xl opacity-20"
          animate={{
            scale: [1, 1.1, 1],
            opacity: [0.2, 0.3, 0.2],
          }}
          transition={{ duration: 8, repeat: Infinity }}
        />

        {/* Floating Shapes */}
        <motion.div
          className="absolute bottom-1/4 left-1/4 w-72 h-72 border border-gold-600/20 rounded-full"
          animate={{
            rotate: 360,
            scale: [1, 1.05, 1],
          }}
          transition={{ duration: 20, repeat: Infinity, ease: 'linear' }}
        />
      </div>

      {/* Content */}
      <motion.div
        className="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center"
        variants={containerVariants}
        initial="hidden"
        animate="visible"
      >
        {/* Subtitle */}
        <motion.div
          variants={itemVariants}
          className="mb-6 inline-block"
        >
          <span className="text-gold-600 font-medium text-sm tracking-widest uppercase">
            Premium Publishing Platform
          </span>
        </motion.div>

        {/* Main Headline */}
        <motion.h1
          variants={itemVariants}
          className="text-hero md:text-hero font-playfair font-bold text-ivory mb-6 leading-tight"
        >
          Your Story Deserves to Be{' '}
          <span className="text-gold-600">Printed</span>
        </motion.h1>

        {/* Subheadline */}
        <motion.p
          variants={itemVariants}
          className="text-lg md:text-xl text-ivory/80 mb-12 max-w-2xl mx-auto leading-relaxed font-light"
        >
          Professional Print on Demand and Self-Publishing Services for Authors,
          Institutions, and Creative Minds
        </motion.p>

        {/* CTA Buttons */}
        <motion.div
          variants={itemVariants}
          className="flex flex-col sm:flex-row gap-4 justify-center items-center"
        >
          <motion.a
            href="#contact"
            className="px-8 py-4 bg-gold-600 text-ink-950 rounded-luxury font-semibold hover:shadow-glow-lg transition-all duration-300"
            whileHover={{ scale: 1.02, backgroundColor: '#B67A2D' }}
            whileTap={{ scale: 0.98 }}
          >
            Start Your Project
          </motion.a>

          <motion.a
            href="#books"
            className="px-8 py-4 border-2 border-gold-600 text-gold-600 rounded-luxury font-semibold hover:bg-gold-600/10 transition-all duration-300"
            whileHover={{ scale: 1.02, borderColor: '#B67A2D', color: '#B67A2D' }}
            whileTap={{ scale: 0.98 }}
          >
            Explore Our Books
          </motion.a>
        </motion.div>

        {/* Scroll Indicator */}
        <motion.div
          className="absolute bottom-8 left-1/2 transform -translate-x-1/2"
          animate={{ y: [0, 10, 0] }}
          transition={{ duration: 2, repeat: Infinity }}
        >
          <div className="w-6 h-10 border-2 border-gold-600 rounded-full flex justify-center">
            <motion.div
              className="w-1 h-2 bg-gold-600 rounded-full mt-2"
              animate={{ y: [0, 8, 0] }}
              transition={{ duration: 2, repeat: Infinity }}
            />
          </div>
        </motion.div>
      </motion.div>
    </section>
  )
}
