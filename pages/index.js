import { useEffect, useState } from 'react';
import Lottie from 'react-lottie';
import Particles from 'react-tsparticles';
import { loadFull } from 'tsparticles';
import Link from 'next/link';
import animationData from '../public/storage-animation.json';
import { useTheme } from 'next-themes';

export default function Home() {
  const [mounted, setMounted] = useState(false);
  const { theme, setTheme } = useTheme();

  useEffect(() => setMounted(true), []);

  const particlesInit = async (main) => {
    await loadFull(main);
  };

  const lottieOptions = {
    loop: true,
    autoplay: true,
    animationData: animationData,
    rendererSettings: {
      preserveAspectRatio: 'xMidYMid slice'
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-white to-gray-100 dark:from-gray-900 dark:to-gray-800">
      {/* Particles Background */}
      <Particles
        id="tsparticles"
        init={particlesInit}
        options={{
          particles: {
            number: { value: 80, density: { enable: true, value_area: 800 } },
            color: { value: theme === 'dark' ? '#ffffff' : '#000000' },
            opacity: { value: 0.5, random: false },
            size: { value: 3, random: true },
            move: { enable: true, speed: 2, direction: 'none' },
            line_linked: { 
              enable: true, 
              distance: 150, 
              color: theme === 'dark' ? '#ffffff' : '#000000',
              opacity: 0.4, 
              width: 1 
            }
          }
        }}
        className="absolute inset-0"
      />

      {/* Main Content */}
      <div className="relative z-10">
        <nav className="p-4">
          <div className="container mx-auto flex justify-between items-center">
            <h1 className="text-2xl font-bold text-gray-800 dark:text-white">
              FileVault
            </h1>
            <button
              onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}
              className="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 transition-colors"
            >
              {mounted && theme === 'dark' ? '🌞' : '🌙'}
            </button>
          </div>
        </nav>

        <main className="container mx-auto px-4 py-16">
          <div className="flex flex-col lg:flex-row items-center justify-between gap-12">
            <div className="lg:w-1/2">
              <h2 className="text-4xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                Secure File Storage
                <span className="text-blue-600 dark:text-blue-400"> Made Simple</span>
              </h2>
              <p className="text-lg text-gray-600 dark:text-gray-300 mb-8">
                Store, manage, and share your files with our modern and secure storage solution.
                Experience seamless file management with our intuitive dashboard.
              </p>
              <Link 
                href="/dashboard"
                className="inline-flex items-center px-6 py-3 text-lg font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-200"
              >
                Access Dashboard
                <svg className="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
              </Link>
            </div>
            
            <div className="lg:w-1/2">
              <div className="w-full max-w-lg mx-auto">
                <Lottie 
                  options={lottieOptions}
                  height={400}
                  width={400}
                />
              </div>
            </div>
          </div>

          {/* Features Section */}
          <div className="grid md:grid-cols-3 gap-8 mt-20">
            <div className="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg transition-transform hover:scale-105">
              <div className="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mb-4">
                <svg className="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
              </div>
              <h3 className="text-xl font-semibold text-gray-900 dark:text-white mb-2">Easy Upload</h3>
              <p className="text-gray-600 dark:text-gray-300">Drag and drop your files or use the traditional file picker.</p>
            </div>

            <div className="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg transition-transform hover:scale-105">
              <div className="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-4">
                <svg className="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
              <h3 className="text-xl font-semibold text-gray-900 dark:text-white mb-2">Secure Storage</h3>
              <p className="text-gray-600 dark:text-gray-300">Your files are encrypted and stored securely in our system.</p>
            </div>

            <div className="p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg transition-transform hover:scale-105">
              <div className="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mb-4">
                <svg className="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
              </div>
              <h3 className="text-xl font-semibold text-gray-900 dark:text-white mb-2">Easy Management</h3>
              <p className="text-gray-600 dark:text-gray-300">Organize and manage your files with our intuitive dashboard.</p>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
} 