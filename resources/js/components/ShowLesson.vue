<template>
  <!-- Reading Progress Indicator -->
  <div class="fixed top-20 right-4 space-y-3 z-30">
    <!-- Reading Progress -->
    <div class="bg-white/95 backdrop-blur-sm px-4 py-3 rounded-xl shadow-lg text-sm text-gray-700 border border-white/20">
        <div class="flex items-center space-x-2">
            <i class="fas fa-eye text-blue-500"></i>
            <span class="font-medium"><span id="reading-progress">{{ readingProgress }}</span>% read</span>
        </div>
    </div>
    
    <!-- Chat Assistant -->
    <div class="bg-white/95 backdrop-blur-sm px-4 py-3 rounded-xl shadow-lg text-sm text-gray-700 border border-white/20">
        <button @click="toggleChat" class="flex items-center space-x-2 hover:text-blue-600 transition-colors group w-full text-left">
            <i :class="chatOpen ? 'fas fa-times text-red-500' : 'fas fa-robot text-green-500'" class="transition-colors"></i>
            <span class="font-medium">{{ chatOpen ? 'Close' : 'AI Assistant' }}</span>
            <div v-if="!chatOpen && chatMessages.length > 0" class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xs font-bold ml-auto">
                {{ chatMessages.length }}
            </div>
        </button>
    </div>
</div>

  <div class="py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" :class="{ 'lg:pr-80': chatOpen }">
              
              <!-- Main Content -->
              <div class="lg:col-span-2 space-y-8">
                  
                  <!-- Lesson Header -->
                  <div class="glass rounded-2xl p-8 relative overflow-hidden hover-lift">
                      <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-500/20 to-purple-600/20 rounded-full -translate-y-16 translate-x-16"></div>
                      <div class="relative">
                          <div class="flex items-start justify-between mb-6">
                              <div class="flex-1">
                                  <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ lesson.title }}</h1>
                                  <div class="flex items-center space-x-4 text-sm text-gray-600">
                                      <div class="flex items-center">
                                          <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                                          Published {{ formatDate(lesson.created_at) }}
                                      </div>
                                      <div class="flex items-center">
                                          <i class="fas fa-clock mr-2 text-green-500"></i>
                                          {{ estimatedReadTime }} min read
                                      </div>
                                  </div>
                              </div>
                              <div class="flex items-center space-x-3">
                                  <button @click="toggleBookmark" class="p-3 rounded-xl bg-white/60 hover:bg-white/80 transition-colors">
                                      <i :class="isBookmarked ? 'fas fa-bookmark text-yellow-500' : 'far fa-bookmark text-gray-400'" class="text-lg"></i>
                                  </button>
                                  <button @click="shareLesson" class="p-3 rounded-xl bg-white/60 hover:bg-white/80 transition-colors">
                                      <i class="fas fa-share-alt text-gray-400 text-lg"></i>
                                  </button>
                              </div>
                          </div>
                          
                          <!-- Enhanced Progress Stats -->
                          <div class="flex flex-wrap gap-4 justify-center md:justify-between">
                              <div class="flex-1 min-w-[120px] text-center p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-colors">
                                  <div class="w-10 h-10 mx-auto mb-2 bg-blue-100 rounded-full flex items-center justify-center">
                                      <i class="fas fa-clock text-blue-600"></i>
                                  </div>
                                  <div class="text-xl font-bold text-gray-900">{{ timeSpent }}m</div>
                                  <div class="text-xs text-gray-600">Time Spent</div>
                              </div>
                              <div class="flex-1 min-w-[120px] text-center p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-colors">
                                  <div class="w-10 h-10 mx-auto mb-2 bg-green-100 rounded-full flex items-center justify-center">
                                      <i class="fas fa-question-circle text-green-600"></i>
                                  </div>
                                  <div class="text-xl font-bold text-gray-900">{{ chatMessages.length }}</div>
                                  <div class="text-xs text-gray-600">Questions Asked</div>
                              </div>
                              <div class="flex-1 min-w-[120px] text-center p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-colors">
                                  <div class="w-10 h-10 mx-auto mb-2 bg-purple-100 rounded-full flex items-center justify-center">
                                      <i class="fas fa-eye text-purple-600"></i>
                                  </div>
                                  <div class="text-xl font-bold text-gray-900">{{ readingProgress }}%</div>
                                  <div class="text-xs text-gray-600">Content Read</div>
                              </div>
                              <div class="flex-1 min-w-[120px] text-center p-4 bg-white/60 rounded-xl hover:bg-white/80 transition-colors">
                                  <div class="w-10 h-10 mx-auto mb-2 bg-orange-100 rounded-full flex items-center justify-center">
                                      <i class="fas fa-trophy text-orange-600"></i>
                                  </div>
                                  <div class="text-xl font-bold text-gray-900">{{ Math.round(progressPercentage) }}%</div>
                                  <div class="text-xs text-gray-600">Progress</div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <!-- Lesson Content -->
                  <div class="glass rounded-2xl p-8 hover-lift" id="lesson-content">
                      <div class="prose prose-lg max-w-full lesson-content-formatted" v-html="lesson.content"></div>
                  </div>
              </div>
          </div>
      </div>
  </div>

    <!-- AI Q&A Modal -->
    <div v-if="chatOpen" class="fixed inset-0 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
      <div class="w-full max-w-4xl h-[80vh rounded-2xl shadow-2xl overflow-hidden">
        <AIQAAssistant :lesson-id="lesson.id" 
          ref="qaAssistant"
          :embedded="false"
          :lesson-title="lesson.title"
          :lesson-content="lesson.content"
          @close="chatOpen = false"
        />
      </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, nextTick, computed } from 'vue'
