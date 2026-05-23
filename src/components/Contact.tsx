'use client'

import { useState } from 'react'
import { motion } from 'framer-motion'

export default function Contact() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    project: '',
  })

  const [submitted, setSubmitted] = useState(false)

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target
    setFormData((prev) => ({ ...prev, [name]: value }))
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    // Handle form submission here
    console.log('Form submitted:', formData)
    setSubmitted(true)
    setTimeout(() => {
      setFormData({ name: '', email: '', phone: '', project: '' })
      setSubmitted(false)
    }, 3000)
  }

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
    <section id="contact" className="py-section px-4 sm:px-6 lg:px-8 bg-white">
      <div className="max-w-3xl mx-auto">
        {/* Section Header */}
        <motion.div
          className="text-center mb-16"
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6 }}
          viewport={{ once: true }}
        >
          <span className="text-gold-600 font-medium text-sm tracking-widest uppercase">
            Get in Touch
          </span>
          <h2 className="text-h1 font-playfair font-bold text-ink-950 mt-4 mb-4">
            Ready to Publish?
          </h2>
          <p className="text-body text-text-secondary">
            Get in touch with our team to discuss your publishing project
          </p>
        </motion.div>

        {/* Contact Form */}
        <motion.form
          onSubmit={handleSubmit}
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true }}
          className="space-y-6"
        >
          {/* Name Field */}
          <motion.div variants={itemVariants}>
            <label className="block text-body font-medium text-ink-950 mb-3">
              Your Name
            </label>
            <input
              type="text"
              name="name"
              value={formData.name}
              onChange={handleChange}
              required
              className="w-full px-6 py-4 rounded-luxury border-2 border-ink-950/10 focus:border-gold-600 focus:outline-none bg-ivory/50 transition-all duration-300 text-ink-950 placeholder-text-secondary"
              placeholder="Enter your full name"
            />
          </motion.div>

          {/* Email Field */}
          <motion.div variants={itemVariants}>
            <label className="block text-body font-medium text-ink-950 mb-3">
              Email Address
            </label>
            <input
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
              className="w-full px-6 py-4 rounded-luxury border-2 border-ink-950/10 focus:border-gold-600 focus:outline-none bg-ivory/50 transition-all duration-300 text-ink-950 placeholder-text-secondary"
              placeholder="your@email.com"
            />
          </motion.div>

          {/* Phone Field */}
          <motion.div variants={itemVariants}>
            <label className="block text-body font-medium text-ink-950 mb-3">
              Phone Number
            </label>
            <input
              type="tel"
              name="phone"
              value={formData.phone}
              onChange={handleChange}
              className="w-full px-6 py-4 rounded-luxury border-2 border-ink-950/10 focus:border-gold-600 focus:outline-none bg-ivory/50 transition-all duration-300 text-ink-950 placeholder-text-secondary"
              placeholder="Your phone number"
            />
          </motion.div>

          {/* Project Details Field */}
          <motion.div variants={itemVariants}>
            <label className="block text-body font-medium text-ink-950 mb-3">
              Project Details
            </label>
            <textarea
              name="project"
              value={formData.project}
              onChange={handleChange}
              required
              rows={6}
              className="w-full px-6 py-4 rounded-luxury border-2 border-ink-950/10 focus:border-gold-600 focus:outline-none bg-ivory/50 transition-all duration-300 text-ink-950 placeholder-text-secondary resize-none"
              placeholder="Tell us about your publishing project..."
            />
          </motion.div>

          {/* Submit Button */}
          <motion.div variants={itemVariants}>
            <motion.button
              type="submit"
              disabled={submitted}
              className="w-full py-4 bg-gold-600 text-ink-950 rounded-luxury font-semibold text-lg hover:shadow-glow-lg transition-all duration-300 disabled:opacity-50"
              whileHover={{ scale: 1.02 }}
              whileTap={{ scale: 0.98 }}
            >
              {submitted ? '✓ Message Sent!' : 'Send Inquiry'}
            </motion.button>
          </motion.div>

          {/* Success Message */}
          {submitted && (
            <motion.div
              className="p-4 bg-gold-600/10 border border-gold-600 rounded-luxury text-center text-gold-600 font-medium"
              initial={{ opacity: 0, y: -10 }}
              animate={{ opacity: 1, y: 0 }}
            >
              Thank you! We'll be in touch soon.
            </motion.div>
          )}
        </motion.form>

        {/* Contact Info */}
        <motion.div
          className="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 pt-16 border-t border-ink-950/10"
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          transition={{ delay: 0.3 }}
          viewport={{ once: true }}
        >
          <motion.div className="text-center" whileHover={{ y: -4 }}>
            <div className="text-3xl mb-3">📧</div>
            <p className="text-body-sm text-text-secondary mb-1">Email</p>
            <a href="mailto:info@kaatibpublishers.com" className="text-body font-semibold text-gold-600 hover:text-gold-700">
              info@kaatibpublishers.com
            </a>
          </motion.div>

          <motion.div className="text-center" whileHover={{ y: -4 }}>
            <div className="text-3xl mb-3">📱</div>
            <p className="text-body-sm text-text-secondary mb-1">Phone</p>
            <a href="tel:+13214567890" className="text-body font-semibold text-gold-600 hover:text-gold-700">
              +1 (321) 456-7890
            </a>
          </motion.div>

          <motion.div className="text-center" whileHover={{ y: -4 }}>
            <div className="text-3xl mb-3">📍</div>
            <p className="text-body-sm text-text-secondary mb-1">Address</p>
            <p className="text-body font-semibold text-ink-950">
              Sugar Land, TX 77479, USA
            </p>
          </motion.div>
        </motion.div>
      </div>
    </section>
  )
}
