'use client'

import { motion } from 'framer-motion'
import Image from 'next/image'

const books = [
  {
    title: 'The Pearl of Heart',
    author: 'Najma Usman',
    genre: 'Travel / Poetry',
    image: 'https://images.unsplash.com/photo-1518831959646-742c3e14ebf3?auto=format&fit=crop&q=80&w=400&h=600',
  },
  {
    title: 'Whispers of a Broken Branch',
    author: 'Najma Usman',
    genre: 'Fiction',
    image: 'https://images.unsplash.com/photo-1444492417251-9c84a5fa18e0?auto=format&fit=crop&q=80&w=400&h=600',
  },
  {
    title: 'Saawan',
    author: 'Dr. Mashood Qadri',
    genre: 'Educational / Screenplay',
    image: 'https://upload.wikimedia.org/wikipedia/en/1/12/Saawan_poster.jpeg',
  },
  {
    title: 'Doosri Kitab',
    author: 'Syed Atif Ali',
    genre: 'Urdu Fiction',
    image: 'https://images.unsplash.com/photo-1507842217343-583f20270319?auto=format&fit=crop&q=80&w=400&h=600',
  },
]

export default function Books() {
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
    <section id="books" className="py-section px-4 sm:px-6 lg:px-8 bg-ivory">
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
            Curated Collection
          </span>
          <h2 className="text-h1 font-playfair font-bold text-ink-950 mt-4 mb-4">
            Recently Published
          </h2>
          <p className="text-body text-text-secondary max-w-2xl mx-auto">
            Curated selections from the Kaatib Publishers catalog
          </p>
        </motion.div>

        {/* Books Grid */}
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: '-100px' }}
          className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8"
        >
          {books.map((book, index) => (
            <motion.div
              key={index}
              variants={itemVariants}
              className="group cursor-pointer"
              whileHover={{ y: -12 }}
            >
              {/* Book Cover */}
              <div className="relative mb-6 overflow-hidden rounded-premium shadow-luxury">
                <motion.div
                  className="aspect-[3/4] relative bg-gradient-luxury"
                  whileHover={{ scale: 1.05 }}
                  transition={{ duration: 0.5 }}
                >
                  <Image
                    src={book.image}
                    alt={book.title}
                    fill
                    className="object-cover"
                    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 25vw"
                  />

                  {/* Overlay on Hover */}
                  <motion.div
                    className="absolute inset-0 bg-ink-950/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                    initial={{ opacity: 0 }}
                    whileHover={{ opacity: 1 }}
                  >
                    <motion.button
                      className="px-6 py-2 bg-gold-600 text-ink-950 rounded-luxury font-semibold"
                      whileHover={{ scale: 1.05 }}
                      whileTap={{ scale: 0.95 }}
                    >
                      View Details
                    </motion.button>
                  </motion.div>
                </motion.div>

                {/* Genre Badge */}
                <motion.div
                  className="absolute top-4 right-4 bg-gold-600/90 text-ink-950 px-3 py-1 rounded-full text-xs font-semibold"
                  initial={{ opacity: 0, scale: 0.8 }}
                  whileInView={{ opacity: 1, scale: 1 }}
                  transition={{ delay: 0.2 }}
                >
                  {book.genre}
                </motion.div>
              </div>

              {/* Book Info */}
              <motion.div
                initial={{ opacity: 0 }}
                whileInView={{ opacity: 1 }}
                transition={{ delay: 0.1 }}
              >
                <h3 className="text-h4 font-playfair font-bold text-ink-950 mb-2 group-hover:text-gold-600 transition-colors">
                  {book.title}
                </h3>
                <p className="text-body-sm text-text-secondary">{book.author}</p>
              </motion.div>
            </motion.div>
          ))}
        </motion.div>

        {/* CTA */}
        <motion.div
          className="text-center mt-16"
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.4 }}
          viewport={{ once: true }}
        >
          <motion.a
            href="#contact"
            className="inline-block px-8 py-4 bg-ink-950 text-ivory rounded-luxury font-semibold hover:shadow-luxury transition-all duration-300"
            whileHover={{ scale: 1.02, backgroundColor: '#1F3A5F' }}
            whileTap={{ scale: 0.98 }}
          >
            Explore Full Catalog
          </motion.a>
        </motion.div>
      </div>
    </section>
  )
}