import AIQAAssistant from './AskQuestion.vue'

const props = defineProps({
  lesson: {
    type: Object,
    required: true
  }
})

// Reactive state
const chatOpen = ref(false)
const startTime = Date.now()
const timeSpent = ref(0)
const readingProgress = ref(0)
const progressPercentage = ref(0)
const chatMessagesRef = ref(null)
const chatMessages = reactive([])
const hasUnreadMessages = ref(false)
const isBookmarked = ref(false)

// Computed properties
const estimatedReadTime = computed(() => {
  const wordsPerMinute = 200
  const textContent = props.lesson.content.replace(/<[^>]*>/g, '')
  const wordCount = textContent.split(/\s+/).length
  return Math.ceil(wordCount / wordsPerMinute)
})

// Methods
function formatDate(dateString) {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

function toggleChat() {
  chatOpen.value = !chatOpen.value
  if (chatOpen.value) {
    nextTick(() => {
      if (chatMessagesRef.value) {
        chatMessagesRef.value.scrollTop = chatMessagesRef.value.scrollHeight
      }
    })
  }
}

function toggleBookmark() {
  isBookmarked.value = !isBookmarked.value
  // Here you would typically save to backend
}

function shareLesson() {
  if (navigator.share) {
    navigator.share({
      title: props.lesson.title,
      url: window.location.href
    })
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href)
  }
}

function initializeProgressTracking() {
  window.addEventListener('scroll', () => {
    const lessonContent = document.getElementById('lesson-content')
    if (!lessonContent) return

    const rect = lessonContent.getBoundingClientRect()
    const windowHeight = window.innerHeight
    const contentHeight = lessonContent.scrollHeight

    const scrolled = window.scrollY + windowHeight
    const contentTop = lessonContent.offsetTop
    const contentBottom = contentTop + contentHeight

    if (scrolled > contentTop && scrolled < contentBottom) {
      readingProgress.value = Math.round(
        Math.min(100, Math.max(0, ((scrolled - contentTop) / contentHeight) * 100))
      )
    } else if (scrolled >= contentBottom) {
      readingProgress.value = 100
    }

    calculateProgress()
  })
}

function updateTimeSpent() {
  timeSpent.value = Math.floor((Date.now() - startTime) / 60000)
  calculateProgress()
}

function calculateProgress() {
  const readingWeight = Math.min(readingProgress.value * 0.4, 40)
  const timeWeight = Math.min(timeSpent.value * 2, 30)
  const engagementWeight = Math.min(chatMessages.length * 5, 30)

  progressPercentage.value = Math.min(100, Math.round(readingWeight + timeWeight + engagementWeight))
}

onMounted(() => {
  initializeProgressTracking()
  updateTimeSpent()
  setInterval(updateTimeSpent, 60000)
})
</script>

<style scoped>
.lesson-content-formatted {
  text-align: justify;
  line-height: 1.8;
  letter-spacing: 0.02em;
  overflow-wrap: break-word;
  word-break: break-word;
  white-space: normal;
}

.lesson-content-formatted p {
  text-align: justify;
  margin-bottom: 1.5rem;
}

.lesson-content-formatted h1,
.lesson-content-formatted h2,
.lesson-content-formatted h3,
.lesson-content-formatted h4 {
  text-align: left;
  font-weight: 700;
  margin-top: 2rem;
  margin-bottom: 1rem;
}

.lesson-content-formatted ul,
.lesson-content-formatted ol {
  text-align: left;
  margin-bottom: 1.5rem;
}

.resize-handle {
  position: absolute;
  left: 0;
  top: 0;
  width: 6px;
  height: 100%;
  cursor: ew-resize;
  z-index: 10;
  background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent);
  opacity: 0;
  transition: opacity 0.2s;
}

.resize-handle:hover {
  opacity: 1;
}

.chat-panel.resizing {
  user-select: none;
}

@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

/* Custom scrollbar for chat */
.chat-panel ::-webkit-scrollbar {
  width: 4px;
}

.chat-panel ::-webkit-scrollbar-track {
  background: transparent;
}

.chat-panel ::-webkit-scrollbar-thumb {
  background: rgba(59, 130, 246, 0.3);
  border-radius: 2px;
}

.chat-panel ::-webkit-scrollbar-thumb:hover {
  background: rgba(59, 130, 246, 0.5);
}

@keyframes bounce-subtle {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-4px); }
}

.animate-bounce-subtle {
  animation: bounce-subtle 2s infinite;
}

@keyframes pulse-glow {
  0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
  50% { box-shadow: 0 0 30px rgba(59, 130, 246, 0.6); }
}

.pulse-glow {
  animation: pulse-glow 2s infinite;
}
</style>
