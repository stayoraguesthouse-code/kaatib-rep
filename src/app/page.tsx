import Navigation from '@/components/Navigation'
import Hero from '@/components/Hero'
import Features from '@/components/Features'
import About from '@/components/About'
import Services from '@/components/Services'
import Pricing from '@/components/Pricing'
import Books from '@/components/Books'
import Contact from '@/components/Contact'
import Footer from '@/components/Footer'

export default function Home() {
  return (
    <main className="min-h-screen bg-ivory">
      <Navigation />
      <Hero />
      <Features />
      <About />
      <Services />
      <Pricing />
      <Books />
      <Contact />
      <Footer />
    </main>
  )
}
