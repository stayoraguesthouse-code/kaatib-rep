'use client'

import { motion } from 'framer-motion'
import Link from 'next/link'
import { Share2 as FacebookIcon, Share2 as LinkedinIcon, Share2 as TwitterIcon } from 'lucide-react'

export default function Footer() {
  const currentYear = new Date().getFullYear()

  const footerVariants = {
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
    <footer className="bg-ink-950 text-ivory pt-section pb-8">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Main Footer Content */}
        <motion.div
          variants={footerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true }}
          className="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12"
        >
          {/* About */}
          <motion.div variants={itemVariants}>
            <h3 className="text-h4 font-playfair font-bold text-gold-600 mb-4">
              About Kaatib
            </h3>
            <p className="text-body-sm text-ivory/70 leading-relaxed">
              Kaatib Publishers LLC is a premier Print on Demand provider specializing in
              high-quality self-publishing solutions for independent authors and institutions
              across the globe.
            </p>
            {/* Social Links */}
            <div className="flex gap-4 mt-6">
              <motion.a
                href="https://www.facebook.com/kaatib.publisher/"
                target="_blank"
                rel="noopener noreferrer"
                className="w-10 h-10 rounded-luxury bg-gold-600/20 flex items-center justify-center text-gold-600 hover:bg-gold-600 hover:text-ink-950 transition-all"
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.95 }}
              >
                <FacebookIcon className="w-5 h-5" />
              </motion.a>
              <motion.a
                href="https://www.linkedin.com/company/kaatib-publishers/"
                target="_blank"
                rel="noopener noreferrer"
                className="w-10 h-10 rounded-luxury bg-gold-600/20 flex items-center justify-center text-gold-600 hover:bg-gold-600 hover:text-ink-950 transition-all"
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.95 }}
              >
                <LinkedinIcon className="w-5 h-5" />
              </motion.a>
              <motion.a
                href="https://x.com/TheKaatib/"
                target="_blank"
                rel="noopener noreferrer"
                className="w-10 h-10 rounded-luxury bg-gold-600/20 flex items-center justify-center text-gold-600 hover:bg-gold-600 hover:text-ink-950 transition-all"
                whileHover={{ scale: 1.1 }}
                whileTap={{ scale: 0.95 }}
              >
                <TwitterIcon className="w-5 h-5" />
              </motion.a>
            </div>
          </motion.div>

          {/* Quick Links */}
          <motion.div variants={itemVariants}>
            <h3 className="text-h4 font-playfair font-bold text-gold-600 mb-4">
              Quick Links
            </h3>
            <ul className="space-y-3">
              {['About Us', 'Services', 'Pricing', 'Our Books', 'Online Store'].map(
                (link, index) => (
                  <li key={index}>
                    <Link
                      href={`#${link.toLowerCase().replace(' ', '-')}`}
                      className="text-body-sm text-ivory/70 hover:text-gold-600 transition-colors"
                    >
                      {link}
                    </Link>
                  </li>
                )
              )}
            </ul>
          </motion.div>

          {/* Contact Info */}
          <motion.div variants={itemVariants}>
            <h3 className="text-h4 font-playfair font-bold text-gold-600 mb-4">
              Contact Info
            </h3>
            <div className="space-y-4 text-body-sm text-ivory/70">
              <p>Sugar Land, TX 77479, USA</p>
              <a href="mailto:info@kaatibpublishers.com" className="hover:text-gold-600 transition-colors">
                info@kaatibpublishers.com
              </a>
              <p>kaatibpublishers.com</p>
            </div>
          </motion.div>

          {/* Pakistan Imprint */}
          <motion.div variants={itemVariants}>
            <h3 className="text-h4 font-playfair font-bold text-gold-600 mb-4">
              Pakistan Imprint
            </h3>
            <p className="text-body-sm text-ivory/70 mb-4">
              Official Registered Publisher with the National Library of Pakistan.
            </p>
            <div className="p-4 bg-gold-600/10 border border-gold-600/30 rounded-luxury">
              <p className="text-body-sm font-mono text-gold-600">
                Imprint ID: 978-969-7932
              </p>
            </div>
          </motion.div>
        </motion.div>

        {/* Divider */}
        <motion.div
          className="h-px bg-gold-600/20 mb-8"
          initial={{ scaleX: 0 }}
          whileInView={{ scaleX: 1 }}
          transition={{ duration: 0.8 }}
          viewport={{ once: true }}
        />

        {/* Bottom Footer */}
        <motion.div
          className="flex flex-col md:flex-row justify-between items-center gap-4 text-body-sm text-ivory/60"
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          transition={{ delay: 0.4 }}
          viewport={{ once: true }}
        >
          <p>
            © {currentYear} Kaatib Publishers LLC. All Rights Reserved.
          </p>
          <p>
            Parent Group:{' '}
            <a
              href="https://bahalimgroup.com"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gold-600 hover:text-gold-500 transition-colors"
            >
              Bahalim Group
            </a>
          </p>
        </motion.div>
      </div>
    </footer>
  )
}
