import type { Metadata } from 'next'
import { Playfair_Display, Inter } from 'next/font/google'
import './globals.css'

const playfair = Playfair_Display({
  subsets: ['latin'],
  variable: '--font-playfair',
  weight: ['400', '500', '600', '700', '800', '900'],
})

const inter = Inter({
  subsets: ['latin'],
  variable: '--font-inter',
})

export const metadata: Metadata = {
  title: 'Kaatib Publishers | Premium Print on Demand Publishing',
  description: 'Professional Print on Demand and Self-Publishing Services for Authors, Institutions, and Creative Minds. Luxury publishing solutions with premium quality.',
  keywords: 'publishing, print on demand, self-publishing, authors, books, POD',
  openGraph: {
    title: 'Kaatib Publishers | Premium Publishing Platform',
    description: 'Professional Print on Demand and Self-Publishing Services',
    type: 'website',
  },
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en" className={`${playfair.variable} ${inter.variable}`}>
      <body className="bg-ivory font-inter text-text-primary antialiased">
        {children}
      </body>
    </html>
  )
}
